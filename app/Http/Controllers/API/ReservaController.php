<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Reserva;
use App\Models\Alojamento;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

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

        // Verificar capacidade do alojamento
        $alojamento = Alojamento::findOrFail($data['alojamento_id']);
        if (isset($alojamento->capacidade) && $data['hospedes'] > $alojamento->capacidade) {
            return response()->json([
                'error' => 'O número de hóspedes excede a capacidade do alojamento.'
            ], 422);
        }

        // Verificar conflito de datas (LÓGICA CORRIGIDA)
        if ($this->hasDateConflict($data['alojamento_id'], $data['checkin'], $data['checkout'])) {
            return response()->json([
                'error' => 'Já existe uma reserva nesse intervalo de datas.'
            ], 409);
        }

        // Preparar dados
        $data['user_id'] = auth()->id();
        $data['estado'] = 'pendente';
        $data['referencia'] = $this->gerarReferencia();
        $data['total'] = $this->calcularPreco(
            $data['checkin'], 
            $data['checkout'], 
            $alojamento
        );

        $reserva = Reserva::create($data);

        return response()->json($reserva->load(['user', 'alojamento']), 201);
    }

    public function update(Request $request, $id)
    {
        $reserva = Reserva::findOrFail($id);

        // Verificar permissão
        if (auth()->id() !== $reserva->user_id && !auth()->user()->is_admin) {
            return response()->json(['message' => 'Não autorizado.'], 403);
        }

        $data = $request->validate([
            'checkin' => 'sometimes|date|after_or_equal:today',
            'checkout' => 'sometimes|date|after:checkin',
            'hospedes' => 'sometimes|integer|min:1',
            'estado' => ['sometimes', Rule::in(['pendente', 'confirmada', 'cancelada'])],
            'observacoes' => 'nullable|string'
        ]);

        // Se as datas forem alteradas, verificar conflito
        if (isset($data['checkin']) || isset($data['checkout'])) {
            $checkin = $data['checkin'] ?? $reserva->checkin;
            $checkout = $data['checkout'] ?? $reserva->checkout;

            if ($this->hasDateConflict($reserva->alojamento_id, $checkin, $checkout, $id)) {
                return response()->json([
                    'error' => 'Já existe uma reserva nesse intervalo de datas.'
                ], 409);
            }

            $data['total'] = $this->calcularPreco($checkin, $checkout, $reserva->alojamento);
        }

        $reserva->update($data);

        return response()->json($reserva->load(['user', 'alojamento']));
    }

    public function cancel(Reserva $reserva)
    {
        if (auth()->id() !== $reserva->user_id) {
            return response()->json(['message' => 'Não autorizado.'], 403);
        }

        if (now()->greaterThanOrEqualTo($reserva->checkin)) {
            return response()->json([
                'message' => 'Não é possível cancelar reservas no dia de entrada ou após.'
            ], 400);
        }

        $reserva->update(['estado' => 'cancelada']);

        return response()->json(['message' => 'Reserva cancelada com sucesso.']);
    }

    public function destroy($id)
    {
        $reserva = Reserva::findOrFail($id);
        $reserva->update(['estado' => 'cancelada']);

        return response()->json(['message' => 'Reserva cancelada.']);
    }

    public function myReservations(Request $request)
    {
        return response()->json(
            Reserva::with('alojamento')
                ->where('user_id', $request->user()->id)
                ->orderBy('checkin', 'desc')
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

        $available = !$this->hasDateConflict(
            $alojamentoId, 
            $data['checkin'], 
            $data['checkout']
        );

        return response()->json([
            'available' => $available,
            'message' => $available
                ? 'O alojamento está disponível!'
                : 'O alojamento não está disponível nesse período.'
        ]);
    }

    /**
     * Verifica se existe conflito de datas (LÓGICA CORRIGIDA)
     */
    private function hasDateConflict($alojamentoId, $checkin, $checkout, $excludeId = null)
    {
        $query = Reserva::where('alojamento_id', $alojamentoId)
            ->where('estado', '!=', 'cancelada')
            ->where(function ($q) use ($checkin, $checkout) {
                // Caso 1: Nova reserva começa durante reserva existente
                $q->where(function ($query) use ($checkin) {
                    $query->where('checkin', '<=', $checkin)
                          ->where('checkout', '>', $checkin);
                })
                // Caso 2: Nova reserva termina durante reserva existente
                ->orWhere(function ($query) use ($checkout) {
                    $query->where('checkin', '<', $checkout)
                          ->where('checkout', '>=', $checkout);
                })
                // Caso 3: Nova reserva engloba completamente reserva existente
                ->orWhere(function ($query) use ($checkin, $checkout) {
                    $query->where('checkin', '>=', $checkin)
                          ->where('checkout', '<=', $checkout);
                });
            });

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->exists();
    }

    /**
     * Calcula o preço total da reserva
     */
    private function calcularPreco($checkin, $checkout, $alojamento)
    {
        $dias = (new \DateTime($checkin))->diff(new \DateTime($checkout))->days;
        $precoPorNoite = $alojamento->preco_noite ?? 100;
        
        return $dias * $precoPorNoite;
    }

    /**
     * Gera uma referência única para a reserva
     */
    private function gerarReferencia()
    {
        do {
            $referencia = 'RES-' . strtoupper(Str::random(8));
        } while (Reserva::where('referencia', $referencia)->exists());

        return $referencia;
    }
}

