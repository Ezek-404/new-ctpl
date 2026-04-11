<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ComprehensiveController extends Controller
{
    public function index()
    {
        // Points to admin/comprehensive folder you created
        return view('admin.comprehensive.index');
    }
}
