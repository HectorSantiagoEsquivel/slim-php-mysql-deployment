<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;


class Pedido extends Model
{


    protected $table = 'pedidos';
    protected $primaryKey = 'id';
    protected $fillable = ['idMesa','idMozo','estado','rutaImagen'];
    public $timestamps = false;


    public static function CrearPedido($idMesa, $idMozo=null,$estado='activo')
    {
        return Pedido::create([
            'idMesa' => $idMesa,
            'idMozo' => $idMozo,
            'estado' => $estado,
        ])->id;
    }


    public static function ObtenerTodos()
    {
        return Pedido::all();
    }

    public static function ObtenerPedido($id)
    {
        return Pedido::where('id', '=', $id)
        ->first();
    }

    public static function ObtenerPedidosActivos()
    {
        return Pedido::where('estado', '=', 'activo')
            ->get();
    }


    public function ModificarPedido()
    {
        $this->update([
            'idMozo'=> $this->idMozo,
            'idMesa'=>$this->idMesa,
            'estado'=>$this->estado,
            'rutaImagen'=>$this->rutaImagen,
        ]);
    }

    public function BorrarPedido()
    {
        $this->delete();
    }
}
