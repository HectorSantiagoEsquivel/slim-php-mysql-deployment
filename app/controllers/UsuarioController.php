<?php
require_once './models/Usuario.php';
require_once './interfaces/IApiUsable.php';
require_once './utils/Validador.php';

class UsuarioController extends Usuario implements IApiUsable
{
    static $areasValidas = array("socio", "mozo","cerveceria","cocina","candy","barra");
    public function CargarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $usuario = $parametros['usuario'];
        $clave = $parametros['clave'];
        $area = $parametros['area'];

        // Creamos el usuario
        if(Validador::ValidarPalabra($area,self::$areasValidas))
        {
          $usr = new Usuario();
          $usr->usuario = $usuario;
          $usr->clave = $clave;
          $usr->area = $area;
          $usr->crearUsuario();
  
          $payload = json_encode(array("mensaje" => "Usuario creado con exito"));
        }
        else
        {
          $payload = json_encode(array("mensaje" => "Usuario no valido"));
        }


        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function ModificarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $usuario = $parametros['usuario'];
        $clave = $parametros['clave'];
        $area = $parametros['area'];
        $id = $parametros['id'];
        if(Validador::ValidarPalabra($area,self::$areasValidas))
        {
          $usr = new Usuario();
          $usr->usuario = $usuario;
          $usr->clave = $clave;
          $usr->area = $area;
          $usr->id = $id;
  
          $usr->modificarUsuario();
  
          $payload = json_encode(array("mensaje" => "Usuario modificado con exito"));
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
        Usuario::borrarUsuario($id);

        $payload = json_encode(array("mensaje" => "Usuario borrado con exito"));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerUno($request, $response, $args)
    {
        $usr = $args['usuario'];
        $usuario = Usuario::obtenerUsuario($usr);
        $payload = json_encode($usuario);

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function TraerTodos($request, $response, $args)
    {
        $lista = Usuario::obtenerTodos();
        $payload = json_encode(array("listaUsuario" => $lista));

        $response->getBody()->write($payload);
        return $response
          ->withHeader('Content-Type', 'application/json');
    }

    public function LogIn($request, $response, $args)
    {
        $params = $request->getParsedBody();
        $idUsuario = $params['idUsuario'];
        $clave = $params['clave'];
        $usuario=Usuario::autenticar($idUsuario, $clave);
        if($usuario) 
        {
            $usuarioData = ['id' => $usuario->id, 'area' => $usuario->area];
            $token = AutentificadorJWT::CrearToken($usuarioData);

            $payload = json_encode(['token' => $token]);
        } 
        else
        {
            $payload = json_encode(['error' => 'contraseña o usuario no valido']);
            $response = $response->withStatus(401);
        }

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }
    

}
