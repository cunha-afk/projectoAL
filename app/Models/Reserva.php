<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reserva extends Model
{
    // Se o nome da tabela for diferente de 'reservas', especifica aqui
    // protected $table = 'nome_da_tua_tabela';

    // Colunas que podem ser preenchidas via create() ou update()
    protected $fillable = [
        'user_id',
        'inicio',
        'fim',
        'estado',
        'preco_total'
    ];

    // Cálculo automático do preço
    public static function calcularPreco($inicio, $fim, $precoPorHora = 10)
    {
        $horas = (strtotime($fim) - strtotime($inicio)) / 3600;
        return $horas * $precoPorHora;
    }
}

