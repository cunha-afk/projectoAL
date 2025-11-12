<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Reserva;
use App\Services\EasypayService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    protected $easypay;

    public function __construct(EasypayService $easypay)
    {
        $this->easypay = $easypay;
    }

    /**
     * Criar pagamento para uma reserva
     */
    public function checkout($reservaId)
    {
        $reserva = Reserva::with('user')->findOrFail($reservaId);

        if ($reserva->estado === 'confirmada') {
            return response()->json(['message' => 'Reserva já paga.'], 400);
        }

        $result = $this->easypay->criarPagamento($reserva);

        if (!$result['success']) {
            return response()->json(['message' => 'Erro ao criar pagamento', 'error' => $result['error']], 500);
        }

        // Guardar o código de pagamento
        $reserva->update(['estado' => 'aguardar_pagamento']);

        return response()->json([
            'message' => 'Pagamento criado com sucesso',
            'pagamento' => $result['data'],
        ]);
    }

    /**
     * Verificar o estado de um pagamento
     */
    public function status(Request $request, $paymentKey)
    {
        $result = $this->easypay->verificarPagamento($paymentKey);

        if (!$result['success']) {
            return response()->json(['message' => 'Erro ao verificar pagamento', 'error' => $result['error']], 500);
        }

        return response()->json($result['data']);
    }

    /**
     * Webhook de confirmação da Easypay
     */
    public function webhook(Request $request)
    {
        $payload = $request->all();

        if (($payload['status'] ?? '') === 'paid') {
            $reservaId = str_replace('reserva-', '', $payload['key']);
            $reserva = Reserva::find($reservaId);

            if ($reserva) {
                $reserva->update(['estado' => 'confirmada']);
            }
        }

        return response()->json(['status' => 'ok']);
    }

    public function index()
    {
        return response()->json(\App\Models\Reserva::with('user', 'alojamento')->get());
    }

}
