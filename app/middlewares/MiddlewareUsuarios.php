<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

require_once './models/Usuario.php';
require_once './utils/AutentificadorJWT.php';
require_once './utils/Validador.php';

class MiddlewareUsuarios
{
    private $areasValidas;

    public function __construct(array $areasValidas)
    {
        $this->areasValidas = $areasValidas;
    }

    public function __invoke(Request $request, RequestHandler $handler): Response
    {
       
        $header = $request->getHeaderLine('Autorizacion');
        $token = self::extraerToken($header);

        try 
        {
            
            AutentificadorJWT::VerificarToken($token);          
            $usuarioData = AutentificadorJWT::ObtenerData($token);
            
            if (Validador::ValidarPalabra($usuarioData->area,$this->areasValidas)) 
            {
                $request = $request->withAttribute('usuarioData', $usuarioData);
                $response = $handler->handle($request);
            } 
            else 
            {

                $response = new Response();
                $payload = json_encode(["mensaje" => "Usuario no autorizado para realizar cambios"]);
                $response->getBody()->write($payload);
            }
            
        } 
        catch (Exception $e) 
        {
            $response = new Response();
            $payload = json_encode(["mensaje" => "ERROR: Hubo un error con el TOKEN"]);
            $response->getBody()->write($payload);
        }

        return $response->withHeader('Content-Type', 'application/json');
    }

    private function extraerToken($header)
    {
        if (empty($header)) 
        {
            throw new Exception("Falta 'Autorizacion' en el header");
        }
        $token = trim(explode("Bearer", $header)[1]);
        if (empty($token)) 
        {
            throw new Exception("Token no encontrado");
        }
        return $token;
    }
}
