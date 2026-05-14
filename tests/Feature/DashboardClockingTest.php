<?php

use App\Enums\ClockingType;
use App\Models\Clocking;
use App\Models\Employee;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('guest is redirected from dashboard', function () {
    $this->get(route('dashboard'))->assertRedirect(route('login'));
});

test('colaborador cannot access dashboard', function () {
    $employee = Employee::factory()->create();

    $this->actingAs($employee)->get(route('dashboard'))->assertForbidden();
});

test('admin sees dashboard with employees', function () {
    $admin = Employee::factory()->admin()->create();
    Employee::factory()->create(['name' => 'Zé Colaborador']);

    $response = $this->actingAs($admin)->get(route('dashboard'));

    $response->assertSuccessful();
    $response->assertSee('Bater o ponto', false);
    $response->assertSee('Zé Colaborador', false);
});

test('colaborador home is bater o ponto', function () {
    $worker = Employee::factory()->create();

    $response = $this->actingAs($worker)->get(route('home'));

    $response->assertSuccessful();
    $response->assertSee('Bater o ponto', false);
    $response->assertSee($worker->name, false);
});

test('admin can register first clocking as entrada', function () {
    $admin = Employee::factory()->admin()->create();
    $worker = Employee::factory()->create();

    $response = $this->actingAs($admin)->post(route('clockings.store'), [
        'employee_id' => $worker->id,
    ]);

    $response->assertRedirect(route('dashboard'));
    $response->assertSessionHas('status');

    $clocking = Clocking::query()->where('employee_id', $worker->id)->first();
    expect($clocking)->not->toBeNull();
    expect($clocking->type)->toBe(ClockingType::Entrada);
    expect($clocking->notes)->toBeNull();
});

test('admin second clocking for same employee is saida', function () {
    $admin = Employee::factory()->admin()->create();
    $worker = Employee::factory()->create();

    $this->actingAs($admin)->post(route('clockings.store'), [
        'employee_id' => $worker->id,
    ]);

    $this->actingAs($admin)->post(route('clockings.store'), [
        'employee_id' => $worker->id,
    ]);

    $types = Clocking::query()->where('employee_id', $worker->id)->orderBy('id')->pluck('type');
    expect($types[0])->toBe(ClockingType::Entrada);
    expect($types[1])->toBe(ClockingType::Saida);
});

test('admin can save optional notes', function () {
    $admin = Employee::factory()->admin()->create();
    $worker = Employee::factory()->create();

    $this->actingAs($admin)->post(route('clockings.store'), [
        'employee_id' => $worker->id,
        'notes' => 'Reunião externa',
    ]);

    expect(Clocking::query()->first()->notes)->toBe('Reunião externa');
});

test('store rejects invalid employee id', function () {
    $admin = Employee::factory()->admin()->create();

    $this->actingAs($admin)->post(route('clockings.store'), [
        'employee_id' => 999_999,
    ])->assertSessionHasErrors('employee_id');
});

test('colaborador can post clocking for self without employee_id', function () {
    $worker = Employee::factory()->create();

    $response = $this->actingAs($worker)->post(route('clockings.store'), []);

    $response->assertRedirect(route('home'));
    $response->assertSessionHas('status');

    expect(Clocking::query()->where('employee_id', $worker->id)->count())->toBe(1);
});

test('colaborador cannot spoof employee_id', function () {
    $worker = Employee::factory()->create();
    $other = Employee::factory()->create();

    $this->actingAs($worker)->post(route('clockings.store'), [
        'employee_id' => $other->id,
    ])->assertSessionHasErrors('employee_id');

    expect(Clocking::query()->count())->toBe(0);
});
