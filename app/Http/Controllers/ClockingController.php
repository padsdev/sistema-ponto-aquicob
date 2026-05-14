<?php

namespace App\Http\Controllers;

use App\Enums\ClockingType;
use App\Http\Requests\StoreClockingRequest;
use App\Models\Clocking;
use App\Models\Employee;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class ClockingController extends Controller
{
    public function store(StoreClockingRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $employeeId = $request->user()->isAdmin()
            ? (int) $validated['employee_id']
            : (int) $request->user()->id;
        $notes = isset($validated['notes']) ? trim((string) $validated['notes']) : null;
        $notes = $notes === '' ? null : $notes;

        DB::transaction(function () use ($employeeId, $notes): void {
            Employee::query()->whereKey($employeeId)->lockForUpdate()->firstOrFail();

            $last = Clocking::query()
                ->where('employee_id', $employeeId)
                ->lockForUpdate()
                ->orderByDesc('punched_at')
                ->orderByDesc('id')
                ->first();

            $type = match ($last?->type) {
                null, ClockingType::Saida => ClockingType::Entrada,
                ClockingType::Entrada => ClockingType::Saida,
            };

            Clocking::query()->create([
                'employee_id' => $employeeId,
                'punched_at' => now(),
                'type' => $type,
                'notes' => $notes,
            ]);
        });

        $redirectRoute = $request->user()->isAdmin() ? 'dashboard' : 'home';

        return redirect()
            ->route($redirectRoute)
            ->with('status', 'Marcação registada com sucesso.');
    }
}
