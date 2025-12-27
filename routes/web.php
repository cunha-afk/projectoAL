<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Models\Alojamento;
use App\Http\Controllers\Admin\UtilizadoresController;
use App\Http\Controllers\Admin\AlojamentoController;
use App\Http\Controllers\Admin\ComentarioController;
use App\Http\Controllers\Admin\ReservaController;
use App\Http\Controllers\CompleteRegistrationController;

/*
|--------------------------------------------------------------------------
| Páginas Públicas
|--------------------------------------------------------------------------
*/

Route::get('/', fn() => Inertia::render('Home'));
Route::get('/reservas', fn() => Inertia::render('Reservas'));
Route::get('/contactos', fn() => Inertia::render('Contactos'));

/*
|--------------------------------------------------------------------------
| paginas utilizador (validaçao)
|--------------------------------------------------------------------------
*/

Route::middleware(['signed', 'throttle:6,1'])->group(function () {
    Route::get('/complete-registration/{id}/{hash}', [CompleteRegistrationController::class, 'show'])
        ->name('complete-registration.show');

    Route::post('/complete-registration/{id}/{hash}', [CompleteRegistrationController::class, 'store'])
        ->name('complete-registration.store');
});



    Route::middleware([
        'auth:sanctum',
        config('jetstream.auth_session'),
        'verified'
    ])->group(function () {

    // Página de perfil
    Route::get('/perfil', fn () => Inertia::render('Perfil'))
        ->name('perfil');

    // Reservas do utilizador
    Route::get('/perfil/reservas', fn () => Inertia::render('ReservasUser'))
        ->name('perfil.reservas');
    });

    Route::post('/logout', function (Request $request) {
    Auth::logout();

    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect('/');
    })->name('logout');

/*
|--------------------------------------------------------------------------
| Laravel Dashboard (Jetstream)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified'])
    ->get('/dashboard', fn() => Inertia::render('Dashboard'))
    ->name('dashboard');

/*
|--------------------------------------------------------------------------
| Alojamentos (Página Pública)
|--------------------------------------------------------------------------
*/

Route::get('/alojamentos', function () {
    $alojamentos = Alojamento::with('fotos')->get();

    return Inertia::render('Alojamentos', [
        'alojamentos' => $alojamentos,
    ]);
});

Route::get('/alojamentos/{id}', function ($id) {
    $alojamento = Alojamento::with('fotos')->findOrFail($id);

    return Inertia::render('AlojamentoDetalhes', [
        'id' => $id,
        'alojamento' => $alojamento,
    ]);
});

/*
|--------------------------------------------------------------------------
| Pagamentos
|--------------------------------------------------------------------------
*/

Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified'])
    ->get('/checkout/{id}', function ($id) {
        $reserva = \App\Models\Reserva::with('alojamento')->findOrFail($id);
        return Inertia::render('Checkout', [
            'reserva' => $reserva
        ]);
    })
    ->name('checkout');


/*
|--------------------------------------------------------------------------
| ADMIN – Apenas Administradores
|--------------------------------------------------------------------------
*/

    Route::middleware([
        'auth:sanctum',
        config('jetstream.auth_session'),
        'verified',
        'role:admin'
    ])->prefix('admin')->name('admin.')->group(function () {

        //admin  (DASHBOARD)
        Route::get('/', fn () => Inertia::render('Admin/Dashboard'))
            ->name('dashboard');
    
        Route::get('/comentarios', fn () => Inertia::render('Admin/ComentariosAdmin'))
        ->name('comentarios');

        // PÁGINAS (Utilizadores)
        Route::get('/utilizadores', fn () => Inertia::render('Admin/Utilizadores/Index'))
        ->name('utilizadores');

        Route::get('/utilizadores/create', fn () => Inertia::render('Admin/Utilizadores/Create'))
        ->name('utilizadores.create');

        Route::get('/utilizadores/{id}/edit', fn ($id) => Inertia::render('Admin/Utilizadores/Edit', [
        'id' => $id
]))
        ->name('utilizadores.edit');
        // PÁGINAS (Alojamentos)
        Route::get('/alojamentos', fn () =>
        Inertia::render('Admin/Alojamentos/Index')
    )->name('alojamentos');

    Route::get('/alojamentos/criar', fn () =>
        Inertia::render('Admin/Alojamentos/Create')
    )->name('alojamentos.create');

    Route::get('/alojamentos/{id}/editar', fn ($id) =>
        Inertia::render('Admin/Alojamentos/Edit', ['id' => $id])
    )->name('alojamentos.edit');
    

    // PÁGINAS (Reservas)
    Route::get('/reservas', fn () =>
        Inertia::render('Admin/Reservas/Index')   // <--- o teu componente atual
    )->name('reservas');

    // Página criar reserva
    Route::get('/reservas/criar', fn () =>
        Inertia::render('Admin/Reservas/Create')
    )->name('reservas.create');

    // Página editar reserva
    Route::get('/reservas/{id}/editar', fn ($id) =>
        Inertia::render('Admin/Reservas/Edit', ['id' => $id])
    )->name('reservas.edit');

});



   
    
    