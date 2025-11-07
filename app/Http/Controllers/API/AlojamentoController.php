<?php
namespace App\Http\Controllers;

use App\Models\Alojamento;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AlojamentoController extends Controller
{
    // Método para exibir a lista de todos os alojamentos
    public function index()
    {
        // Buscar todos os alojamentos
        $alojamentos = Alojamento::all(); // Você pode usar .get() ou outros métodos de consulta

        // Retorna a página de alojamentos com os dados
        return Inertia::render('Alojamentos', [
            'alojamentos' => $alojamentos,
        ]);
    }

    // Método para exibir os detalhes de um alojamento
    public function show($id)
    {
        // Buscar o alojamento pelo ID
        $alojamento = Alojamento::findOrFail($id);

        // Retorna a página de detalhes do alojamento
        return Inertia::render('AlojamentoDetalhes', [
            'alojamento' => $alojamento,
        ]);
    }
}