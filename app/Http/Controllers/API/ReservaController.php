<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Reserva;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ReservaController extends Controller
{
    public function index()
    {
        return response()->json(
            Reserva::with(['user', 'alojamento'])->get()
        );
    }

    public function show($id)
    {
        return response()->json(
            Reserva::with(['user', 'alojamento'])->findOrFail($id)
        );
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'alojamento_id' => 'required|exists:alojamentos,id',
            'checkin' => 'required|date|after_or_equal:today',
            'checkout' => 'required|date|after:checkin',
            'hospedes' => 'required|integer|min:1',
            'observacoes' => 'nullable|string'
        ]);

        $data['user_id'] = auth()->id();
        $data['estado'] = 'pendente';
        $data['total'] = Reserva::calcularPreco($data['checkin'], $data['checkout'], $data['alojamento_id']);

        // Verificar conflito de datas
        $overlap = Reserva::where('alojamento_id', $data['alojamento_id'])
            ->where(function ($query) use ($data) {
                $query->whereBetween('checkin', [$data['checkin'], $data['checkout']])
                      ->orWhereBetween('checkout', [$data['checkin'], $data['checkout']])
                      ->orWhere(function ($q) use ($data) {
                          $q->where('checkin', '<', $data['checkin'])
                            ->where('checkout', '>', $data['checkout']);
                      });
            })
            ->where('estado', '!=', 'cancelada')
            ->exists();

        if ($overlap) {
            return response()->json(['error' => 'Já existe uma reserva nesse intervalo.'], 409);
        }

        $reserva = Reserva::create($data);

        return response()->json($reserva, 201);
    }

    public function update(Request $request, $id)
    {
        $reserva = Reserva::findOrFail($id);

        $data = $request->validate([
            'checkin' => 'sometimes|date',
            'checkout' => 'sometimes|date|after:checkin',
            'hospedes' => 'sometimes|integer|min:1',
            'estado' => ['sometimes', Rule::in(['pendente', 'confirmada', 'cancelada'])],
            'observacoes' => 'nullable|string'
        ]);

        // Se as datas forem alteradas, verificar overlap
        if ($request->has(['checkin', 'checkout'])) {

            $inicio = $data['checkin'];
            $fim = $data['checkout'];

            $overlap = Reserva::where('alojamento_id', $reserva->alojamento_id)
                ->where('id', '!=', $id)
                ->where(function ($query) use ($inicio, $fim) {
                    $query->whereBetween('checkin', [$inicio, $fim])
                          ->orWhereBetween('checkout', [$inicio, $fim])
                          ->orWhere(function ($q) use ($inicio, $fim) {
                              $q->where('checkin', '<', $inicio)
                                ->where('checkout', '>', $fim);
                          });
                })
                ->where('estado', '!=', 'cancelada')
                ->exists();

            if ($overlap) {
                return response()->json(['error' => 'Já existe uma reserva nesse intervalo.'], 409);
            }

            $data['total'] = Reserva::calcularPreco($inicio, $fim, $reserva->alojamento_id);
        }

        $reserva->update($data);

        return response()->json($reserva);
    }

    public function cancel(Reserva $reserva)
    {
        if (auth()->id() !== $reserva->user_id) {
            return response()->json(['message' => 'Não autorizado.'], 403);
        }

        if (now()->greaterThanOrEqualTo($reserva->checkin)) {
            return response()->json(['message' => 'Não é possível cancelar reservas no dia de entrada ou após.'], 400);
        }

        $reserva->update(['estado' => 'cancelada']);

        return response()->json(['message' => 'Reserva cancelada.']);
    }

    public function destroy($id)
    {
        $reserva = Reserva::findOrFail($id);
        $reserva->update(['estado' => 'cancelada']);

        return response()->json(['message' => 'Reserva marcada como cancelada.']);
    }

    public function myReservations(Request $request)
    {
        return response()->json(
            Reserva::with('alojamento')
                ->where('user_id', $request->user()->id)
                ->latest()
                ->get()
        );
    }

    public function indexAdmin()
    {
        return response()->json(
            Reserva::with(['user', 'alojamento'])
                ->orderBy('checkin', 'desc')
                ->get()
        );
    }

    public function updateStatus(Request $request, Reserva $reserva)
    {
        $request->validate([
            'estado' => ['required', Rule::in(['pendente', 'confirmada', 'cancelada'])],
        ]);

        $reserva->update(['estado' => $request->estado]);

        return response()->json([
            'message' => "Estado atualizado para {$request->estado}.",
            'reserva' => $reserva->load(['user', 'alojamento'])
        ]);
    }

    public function available(Request $request, $alojamentoId)
    {
        $data = $request->validate([
            'checkin' => 'required|date|after_or_equal:today',
            'checkout' => 'required|date|after:checkin',
        ]);

        $overlap = Reserva::where('alojamento_id', $alojamentoId)
            ->where(function ($query) use ($data) {
                $query->whereBetween('checkin', [$data['checkin'], $data['checkout']])
                      ->orWhereBetween('checkout', [$data['checkin'], $data['checkout']])
                      ->orWhere(function ($q) use ($data) {
                          $q->where('checkin', '<', $data['checkin'])
                            ->where('checkout', '>', $data['checkout']);
                      });
            })
            ->where('estado', '!=', 'cancelada')
            ->exists();

        return response()->json([
            'available' => !$overlap,
            'message' => $overlap
                ? 'O alojamento não está disponível.'
                : 'O alojamento está disponível!'
        ]);
    }
}

