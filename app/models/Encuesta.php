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
        'puntajeMesa',
        'puntajeRestaurante',
        'puntajeMozo',
        'puntajeCocinero'
    ];

    public static function CrearEncuesta($idPedido, $puntajeMesa, $puntajeRestaurante, $puntajeMozo, $puntajeCocinero, $reseña)
    {
        
        $puntajePromedio = ($puntajeMesa + $puntajeRestaurante + $puntajeMozo + $puntajeCocinero) / 4;

        return Encuesta::create([
            'idPedido' => $idPedido,
            'puntaje' => $puntajePromedio,
            'reseña' => $reseña,
            'puntajeMesa' => $puntajeMesa,
            'puntajeRestaurante' => $puntajeRestaurante,
            'puntajeMozo' => $puntajeMozo,
            'puntajeCocinero' => $puntajeCocinero,
        ]);
    }

    public static function ObtenerMejoresComentarios()
    {
        return Encuesta::orderBy('puntaje', 'desc')->get();
    }
}

