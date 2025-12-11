<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Alojamento extends Model
{
    use HasFactory;

    // Definindo os campos que podem ser preenchidos
    protected $fillable = [
        'titulo',
        'descricao',
        'preco_noite',
    ];

    // Definindo os campos que não podem ser preenchidos
    protected $guarded = ['id'];

    public function reservas() { return $this->hasMany(Reserva::class); }
    public function comentarios() { return $this->hasMany(Comentario::class); }
    public function fotos() { return $this->hasMany(Foto::class); }
    public function videos() { return $this->hasMany(Video::class); }
    public function bloqueios() { return $this->hasMany(Bloqueio::class); }

    
}