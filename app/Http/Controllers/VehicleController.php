<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class VehicleController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Vehicle::select(['vehicle_id', 'file_no', 'plate_no', 'assured', 'address', 'engine_no', 'chassis_no', 'denomination', 'color']);

            return datatables()->of($query)
                ->addIndexColumn()
                ->addColumn('action', function($row){
                    // View Button
                    $viewBtn = '<a href="'.route('admin.vehicles.show', $row->vehicle_id).'" class="btn btn-xs btn-info" title="View"><i class="fas fa-eye"></i></a>';
                    
                    // Edit Button
                    $editBtn = '<a href="'.route('admin.vehicles.edit', $row->vehicle_id).'" class="btn btn-xs btn-warning" title="Edit"><i class="fas fa-edit"></i></a>';

                    // Return only the 2 buttons
                    return '<div class="btn-group">' . $viewBtn . $editBtn . '</div>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('admin.vehicles.index');
    }

    public function show($id)
    {
        $vehicle = Vehicle::where('vehicle_id', $id)->firstOrFail();
        
        $issuance = \DB::table('ctpl_issuances')
                        ->where('vehicle_id', $id)
                        ->latest('created_at')
                        ->first();

        return view('admin.vehicles.show', compact('vehicle', 'issuance'));
    }

    public function edit($id)
    {
        $vehicle = Vehicle::where('vehicle_id', $id)->firstOrFail();
        return view('admin.vehicles.edit', compact('vehicle'));
    }
        
}