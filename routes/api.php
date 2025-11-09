<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\MeteoController;
use App\Http\Controllers\Api\ReservaController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\CurrencyController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\AlojamientoController;
use App\Http\Controllers\API\ComentarioController;
use App\Http\Controllers\API\AdminController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/public/meteo', [MeteoController::class, 'index']);

Route::apiResource('reservas', ReservaController::class);

Route::get('/alojamentos', [AlojamentoController::class, 'index']);
Route::get('/alojamentos/{id}', [AlojamentoController::class, 'show']);

Route::post('/alojamentos/{id}/available', [ReservaController::class, 'available']);
Route::middleware('auth:sanctum')->group(function() {
    Route::post('/reservas', [ReservaController::class, 'store']);
    Route::get('/reservas/me', [ReservaController::class, 'myReservations']);
});


Route::prefix('pagamentos')->group(function () {
    Route::post('/checkout/{reservaId}', [PaymentController::class, 'checkout']);
    Route::get('/status/{paymentKey}', [PaymentController::class, 'status']);
    Route::post('/webhook', [PaymentController::class, 'webhook']);
});

Route::get('/reservas', [ReservaController::class, 'index']);

Route::get('/public/conversao', [CurrencyController::class, 'convert']);
