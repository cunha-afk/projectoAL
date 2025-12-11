<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Reserva;
use App\Models\Pagamento;
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
     * Criar pagamento real com ligação BD
     */
    public function checkout($reservaId)
    {
        $reserva = Reserva::with('user')->findOrFail($reservaId);

        if ($reserva->estado === 'confirmada') {
            return response()->json(['message' => 'Reserva já paga.'], 400);
        }

        // Criar pagamento na BD
        $pagamento = Pagamento::create([
            'reserva_id' => $reserva->id,
            'valor' => $reserva->preco_total,
            'estado' => 'pendente'
        ]);

        // Criar pagamento no Easypay
        $result = $this->easypay->createPayment([
            'id' => $pagamento->id,
            'value' => $reserva->preco_total,
            'description' => "Reserva #" . $reserva->id
        ]);

        if (!$result || !isset($result['key'], $result['url'])) {
            return response()->json([
                'message' => 'Erro ao criar pagamento'
            ], 500);
        }

        // Guardar chave easypay
        $pagamento->update([
            'payment_key' => $result['key']
        ]);

        return response()->json([
            'payment_url' => $result['url']
        ]);
    }

    /**
     * Verificar estado do pagamento
     */
    public function status($paymentKey)
    {
        $pagamento = Pagamento::where('payment_key', $paymentKey)->firstOrFail();

        $result = $this->easypay->checkPayment($paymentKey);

        return response()->json($result);
    }

    /**
     * Webhook — Easypay confirma pagamento
     */
    public function webhook(Request $request)
    {
        $key = $request->input('key');
        $status = $request->input('status');

        $pagamento = Pagamento::where('payment_key', $key)->first();

        if (!$pagamento) {
            return response()->json(['error' => 'Pagamento não encontrado'], 404);
        }

        if ($status === 'paid') {
            $pagamento->update(['estado' => 'pago']);
            $pagamento->reserva->update(['estado' => 'confirmada']);
        } else {
            $pagamento->update(['estado' => 'falhou']);
        }

        return response()->json(['success' => true]);
    }

    public function index()
    {
        return response()->json(
            Pagamento::with('reserva.user', 'reserva.alojamento')->get()
        );
    }
    public function mbway(Request $request, $reservaId)
    {
        $reserva = Reserva::findOrFail($reservaId);

        $request->validate([
            'phone' => 'required|digits:9'
        ]);

        $result = $this->easypay->solicitarMBWay($reserva, $request->phone);

        if (!$result['success']) {
            return response()->json(['error' => $result['error']], 400);
        }

        return response()->json(['message' => 'Pedido MBWay enviado']);
    }
}
