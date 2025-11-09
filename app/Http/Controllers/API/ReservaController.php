<?php

namespace App\Http\Controllers\Api;

use App\Models\Reserva;
use Illuminate\Http\Request;

class ReservaController extends Controller
{
    /**
     * Mostrar todas as reservas (GET /api/reservas)
     */
    public function index()
    {
        // Carrega reservas com o utilizador e alojamento associados
        $reservas = Reserva::with(['user', 'alojamento'])->get();

        return response()->json($reservas);
    }

    /**
     * Criar nova reserva (POST /api/reservas)
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
            'alojamento_id' => 'required|exists:alojamentos,id',
            'data_inicio' => 'required|date',
            'data_fim' => 'required|date|after:data_inicio',
        ]);

        $data['estado'] = 'pendente';
        $data['preco_total'] = 100.00; // ou calcula dinamicamente

        $reserva = Reserva::create($data);

        return response()->json($reserva, 201);
    }

    /**
     * Mostrar uma reserva específica (GET /api/reservas/{id})
     */
    public function show($id)
    {
        $reserva = Reserva::with(['user', 'alojamento'])->findOrFail($id);
        return response()->json($reserva);
    }

    /**
     * Atualizar uma reserva (PUT /api/reservas/{id})
     */
    public function update(Request $request, $id)
    {
        $reserva = Reserva::findOrFail($id);

        $data = $request->validate([
            'estado' => 'in:pendente,confirmada,cancelada',
        ]);

        $reserva->update($data);

        return response()->json($reserva);
    }

    /**
     * Apagar uma reserva (DELETE /api/reservas/{id})
     */
    public function destroy($id)
    {
        Reserva::destroy($id);
        return response()->json(['message' => 'Reserva removida com sucesso']);
    }
}
