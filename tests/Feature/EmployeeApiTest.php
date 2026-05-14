<?php

use App\Models\Employee;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('index lists employees ordered by name', function () {
    Employee::factory()->create(['name' => 'Zeca']);
    Employee::factory()->create(['name' => 'Ana']);

    $response = $this->getJson('/api/employees');

    $response->assertSuccessful();
    $response->assertJsonCount(2, 'data');
    expect($response->json('data.0.name'))->toBe('Ana');
});

test('store creates an employee', function () {
    $payload = [
        'name' => 'João Teste',
        'cpf' => '12345678909',
        'position' => 'Auxiliar',
    ];

    $response = $this->postJson('/api/employees', $payload);

    $response->assertCreated();
    $response->assertJsonPath('data.name', 'João Teste');
    $this->assertDatabaseHas('employees', ['cpf' => '12345678909']);
});

test('store rejects duplicate cpf', function () {
    Employee::factory()->create(['cpf' => '12345678909']);

    $response = $this->postJson('/api/employees', [
        'name' => 'Outro',
        'cpf' => '12345678909',
        'position' => 'Cargo',
    ]);

    $response->assertUnprocessable();
});

test('store rejects invalid cpf format', function () {
    $response = $this->postJson('/api/employees', [
        'name' => 'João',
        'cpf' => '123',
        'position' => 'Cargo',
    ]);

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
        'name' => 'Nome novo',
    ]);

    $response->assertSuccessful();
    $response->assertJsonPath('data.cpf', '12345678909');
});

test('destroy deletes employee', function () {
    $employee = Employee::factory()->create();

    $response = $this->deleteJson("/api/employees/{$employee->id}");

    $response->assertNoContent();
    $this->assertDatabaseMissing('employees', ['id' => $employee->id]);
});
