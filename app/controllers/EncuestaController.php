<?php

require_once './utils/Validador.php';
use App\Models\Encuesta;

class EncuestaController 
{

    public function CargarEncuesta($request, $response, $args)
    {
        $params = $request->getParsedBody();

        $idPedido = $params['idPedido'];
        $puntaje = $params['puntaje'];
        $reseña = $params['reseña'];
        
        if($puntaje>=1 && $puntaje<=10)
        {

            if(strlen($reseña) <= 200)
            {
                Encuesta::CrearEncuesta($idPedido,$puntaje,$reseña);
                $payload = json_encode(["mensaje" => "Encuesta guardada con éxito"]);
            }
            else
            {
                $payload = json_encode(["mensaje" => "La reseña no puede tener más de 200 caracteres"]);
            }
        }
        else
        {
            $payload = json_encode(["mensaje" => "El puntaje debe estar entre 1 y 10"]);
        }

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function ObtenerMejoresComentarios($request, $response, $args)
    {

        $encuestas = Encuesta::ObtenerMejoresComentarios();
        
        if (!$encuestas->isEmpty()) 
        {
            $payload = json_encode($encuestas);
            
        } 
        else
        {
            $payload = json_encode(["mensaje" => "No hay encuestas disponibles"]);
        }

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }
}
