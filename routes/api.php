<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\MeteoController;
use App\Http\Controllers\Api\ReservaController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\CurrencyController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\AlojamentoController;
use App\Http\Controllers\API\ComentarioController;
use App\Http\Controllers\API\AdminController;
use App\Http\Controllers\Admin\ComentariosController;


//  Rota para obter o utilizador autenticado
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

//  API pública - Meteo
Route::get('/public/meteo', [MeteoController::class, 'index']);

//  Comentários
Route::apiResource('comentarios', ComentarioController::class);

//  Conversão de moedas
Route::get('/public/conversao', [CurrencyController::class, 'convert']);

Route::apiResource('reservas', ReservaController::class);

//  Alojamentos (públicos)
Route::get('/alojamentos', [AlojamentoController::class, 'index']);
Route::get('/alojamentos/{id}', [AlojamentoController::class, 'show']);

//  Reservas
Route::get('/reservas', [ReservaController::class, 'index']);

Route::post('/alojamentos/{id}/available', [ReservaController::class, 'available']);

//  Reservas autenticadas
Route::middleware('auth:sanctum')->group(function() {
    Route::post('/reservas', [ReservaController::class, 'store']);
     Route::get('/reservas', [ReservaController::class, 'index']);
    //Route::get('/reservas/me', [ReservaController::class, 'myReservations']);
});

//  Pagamentos
Route::prefix('pagamentos')->group(function () {
    Route::post('/checkout/{reservaId}', [PaymentController::class, 'checkout']);
    Route::get('/status/{paymentKey}', [PaymentController::class, 'status']);
    Route::post('/webhook', [PaymentController::class, 'webhook']);
});




Route::get('/public/conversao', [CurrencyController::class, 'convert']);


Route::middleware(['auth:sanctum', 'role:admin'])
    ->prefix('admin')
    ->group(function () {
        Route::get('/comentarios', [ComentariosController::class, 'index']);
        Route::delete('/comentarios/{id}', [ComentariosController::class, 'destroy']);
        Route::patch('/comentarios/{id}/toggle', [ComentariosController::class, 'toggleAprovado']);
    });
