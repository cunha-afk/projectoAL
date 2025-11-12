<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Comentario;
use App\Http\Requests\StoreComentarioRequest;
use Illuminate\Http\Request;

class ComentarioController extends Controller
{
    // 🔹 Listar todos os comentários
    public function index()
    {
        return response()->json(Comentario::all());
    }

    // 🔹 Mostrar um comentário específico
    public function show($id)
    {
        return response()->json(Comentario::findOrFail($id));
    }

    // 🔹 Criar um novo comentário (com validação)
    public function store(StoreComentarioRequest $request)
    {
        $dados = $request->validated();
        $comentario = Comentario::create($dados);
        return response()->json($comentario, 201);
    }

    // 🔹 Atualizar um comentário
    public function update(StoreComentarioRequest $request, $id)
    {
        $comentario = Comentario::findOrFail($id);
        $comentario->update($request->validated());
        return response()->json($comentario);
    }

    // 🔹 Eliminar um comentário
    public function destroy($id)
    {
        $comentario = Comentario::findOrFail($id);
        $comentario->delete();
        return response()->json(['message' => 'Comentário eliminado com sucesso.']);
    }
}