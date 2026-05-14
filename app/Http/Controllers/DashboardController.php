<?php

namespace App\Http\Controllers;

use App\Models\Clocking;
use App\Models\Employee;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $employees = Employee::query()->with('latestClocking')->orderBy('name')->get();

        $recentClockings = Clocking::query()
            ->with('employee')
            ->orderByDesc('punched_at')
            ->orderByDesc('id')
            ->limit(30)
            ->get();

        $selectEmployee = true;

        return view('dashboard', compact('employees', 'recentClockings', 'selectEmployee'));
    }

    public function employeeHome(): View
    {
        /** @var Employee $employee */
        $employee = auth()->user()->load('latestClocking');
        $employees = new Collection([$employee]);

        $recentClockings = Clocking::query()
            ->with('employee')
            ->where('employee_id', $employee->id)
            ->orderByDesc('punched_at')
            ->orderByDesc('id')
            ->limit(30)
            ->get();

        $selectEmployee = false;

        return view('dashboard', compact('employees', 'recentClockings', 'selectEmployee'));
    }
}
