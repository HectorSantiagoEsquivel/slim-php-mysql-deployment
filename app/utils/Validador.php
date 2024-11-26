<?php



class Validador
{
    public static function ValidarPalabra($palabra,$arrayPValidas)
    {
        $exito= false;
        if($arrayPValidas!=null && $palabra!=null)
        {

            foreach($arrayPValidas as $palabraValida )
            {
                if(strcmp($palabra,$palabraValida)==0)
                {
                    $exito=true;
                    break;
                }
            }
        }
        return $exito;
    }

    public static function ValidarImagen($tipoArchivo, $tiposValidos, $archivoPeso,$pesoValido)
    {
        
        if(Validador::ValidarTipoArchivo($tipoArchivo,$tiposValidos) && $archivoPeso<=$pesoValido)
        {
            return true;    
        }
        echo "La extensión o el tamaño de los archivos no es correcta.\n";
        return false;
    }

    public static function ValidarTipoArchivo($tipoArchivo, $tiposValidos)
    {
        $exito= false;
        if($tiposValidos!=null && $tipoArchivo!=null)
        {
            foreach($tiposValidos as $tipoValido )
            {
                if(strpos($tipoArchivo, $tipoValido))
                {
                    $exito=true;
                    break;
                }
            }
        }
        return $exito;
    }

    public static function ValidarTIME($timeAValidar)
    {
        if (preg_match('/^([0-1][0-9]|2[0-3]):[0-5][0-9]:[0-5][0-9]$/', $timeAValidar))
        {
            return true;
        }
        return false;
    }
}


?>