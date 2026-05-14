<?php

use App\Enums\ClockingType;
use App\Models\Clocking;
use App\Models\Employee;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('colaborador cannot access relatorio de ponto', function () {
    $worker = Employee::factory()->create();

    $this->actingAs($worker)->get(route('reports.clock'))->assertForbidden();
});

test('admin sees relatorio page', function () {
    $admin = Employee::factory()->admin()->create();

    $this->actingAs($admin)->get(route('reports.clock'))->assertSuccessful()->assertSee('Relatório de ponto', false);
});

test('admin gerar shows batidas and horas no dia', function () {
    $admin = Employee::factory()->admin()->create();
    $worker = Employee::factory()->create(['name' => 'Carla Teste']);

    $day = now()->startOfDay();
    Clocking::query()->create([
        'employee_id' => $worker->id,
        'punched_at' => $day->copy()->setTime(8, 59),
        'type' => ClockingType::Entrada,
        'notes' => null,
    ]);
    Clocking::query()->create([
        'employee_id' => $worker->id,
        'punched_at' => $day->copy()->setTime(12, 3),
        'type' => ClockingType::Saida,
        'notes' => null,
    ]);

    $from = $day->toDateString();
    $to = $day->toDateString();

    $response = $this->actingAs($admin)->get(route('reports.clock', [
        'gerar' => '1',
        'employee_id' => $worker->id,
        'date_from' => $from,
        'date_to' => $to,
    ]));

    $response->assertSuccessful();
    $response->assertSee('08:59 E', false);
    $response->assertSee('12:03 S', false);
    $response->assertSee('04:04', false);
    $response->assertSee('Total no período', false);
});

test('admin can download csv', function () {
    $admin = Employee::factory()->admin()->create();
    $worker = Employee::factory()->create();

    $day = now()->startOfDay();
    Clocking::query()->create([
        'employee_id' => $worker->id,
        'punched_at' => $day->copy()->setTime(9, 0),
        'type' => ClockingType::Entrada,
        'notes' => null,
    ]);

    $from = $day->toDateString();
    $to = $day->toDateString();

    $response = $this->actingAs($admin)->get(route('reports.clock.export.csv', [
        'employee_id' => $worker->id,
        'date_from' => $from,
        'date_to' => $to,
    ]));
    $response->assertOk();
    expect(strtolower((string) $response->headers->get('content-type')))->toContain('csv');
});
