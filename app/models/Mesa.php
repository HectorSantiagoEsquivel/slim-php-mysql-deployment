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

    
    public static function FiltrarDatos($filtro = [])
    {

        $datos = Mesa::join('pedidos', 'mesas.id', '=', 'pedidos.idMesa')
            ->select(
                'mesas.id as idMesa',
                'mesas.estado as estadoMesa',
                'pedidos.id as idPedido',
                'pedidos.estado as estadoPedido',
                'pedidos.idMozo',
                'pedidos.idMesa',
                'pedidos.rutaImagen'
            );
    
        foreach ($filtro as $columna => $valor) 
        {
            if ($columna !== null) 
            {
                $datos->where($columna, $valor);
            }
        }
    
        return $datos->get();
    }


}
