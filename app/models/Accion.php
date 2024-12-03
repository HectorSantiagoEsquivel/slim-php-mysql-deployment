<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;


class Accion extends Model
{
    protected $table = 'acciones';
    protected $primaryKey = 'id';
    protected $fillable = ['idUsuario', 'accion'];
    public $timestamps = true; 

    const CREATED_AT = 'fecha'; 
    const UPDATED_AT = null;   



    public static function GuardarAccion($idUsuario, $accion)
    {
        
        return Accion::create([
            'idUsuario' => $idUsuario,
            'accion' => $accion,
        ])->id;
    }


    public static function ObtenerTodos()
    {
        return Accion::all();
    }

    public static function ObtenerAccion($id)
    {
        return Accion::find($id); 
    }
    
}