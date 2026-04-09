<?php
namespace App\Http\Controllers;
use App\Models\CtplIssuance;
use App\Models\CocTable;
use App\Models\Vehicle;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class CtplController extends Controller
{
    public function index() 
    { 
        $allCocs = CocTable::all(); 
        return view('admin.ctpl.index', compact('allCocs')); 
    }

    public function searchVehicle(Request $request)
    {
        $query = $request->get('query');
        $vehicle = Vehicle::where('plate_no', $query)->orWhere('file_no', $query)->first();

        if ($vehicle) {
            // Fetch the latest transaction date for this vehicle
            $latestTransaction = \DB::table('ctpl_issuances')
                // Match based on vehicle_id
                ->where('vehicle_id', $vehicle->vehicle_id) 
                ->latest('created_at')
                ->first();

            return response()->json([
                'success' => true, 
                'data' => $vehicle,
                'latest_transaction' => $latestTransaction ? date('M d, Y h:i A', strtotime($latestTransaction->created_at)) : null
            ]);
        }

        return response()->json(['success' => false, 'message' => 'Vehicle not found.']);
    }

    public function store(Request $request)
    {
        $request->validate([
            'coc_id_hidden' => 'required|exists:coc_table,coc_id',
            'policy_no' => 'required|string',
            'assured' => 'required',
            'denomination' => 'required|string', // Ensure denomination is present for logic
            'amount' => [
                'required',
                'numeric',
                function ($attribute, $value, $fail) use ($request) {
                    // Define logic ranges to match your JS priceConfig
                    $ranges = [
                        'MC' => [550, 650], 'MTC' => [550, 650], 'TRICYCLE' => [550, 650],
                        'CAR' => [950, 1380], 'SEDAN' => [950, 1380], 'HATCHBACK' => [950, 1380], 
                        'PASSENGER CAR' => [950, 1380], 'COUPE' => [950, 1380],
                        'UTILITY VEHICLE' => [1050, 1380], 'SUV' => [1050, 1380],
                        'TRUCK' => [1500, 2000], 'TRAILER' => [1500, 2000],
                    ];

                    $denom = $request->denomination;

                    if (isset($ranges[$denom])) {
                        [$min, $max] = $ranges[$denom];
                        if ($value < $min || $value > $max) {
                            $fail("The amount for $denom must be between ₱$min and ₱$max.");
                        }
                    }
                },
            ],
        ]);

        try {
            $transactionId = DB::transaction(function () use ($request) {
                // 1. Handle Vehicle Info
                $vehicle = Vehicle::updateOrCreate(
                    ['vehicle_id' => $request->vehicle_id_hidden],
                    [
                        'plate_no'     => $request->plate_no,
                        'file_no'      => $request->file_no,
                        'assured'      => $request->assured,
                        'address'      => $request->address,
                        'year_model'   => $request->year_model,
                        'make'         => $request->make,
                        'series'       => $request->series,
                        'color'        => $request->color,
                        'engine_no'    => $request->engine_no,
                        'chassis_no'   => $request->chassis_no,
                        'denomination' => $request->denomination,
                    ]
                );

                // 2. Mark COC as Used
                CocTable::where('coc_id', $request->coc_id_hidden)->update(['coc_status' => 'Used']);

                // 3. Create Issuance with the validated amount
                $issuance = CtplIssuance::create([
                    'agent'      => $request->agent,
                    'amount'     => $request->amount,
                    'policy_no'  => $request->policy_no,
                    'coc_id'     => $request->coc_id_hidden,
                    'vehicle_id' => $vehicle->vehicle_id,    
                ]);

                return $issuance->transaction_id;
            });

            return redirect()->route('admin.ctpl.print', ['id' => $transactionId]);

        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function savedTransactions(Request $request)
    {
        if ($request->ajax()) {
            $data = CtplIssuance::with(['vehicle', 'coc'])->select('ctpl_issuances.*');

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('checkbox', function($row) {
                    return '<input type="checkbox" class="row-checkbox" value="'.$row->id.'">';
                })
                ->editColumn('created_at', function($row) {
                    return '<span class="text-nowrap">' . 
                            $row->created_at->format('M d, Y') . ' | ' . 
                            $row->created_at->format('h:i A') . 
                        '</span>';
                })
                ->addColumn('coc_no', function($row) {
                    return '<span class="text-danger font-weight-bold">' . ($row->coc->coc_no ?? 'N/A') . '</span>';
                })
                // UPDATED: Detects type for Ribbon, then cleans the name
                ->editColumn('agent', function($row) {
                    $agent = $row->agent ?? 'N/A';
                    $ribbon = '';

                    // 1. Determine Ribbon Type
                    if (stripos($agent, 'W/SMOKE') !== false || stripos($agent, 'W/ SMOKE') !== false) {
                        // Use Info color for Smoke variants
                        $ribbon = '<div class="ribbon-wrapper ribbon-xs"><div class="ribbon bg-info text-xs">SMOKE</div></div>';
                    } elseif (stripos($agent, 'TPL') !== false || stripos($agent, 'PAUL') !== false) {
                        // Use Warning (Orange/Yellow) for TPL and Paul
                        $ribbon = '<div class="ribbon-wrapper ribbon-xs"><div class="ribbon bg-warning text-xs">TPL</div></div>';
                    } elseif (stripos($agent, 'NA') !== false) {
                        // Use Success (Green) for NA
                        $ribbon = '<div class="ribbon-wrapper ribbon-xs"><div class="ribbon bg-success text-xs">NA</div></div>';
                    }

                    // 2. Clean the string for display
                    $unwanted = [' NA', ' TPL', ' W/SMOKE', ' W/ SMOKE'];
                    $cleanAgent = trim(preg_replace('/\s#\d+/', '', str_ireplace($unwanted, '', $agent)));

                    // 3. Return with Relative Container
                    return '
                        <div class="position-relative p-2" style="min-height: 45px;">
                            ' . $ribbon . '
                            <span class="text-uppercase">' . $cleanAgent . '</span>
                        </div>';
                })
                ->editColumn('vehicle.assured', function($row) {
                    return '<span class="text-uppercase">' . ($row->vehicle->assured ?? 'N/A') . '</span>';
                })
                ->addColumn('action', function($row) {
                    $viewUrl  = route('admin.ctpl.view', $row->transaction_id);
                    $editUrl  = route('admin.ctpl.edit', $row->transaction_id);
                    $printUrl = route('admin.ctpl.print', $row->transaction_id);

                    return '
                    <div class="action-buttons d-flex justify-content-center">
                        <a href="'.$viewUrl.'" class="btn btn-sm text-primary p-1 mx-1" title="View Details"><i class="fas fa-eye"></i></a>
                        <a href="'.$editUrl.'" class="btn btn-sm text-warning p-1 mx-1" title="Edit Policy"><i class="fas fa-edit"></i></a>
                        <a href="'.$printUrl.'" class="btn btn-sm text-primary p-1 mx-1" title="Print Policy"><i class="fas fa-print"></i></a>
                    </div>';
                })
                ->rawColumns(['checkbox', 'created_at', 'agent', 'coc_no', 'vehicle.assured', 'action'])
                ->make(true);
        }

        return view('admin.ctpl.transactions');
    }

    // Display the View page we created
    public function show($id)
    {
        $issuance = \App\Models\CtplIssuance::with(['vehicle', 'coc'])->findOrFail($id);
        
        return view('admin.ctpl.view', compact('issuance'));
    }

    // Display the Edit page
    public function edit($id)
    {
        $issuance = \App\Models\CtplIssuance::with(['vehicle', 'coc'])->findOrFail($id);
        return view('admin.ctpl.edit', compact('issuance'));
    }

    public function update(Request $request, $id)
    {
        $issuance = \App\Models\CtplIssuance::with('vehicle')->findOrFail($id);

        // Update the linked vehicle record
        $issuance->vehicle->update([
            'assured'    => strtoupper($request->assured),
            'plate_no'   => strtoupper($request->plate_no),
            'file_no'    => $request->file_no,
            'engine_no'  => $request->engine_no,
            'chassis_no' => $request->chassis_no,
        ]);

        return redirect()->route('admin.ctpl.view', $id)
                        ->with('success', 'Vehicle details updated successfully.');
    }

    public function showPrint($id)
    {
        $issuance = CtplIssuance::with(['vehicle', 'coc'])->where('transaction_id', $id)->firstOrFail();
        $denom = strtoupper($issuance->vehicle->denomination);
        $folder = 'pc';
        if (str_contains($denom, 'MC') || str_contains($denom, 'MTC')) {
            $folder = 'mc';
        } elseif (str_contains($denom, 'TRICYCLE')) {
            $folder = 'tc';
        } elseif (str_contains($denom, 'TRUCK') || str_contains($denom, 'TRAILER')) {
            $folder = 'cv';
        } else {
            $folder = 'pc';
        }
        return view("admin.ctpl.print.{$folder}.coc", compact('issuance', 'folder'));
    }
}