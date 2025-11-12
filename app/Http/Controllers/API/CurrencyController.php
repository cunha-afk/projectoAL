<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\CurrencyService;

class CurrencyController extends Controller
{
    private CurrencyService $currencyService;

    public function __construct(CurrencyService $currencyService)
    {
        $this->currencyService = $currencyService;
    }

    public function convert(Request $request)
    {
        $from = $request->query('from', 'EUR');
        $to = $request->query('to', 'USD');
        $valor = (float) $request->query('valor', 1);

        $resultado = $this->currencyService->converter($from, $to, $valor);

        return response()->json($resultado);
    }
}
