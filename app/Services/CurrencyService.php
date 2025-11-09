<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class CurrencyService
{
    private string $baseUrl = 'https://api.exchangerate.host';

    public function converter(string $from, string $to, float $valor): array
    {
        $from = strtoupper($from);
        $to = strtoupper($to);

        $response = Http::get("https://api.frankfurter.app/latest", [
            'from' => $from,
            'to' => $to
        ]);

        if ($response->failed()) {
            return [
               'success' => false,
              'erro' => 'Erro ao contactar o serviço de câmbio.'
            ];
        }

        $data = $response->json();

        // Verificar se a taxa existe
        $rate = $data['rates'][$to] ?? null;

        if (!$rate) {
            return [
                'success' => false,
                'erro' => "Conversão não disponível de {$from} para {$to}"
            ];
        }

         $valorConvertido = round($valor * $rate, 2);

        return [
            'success' => true,
            'de' => $from,
            'para' => $to,
            'valor_original' => $valor,
            'valor_convertido' => $valorConvertido,
            'taxa' => $rate,
        ];
    }
}