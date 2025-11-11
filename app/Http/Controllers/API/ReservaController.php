<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Api\Controller;
use App\Models\Reserva;
use Illuminate\Http\Request;

class ReservaController extends Controller
{
    /**
     * Mostrar todas as reservas (GET /api/reservas)
     */
    public function index()
    {
        $reservas = Reserva::with(['user', 'alojamento'])->get();
        return response()->json($reservas);
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

        // 🔹 Verificar overlap de reservas
        $overlap = Reserva::where('alojamento_id', $data['alojamento_id'])
            ->where(function ($query) use ($data) {
                $query->whereBetween('data_inicio', [$data['data_inicio'], $data['data_fim']])
                      ->orWhereBetween('data_fim', [$data['data_inicio'], $data['data_fim']]);
            })
            ->where('estado', '!=', 'cancelada')
            ->exists();

        if ($overlap) {
            return response()->json(['error' => 'Já existe uma reserva nesse intervalo.'], 409);
        }

        // 🔹 Calcular preço (podes depois mover isto para o Model)
        //$data['user_id'] = auth()->id();
        $data['preco_total'] = Reserva::calcularPreco($data['data_inicio'], $data['data_fim']);
        $data['estado'] = 'pendente';

        $reserva = Reserva::create($data);

        return response()->json($reserva, 201);
    }

    /**
     * Atualizar uma reserva (PUT /api/reservas/{id})
     */
    public function update(Request $request, $id)
    {
        $reserva = Reserva::findOrFail($id);

        $data = $request->validate([
            'data_inicio' => 'sometimes|date',
            'data_fim' => 'sometimes|date|after:data_inicio',
            'estado' => 'sometimes|in:pendente,confirmada,cancelada'
        ]);

        // Se mudar as datas, verificar overlap novamente
        if ($request->has(['data_inicio', 'data_fim'])) {
            $overlap = Reserva::where('alojamento_id', $reserva->alojamento_id)
                ->where(function ($query) use ($data, $id) {
                    $query->whereBetween('data_inicio', [$data['data_inicio'], $data['data_fim']])
                          ->orWhereBetween('data_fim', [$data['data_inicio'], $data['data_fim']]);
                })
                ->where('id', '!=', $id)
                ->where('estado', '!=', 'cancelada')
                ->exists();

            if ($overlap) {
                return response()->json(['error' => 'Já existe uma reserva nesse intervalo.'], 409);
            }

            $data['preco_total'] = Reserva::calcularPreco($data['data_inicio'], $data['data_fim']);
        }

        $reserva->update($data);

        return response()->json($reserva);
    }

    /**
     * Cancelar reserva (DELETE /api/reservas/{id})
     */
    public function destroy($id)
    {
        $reserva = Reserva::findOrFail($id);
        $reserva->update(['estado' => 'cancelada']);
        return response()->json(['message' => 'Reserva cancelada com sucesso.']);
    }
}
