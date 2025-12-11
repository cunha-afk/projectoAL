<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class EasypayService
{
    protected $baseURL;
    protected $apiKey;
    protected $accountId;

    public function __construct()
    {
        $this->baseURL   = config('services.easypay.base_url');
        $this->apiKey    = config('services.easypay.api_key');
        $this->accountId = config('services.easypay.account_id');
    }

    /**
     * Criar pagamento (Multibanco + MBWay opcional)
     */
    public function criarPagamento($reserva)
    {
        $endpoint = "{$this->baseURL}/single";

        try {
            $response = Http::withHeaders([
                'AccountId' => $this->accountId,
                'ApiKey'    => $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post($endpoint, [

                "key" => "reserva-{$reserva->id}",
                "type" => "sale",
                "value" => $reserva->preco_total,
                "currency" => "EUR",

                "customer" => [
                    "name"  => $reserva->user->name,
                    "email" => $reserva->user->email,
                ],

                // Ativa MULTIBANCO E MBWAY
                "methods" => [
                    "mb" => [
                        "entity" => "11249",  // coloca a tua
                        "subentity" => "000",
                    ],
                    "mbw" => [
                        "phone" => null  // Utilizador introduz depois
                    ],
                ],

                "sandbox" => true, // ok aqui
            ]);

            if ($response->failed()) {
                return ['success' => false, 'error' => $response->json()];
            }

            return ['success' => true, 'data' => $response->json()];

        } catch (\Throwable $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Pedir MBWay após o utilizador inserir o número
     */
    public function solicitarMBWay($reserva, $phone)
    {
        try {
            $response = Http::withHeaders([
                'AccountId' => $this->accountId,
                'ApiKey'    => $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post("{$this->baseURL}/single/mbw", [
                "key" => "reserva-{$reserva->id}",
                "phone" => $phone
            ]);

            if ($response->failed()) {
                return ['success' => false, 'error' => $response->json()];
            }

            return ['success' => true, 'data' => $response->json()];

        } catch (\Throwable $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }


    /**
     * Verificar estado de pagamento
     */
    public function verificarPagamento($paymentKey)
    {
        try {
            $response = Http::withHeaders([
                'AccountId' => $this->accountId,
                'ApiKey'    => $this->apiKey,
            ])->get("{$this->baseURL}/single/{$paymentKey}");

            if ($response->failed()) {
                return ['success' => false, 'error' => $response->json()];
            }

            return ['success' => true, 'data' => $response->json()];

        } catch (\Throwable $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
}
