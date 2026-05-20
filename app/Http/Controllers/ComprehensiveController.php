<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ComprehensiveInsurance; 
use Validator;

class ComprehensiveController extends Controller
{
    /**
     * Display the comprehensive insurance landing page.
     */
    public function index()
    {
        // Mapped exactly to: resources/views/admin/comprehensive/index.blade.php
        return view('admin.comprehensive.index'); 
    }

    /**
     * Store a newly created policy resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'policy_no'   => 'required|string|unique:comprehensive_insurances,policy_no',
            'assured'     => 'required|string|max:255',
            'value'       => 'required|numeric',
            'rate'        => 'required|numeric',
            'mortgagee'   => 'nullable|string|max:255',
            'pd'          => 'nullable|numeric',
            'bi'          => 'nullable|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        try {
            $data = $request->only([
                'policy_no', 'soa_no', 'assured', 'address', 'mortgagee', 
                'value', 'rate', 'pd', 'bi', 'model', 'brand', 'type', 
                'color', 'plate_no', 'chassis_no', 'engine_no', 'file_no'
            ]);

            ComprehensiveInsurance::create($data);

            return response()->json(['success' => true, 'message' => 'Policy successfully recorded.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}