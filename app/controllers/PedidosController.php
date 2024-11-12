<?php
require_once './models/Pedido.php';
require_once './interfaces/IApiUsable.php';

class PedidosController extends Pedido implements IApiUsable
{
    
    public function CargarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $idMesa = $parametros['idMesa'];
        $idProducto = $parametros['idProducto'];
        $cantidad = $parametros['cantidad'];
        $estado = $parametros['estado'];
        $tiempoEstimado = $parametros['tiempoEstimado'];

        $pedido = new Pedido();
        $pedido->idMesa = $idMesa;

        $idPedido= $pedido->crearPedido();

        Pedido::agregarProductoAlPedido($idPedido,$idProducto,$cantidad,$estado,$tiempoEstimado);

        $payload = json_encode(array("mensaje" => "Pedido creado con exito"));

        $response->getBody()->write($payload);
        return $response
            ->withHeader('Content-Type', 'application/json');
    }


    public function ModificarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $id = $parametros['id'];
        $idMesa = $parametros['idMesa'];

        $pedido = new Pedido();
        $pedido->id = $id;
        $pedido->idMesa = $idMesa;

        $pedido->modificarPedido();

        $payload = json_encode(array("mensaje" => "Pedido modificado con exito"));

        $response->getBody()->write($payload);
        return $response
            ->withHeader('Content-Type', 'application/json');
    }


    public function BorrarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $id = $parametros['id'];
        Pedido::borrarPedido($id);

        $payload = json_encode(array("mensaje" => "Pedido borrado con exito"));

        $response->getBody()->write($payload);
        return $response
            ->withHeader('Content-Type', 'application/json');
    }


    public function TraerUno($request, $response, $args)
    {
        $pedido = $args['pedido'];
        $id = Pedido::obtenerPedido($pedido);
        $payload = json_encode($id);

        $response->getBody()->write($payload);
        return $response
            ->withHeader('Content-Type', 'application/json');
    }


    public function TraerTodos($request, $response, $args)
    {
        $lista = Pedido::obtenerTodos();
        $payload = json_encode(array("listaPedido" => $lista));

        $response->getBody()->write($payload);
        return $response
            ->withHeader('Content-Type', 'application/json');
    }
}
