<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\ExaminationApiController;
use App\Http\Controllers\Api\V1\ImportApiController;
use App\Http\Controllers\Api\V1\ResultApiController;
use App\Models\Programme;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::prefix('v1')->group(function () {
    // Route::post('/auth/token', [AuthController::class, 'token']);
    // Route::middleware('auth:sanctum')->group(function () {
        Route::get('/programmes', fn() => Programme::query()->where('is_active', true)->paginate(100));
        Route::get('/students', fn() => Student::query()->with('programme')->paginate(100));
        Route::get('/examinations', [ExaminationApiController::class, 'index']);
        Route::post('/examinations/{examination}/marks/imports', [ExaminationApiController::class, 'import']);
        Route::get('/imports/{uuid}', [ImportApiController::class, 'show']);
        Route::post('/examinations/{examination}/calculate', [ResultApiController::class, 'calculate']);
        Route::post('/examinations/{examination}/publish', [ResultApiController::class, 'publish']);
        Route::get('/examinations/{examination}/results', [ResultApiController::class, 'results']);
    // });
});
