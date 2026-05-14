<?php

namespace App\Services;

use App\Enums\ClockingType;
use App\Models\Clocking;
use Carbon\CarbonInterface;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

final class ClockReportService
{
    /**
     * @return array{rows: list<array{date: string, punches_line: string, minutes: int, hours_label: string}>, total_minutes: int}
     */
    public function build(int $employeeId, CarbonInterface $dateFrom, CarbonInterface $dateTo): array
    {
        $tz = (string) config('app.timezone');

        $from = Carbon::parse($dateFrom, $tz)->startOfDay();
        $to = Carbon::parse($dateTo, $tz)->endOfDay();

        /** @var Collection<string, Collection<int, Clocking>> $byDay */
        $byDay = Clocking::query()
            ->where('employee_id', $employeeId)
            ->whereBetween('punched_at', [$from, $to])
            ->orderBy('punched_at')
            ->orderBy('id')
            ->get()
            ->groupBy(fn (Clocking $c): string => $c->punched_at->timezone($tz)->format('Y-m-d'))
            ->sortKeys();

        $rows = [];
        $totalMinutes = 0;

        foreach ($byDay as $dateStr => $dayClockings) {
            $minutes = $this->minutesWorkedForDay($dayClockings);
            $totalMinutes += $minutes;

            $parts = [];
            foreach ($dayClockings->sortBy(['punched_at', 'id']) as $c) {
                $t = $c->punched_at->timezone($tz)->format('H:i');
                $letter = $c->type === ClockingType::Entrada ? 'E' : 'S';
                $parts[] = "{$t} {$letter}";
            }

            $rows[] = [
                'date' => $dateStr,
                'punches_line' => implode(', ', $parts),
                'minutes' => $minutes,
                'hours_label' => $this->formatHoursMinutes($minutes, padHours: true),
            ];
        }

        return [
            'rows' => $rows,
            'total_minutes' => $totalMinutes,
        ];
    }

    /**
     * @param  Collection<int, Clocking>  $dayClockings
     */
    private function minutesWorkedForDay(Collection $dayClockings): int
    {
        $ordered = $dayClockings->sortBy(['punched_at', 'id'])->values();
        $total = 0;
        $openEntrada = null;

        foreach ($ordered as $c) {
            if ($c->type === ClockingType::Entrada) {
                $openEntrada = $c->punched_at;
            } elseif ($c->type === ClockingType::Saida && $openEntrada !== null) {
                $total += (int) round($openEntrada->diffInMinutes($c->punched_at));
                $openEntrada = null;
            }
        }

        return $total;
    }

    private function formatHoursMinutes(int $totalMinutes, bool $padHours): string
    {
        $h = intdiv($totalMinutes, 60);
        $m = $totalMinutes % 60;

        if ($padHours) {
            return sprintf('%02d:%02d', $h, $m);
        }

        return sprintf('%d:%02d', $h, $m);
    }

    public function formatPeriodTotal(int $totalMinutes): string
    {
        return $this->formatHoursMinutes($totalMinutes, padHours: false);
    }
}
