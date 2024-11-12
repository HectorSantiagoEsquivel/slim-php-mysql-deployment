<?php
require_once './models/Producto.php';
require_once './interfaces/IApiUsable.php';

class ProductoController extends Producto implements IApiUsable
{
    static $areasValidas = array("cerveceria","cocina","candy","barra");
    public function CargarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $nombre = $parametros['nombre'];
        $precio = $parametros['precio'];
        $area = $parametros['area'];

        if(Validador::ValidarPalabra($area,self::$areasValidas))
        {
          $producto = new Producto();
          $producto->nombre = $nombre;
          $producto->precio = $precio;
          $producto->area = $area;
          $producto->crearProducto();
  
          $payload = json_encode(array("mensaje" => "Producto creado con exito"));
        }
        else
        {
          $payload = json_encode(array("mensaje" => "Producto no valido"));
        }

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

        public function ModificarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $nombre = $parametros['nombre'];
        $precio = $parametros['precio'];
        $area = $parametros['area'];
        $disponible= $parametros['disponible'];
        $id = $parametros['id'];

        if(Validador::ValidarPalabra($area,self::$areasValidas))
        {
          $producto = new Producto();
          $producto->nombre = $nombre;
          $producto->precio = $precio;
          $producto->area = $area;
          $producto->id = $id;
          $producto->disponible=$disponible;
  
          $producto->modificarProducto();
  
          $payload = json_encode(array("mensaje" => "Producto modificado con exito"));
        }
        else
        {
          $payload = json_encode(array("mensaje" => "Modificacion no valida"));
        }

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function BorrarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $id = $parametros['id'];
        Producto::borrarProducto($id);

        $payload = json_encode(array("mensaje" => "Producto borrado con exito"));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerUno($request, $response, $args)
    {

        $producto = $args['producto'];
        $id = Producto::obtenerProducto($producto);
        $payload = json_encode($id);

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerTodos($request, $response, $args)
    {
        $lista = Producto::obtenerTodos();
        $payload = json_encode(array("listaProducto" => $lista));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }
    

}
