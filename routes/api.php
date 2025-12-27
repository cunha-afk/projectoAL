<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\MeteoController;
use App\Http\Controllers\Api\ReservaController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\CurrencyController;
use App\Http\Controllers\Api\AlojamentoController;
use App\Http\Controllers\Api\ComentarioController;
use App\Http\Controllers\Admin\ComentarioController as AdminComentariosController;
use App\Http\Controllers\Admin\UtilizadoresController;
use App\Http\Controllers\Admin\AlojamentoController as AdminAlojamentoController;
use App\Http\Controllers\Admin\ReservaController as AdminReservaController;
use App\Http\Controllers\Admin\DashboardController;

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
    //Route::post('/logout', [AuthController::class, 'logout']);
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

Route::middleware(['auth:sanctum','role:admin'])
    ->prefix('admin')
    ->group(function () {
        // ---------- utilizadrores (PAGES) ----------
        Route::get('/utilizadores', [UtilizadoresController::class, 'index']);
        Route::post('/utilizadores', [UtilizadoresController::class, 'store']);
        Route::get('/utilizadores/{user}', [UtilizadoresController::class, 'show']);
        Route::put('/utilizadores/{user}', [UtilizadoresController::class, 'update']);
        Route::delete('/utilizadores/{user}', [UtilizadoresController::class, 'destroy']);
        Route::get('/roles', [UtilizadoresController::class, 'roles']);
        Route::get('/options/utilizadores', [UtilizadoresController::class, 'options']);


        // ---------- alojamentos (PAGES) ----------
        Route::get('/alojamentos', [AdminAlojamentoController::class, 'index']);
        Route::post('/alojamentos', [AdminAlojamentoController::class, 'store']);
        Route::get('/alojamentos/{alojamento}', [AdminAlojamentoController::class, 'show']);
        Route::put('/alojamentos/{alojamento}', [AdminAlojamentoController::class, 'update']);
        Route::delete('/alojamentos/{alojamento}', [AdminAlojamentoController::class, 'destroy']);
        Route::post('/alojamentos/{alojamento}/fotos', [AdminAlojamentoController::class, 'uploadFotos']);
        Route::delete('/alojamentos/fotos/{foto}', [AlojamentoController::class, 'deleteFoto']);
        Route::get('/options/alojamentos', [AdminAlojamentoController::class, 'options']);


 
        // ---------- reservas (PAGES) ----------
       Route::get('/reservas', [AdminReservaController::class, 'index']);
        Route::post('/reservas', [AdminReservaController::class, 'store']);
        Route::get('/reservas/{reserva}', [AdminReservaController::class, 'show']);
        Route::put('/reservas/{reserva}', [AdminReservaController::class, 'update']);
        Route::patch('/reservas/{reserva}/cancelar', [AdminReservaController::class, 'cancelar']);
        Route::delete('/reservas/{reserva}', [AdminReservaController::class, 'destroy']);


          // ---------- comentarios (PAGES) ----------
         Route::get('/comentarios', [AdminComentariosController::class, 'index']);
        Route::post('/comentarios/{comentario}/aprovar', [AdminComentariosController::class, 'aprovar']);
        Route::delete('/comentarios/{comentario}', [AdminComentariosController::class, 'destroy']);
        Route::post('/comentarios/{comentario}/responder', [AdminComentariosController::class, 'responder']);

          // ---------- dashboard (PAGES) ----------
           Route::get('/dashboard', [DashboardController::class, 'index']);
    });


    
