<?php

namespace App\Actions\Fortify;

use Illuminate\Support\Facades\Auth;
use Laravel\Fortify\Contracts\RegisterResponse;

class RedirectAfterRegister implements RegisterResponse
{
    public function toResponse($request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with(
            'status',
            'Conta criada! Verifica o teu email e conclui o registo para ativar a conta.'
        );
    }
}