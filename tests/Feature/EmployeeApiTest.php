<?php

use App\Enums\EmployeeRole;
use App\Models\Employee;
use App\Support\Cpf;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/**
 * @param  array<string, mixed>  $overrides
 * @return array<string, mixed>
 */
function apiEmployeePayload(array $overrides = []): array
{
    return array_merge([
        'password' => 'SenhaSegura!8',
        'password_confirmation' => 'SenhaSegura!8',
    ], $overrides);
}

test('index lists employees ordered by name', function () {
    Employee::factory()->create(['name' => 'Zeca']);
    Employee::factory()->create(['name' => 'Ana']);

    $response = $this->getJson('/api/employees');

    $response->assertSuccessful();
    $response->assertJsonCount(2, 'data');
    expect($response->json('data.0.name'))->toBe('Ana');
});

test('store creates an employee', function () {
    $payload = array_merge(apiEmployeePayload(), [
        'name' => 'João Teste',
        'cpf' => '12345678909',
        'position' => 'Auxiliar',
    ]);

    $response = $this->postJson('/api/employees', $payload);

    $response->assertCreated();
    $response->assertJsonPath('data.name', 'João Teste');
    $response->assertJsonPath('data.cpf', Cpf::formatMasked('12345678909'));
    $response->assertJsonPath('data.role', EmployeeRole::Colaborador->value);
    $this->assertDatabaseHas('employees', ['cpf' => '12345678909', 'role' => EmployeeRole::Colaborador->value]);
});

test('store rejects role on api', function () {
    $response = $this->postJson('/api/employees', array_merge(apiEmployeePayload(), [
        'name' => 'Tentativa Admin',
        'cpf' => '11144477735',
        'position' => 'Cargo',
        'role' => EmployeeRole::Admin->value,
    ]));

    $response->assertUnprocessable();
    $response->assertJsonValidationErrors(['role']);
});

test('update rejects role on api', function () {
    $employee = Employee::factory()->create(['role' => EmployeeRole::Colaborador]);

    $response = $this->putJson("/api/employees/{$employee->id}", [
        'position' => 'Outro cargo',
        'role' => EmployeeRole::Admin->value,
    ]);

    $response->assertUnprocessable();
    $response->assertJsonValidationErrors(['role']);
    expect($employee->fresh()->role)->toBe(EmployeeRole::Colaborador);
});

test('store rejects duplicate cpf', function () {
    Employee::factory()->create(['cpf' => '12345678909']);

    $response = $this->postJson('/api/employees', array_merge(apiEmployeePayload(), [
        'name' => 'Outro Nome Único',
        'cpf' => '12345678909',
        'position' => 'Cargo',
    ]));

    $response->assertUnprocessable();
});

test('store rejects invalid cpf format', function () {
    $response = $this->postJson('/api/employees', array_merge(apiEmployeePayload(), [
        'name' => 'João',
        'cpf' => '123',
        'position' => 'Cargo',
    ]));

    $response->assertUnprocessable();
});

test('store rejects cpf with invalid check digits', function () {
    $response = $this->postJson('/api/employees', array_merge(apiEmployeePayload(), [
        'name' => 'João Check',
        'cpf' => '12345678901',
        'position' => 'Cargo',
    ]));

    $response->assertUnprocessable();
});

test('store rejects duplicate name', function () {
    Employee::factory()->create([
        'name' => 'Nome Único Colisão',
        'cpf' => '52998224725',
    ]);

    $response = $this->postJson('/api/employees', array_merge(apiEmployeePayload(), [
        'name' => 'Nome Único Colisão',
        'cpf' => '11144477735',
        'position' => 'Cargo',
    ]));

    $response->assertUnprocessable();
});

test('show returns one employee', function () {
    $employee = Employee::factory()->create(['name' => 'Carla']);

    $response = $this->getJson("/api/employees/{$employee->id}");

    $response->assertSuccessful();
    $response->assertJsonPath('data.name', 'Carla');
});

test('show returns not found for missing id', function () {
    $this->getJson('/api/employees/99999')->assertNotFound();
});

test('update patches employee fields', function () {
    $employee = Employee::factory()->create(['position' => 'Atendente']);

    $response = $this->putJson("/api/employees/{$employee->id}", [
        'position' => 'Supervisora',
    ]);

    $response->assertSuccessful();
    $response->assertJsonPath('data.position', 'Supervisora');
    expect($employee->fresh()->position)->toBe('Supervisora');
});

test('update allows keeping same cpf', function () {
    $employee = Employee::factory()->create(['cpf' => '12345678909']);

    $response = $this->putJson("/api/employees/{$employee->id}", [
        'cpf' => '12345678909',
        'name' => 'Nome novo único para teste',
    ]);

    $response->assertSuccessful();
    $response->assertJsonPath('data.cpf', Cpf::formatMasked('12345678909'));
});

test('destroy deletes employee', function () {
    $employee = Employee::factory()->create();

    $response = $this->deleteJson("/api/employees/{$employee->id}");

    $response->assertNoContent();
    $this->assertDatabaseMissing('employees', ['id' => $employee->id]);
});
