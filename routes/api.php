<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\MeteoController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/public/meteo', [MeteoController::class, 'index']);


Route::get('/alojamentos', [AlojamentoController::class, 'index']);
Route::get('/alojamentos/{id}', [AlojamentoController::class, 'show']);

Route::post('/alojamentos/{id}/available', [ReservaController::class, 'available']);
Route::middleware('auth:sanctum')->group(function() {
    Route::post('/reservas', [ReservaController::class, 'store']);
    Route::get('/reservas/me', [ReservaController::class, 'myReservations']);
});
