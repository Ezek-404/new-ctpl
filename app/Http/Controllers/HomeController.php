<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CocTable;
use App\Models\CtplIssuance;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        // Inventory Counts for Top Cards (Available)
        $availPC = CocTable::where('coc_type', 'PC')->where('coc_status', 'Available')->count();
        $availTC = CocTable::where('coc_type', 'TC')->where('coc_status', 'Available')->count();
        $availMC = CocTable::where('coc_type', 'MC')->where('coc_status', 'Available')->count();
        $availCV = CocTable::where('coc_type', 'CV')->where('coc_status', 'Available')->count();

        $currentYear = now()->year;
        // Prepare arrays for Jan to April
        $months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
        $pcData = []; $mcData = []; $tcData = []; $cvData = [];

        // Loop through months 1 to 4 (Jan to Apr)
        for ($m = 1; $m <= 12; $m++) {
            // PC Insured
           $pcData[] = CtplIssuance::whereYear('created_at', $currentYear)
            ->whereMonth('created_at', $m)
            ->whereHas('vehicle', fn($q) => $q->whereIn('denomination', ['Car', 'SUV', 'Sedan', 'Utility Vehicle', 'Hatchback', 'Coupe', 'Passenger Car']))
            ->count();

            // MC Insured
            $mcData[] = CtplIssuance::whereYear('created_at', $currentYear)
            ->whereMonth('created_at', $m)
                ->whereHas('vehicle', fn($q) => $q->whereIn('denomination', ['MC', 'MTC']))
                ->count();

            // TC Insured
            $tcData[] = CtplIssuance::whereYear('created_at', $currentYear)
            ->whereMonth('created_at', $m)
                ->whereHas('vehicle', fn($q) => $q->where('denomination', 'Tricycle'))
                ->count();

            // CV Insured
            $cvData[] = CtplIssuance::whereYear('created_at', $currentYear)
            ->whereMonth('created_at', $m)
                ->whereHas('vehicle', fn($q) => $q->whereIn('denomination', ['Truck', 'Trailer']))
                ->count();
        }

        // Totals for the footer report
        $pcInsured = array_sum($pcData);
        $mcInsured = array_sum($mcData);
        $tcInsured = array_sum($tcData);
        $cvInsured = array_sum($cvData);

        return view('home', compact(
            'availPC', 'availTC', 'availMC', 'availCV',
            'months', 'pcData', 'mcData', 'tcData', 'cvData',
            'pcInsured', 'mcInsured', 'tcInsured', 'cvInsured'
        ));
    }
}
