<?php

namespace App\Models;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class PedidoProducto extends Model
{
    protected $table = 'pedidosproductos';
    protected $primaryKey = 'id';
    protected $fillable = ['idPedido', 'idProducto', 'cantidad', 'estado', 'tiempoEstimado', 'idEmpleado','tiempoTotal','tiempoInicio'];
    public $timestamps = false;


    public static function AgregarProducto($idPedido, $idProducto, $cantidad, $estado = 'pendiente', $tiempoEstimado = null, $idEmpleado = null,$tiempoTotal= null)
    {
        return PedidoProducto::create([
            'idPedido' => $idPedido,
            'idProducto' => $idProducto,
            'cantidad' => $cantidad,
            'estado' => $estado,
            'tiempoEstimado' => $tiempoEstimado,
            'idEmpleado' => $idEmpleado,
            'tiempoTotal' => $tiempoTotal,
            'tiempoInicio' => Carbon::now(),
        ]);
    }

    public static function ObtenerPorPedido($idPedido)
    {
        return PedidoProducto::where('idPedido', $idPedido)->get();
    }

    public static function ObtenerPedidoProducto($id)
    {
        return PedidoProducto::find($id); 
    }

    public function ObtenerDatosProducto()
    {
        $filtro = [
        'pedidosproductos.id' => $this->id,
        ];

        $datosProducto = self::FiltrarDatos($filtro);
        return $datosProducto->first();
    }


    public static function FiltrarDatos($filtro = [])
    {

        $productos = PedidoProducto::join('productos', 'pedidosproductos.idProducto', '=', 'productos.id')
            ->join('pedidos', 'pedidosproductos.idPedido', '=', 'pedidos.id')
            ->select(
                'productos.nombre as producto',
                'productos.area as area',
                'productos.precio as precio',
                'pedidosproductos.id as id',
                'pedidosproductos.idPedido as idPedido',
                'pedidosproductos.cantidad as cantidad',
                'pedidosproductos.estado as estado',
                'pedidosproductos.idEmpleado as idEmpleado',
                'pedidos.idMesa as idMesa',
                'pedidos.idMozo as idMozo',
                'pedidos.estado as estadoPedido'
            );
    
        foreach ($filtro as $columna => $valor) 
        {
            if ($columna !== null) 
            {
                $productos->where($columna, $valor);
            }
        }
    
        return $productos->get();
    }

    public function ModificarPedidoProducto()
    {
        $this->update([
            'estado'=> $this->estado,
            'tiempoEstimado'=>$this->tiempoEstimado,
        ]);
    }

    public function CargarTiempoTotal()
    {
        $this->update([
            'tiempoEstimado'=>Carbon::now(),
        ]);
    }

    public function calcularTiempoTranscurrido()
    {
        $tiempoInicio = new \DateTime($this->tiempoInicio);

        if($this->tiempoTotal)
        {
            $tiempoTotal = new \DateTime($this->tiempoTotal);
            $intervalo = $tiempoInicio->diff($tiempoTotal);
        }
        else
        {
            $ahora = new \DateTime();
            $intervalo = $tiempoInicio->diff($ahora);
        }
        return $intervalo->format('%h : %i ');
    }

    public function FormatearDetalle()
    {
        return [
            'nombre' => $this->producto,
            'cantidad' => $this->cantidad,
            'estado' => $this->estado,
            'tiempoEstimado' => $this->tiempoEstimado,
            'demora' => $this->calcularTiempoTranscurrido(),
        ];
    }

    public static function FormatearDetalles($pedidoProductosPorPedido)
    {
        
        $detallesPedido = [
            'idPedido' => $pedidoProductosPorPedido->first()->idPedido,  
            'mesa' => $pedidoProductosPorPedido->first()->idMesa,        
            'mozo' => $pedidoProductosPorPedido->first()->idMozo,        
            'productos' => []                                     
        ];
        foreach ($pedidoProductosPorPedido as $producto) {
            $detallesPedido['productos'][] = $producto->FormatearDetalle();
        }

        return $detallesPedido;
    }

    public static function calcularTotal($idPedido)
    {
        $productos = PedidoProducto::FiltrarDatos(['pedidosproductos.idPedido' => $idPedido]);
        $total = 0;

        foreach ($productos as $producto) 
        {
            $total += $producto->precio * $producto->cantidad;
        }

        return $total;
    }

}
