<?php

require_once './utils/Validador.php';
use App\Models\Encuesta;
use App\Models\Accion;


class EncuestaController 
{

    public function CargarEncuesta($request, $response, $args)
    {
        $params = $request->getParsedBody();

        $idPedido = $params['idPedido'];
        $puntajeMesa = $params['puntajeMesa'];
        $puntajeRestaurante = $params['puntajeRestaurante'];
        $puntajeMozo = $params['puntajeMozo'];
        $puntajeCocinero = $params['puntajeCocinero'];
        $reseña = $params['reseña'];
        
        if (
            $puntajeMesa >= 1 && $puntajeMesa <= 10 &&
            $puntajeRestaurante >= 1 && $puntajeRestaurante <= 10 &&
            $puntajeMozo >= 1 && $puntajeMozo <= 10 &&
            $puntajeCocinero >= 1 && $puntajeCocinero <= 10
        )
        {

            if(strlen($reseña) <= 200)
            {   
                
                Encuesta::CrearEncuesta($idPedido,$puntajeMesa,$puntajeRestaurante,$puntajeMozo,$puntajeCocinero,$reseña);
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
