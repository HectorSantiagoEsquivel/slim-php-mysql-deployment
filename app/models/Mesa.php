<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mesa extends Model
{
    protected $table = 'mesas';
    public $timestamps = false;

    

    protected $fillable = [
        'estado',
    ];
    public static function crearMesa()
    {
        return Mesa::create(['estado' => 'disponible']);
    }

    public static function obtenerTodos()
    {
        return Mesa::all();
    }

    public static function obtenerMesa($mesaId)
    {
        return Mesa::find($mesaId);
    }

    public function modificarMesa()
    {
        $this->update([
            'estado' => $this->estado,
        ]);

    }

    public function borrarMesa($id)
    {
        $this->update(['estado' => 'inhabilitada']);
    }

    public function pedidos()
    {
        return $this->hasMany(Pedido::class, 'idMesa'); 
    
    }

    public static function TraerMasUsada()
    {
        return Mesa::withCount('pedidos')
        ->orderByDesc('pedidos_count') 
        ->first(); 
    }


}
