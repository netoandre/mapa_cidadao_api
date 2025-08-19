<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\OcurrenceController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\TypeOcurrenceController;
use App\Models\TypeOcurrence;
use Illuminate\Support\Facades\Route;

Route::post('/register', RegisterController::class);

Route::prefix('user')->group(function () {

    Route::post('/login', [AuthController::class, 'login']);

    // Protegidas com Sanctum
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/me', [AuthController::class, 'me']);
        Route::post('/logout', [AuthController::class, 'logout']);
    });

});

Route::prefix('ocurrences')->group(function () {

    Route::get('', [OcurrenceController::class, 'index']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('', [OcurrenceController::class, 'store']);
        Route::delete('/{ocurrence}', [OcurrenceController::class, 'destroy']);
    });

});

Route::get('/types-ocurrence', function () {


    $typesOcurrences = TypeOcurrence::all();

    return response()->json([
        'types_ocurrences' => $typesOcurrences,
    ]);
});
