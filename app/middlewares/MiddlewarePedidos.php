<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

require_once './models/Usuario.php';


class MiddlewareUsuarios
{
    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        $parametros = $request->getParsedBody();
        $idUsuarioActual = $parametros['idUsuarioActual'];
        $usuario = Usuario::obtenerUsuario($idUsuarioActual);

        if ($usuario) 
        {   

            if($usuario->area=="socio")
            {
                $response = $handler->handle($request);
            }
            else
            {
                $response = new Response();
                $payload = json_encode(["mensaje" => "Usuario no autorizado para realizar cambios"]);
                $response->getBody()->write($payload);
            }
            
        } 
        else 
        {

            $response = new Response();
            $payload = json_encode(["mensaje" => "Usuario incorrecto"]);
            $response->getBody()->write($payload);
        }

        return $response->withHeader('Content-Type', 'application/json');
    }
}
