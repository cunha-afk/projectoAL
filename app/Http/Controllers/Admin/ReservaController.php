<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reserva;
use App\Models\Alojamento;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ReservaController extends Controller
{
    /**
     * LISTAR + FILTROS + PAGINAÇÃO
     * GET /admin/api/reservas
     */
    public function index(Request $request)
    {
        $search       = $request->input('search');          // ID ou referência
        $estado       = $request->input('estado');          // pendente, confirmado, cancelado
        $alojamentoId = $request->input('alojamento_id');   // ID do alojamento
        $perPage      = (int) $request->input('per_page', 10);

        $query = Reserva::with(['user', 'alojamento'])
            ->orderByDesc('created_at');

        // 🔍 Filtro por ID / referência
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('id', $search)
                  ->orWhere('referencia', 'like', "%{$search}%");
            });
        }

        // 🎯 Filtro por estado (ignora "todas")
        if (!empty($estado) && $estado !== 'todas') {
            $query->where('estado', $estado);
        }

        // 🏠 Filtro por alojamento
        if (!empty($alojamentoId)) {
            $query->where('alojamento_id', $alojamentoId);
        }

        $reservas = $query->paginate($perPage);

        $alojamentos = Alojamento::orderBy('titulo')
            ->get(['id', 'titulo']);

        // Estados que o admin usa (filtro/visualização)
        $estados = [
            'pendente',
            'confirmado',
            'cancelado',
        ];

        return response()->json([
            'reservas'    => $reservas,
            'alojamentos' => $alojamentos,
            'estados'     => $estados,
        ]);
    }

    /**
     * CRIAR RESERVA (ADMIN)
     * POST /admin/api/reservas
     *
     * Regra: estado é sempre "pendente" (pagamento/confirmado é tratado fora do admin)
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id'       => 'required|exists:users,id',
            'alojamento_id' => 'required|exists:alojamentos,id',
            'checkin'       => 'required|date|after_or_equal:today',
            'checkout'      => 'required|date|after:checkin',
            'hospedes'      => 'required|integer|min:1',
            'observacoes'   => 'nullable|string',
        ]);

        $total = Reserva::calcularPreco(
            $data['checkin'],
            $data['checkout'],
            $data['alojamento_id']
        );

        $referencia = $this->gerarReferenciaUnica();

        $reserva = Reserva::create([
            'user_id'       => $data['user_id'],
            'alojamento_id' => $data['alojamento_id'],
            'checkin'       => $data['checkin'],
            'checkout'      => $data['checkout'],
            'hospedes'      => $data['hospedes'],
            'total'         => $total,
            'estado'        => 'pendente', // ✅ fixo
            'referencia'    => $referencia,
            'observacoes'   => $data['observacoes'] ?? null,
        ]);

        $reserva->load(['user', 'alojamento']);

        return response()->json($reserva, 201);
    }

    /**
     * VER DETALHES
     * GET /admin/api/reservas/{reserva}
     */
    public function show(Reserva $reserva)
    {
        $reserva->load(['user', 'alojamento']);

        return response()->json($reserva);
    }

    /**
     * ATUALIZAR DADOS (SEM ESTADO)
     * PUT /admin/api/reservas/{reserva}
     *
     * Regra: admin NÃO altera estado.
     */
    public function update(Request $request, Reserva $reserva)
    {
        if ($reserva->estado === 'cancelado') {
    return response()->json([
        'message' => 'Não é possível editar uma reserva cancelada.',
    ], 422);
}
      
        $data = $request->validate([
            'user_id'       => 'required|exists:users,id',
            'alojamento_id' => 'required|exists:alojamentos,id',
            'checkin'       => 'required|date',
            'checkout'      => 'required|date|after:checkin',
            'hospedes'      => 'required|integer|min:1',
            'observacoes'   => 'nullable|string',
        ]);

        $total = Reserva::calcularPreco(
            $data['checkin'],
            $data['checkout'],
            $data['alojamento_id']
        );

        $reserva->update([
            'user_id'       => $data['user_id'],
            'alojamento_id' => $data['alojamento_id'],
            'checkin'       => $data['checkin'],
            'checkout'      => $data['checkout'],
            'hospedes'      => $data['hospedes'],
            'total'         => $total,
            'observacoes'   => $data['observacoes'] ?? null,
        ]);

        $reserva->load(['user', 'alojamento']);

        return response()->json($reserva);
    }

    /**
     * CANCELAR RESERVA (ÚNICA AÇÃO DE ESTADO DO ADMIN)
     * PATCH /admin/api/reservas/{reserva}/cancelar
     */
    public function cancelar(Reserva $reserva)
    {
        if ($reserva->estado === 'cancelado') {
            return response()->json([
                'message' => 'A reserva já está cancelada.',
            ], 422);
        }

        $reserva->estado = 'cancelado';
        $reserva->save();

        $reserva->load(['user', 'alojamento']);

        return response()->json([
            'message' => 'Reserva cancelada com sucesso.',
            'reserva' => $reserva,
        ]);
    }

    /**
     * APAGAR RESERVA
     * DELETE /admin/api/reservas/{reserva}
     */
    public function destroy(Reserva $reserva)
    {
        $reserva->delete();

        return response()->json(null, 204);
    }

    /**
     * Gera uma referência única para a reserva
     */
    protected function gerarReferenciaUnica(): string
    {
        do {
            $ref = 'RES-' . strtoupper(Str::random(8));
        } while (Reserva::where('referencia', $ref)->exists());

        return $ref;
    }
}
