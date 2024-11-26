<?php
// Error Handling
error_reporting(-1);
ini_set('display_errors', 1);

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;
use Slim\Routing\RouteContext;
use Illuminate\Database\Capsule\Manager as Capsule;


require __DIR__ . '/../vendor/autoload.php';

require_once './db/AccesoDatos.php';
require_once './middlewares/Logger.php';
require_once './middlewares/MiddlewareUsuarios.php';

require_once './controllers/UsuarioController.php';
require_once './controllers/MesaController.php';
require_once './controllers/ProductoController.php';
require_once './controllers/PedidosController.php';
require_once './controllers/EncuestaController.php';
// Load ENV
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

// Instantiate App
$app = AppFactory::create();

// Eloquent
$container=$app->getContainer();

$capsule = new Capsule;
$capsule->addConnection([
    'driver'    => 'mysql',
    'host'      => $_ENV['MYSQL_HOST'],
    'database'  => $_ENV['MYSQL_DB'],
    'username'  => $_ENV['MYSQL_USER'],
    'password'  => $_ENV['MYSQL_PASS'],
    'charset'   => 'utf8',
    'collation' => 'utf8_unicode_ci',
    'prefix'    => '',
]);

$capsule->setAsGlobal();
$capsule->bootEloquent();

// Add error middleware
$app->addErrorMiddleware(true, true, true);

// Add parse body
$app->addBodyParsingMiddleware();

// Routes
$app->group('/login', function (RouteCollectorProxy $group)
{
  $group->post('[/]', \UsuarioController::class . ':LogIn');
});

$app->group('/usuarios', function (RouteCollectorProxy $group) {
    $group->get('[/]', \UsuarioController::class . ':TraerTodos');
    $group->get('/{usuario}', \UsuarioController::class . ':TraerUno');
    $group->post('[/]', \UsuarioController::class . ':CargarUno');
    $group->post('/modificar', \UsuarioController::class . ':ModificarUno');
    $group->post('/borrar', \UsuarioController::class . ':BorrarUno');
  })->add(new MiddlewareUsuarios(["socio"]));

$app->group('/mesas', function (RouteCollectorProxy $group) 
{
  $group->get('[/]', \MesaController::class . ':TraerTodos');
  $group->get('/{mesa}', \MesaController::class . ':TraerUno');
  $group->post('[/]', \MesaController::class . ':CargarUno');
  $group->post('/modificar', \MesaController::class . ':ModificarUno');
  $group->post('/borrar', \MesaController::class . ':BorrarUno');
  
})->add(new MiddlewareUsuarios(["socio"]));

$app->group('/productos', function (RouteCollectorProxy $group) 
{
  $group->get('[/]', \ProductoController::class . ':TraerTodos');
  $group->get('/{producto}', \ProductoController::class . ':TraerUno');
  $group->post('[/]', \ProductoController::class . ':CargarUno');
  $group->post('/modificar', \ProductoController::class . ':ModificarUno');
  $group->post('/borrar', \ProductoController::class . ':BorrarUno');
  
})->add(new MiddlewareUsuarios(["socio"]));

$app->group('/pedidos', function (RouteCollectorProxy $group) {
  $group->get('[/]', \PedidosController::class . ':TraerTodos');
  $group->get('/{pedido}', \PedidosController::class . ':TraerUno');
  $group->get('/ListarPedidoProductosPorEstado/{estado}', \PedidosController::class . ':ListarPedidoProductosPorEstado');
  $group->post('[/]', \PedidosController::class . ':CargarUno');
  $group->post('/agregarproducto', \PedidosController::class . ':AgregarProducto');
  $group->post('/cargarimagen', \PedidosController::class . ':CargarImagen');
  $group->post('/cambiarEstadoPedidoProducto', \PedidosController::class . ':cambiarEstadoPedidoProducto');
  $group->post('/CobrarCuenta', \PedidosController::class . ':CobrarCuenta');
})->add(new MiddlewareUsuarios(["socio","mozo","cocina","barra","cerveceria","candy"]));;

$app->group('/clientes', function (RouteCollectorProxy $group) {
  $group->get('/{pedido}', \PedidosController::class . ':TraerUno');
  $group->post('[/]', \EncuestaController::class . ':CargarEncuesta');

});

$app->group('/estadisticas', function (RouteCollectorProxy $group) {
  $group->get('/encuestas', \EncuestaController::class . ':ObtenerMejoresComentarios');
  $group->get('/MesaMasUsada', \MesaController::class . ':ObtenerMesaMasUsada');
})->add(new MiddlewareUsuarios(["socio"]));

$app->get('[/]', function (Request $request, Response $response) {    
    $payload = json_encode(array("mensaje" => "Slim Framework 4 PHP"));
    
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
});

$app->run();
