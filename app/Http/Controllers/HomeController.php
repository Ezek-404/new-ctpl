<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CocTable;

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
        $pcCount = CocTable::where('coc_type', 'PC')->where('coc_status', 'Available')->count();
        $tcCount = CocTable::where('coc_type', 'TC')->where('coc_status', 'Available')->count();
        $mcCount = CocTable::where('coc_type', 'MC')->where('coc_status', 'Available')->count();
        $cvCount = CocTable::where('coc_type', 'CV')->where('coc_status', 'Available')->count();

        return view('home', compact('pcCount', 'tcCount', 'mcCount', 'cvCount'));
    }
}
