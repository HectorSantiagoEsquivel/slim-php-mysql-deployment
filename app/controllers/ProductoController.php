<?php

require_once './interfaces/IApiUsable.php';
require_once './utils/Validador.php';

use App\Models\Producto;

class ProductoController implements IApiUsable
{
    static $areasValidas = array("cerveceria","cocina","candy","barra");
    public function CargarUno($request, $response, $args)
    {

      $parametros = $request->getParsedBody();

      $nombre = $parametros['nombre'];
      $precio = $parametros['precio'];
      $area = $parametros['area'];

      if (Validador::ValidarPalabra($area, self::$areasValidas)) 
      {
        $productoID= Producto::crearProducto($nombre, $precio, $area);

        $payload = json_encode(["mensaje" => "Producto creado con éxito", "id" => $productoID]);
      }
      else
      {
        $payload = json_encode(["mensaje" => "Área no válida"]);
      }

      $response->getBody()->write($payload);
      return $response->withHeader('Content-Type', 'application/json');
    }

    public function ModificarUno($request, $response, $args)
    {
      $parametros = $request->getParsedBody();

      $nombre = $parametros['nombre'];
      $precio = $parametros['precio'];
      $area = $parametros['area'];
      $id = $parametros['id'];

      if (Validador::ValidarPalabra($area, self::$areasValidas)) 
      {
        $producto = Producto::ObtenerProducto($id);
        if ($producto) 
        {
          $producto->nombre=$nombre;
          $producto->precio=$precio;
          $producto->area=$area;
          $producto->ModificarProducto();
          $payload = json_encode(["mensaje" => "Producto modificado con éxito"]);
        } 
        else 
        {
          $payload = json_encode(["mensaje" => "Producto no encontrado"]);
        }
      }
      else 
      {
        $payload = json_encode(["mensaje" => "Área no válida"]);
      }

      $response->getBody()->write($payload);
      return $response->withHeader('Content-Type', 'application/json');
    }

    public function BorrarUno($request,$response,$args)
    {
      $parametros = $request->getParsedBody();

      $id = $parametros['id'];
      $producto = Producto::ObtenerProducto($id);

      if ($producto)
      {
        $producto->BorrarUno();
        $payload = json_encode(["mensaje" => "Producto desactivado con éxito"]);
      }
      else
      {
        $payload = json_encode(["mensaje" => "Producto no encontrado"]);
      }

      $response->getBody()->write($payload);
      return $response->withHeader('Content-Type', 'application/json');
    }


    public function TraerUno($request, $response, $args)
    {
      $id = $args['producto'];
      $producto = Producto::ObtenerProducto($id);

      if ($producto) 
      {
        $payload = json_encode($producto);
      }
      else
      {
        $payload = json_encode(["mensaje" => "Producto no encontrado"]);
      }
      $response->getBody()->write($payload);
      return $response->withHeader('Content-Type', 'application/json');
    }

    public function TraerTodos($request, $response, $args)
    {
      $productos = Producto::ObtenerTodos();
      $payload = json_encode(["listaProducto" => $productos]);

      $response->getBody()->write($payload);
      return $response->withHeader('Content-Type', 'application/json');
    }

    public function DescargarCSV($request, $response, $args)
    {
        
        $datosCSV = Producto::GenerarCSV();
        
        $response = $response->withHeader('Content-Type', 'text/csv')
                             ->withHeader('Content-Disposition', 'attachment; filename="productos.csv"');

        
        $response->getBody()->write($datosCSV);

        return $response;
    }

    public function SubirCSV($request, $response, $args)
    {
      
      $tiposArchivoValidos = ["csv"];      
      $archivos = $request->getUploadedFiles();
      $archivoCSV = $archivos['archivo'];
      $tipo_archivo = $archivoCSV->getClientMediaType();

      if (Validador::ValidarTipoArchivo($tipo_archivo, $tiposArchivoValidos)) 
      {
        $rutaCSV = $archivoCSV->getStream()->getMetadata('uri');  
        if (Producto::CargarDeCSV($rutaCSV)) 
        {
          $payload = json_encode(["mensaje" => "Productos subidos con exito"]);
        } 
        else
        {
          $payload = json_encode(["mensaje" => "Hubo un error al subir los productos"]);
        }            
      }
      else
      {  
        $payload = json_encode(["mensaje" => "Tipo no valido, solo se permite csv"]);
      }

      $response->getBody()->write($payload);
      return $response->withHeader('Content-Type', 'application/json');
    }    


}
