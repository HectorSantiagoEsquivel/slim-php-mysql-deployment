<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Usuario extends Model
{

    use SoftDeletes;
    
    protected $table = 'usuarios';
    protected $primaryKey = 'id';
    protected $fillable = ['usuario', 'clave', 'area'];
    const DELETED_AT = 'fechaBaja';
    public $timestamps = false;



    public static function CrearUsuario($usuario, $clave, $area)
    {
        $claveHash=password_hash($clave, PASSWORD_DEFAULT);
        return Usuario::create([
            'usuario' => $usuario,
            'clave' => $claveHash,
            'area' => $area,
        ])->id;
    }


    public static function ObtenerTodos()
    {
        return Usuario::all();
    }

    public static function ObtenerUsuario($id)
    {
        return Usuario::find($id); 
    }
    

    public function Autenticar($clave)
    {
        if (password_verify($clave, $this->clave)) 
        {
            return true;
        }
        return false;
    }
    
    public function ModificarUsuario()
    {

        $claveHash=password_hash($this->clave, PASSWORD_DEFAULT);
        $this->update([
            'usuario' => $this->usuario,
            'clave' => $claveHash,
            'area' => $this->area,
        ]);
        
    }
    
    public function BorrarUsuario()
    {

        $this->delete(); 
  
    }
    
}