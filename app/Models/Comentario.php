<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comentario extends Model
{
    use HasFactory;

    protected $fillable = ['user_id','alojamento_id','titulo','texto','rating','aprovado','resposta_admin'];

    public function user() { return $this->belongsTo(User::class); }
    public function alojamento() { return $this->belongsTo(Alojamento::class); }
}
