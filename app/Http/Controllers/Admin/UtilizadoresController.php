<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UtilizadoresController extends Controller
{
    /**
     * LISTAR UTILIZADORES (GET)
     */
    public function index()
    {
        return User::orderBy('created_at', 'desc')->paginate(10);
    }

    /**
     * CRIAR UTILIZADOR (POST)
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:6',
        ]);

        $data['password'] = bcrypt($data['password']);

        $user = User::create($data);

        return response()->json($user, 201);
    }

    /**
     * MOSTRAR UM UTILIZADOR ESPECÍFICO (GET /id)
     */
    public function show(User $user)
    {
        return response()->json($user);
    }

    /**
     * ATUALIZAR UTILIZADOR (PUT/PATCH /id)
     */
    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name'     => 'sometimes|string|max:255',
            'email'    => "sometimes|email|unique:users,email,{$user->id}",
            'password' => 'sometimes|min:6',
        ]);

        if (isset($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        }

        $user->update($data);

        return response()->json($user);
    }

    /**
     * APAGAR (DELETE /id)
     */
    public function destroy(User $user)
    {
        $user->delete();

        return response()->json(['message' => 'Utilizador eliminado.']);
    }
}