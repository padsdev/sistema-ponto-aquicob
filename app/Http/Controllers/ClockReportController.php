<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class ClockReportController extends Controller
{
    public function index(): View
    {
        return view('reports.clock');
    }
}
