<?php

namespace App\Http\Controllers;

use App\Models\Reserva;
use Illuminate\Http\Request;

class ReservaController extends Controller
{
    // 🔹 Listar todas as reservas
    public function index()
    {
        return response()->json(Reserva::all());
    }

    // 🔹 Mostrar uma reserva específica
    public function show($id)
    {
        return response()->json(Reserva::findOrFail($id));
    }

    // 🔹 Criar uma nova reserva
    public function store(Request $request)
    {
        $request->validate([
            'inicio' => 'required|date',
            'fim' => 'required|date|after:inicio',
            'user_id' => 'nullable|exists:users,id'
        ]);

        // Evitar overlap de reservas
        $overlap = Reserva::where(function($query) use ($request) {
            $query->whereBetween('inicio', [$request->inicio, $request->fim])
                  ->orWhereBetween('fim', [$request->inicio, $request->fim]);
        })
        ->where('estado', '!=', 'cancelada')
        ->exists();

        if ($overlap) {
            return response()->json(['error' => 'Já existe uma reserva nesse intervalo.'], 409);
        }

        $preco = Reserva::calcularPreco($request->inicio, $request->fim);

        $reserva = Reserva::create([
            'user_id' => $request->user_id,
            'inicio' => $request->inicio,
            'fim' => $request->fim,
            'estado' => 'pendente',
            'preco_total' => $preco
        ]);

        return response()->json($reserva, 201);
    }

    // 🔹 Atualizar reserva (estado, horários, etc.)
    public function update(Request $request, $id)
    {
        $reserva = Reserva::findOrFail($id);

        $request->validate([
            'inicio' => 'sometimes|date',
            'fim' => 'sometimes|date|after:inicio',
            'estado' => 'sometimes|in:pendente,confirmada,cancelada'
        ]);

        // Se alterar datas, verificar overlap novamente
        if ($request->has(['inicio', 'fim'])) {
            $overlap = Reserva::where(function($query) use ($request, $id) {
                $query->whereBetween('inicio', [$request->inicio, $request->fim])
                      ->orWhereBetween('fim', [$request->inicio, $request->fim]);
            })
            ->where('id', '!=', $id)
            ->where('estado', '!=', 'cancelada')
            ->exists();

            if ($overlap) {
                return response()->json(['error' => 'Já existe uma reserva nesse intervalo.'], 409);
            }

            $request->merge([
                'preco_total' => Reserva::calcularPreco($request->inicio, $request->fim)
            ]);
        }

        $reserva->update($request->all());

        return response()->json($reserva);
    }

    // 🔹 Cancelar reserva
    public function destroy($id)
    {
        $reserva = Reserva::findOrFail($id);
        $reserva->update(['estado' => 'cancelada']);
        return response()->json(['message' => 'Reserva cancelada com sucesso.']);
    }
}

