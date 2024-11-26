<?php


require_once './interfaces/IApiUsable.php';
require_once './utils/Validador.php';
require_once './utils/AutentificadorJWT.php';

use app\models\Usuario;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class UsuarioController implements IApiUsable
{
    static $areasValidas = ["socio", "mozo", "cerveceria", "cocina", "candy", "barra"];

    public function CargarUno($request,$response,$args)
    {
        $parametros = $request->getParsedBody();

        $usuario = $parametros['usuario'];
        $clave = $parametros['clave'];
        $area = $parametros['area'];

        if (Validador::ValidarPalabra($area, self::$areasValidas)) 
        {
          $usrID= Usuario::crearUsuario($usuario, $clave, $area);

          $payload = json_encode(["mensaje" => "Usuario creado con éxito", "id" => $usrID]);
        }
        else
        {
          $payload = json_encode(["mensaje" => "Área no válida"]);
        }

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function ModificarUno($request,$response,$args)
    {
      $parametros = $request->getParsedBody();

      $id = $parametros['id'];
      $usuario = $parametros['usuario'];
      $clave = $parametros['clave'];
      $area = $parametros['area'];

      if (Validador::ValidarPalabra($area, self::$areasValidas)) 
      {
        $usr = Usuario::ObtenerUsuario($id);
        if ($usr) 
        {
          $usr->usuario=$usuario;
          $usr->clave=$clave;
          $usr->area=$area;
          $usr->ModificarUsuario();
          $payload = json_encode(["mensaje" => "Usuario modificado con éxito"]);
        } 
        else 
        {
          $payload = json_encode(["mensaje" => "Usuario no encontrado"]);
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
      $usr = Usuario::ObtenerUsuario($id);

      if ($usr)
      {
        $usr->BorrarUno();
        $payload = json_encode(["mensaje" => "Usuario desactivado con éxito"]);
      }
      else
      {
        $payload = json_encode(["mensaje" => "Usuario no encontrado"]);
      }

      $response->getBody()->write($payload);
      return $response->withHeader('Content-Type', 'application/json');
    }

    public function TraerUno($request,$response,$args)
    {
      $id = $args['usuario'];
      $usuario = Usuario::ObtenerUsuario($id);

      if ($usuario) 
      {
        $payload = json_encode($usuario);
      }
      else
      {
        $payload = json_encode(["mensaje" => "Usuario no encontrado"]);
      }

      $response->getBody()->write($payload);
      return $response->withHeader('Content-Type', 'application/json');
    }

    public function TraerTodos($request,$response,$args)
    {
      $usuarios = Usuario::ObtenerTodos();
      $payload = json_encode(["listaUsuario" => $usuarios]);

      $response->getBody()->write($payload);
      return $response->withHeader('Content-Type', 'application/json');
    }

    public function LogIn($request,$response,$args)
    {
        $params = $request->getParsedBody();
        $idUsuario = $params['idUsuario'];
        $clave = $params['clave'];

        $usuario = Usuario::ObtenerUsuario($idUsuario);

        if ($usuario->Autenticar($clave)) 
        {
          $usuarioData = ['id' => $usuario->id, 'area' => $usuario->area];
          $token = AutentificadorJWT::CrearToken($usuarioData);

          $payload = json_encode(['token' => $token]);
        } 
        else
        {
          $payload = json_encode(['error' => 'Usuario o contraseña inválidos']);
          $response = $response->withStatus(401);
        }

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }
}
