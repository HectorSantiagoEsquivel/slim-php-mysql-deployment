<?php
require_once './models/Pedido.php';
require_once './interfaces/IApiUsable.php';
require_once './utils/Validador.php';
require_once './utils/ControladorArchivos.php';

use App\Models\Pedido;
use App\Models\PedidoProducto;
use App\Models\Mesa;
use App\Models\Producto;



class PedidosController implements IApiUsable
{

    public function CargarUno($request, $response, $args)
    {
        $params = $request->getParsedBody();
        $idMesa = $params['idMesa'];
        $mesa=Mesa::obtenerMesa($idMesa);
        $usuarioData = $request->getAttribute('usuarioData');
        $idMozo=$usuarioData->id;
        $areaUsuario=$usuarioData->area;


        if($mesa->estado=='disponible')
        {
            if($areaUsuario=='mozo')
            {
                $mesa->estado='ocupada';
                $mesa->ModificarMesa();
                $pedidoId = Pedido::CrearPedido($idMesa,$idMozo);

                $payload = json_encode(["mensaje" => "Pedido creado con éxito", "idPedido" => $pedidoId]);
            }
            else
            {
                $payload = json_encode(["mensaje" => "Usuario no encontrado o responsabilidad incorrecta"]);
            }

        }
        else
        {
            $payload = json_encode(["mensaje" => "Mesa no disponible"]);
        }
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }


    public function CargarImagen($request,$response,$args)
    {
        $carpeta_archivos = 'archivos/ImagenesDeMesas/';
        $tiposArchivoValidos=array ("png","jpg","jpeg");
        $tipo_archivo = $_FILES['archivo']['type'];
        $tamano_archivo = $_FILES['archivo']['size'];

        $params = $request->getParsedBody();
        $idPedido=$params['idPedido'];
        $pedido=Pedido::ObtenerPedido($idPedido);

        if($pedido)
        {

            if (Validador::ValidarImagen($tipo_archivo, $tiposArchivoValidos, $tamano_archivo, 2048000)) 
            {
                $nombre_archivo=$pedido->id;
                $ruta_destino = $carpeta_archivos . $nombre_archivo .'.'. substr($tipo_archivo, strpos($tipo_archivo, "/") + 1);
                ControladorArchivos::GuardarArchivo($_FILES['archivo']['tmp_name'],$ruta_destino);
                $pedido->rutaImagen=$ruta_destino;   
                $pedido->ModificarPedido();
                $payload = json_encode(["mensaje" => "Imagen subida correctamente"]);
            }
            else 
            {
                $payload = json_encode(["mensaje" => "La extensión o el tamaño de los archivos no es correcta."]);
            }
        }
        else
        {
            $payload = json_encode(["mensaje" => "Pedido no encontrado"]);
        }
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');


    }

    public function AgregarProducto($request,$response,$args)
    {
        $params = $request->getParsedBody();
        $idPedido=$params['idPedido'];
        $idProducto=$params['idProducto'];
        $cantidad=$params['cantidad'];
        $pedido=Pedido::ObtenerPedido($idPedido);
        
        if($pedido)
        {
            $producto=Producto::obtenerProducto($idProducto);
            
            if($producto && $cantidad>0)
            {
                if($producto->disponible==1)
                {
                    PedidoProducto::AgregarProducto($idPedido,$idProducto,$cantidad);
                    $payload = json_encode(["mensaje" => "Producto agregado exitosamente"]);
                }
                else 
                {
                    $payload = json_encode(["mensaje" => "Producto no disponible"]); 
                }

            }
            else
            {
                $payload = json_encode(["mensaje" => "Producto no encontrado o cantidad no suficiente"]);
            }
        }
        else
        {
            $payload = json_encode(["mensaje" => "Pedido no encontrado"]);
        }
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function ListarPedidoProductosPorEstado($request, $response, $args)
    {
        $usuarioData = $request->getAttribute('usuarioData');
        $estado = $args['estado'];  
        $filtro=null;

        switch ($estado) 
        {
            case 'pendiente':
                $filtro = [
                    'productos.area' => $usuarioData->area,
                    'pedidosproductos.estado' => $estado,];
                break;
            case 'preparacion':
                $filtro = [
                    'pedidosproductos.idEmpleado' => $usuarioData->id,
                    'pedidosproductos.estado' => $estado,
                ];
                break;
            case 'listo':
                $filtro = [

                    'pedidosproductos.estado' => $estado,
                ];
                break;
            default:
                $payload = json_encode(["mensaje" => "Estado no válido"]);
                break;
        }

        if($filtro)
        {
            $productos = PedidoProducto::FiltrarDatos($filtro);
            $payload = json_encode(["productos" => $productos]);
        }


        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function cambiarEstadoPedidoProducto($request, $response, $args)
    {
        $params = $request->getParsedBody();
        $idPedidoProducto = $params['idPedidoProducto'];
        $usuarioData = $request->getAttribute('usuarioData'); 
        
    
        $pedidoProducto = PedidoProducto::ObtenerPedidoProducto($idPedidoProducto);
        $datosProducto=$pedidoProducto->ObtenerDatosProducto();

        if ($pedidoProducto) 
        {
            switch ($pedidoProducto->estado) 
            {
                case 'pendiente':

                    if($usuarioData->area==$datosProducto->area)
                    {
                        $tiempoEstimado=$params['tiempoEstimado'];
                        if(Validador::ValidarTIME($tiempoEstimado))
                        {
                            $pedidoProducto->tiempoEstimado=$tiempoEstimado;
                            $pedidoProducto->idEmpleado=$usuarioData->id;
                            $pedidoProducto->estado="preparacion";
                            $pedidoProducto->ModificarPedidoProducto();
                            $payload = json_encode(["mensaje" => "Empleado asignado exitosamente"]);
                        }
                        else
                        {
                            $payload = json_encode(["mensaje" => "Tiempo Estimado no valido"]);
                        }

                    }
                    else
                    {
                        $payload = json_encode(["mensaje" => "Responsabilidad incorrecta"]);
                    }
                    break;
                case 'preparacion':
                    if($usuarioData->id==$datosProducto->idEmpleado)
                    {
                        $pedidoProducto->estado = 'listo';
                        $pedidoProducto->ModificarPedidoProducto(); 
                        $payload = json_encode(["mensaje" => "Producto marcado como listo para servir"]);
    
                    }
                    else
                    {
                        $payload = json_encode(["mensaje" => "Producto no asignado a este usuario"]);
                    }
                    break;
                case 'listo':
                    if($usuarioData->id==$datosProducto->idMozo)
                    {
    
                        $pedidoProducto->estado = 'entregado';
                        $pedidoProducto->ModificarPedidoProducto();
                        $pedidoProducto->CargarTiempoTotal();  
    
                        $payload = json_encode(["mensaje" => "Producto marcado como entregado"]);
    
                    }
                    else
                    {
                        $payload = json_encode(["mensaje" => "Pedido no asignado a este mozo"]);
                    }
                    break;
                default:
                    $payload = json_encode(["mensaje" => "Estado no válido"]);
                    break;
            }
            
        } 
        else 
        {
            $payload = json_encode(["mensaje" => "Producto no encontrado"]);
        }
        
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function TraerUno($request, $response, $args)
    {
        $idPedido = $args['pedido'];

        $pedidoProductosPorPedido=PedidoProducto::ObtenerPorPedido($idPedido);
    
        if (!$pedidoProductosPorPedido->isEmpty()) 
        {
            $detallePedido = PedidoProducto::FormatearDetalles($pedidoProductosPorPedido);
            $payload = json_encode($detallePedido);
        } 
        else 
        {
            $payload = json_encode(["mensaje" => "No se encontraron productos para el pedido especificado."]);
        }
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }


    public function TraerTodos($request, $response, $args)
    {
        $pedidosActivos = Pedido::ObtenerPedidosActivos();
        if (!$pedidosActivos->isEmpty()) 
        {
            $detallePedidos = [];

            foreach ($pedidosActivos as $pedido) 
            {

                $pedidoProductos = PedidoProducto::ObtenerPorPedido($pedido->id);

                if (!$pedidoProductos->isEmpty()) 
                {
                    $detallePedidos[] = PedidoProducto::FormatearDetalles($pedidoProductos);
                }
            }

            $payload = json_encode($detallePedidos);
        }
        else 
        {
            $payload = json_encode(["mensaje" => "No hay pedidos activos en este momento."]);
        }
        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }
    
    public function CobrarCuenta($request, $response, $args)
    {
        $params = $request->getParsedBody();
        $idPedido = $params['idPedido'];
        $pedido = Pedido::ObtenerPedido($idPedido);
        $usuarioData = $request->getAttribute('usuarioData');
        $idMozo=$usuarioData->id;
        
        if ($pedido) 
        {
            if ($pedido->estado == 'activo') 
            {
                if($pedido->idMozo==$idMozo)
                {
                                      
                    $productosPendientes = PedidoProducto::FiltrarDatos([
                        'pedidosproductos.idPedido' => $pedido->id,
                        'pedidosproductos.estado' => 'pendiente'
                    ]);
                    $productosEnPreparacion = PedidoProducto::FiltrarDatos([
                        'pedidosproductos.idPedido' => $pedido->id,
                        'pedidosproductos.estado' => 'preparacion'
                    ]);
                    $productosListos = PedidoProducto::FiltrarDatos([
                        'pedidosproductos.idPedido' => $pedido->id,
                        'pedidosproductos.estado' => 'listo'
                    ]);                    
                    $productosCombinados = $productosPendientes->merge($productosEnPreparacion)->merge($productosListos);

                    if($productosCombinados->isEmpty())
                    {
                        $total = PedidoProducto::calcularTotal($pedido->id);
                        $pedido->estado='cerrado';
                        $pedido->ModificarPedido();
                        $payload = json_encode([
                            'total' => $total,
                            'mensaje' => 'La cuenta ha sido cobrada y el pedido está cerrado.'
                        ]);
                    }
                    else
                    {
                        $payload = json_encode(["mensaje" => "Esta cuenta todavia tiene pedidos en proceso"]);
                    }

                }
                else
                {
                    $payload = json_encode(["mensaje" => "Pedido no asignado a este mozo"]);
                }
            } 
            else 
            {
                $payload = json_encode(["mensaje" => "El Pedido no esta activo."]);
            }
        } 
        else 
        {
            $payload = json_encode([
                'mensaje' => 'Pedido no encontrado.'
            ]);
        }

        $response->getBody()->write($payload);
        return $response->withHeader('Content-Type', 'application/json');
    }


    public function ModificarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $id = $parametros['id'];
        $idMesa = $parametros['idMesa'];

        $pedido = new Pedido();
        $pedido->id = $id;
        $pedido->idMesa = $idMesa;

        $pedido->modificarPedido();

        $payload = json_encode(array("mensaje" => "Pedido modificado con exito"));

        $response->getBody()->write($payload);
        return $response
            ->withHeader('Content-Type', 'application/json');
    }


    public function BorrarUno($request, $response, $args)
    {
        $parametros = $request->getParsedBody();

        $id = $parametros['id'];
        $pedido=Pedido::ObtenerPedido($id);
        $pedido->BorrarUno();

        $payload = json_encode(array("mensaje" => "Pedido borrado con exito"));

        $response->getBody()->write($payload);
        return $response
            ->withHeader('Content-Type', 'application/json');
    }


}
