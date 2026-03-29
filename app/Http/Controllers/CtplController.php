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
        ]);
        try {
            $transactionId = DB::transaction(function () use ($request) {
                $vehicle = Vehicle::updateOrCreate(
                    ['vehicle_id' => $request->vehicle_id_hidden],
                    [
                        'plate_no' => $request->plate_no,
                        'file_no' => $request->file_no,
                        'assured' => $request->assured,
                        'address' => $request->address,
                        'year_model' => $request->year_model,
                        'make' => $request->make,
                        'series' => $request->series,
                        'color' => $request->color,
                        'engine_no' => $request->engine_no,
                        'chassis_no' => $request->chassis_no,
                        'denomination' => $request->denomination,
                    ]
                );
                CocTable::where('coc_id', $request->coc_id_hidden)->update(['coc_status' => 'Used']);
                $issuance = CtplIssuance::create([
                    'agent' => $request->agent,
                    'policy_no' => $request->policy_no,
                    'coc_id' => $request->coc_id_hidden,
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
            // Eager load relations to include MV File in search results
            $data = CtplIssuance::with(['vehicle', 'coc'])->select('ctpl_issuances.*');

            return DataTables::of($data)
                ->addIndexColumn()
                // Format Date & Time into a single line
                ->editColumn('created_at', function($row) {
                    return '<span class="text-nowrap font-weight-bold">' . 
                            $row->created_at->format('M d, Y') . ' | ' . 
                            $row->created_at->format('h:i A') . 
                        '</span>';
                })
                // Separate Column for COC No
                ->addColumn('coc_no', function($row) {
                    return '<span class="text-danger font-weight-bold">' . ($row->coc->coc_no ?? 'N/A') . '</span>';
                })
                // Separate Column for Agent
                ->editColumn('agent', function($row) {
                    return '<span class="text-uppercase">' . ($row->agent ?? 'N/A') . '</span>';
                })
                // Assured column (MV File removed from display)
                ->editColumn('vehicle.assured', function($row) {
                    return '<span class="text-uppercase font-weight-bold">' . ($row->vehicle->assured ?? 'N/A') . '</span>';
                })
                // Compact Action Column with Icons
                ->addColumn('action', function($row) {
                    // We use the transaction_id for all actions now
                    $viewUrl  = route('admin.ctpl.view', $row->transaction_id);
                    $editUrl  = route('admin.ctpl.edit', $row->transaction_id);
                    $printUrl = route('admin.ctpl.print', $row->transaction_id);

                    return '
                    <div class="action-buttons d-flex justify-content-center">
                        <a href="'.$viewUrl.'" class="btn btn-sm text-primary p-1 mx-1" title="View Details">
                            <i class="fa fa-lg fa-eye"></i>
                        </a>
                        <a href="'.$editUrl.'" class="btn btn-sm text-warning p-1 mx-1" title="Edit Policy">
                            <i class="fa fa-lg fa-pen"></i>
                        </a>
                        <a href="'.$printUrl.'" class="btn btn-sm text-info p-1 mx-1" title="Print Policy">
                            <i class="fa fa-lg fa-print"></i>
                        </a>
                    </div>';
                })
                ->rawColumns(['created_at', 'agent', 'coc_no', 'vehicle.assured', 'action'])
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