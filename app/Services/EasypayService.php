<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class EasypayService
{
    protected $baseURL;
    protected $apiKey;
    protected $clientID;

    public function __construct(){
        $this->baseURL = config('services.easypay.base_url');
        $this->apiKey = config('services.easypay.api_key');
        $this->clientID = config('services.easypay.client_id');
    }
    /**
     * Criar uma nova transação de pagamento.
     */
    public function criarPagamento($reserva)
    {
        $endpoint = "{$this->baseURL}/payment";

        $response = Http::withHeaders([
            'clientID' => $this->clientID,
            'ApiKey' => $this->apiKey,
            'Content-Type' => 'application/json',

        ])->post($endpoint, [
            'method' => 'mb',
            'type' => 'sale',
            'capture' => true,
            'currency' => 'EUR',
            'value' => $reserva->preco_total,
            'customer' => [
                'name' => $reserva->user->name,
                'email' => $reserva->user->email,
            ],
            'key' => 'reserva-' . $reserva->id,
            'capture_date' => now()->toDateTimeString(),
            'sandbox' => true,
        ]);

        if ($response->failed()) {
            return [
                'success' => false,
                'error' => $response->json(),
            ];
        }

        return [
            'success' => true,
            'data' => $response->json(),
        ];
    }

    /**
     * Consultar o estado de um pagamento
     */
    public function verificarPagamento($paymentKey)
    {
        $endpoint = "{$this->baseUrl}/payment/{$paymentKey}";

        $response = Http::withHeaders([
            'clientId' => $this->clientId,
            'ApiKey' => $this->apiKey,
        ])->get($endpoint);

        if ($response->failed()) {
            return ['success' => false, 'error' => $response->json()];
        }

        return ['success' => true, 'data' => $response->json()];
    }
}