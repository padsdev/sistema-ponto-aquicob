<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Services\ClockReportService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ClockReportController extends Controller
{
    private const MAX_PERIOD_DAYS = 366;

    public function __construct(
        private ClockReportService $clockReportService
    ) {}

    public function index(Request $request): View
    {
        $employees = Employee::query()->orderBy('name')->get();

        $defaultFrom = Carbon::now()->startOfMonth()->toDateString();
        $defaultTo = Carbon::now()->endOfMonth()->toDateString();

        $reportRows = [];
        $totalMinutes = 0;
        $selectedEmployee = null;
        $dateFromValue = old('date_from', $request->input('date_from', $defaultFrom));
        $dateToValue = old('date_to', $request->input('date_to', $defaultTo));
        $employeeIdValue = old('employee_id', $request->input('employee_id', $employees->first()?->id));

        if ($request->boolean('gerar') && $employees->isNotEmpty()) {
            $validated = $this->validatedFilters($request);
            $this->assertPeriodWithinMaxDays($validated['date_from'], $validated['date_to']);

            $selectedEmployee = Employee::query()->findOrFail($validated['employee_id']);
            $dateFromValue = $validated['date_from'];
            $dateToValue = $validated['date_to'];
            $employeeIdValue = (string) $validated['employee_id'];

            $built = $this->clockReportService->build(
                (int) $validated['employee_id'],
                Carbon::parse($validated['date_from']),
                Carbon::parse($validated['date_to'])
            );
            $reportRows = $built['rows'];
            $totalMinutes = $built['total_minutes'];
        }

        $totalPeriodLabel = $this->clockReportService->formatPeriodTotal($totalMinutes);

        return view('reports.clock', compact(
            'employees',
            'reportRows',
            'totalMinutes',
            'totalPeriodLabel',
            'selectedEmployee',
            'dateFromValue',
            'dateToValue',
            'employeeIdValue'
        ));
    }

    public function exportCsv(Request $request): StreamedResponse
    {
        $validated = $this->validatedFilters($request);
        $this->assertPeriodWithinMaxDays($validated['date_from'], $validated['date_to']);

        $employee = Employee::query()->findOrFail($validated['employee_id']);
        $built = $this->clockReportService->build(
            (int) $validated['employee_id'],
            Carbon::parse($validated['date_from']),
            Carbon::parse($validated['date_to'])
        );

        $filename = 'relatorio-ponto-'.$employee->id.'-'.$validated['date_from'].'-'.$validated['date_to'].'.csv';

        $totalLabel = $this->clockReportService->formatPeriodTotal($built['total_minutes']);

        return response()->streamDownload(function () use ($built, $employee, $validated, $totalLabel): void {
            $out = fopen('php://output', 'w');
            if ($out === false) {
                return;
            }
            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, ['Funcionário', $employee->name], ';');
            fputcsv($out, ['Período', $validated['date_from'].' a '.$validated['date_to']], ';');
            fputcsv($out, [], ';');
            fputcsv($out, ['Dia', 'Batidas (hh:mm)', 'Horas no dia'], ';');

            foreach ($built['rows'] as $row) {
                $day = Carbon::parse($row['date'])->format('d/m/Y');
                fputcsv($out, [$day, $row['punches_line'], $row['hours_label']], ';');
            }

            fputcsv($out, [], ';');
            fputcsv($out, ['Total no período', $totalLabel], ';');
            fclose($out);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function exportPrint(Request $request): View
    {
        $validated = $this->validatedFilters($request);
        $this->assertPeriodWithinMaxDays($validated['date_from'], $validated['date_to']);

        $employee = Employee::query()->findOrFail($validated['employee_id']);
        $built = $this->clockReportService->build(
            (int) $validated['employee_id'],
            Carbon::parse($validated['date_from']),
            Carbon::parse($validated['date_to'])
        );

        $totalPeriodLabel = $this->clockReportService->formatPeriodTotal($built['total_minutes']);
        $periodLabel = Carbon::parse($validated['date_from'])->format('d/m/Y')
            .' — '
            .Carbon::parse($validated['date_to'])->format('d/m/Y');

        return view('reports.clock-print', [
            'employee' => $employee,
            'reportRows' => $built['rows'],
            'totalPeriodLabel' => $totalPeriodLabel,
            'periodLabel' => $periodLabel,
        ]);
    }

    /**
     * @return array{employee_id: int, date_from: string, date_to: string}
     */
    private function validatedFilters(Request $request): array
    {
        /** @var array{employee_id: int, date_from: string, date_to: string} $validated */
        $validated = Validator::make($request->query(), [
            'employee_id' => ['required', 'integer', Rule::exists('employees', 'id')],
            'date_from' => ['required', 'date'],
            'date_to' => ['required', 'date', 'after_or_equal:date_from'],
        ])->validate();

        return $validated;
    }

    private function assertPeriodWithinMaxDays(string $dateFrom, string $dateTo): void
    {
        $from = Carbon::parse($dateFrom)->startOfDay();
        $to = Carbon::parse($dateTo)->startOfDay();

        if ($from->diffInDays($to) > self::MAX_PERIOD_DAYS) {
            throw ValidationException::withMessages([
                'date_to' => 'O período não pode ultrapassar '.self::MAX_PERIOD_DAYS.' dias.',
            ]);
        }
    }
}
