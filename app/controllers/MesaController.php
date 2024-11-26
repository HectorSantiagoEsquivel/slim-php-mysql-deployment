<?php

require_once './interfaces/IApiUsable.php';
require_once './utils/Validador.php';

use App\Models\Mesa;

class MesaController implements IApiUsable
{
    static $estadosValidos = ["disponible", "ocupada", "cerrada"];
    public function CargarUno($request, $response, $args)
    {
        Mesa::crearMesa();
        $payload = json_encode(array("mensaje" => "Mesa creada con exito"));
        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    

    public function ModificarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $id = $parametros['id'];
        $estado = $parametros['estado'];
        

        if (Validador::ValidarPalabra($estado, self::$estadosValidos)) 
        {
          $mesa=Mesa::obtenerMesa($id);
          if($mesa)
          {
            $mesa->estado=$estado;
            $mesa->modificarMesa();
            $payload = json_encode(["mensaje" => "Mesa modificada con éxito"]);
          }
          else
          {
            $payload = json_encode(["mensaje" => "Mesa no encontrada"]);
          }
        }
        else
        {
          $payload = json_encode(["mensaje" => "Estado no válido"]);
        }

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function BorrarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $id = $parametros['id'];
        $mesa=Mesa::obtenerMesa($id);
        if ($mesa)
        {
          $mesa->BorrarMesa();
          $payload = json_encode(["mensaje" => "Usuario desactivado con éxito"]);
        }
        else
        {
          $payload = json_encode(["mensaje" => "Usuario no encontrado"]);
        }

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerUno($request, $response, $args)
    {
        
      $id = $args['mesa'];
      $mesa = Mesa::obtenerMesa($id);

      if ($mesa) 
      {
        $payload = json_encode($mesa);
      }
      else
      {
        $payload = json_encode(["mensaje" => "Usuario no encontrado"]);
      }

      $response->getBody()->write($payload);
      return $response->withHeader('Content-Type', 'application/json');
    }

    public function TraerTodos($request, $response, $args)
    {
        $lista = Mesa::obtenerTodos();
        $payload = json_encode(array("listaMesa" => $lista));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function ObtenerMesaMasUsada($request, $response, $args)
  {
      
    $mesaMasUsada = Mesa::TraerMasUsada(); 


    if ($mesaMasUsada) 
    {
      $payload = json_encode([
          "mesa" => $mesaMasUsada,
          "cantidadPedidos" => $mesaMasUsada->pedidos_count
      ]);
    } 
    else 
    {
      $payload = json_encode(["mensaje" => "No se encontraron mesas con pedidos"]);
    }

    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
  }

    

}
