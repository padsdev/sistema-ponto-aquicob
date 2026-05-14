<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ClockReportController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmployeeController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function (): void {
    Route::get('/', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->name('login.store');
});

Route::middleware('auth')->group(function (): void {
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');
    Route::get('/inicio', [DashboardController::class, 'employeeHome'])->name('home');
});

Route::middleware(['auth', 'admin'])->group(function (): void {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/relatorio-ponto', [ClockReportController::class, 'index'])->name('reports.clock');
    Route::resource('employees', EmployeeController::class)->only(['index', 'store', 'update', 'destroy']);
});
