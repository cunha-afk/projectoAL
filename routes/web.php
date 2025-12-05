<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Models\Alojamento;
use App\Http\Controllers\Admin\UtilizadoresController;
use App\Http\Controllers\Admin\AlojamentoController;


/* Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
}); */

Route::get('/', function () {
    return Inertia::render('Home');
});

Route::get('/reservas', function () {
    return Inertia::render('Reservas');
});

// Rota para a página de Contactos
Route::get('/contactos', function () {
    return Inertia::render('Contactos');  // Aqui estamos renderizando a página de "Contactos"
});

//// Rota para a página de perfil
Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified'])
    ->get('/perfil', function () {
        return Inertia::render('Perfil');
    })
    ->name('perfil');

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');
});


Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
    'role:admin', 
])
->prefix('admin')
->name('admin.')
->group(function () {

        //admin  (DASHBOARD)
        Route::get('/', fn () => Inertia::render('Admin/Dashboard'))
            ->name('dashboard');
        Route::get('/reservas', fn () => Inertia::render('Admin/RservasAdmin'))
            ->name('reservas');
        Route::get('/alojamento', fn () => Inertia::render('Admin/AlojamentoAdmin'))
            ->name('alojamento');
        Route::get('/comentarios', fn () => Inertia::render('Admin/ComentariosAdmin'))
            ->name('comentarios');



    // ---------- UTILIZADORES (PAGES) ----------
    Route::get('/utilizadores', fn () =>
        Inertia::render('Admin/Utilizadores/Index')
    )->name('utilizadores');

    Route::get('/utilizadores/criar', fn () =>
        Inertia::render('Admin/Utilizadores/Create')
    )->name('utilizadores.create');

    Route::get('/utilizadores/{id}/editar', fn ($id) =>
        Inertia::render('Admin/Utilizadores/Edit', ['id' => $id])
    )->name('utilizadores.edit');


     // ---------- Alojamentos (PAGES) ----------

 Route::get('/alojamentos', fn () =>
        Inertia::render('Admin/Alojamentos/Index')
    )->name('alojamentos');

    Route::get('/alojamentos/criar', fn () =>
        Inertia::render('Admin/Alojamentos/Create')
    )->name('alojamentos.create');

    Route::get('/alojamentos/{id}/editar', fn ($id) =>
        Inertia::render('Admin/Alojamentos/Edit', ['id' => $id])
    )->name('alojamentos.edit');

    // ================================
    //      API INTERNA (JSON)
    // ================================
    Route::prefix('api')->group(function () {

        // ---------- API UTILIZADORES ----------
        Route::get('/utilizadores', [UtilizadoresController::class, 'index']);
        Route::post('/utilizadores', [UtilizadoresController::class, 'store']);
        Route::get('/utilizadores/{user}', [UtilizadoresController::class, 'show']);
        Route::put('/utilizadores/{user}', [UtilizadoresController::class, 'update']);
        Route::delete('/utilizadores/{user}', [UtilizadoresController::class, 'destroy']);

        // ---------- API ALOJAMENTOS ----------
        Route::get('/alojamentos', [AlojamentoController::class, 'index']);
        Route::post('/alojamentos', [AlojamentoController::class, 'store']);
        Route::get('/alojamentos/{alojamento}', [AlojamentoController::class, 'show']);
        Route::put('/alojamentos/{alojamento}', [AlojamentoController::class, 'update']);
        Route::delete('/alojamentos/{alojamento}', [AlojamentoController::class, 'destroy']);
        Route::post('/alojamentos/{alojamento}/fotos', [AlojamentoController::class, 'uploadFotos']);
        Route::delete('/alojamentos/fotos/{foto}', [AlojamentoController::class, 'deleteFoto']);
    
           });
});