<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\MeteoController;
use App\Http\Controllers\Api\ReservaController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\CurrencyController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AlojamentoController;
use App\Http\Controllers\Api\ComentarioController;
use App\Http\Controllers\Admin\ComentariosController;
use App\Http\Controllers\Admin\UtilizadoresController;

/*
|--------------------------------------------------------------------------
| Rotas Públicas
|--------------------------------------------------------------------------
*/

Route::get('/public/meteo', [MeteoController::class, 'index']);
Route::get('/public/conversao', [CurrencyController::class, 'convert']);

Route::get('/alojamentos', [AlojamentoController::class, 'index']);
Route::get('/alojamentos/{id}', [AlojamentoController::class, 'show']);

Route::post('/reservas/available/{alojamentoId}', [ReservaController::class, 'available']);

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::get('/comentarios', [ComentarioController::class, 'index']);
Route::get('/comentarios/{id}', [ComentarioController::class, 'show']);

/*
|--------------------------------------------------------------------------
| Rotas Autenticadas
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {

    Route::get('/user', fn(Request $request) => $request->user());

    // Reservas
    Route::post('/reservas', [ReservaController::class, 'store']);

    Route::prefix('reservas')->group(function () {
        Route::get('/me', [ReservaController::class, 'minhasReservas']);
        Route::get('/{id}', [ReservaController::class, 'show']);
        Route::put('/{id}', [ReservaController::class, 'update']);
        Route::delete('/{id}', [ReservaController::class, 'destroy']);
        Route::post('/{reserva}/cancel', [ReservaController::class, 'cancel']);
    });

    // Comentários
    Route::post('/comentarios', [ComentarioController::class, 'store']);
    Route::put('/comentarios/{id}', [ComentarioController::class, 'update']);
    Route::delete('/comentarios/{id}', [ComentarioController::class, 'destroy']);

    // Pagamentos
    Route::prefix('pagamentos')->group(function () {
        Route::post('/checkout/{reservaId}', [PaymentController::class, 'checkout']);
        Route::get('/status/{paymentKey}', [PaymentController::class, 'status']);
        Route::post('/mbway/{reservaId}', [PaymentController::class, 'mbway']);
    });

    // Logout
    Route::post('/logout', [AuthController::class, 'logout']);
});

/*
|--------------------------------------------------------------------------
| Webhook do Easypay (PÚBLICO)
|--------------------------------------------------------------------------
*/

Route::post('/pagamentos/webhook', [PaymentController::class, 'webhook']);

/*
|--------------------------------------------------------------------------
| Rotas Admin
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->prefix('admin')->group(function () {

    // Reservas admin
    Route::prefix('reservas')->group(function () {
        Route::get('/', [ReservaController::class, 'indexAdmin']);
        Route::patch('/{reserva}/status', [ReservaController::class, 'updateStatus']);
    });

    // Comentários admin
    Route::prefix('comentarios')->group(function () {
        Route::get('/', [ComentariosController::class, 'index']);
        Route::delete('/{id}', [ComentariosController::class, 'destroy']);
        Route::patch('/{id}/toggle', [ComentariosController::class, 'toggleAprovado']);
    });

    // Utilizadores admin
    Route::apiResource('utilizadores', UtilizadoresController::class);

    // Alojamentos admin
    Route::apiResource('alojamentos', AlojamentoController::class)->except(['index', 'show']);
});

