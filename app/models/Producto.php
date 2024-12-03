<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Producto extends Model
{

    protected $table = 'productos';
    protected $primaryKey = 'id';
    protected $fillable = ['nombre', 'precio', 'area', 'disponible'];
    public $timestamps = false;
    protected static function booted()
    {
        static::addGlobalScope('disponible', function ($query) {
            $query->where('disponible', '=', 1);
        });
    }

    public static function crearProducto($nombre, $precio, $area, $disponible=1)
    {
        return Producto::create([
            'nombre' => $nombre,
            'precio' => $precio,
            'area' => $area,
            'disponible' => $disponible 
        ])->id;
    }

    public static function ObtenerTodos()
    {
        return Producto::all(); 
    }

    public static function ObtenerTodosMasNoDisponibles()
    {
        return self::withoutGlobalScope('disponible')->get(); 
    }

    public static function obtenerProducto($id)
    {
        return Producto::find($id);
    }
    public static function obtenerProductoNoDisponible($id)
    {
        return Producto::withoutGlobalScope('disponible')->find($id);
    }

    public function modificarProducto()
    {
        $this->update([
            'nombre' => $this->nombre,
            'precio' => $this->precio,
            'area' => $this->area,
            'disponible' => $this->disponible,
        ]);
    }
    

    public function borrarProducto($id)
    {
        $this->disponible = 0;
        $this->save();

    }

    public function restaurarProducto($id)
    {

        $this->disponible = 1;
        $this->save();

    }

    public static function GenerarCSV()
    {
       
        $productos = Producto::all();
    
        
        
        $datosCSV = "id,nombre,precio,area,disponible\n";
    
        
        foreach ($productos as $producto) {
            $datosCSV .= "{$producto->id},{$producto->nombre},{$producto->precio},{$producto->area},{$producto->disponible}\n";
        }
    
        return $datosCSV;
    
    }

    public static function CargarDeCSV($rutaCSV): bool
    {
        $productos = [];
    
        if (($fileHandle = fopen($rutaCSV, 'r')) === false) {
            return false;
        }
    
        fgetcsv($fileHandle); // Skip header
    
        while (($line = fgetcsv($fileHandle)) !== false) {
            if (count($line) < 5) {
                continue;
            }
            $productos[] = [
                'id' => intval(trim($line[0])),
                'nombre' => trim($line[1]),
                'precio' => floatval(trim($line[2])),
                'area' => trim($line[3]),
                'disponible' => intval(trim($line[4]))
            ];
        }
    
        fclose($fileHandle); 
        try {
            foreach ($productos as $producto) 
            {
                $productoCargado=self::obtenerProductoNoDisponible($producto['id']);
                
                if ($productoCargado) 
                {
                  
                    $productoCargado->nombre=$producto['nombre'];
                    $productoCargado->precio=$producto['precio'];
                    $productoCargado->area=$producto['area'];
                    $productoCargado->disponible=$producto['disponible'];
                    $productoCargado->modificarProducto();
                } 
                else
                {
                    
                    self::crearProducto($producto['nombre'],$producto['precio'],$producto['area'],$producto['disponible']);
                }
            }
            return true;
        } catch (\Exception $e) {
            error_log('Error: ' . $e->getMessage());
            return false;
        }
    }
    
}
