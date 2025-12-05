<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Comentario;
use Illuminate\Http\Request;

class ComentarioController extends Controller
{
    // Lista JSON para Vue (com filtro por estado) – tal como AlojamentoController@index
    public function index(Request $request)
    {
        $status = $request->query('status'); // pendentes, aprovados, todos

        $query = Comentario::with(['user', 'alojamento'])
            ->orderBy('created_at', 'desc');

        if ($status === 'pendentes') {
            $query->where('aprovado', false);
        } elseif ($status === 'aprovados') {
            $query->where('aprovado', true);
        }

        $comentarios = $query->paginate(10);

        // Transformar para simplificar no frontend
        $comentarios->getCollection()->transform(function ($c) {
            return [
                'id'             => $c->id,
                'titulo'         => $c->titulo,
                'texto'          => $c->texto,
                'rating'         => $c->rating,
                'aprovado'       => (bool) $c->aprovado,
                'resposta_admin' => $c->resposta_admin,
                'created_at'     => $c->created_at,
                'user'           => $c->user ? [
                    'id'    => $c->user->id,
                    'name'  => $c->user->name,
                    'email' => $c->user->email,
                ] : null,
                'alojamento'     => $c->alojamento ? [
                    'id'     => $c->alojamento->id,
                    'titulo' => $c->alojamento->titulo,
                ] : null,
            ];
        });

        return response()->json($comentarios);
    }

    // APROVAR comentário (aprovado = true)
    public function aprovar(Comentario $comentario)
    {
        if ($comentario->aprovado) {
            return response()->json([
                'message' => 'Comentário já está aprovado.',
            ], 400);
        }

        $comentario->update([
            'aprovado' => true,
        ]);

        return response()->json([
            'message'    => 'Comentário aprovado com sucesso.',
            'comentario' => $comentario->fresh(),
        ]);
    }

    // APAGAR comentário
    public function destroy(Comentario $comentario)
    {
        $comentario->delete();

        return response()->json([
            'message' => 'Comentário apagado com sucesso.',
        ]);
    }

    // RESPONDER ao comentário (grava em resposta_admin)
    public function responder(Request $request, Comentario $comentario)
    {
        $data = $request->validate([
            'resposta_admin' => 'required|string',
        ]);

        $comentario->update([
            'resposta_admin' => $data['resposta_admin'],
            'aprovado'       => true,
        ]);

        return response()->json([
            'message'    => 'Resposta do admin guardada com sucesso.',
            'comentario' => $comentario->fresh(),
        ]);
    }
}
