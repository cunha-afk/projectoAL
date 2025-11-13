<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reserva extends Model
{
    protected $fillable = [
        'user_id',
        'alojamento_id',
        'checkin',
        'checkout',
        'hospedes',
        'total',
        'estado',
        'referencia',
        'observacoes'
    ];

    public static function calcularPreco($inicio, $fim, $alojamentoId)
    {
        $dias = (new \DateTime($inicio))->diff(new \DateTime($fim))->days;
        $precoPorNoite = 100; // Podes mudar para ler do alojamento
        return $dias * $precoPorNoite;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function alojamento()
    {
        return $this->belongsTo(Alojamento::class);
    }
}


