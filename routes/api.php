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

/*
|--------------------------------------------------------------------------
| Rotas Públicas
|--------------------------------------------------------------------------
*/

// Meteo
Route::get('/public/meteo', [MeteoController::class, 'index']);

// Conversão de moedas
Route::get('/public/conversao', [CurrencyController::class, 'convert']);

// Alojamentos (públicos)
Route::get('/alojamentos', [AlojamentoController::class, 'index']);
Route::get('/alojamentos/{id}', [AlojamentoController::class, 'show']);

// Verificar disponibilidade de alojamento (público)
Route::post('/reservas/available/{alojamentoId}', [ReservaController::class, 'available']);

// Comentários (públicos - leitura)
Route::get('/comentarios', [ComentarioController::class, 'index']);
Route::get('/comentarios/{id}', [ComentarioController::class, 'show']);

/*
|--------------------------------------------------------------------------
| Rotas Autenticadas
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function() {
    
    // Obter utilizador autenticado
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Reservas do utilizador
    Route::prefix('reservas')->group(function() {
        Route::get('/me', [ReservaController::class, 'myReservations']);
        Route::post('/', [ReservaController::class, 'store']);
        Route::get('/{id}', [ReservaController::class, 'show']);
        Route::put('/{id}', [ReservaController::class, 'update']);
        Route::delete('/{id}', [ReservaController::class, 'destroy']);
        Route::post('/{reserva}/cancel', [ReservaController::class, 'cancel']);
    });

    // Comentários (criar)
    Route::post('/comentarios', [ComentarioController::class, 'store']);
    Route::put('/comentarios/{id}', [ComentarioController::class, 'update']);
    Route::delete('/comentarios/{id}', [ComentarioController::class, 'destroy']);

    // Pagamentos
    Route::prefix('pagamentos')->group(function () {
        Route::post('/checkout/{reservaId}', [PaymentController::class, 'checkout']);
        Route::get('/status/{paymentKey}', [PaymentController::class, 'status']);
        Route::post('/webhook', [PaymentController::class, 'webhook']);
    });
});

/*
|--------------------------------------------------------------------------
| Webhooks (Sem autenticação)
|--------------------------------------------------------------------------
*/

Route::post('/pagamentos/webhook', [PaymentController::class, 'webhook']);

/*
|--------------------------------------------------------------------------
| Rotas Admin
|--------------------------------------------------------------------------
*/

Route::middleware(['auth:sanctum', 'role:admin'])
    ->prefix('admin')
    ->group(function () {
        
        // Reservas Admin
        Route::prefix('reservas')->group(function() {
            Route::get('/', [ReservaController::class, 'indexAdmin']);
            Route::patch('/{reserva}/status', [ReservaController::class, 'updateStatus']);
        });
        
        // Comentários Admin
        Route::prefix('comentarios')->group(function() {
            Route::get('/', [ComentariosController::class, 'index']);
            Route::delete('/{id}', [ComentariosController::class, 'destroy']);
            Route::patch('/{id}/toggle', [ComentariosController::class, 'toggleAprovado']);
        });

        // Alojamentos Admin (se necessário)
        // Route::apiResource('alojamentos', AlojamentoController::class)->except(['index', 'show']);
    });
