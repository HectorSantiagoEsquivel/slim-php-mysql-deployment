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

    public function modificarProducto()
    {
        $this->update([
            'nombre' => $this->nombre,
            'precio' => $this->precio,
            'area' => $this->area,
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

    public static function processCSVFile(string $csvPath): bool
    {
        $productos = [];
    
        
        if (($fileHandle = fopen($csvPath, 'r')) === false) 
        {
            return false;
        }
    
        while (($line = fgets($fileHandle)) !== false) 
        {
            $lineElements = explode(',', $line);
    
            
            if (count($lineElements) < 4) 
            {
                continue;
            }
    
            $productos[] = [
                'id' => trim($lineElements[0]),
                'nombre' => trim($lineElements[1]),
                'precio' => floatval(trim($lineElements[2])),
                'stock' => intval(trim($lineElements[3]))
            ];
        }
    
        fclose($fileHandle); 
    
        
        try 
        {
            foreach ($productos as $producto) 
            {
                self::updateOrCreate(
                    ['id' => $producto['id']],
                    [
                        'nombre' => $producto['nombre'],
                        'precio' => $producto['precio'],
                        'stock' => $producto['stock']
                    ]
                );
            }
            return true; 
        }
        catch (\Exception $e) 
        {
            return false; 
        }
    }
    


}
