<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Encuesta extends Model
{
    protected $table = 'encuestas';
    public $timestamps = false;

    protected $fillable = [
        'pedidoId',
        'puntaje',
        'reseña',
    ];

    public static function CrearEncuesta($pedidoId,$puntaje,$reseña)
    {
        return Encuesta::create([
            'pedidoId' => $pedidoId,
            'puntaje' => $puntaje,
            'reseña' => $reseña
        ]);
    }

    public static function ObtenerMejoresComentarios()
    {
        return Encuesta::orderBy('puntaje', 'desc')->get();
    }

}
