<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExaminationController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\StudentController;
use Illuminate\Support\Facades\Route;

Route::get('/', DashboardController::class)->name('home');

// Route::middleware(['auth', 'verified'])->group(function () {
//     Route::inertia('dashboard', 'dashboard')->name('dashboard');
// });

// Route::middleware(['auth'])->group(function () {
Route::get('/dashboard', DashboardController::class)->name('dashboard');
Route::get('/examinations', [ExaminationController::class, 'index'])->name('examinations.index');
Route::get('/examinations/{examination}', [ExaminationController::class, 'show'])->name('examinations.show');
Route::post('/examinations/{examination}/marks/imports', [ExaminationController::class, 'import'])->name('examinations.import');
Route::get('/students', [StudentController::class, 'index'])->name('students.index');
Route::get('/imports/{uuid}', [ImportController::class, 'show'])->name('imports.show');
// });

require __DIR__ . '/settings.php';
