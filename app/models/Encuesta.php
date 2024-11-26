<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Encuesta extends Model
{
    protected $table = 'encuestas';
    public $timestamps = false;

    protected $fillable = [
        'idPedido',
        'puntaje',
        'reseña',
    ];

    public static function CrearEncuesta($idPedido,$puntaje,$reseña)
    {
        return Encuesta::create([
            'idPedido' => $idPedido,
            'puntaje' => $puntaje,
            'reseña' => $reseña
        ]);
    }

    public static function ObtenerMejoresComentarios()
    {
        return Encuesta::orderBy('puntaje', 'desc')->get();
    }

}
