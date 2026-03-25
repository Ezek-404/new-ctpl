<?php
namespace App\Http\Controllers;
use App\Models\CtplIssuance;
use App\Models\CocTable;
use App\Models\Vehicle;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

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

    public function savedTransactions()
    {
        $issuance = CtplIssuance::with('vehicle')->orderBy('created_at', 'desc')->get();
        return view("admin.ctpl.transactions", compact('issuance'));
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