<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reserva;
use App\Models\Comentario;
use App\Models\Alojamento;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $today = Carbon::today();
        $startMonth = Carbon::now()->startOfMonth();
        $endMonth = Carbon::now()->endOfMonth();

        // KPIs
        $reservasMes = Reserva::whereBetween('created_at', [$startMonth, $endMonth])->count();
        $receitaMes  = (float) Reserva::whereBetween('created_at', [$startMonth, $endMonth])->sum('total');

        $alojamentosAtivos = Alojamento::count();

        // Ocupação (estimativa): noites reservadas no mês / (alojamentos * noites do mês)
        $noitesNoMes = $startMonth->diffInDays($endMonth) + 1;

        $noitesReservadas = Reserva::query()
            ->where('checkin', '<=', $endMonth)
            ->where('checkout', '>=', $startMonth)
            ->get(['checkin', 'checkout'])
            ->sum(function ($r) use ($startMonth, $endMonth) {
                $checkin  = Carbon::parse($r->checkin)->max($startMonth);
                $checkout = Carbon::parse($r->checkout)->min($endMonth);

                // checkout não conta como noite
                return max(0, $checkin->diffInDays($checkout));
            });

        $capacidadeNoites = max(1, $alojamentosAtivos * $noitesNoMes);
        $ocupacaoPercent  = round(($noitesReservadas / $capacidadeNoites) * 100, 1);

        // Comentários pendentes (no teu model é boolean aprovado)
        $comentariosPendentes = Comentario::where('aprovado', false)->count();

        // Listas
        $ultimasReservas = Reserva::with(['user:id,name', 'alojamento:id,titulo'])
            ->orderByDesc('id')
            ->limit(8)
            ->get()
            ->map(fn ($r) => [
                'id'         => $r->id,
                'user'       => $r->user?->name,
                'alojamento' => $r->alojamento?->titulo,
                'checkin'    => (string) $r->checkin,
                'checkout'   => (string) $r->checkout,
                'estado'     => (string) $r->estado,
                'total'      => (float) $r->total,
                'created_at' => $r->created_at?->toDateTimeString(),
            ]);

        $checkinsHoje = Reserva::with(['user:id,name', 'alojamento:id,titulo'])
            ->whereDate('checkin', $today)
            ->orderBy('checkin')
            ->limit(8)
            ->get()
            ->map(fn ($r) => [
                'id'         => $r->id,
                'user'       => $r->user?->name,
                'alojamento' => $r->alojamento?->titulo,
                'checkin'    => (string) $r->checkin,
                'checkout'   => (string) $r->checkout,
                'estado'     => (string) $r->estado,
            ]);

        $comentariosPend = Comentario::with(['user:id,name'])
            ->where('aprovado', false)
            ->orderByDesc('id')
            ->limit(8)
            ->get()
            ->map(fn ($c) => [
                'id'         => $c->id,
                'user'       => $c->user?->name,
                'titulo'     => $c->titulo,
                'rating'     => $c->rating !== null ? (int) $c->rating : null,
                'created_at' => $c->created_at?->toDateTimeString(),
            ]);

        // Chart: Reservas por dia (últimos 30 dias)
        $chartStart = Carbon::now()->subDays(29)->startOfDay();

        $labels = collect(range(0, 29))
            ->map(fn ($i) => $chartStart->copy()->addDays($i)->toDateString());

        $counts = Reserva::query()
            ->whereBetween('created_at', [$chartStart, Carbon::now()->endOfDay()])
            ->selectRaw('DATE(created_at) as d, COUNT(*) as c')
            ->groupBy('d')
            ->pluck('c', 'd');

        $reservasPorDia = $labels->map(fn ($d) => [
            'date'  => $d,
            'count' => (int) ($counts[$d] ?? 0),
        ]);

        return response()->json([
            'kpis' => [
                'reservas_mes'          => $reservasMes,
                'receita_mes'           => $receitaMes,
                'ocupacao_percent'      => $ocupacaoPercent,
                'comentarios_pendentes' => $comentariosPendentes,
                'alojamentos'           => $alojamentosAtivos,
            ],
            'lists' => [
                'ultimas_reservas'      => $ultimasReservas,
                'checkins_hoje'         => $checkinsHoje,
                'comentarios_pendentes' => $comentariosPend,
            ],
            'chart' => [
                'reservas_por_dia' => $reservasPorDia,
            ],
        ]);
    }
}
