<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CocTable;

class CocController extends Controller
{
    public function index()
    {
        $cocs    = CocTable::orderBy('coc_id', 'desc')->get();
        $pcCount = CocTable::where('coc_type', 'PC')->count();
        $tcCount = CocTable::where('coc_type', 'TC')->count();
        $mcCount = CocTable::where('coc_type', 'MC')->count();
        $cvCount = CocTable::where('coc_type', 'CV')->count();

        return view('admin.coc.index', compact('cocs', 'pcCount', 'tcCount', 'mcCount', 'cvCount'));
    }

    public function seriesUpload(Request $request)
    {
        $request->validate([
            'start_no' => 'required|numeric',
            'end_no' => 'required|numeric',
            'coc_type' => 'required'
        ]);

        $start = (int)$request->start_no;
        $end = (int)$request->end_no;
        
        if ($end < $start) {
            return back()->with('error', "Invalid Range: End number ($end) is lower than start number ($start).");
        }

        $totalRequested = ($end - $start) + 1;
        if ($totalRequested > 500) {
            return back()->with('error', "Limit Exceeded: Max 500 records per batch. (You tried $totalRequested)");
        }

        $existingRecords = CocTable::whereRaw('CAST(coc_no AS UNSIGNED) BETWEEN ? AND ?', [$start, $end])->get();

        if ($existingRecords->count() > 0) {
            $usedCount = $existingRecords->where('coc_status', 'Used')->count();
            $msg = "Upload Blocked: " . $existingRecords->count() . " COC number(s) already exist.";
            if ($usedCount > 0) {
                $msg .= " ($usedCount are already 'Used').";
            }
            return back()->with('error', $msg);
        }

        for ($i = $start; $i <= $end; $i++) {
            CocTable::create([
                'coc_no' => $i,
                'coc_type' => $request->coc_type,
                'coc_status' => 'Available'
            ]);
        }

        return back()->with('success', "Successfully generated $totalRequested new COCs.");
    }

    public function seriesDelete(Request $request)
    {
        $start = $request->start_no;
        $end = $request->end_no;

        $query = CocTable::whereRaw('CAST(coc_no AS UNSIGNED) BETWEEN ? AND ?', [$start, $end])
                        ->where('coc_status', 'Available');

        $count = $query->count();

        if ($count > 0) {
            $query->delete();
            return back()->with('success', "Successfully deleted $count available COCs."); 
        }

        return back()->with('error', "No available COCs found in that range to delete.");
    }

    public function previewSeries(Request $request)
    {
        $start = $request->start_no;
        $end = $request->end_no;
        $query = CocTable::whereRaw('CAST(coc_no AS UNSIGNED) BETWEEN ? AND ?', [$start, $end]);

        return response()->json([
            'total' => (clone $query)->count(),
            'available' => (clone $query)->where('coc_status', 'Available')->count(),
            'used' => (clone $query)->where('coc_status', 'Used')->count()
        ]);
    }
}