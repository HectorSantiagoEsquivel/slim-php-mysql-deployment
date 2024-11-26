<?php

class ControladorArchivos
{

    public static function GuardarArchivo($rutaTemporal,$rutaDestino)
    {
        if (move_uploaded_file($rutaTemporal,  $rutaDestino))
        {
            echo "El archivo ha sido cargado correctamente.";
        }
        else
        {
            echo "Ocurrió algún error al subir archivo. No pudo guardarse.";
        }
    }


}

?>