<?php

/*
BACCHETTA, TOMÁS
TP PROGRAMACION 3 "LA COMANDA"
SPRINT 1
ALTA
VISUALIZACION
BASE DE DATOS

*/


error_reporting(-1);
ini_set('display_errors', 1);
use Illuminate\Support\Facades\Config as config;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy;
use Slim\Routing\RouteContext;
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Support\Facades\Redis;
use Slim\Http\Stream as Stream;


require __DIR__ . '/../vendor/autoload.php';

require_once './MiddleWare/AutentificadorJWT.php';
require_once './MiddleWare/AutentificadorJWT_Clientes.php';
require_once './MiddleWare/MiddlewareJWT.php';
require_once './MiddleWare/Logger.php';
require_once './MiddleWare/ValidadorParams.php';
require_once './MiddleWare/FiltrosListas.php';

require_once "./Controllers/LoginController.php";

require_once './models/estadisticas.php';
require_once "./Controllers/EstadisticasController.php";
require_once './models/registro.php';
require_once "./Controllers/RegistroController.php";
require_once './models/encuesta.php';
require_once "./Controllers/EncuestaController.php";
require_once './models/empleado.php';
require_once "./Controllers/EmpleadoController.php";
require_once "./models/mesa.php";
require_once "./Controllers/MesaController.php";
require_once "./models/producto.php";
require_once "./Controllers/ProductoController.php";
require_once "./models/pedido.php";
require_once "./Controllers/PedidoController.php";  
require_once "./models/orden.php";
require_once "./Controllers/OrdenController.php";
require_once './models/admin.php';
require_once "./Controllers/AdminController.php";



// Load ENV
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

// Instantiate App
$app = AppFactory::create();

//ORM
$container=$app->getContainer();
$capsule = new Capsule();


$capsule->addConnection([
  'driver' => 'mysql',
  'host' => $_ENV['MYSQL_HOST'],
  'database' => $_ENV['MYSQL_DB'],
  'username' => $_ENV['MYSQL_USER'],
  'password' => $_ENV['MYSQL_PASS'],
  'charset' => 'utf8',
  'collation' => 'utf8_unicode_ci',
  'prefix' => '',
]);



$capsule->setAsGlobal();
$capsule->bootEloquent();
// Add error middleware
$app->addErrorMiddleware(true, true, true);

// Add parse body
$app->addBodyParsingMiddleware();

//routes
$app->group("/admins", function (RouteCollectorProxy $group) {
  $group->get('[/]', \AdminController::class . ':TraerTodos');
  $group->get('/{id}', \AdminController::class . ':TraerUno');
  $group->post('[/]', \AdminController::class . ':CargarUno');
  $group->put('/{id}', \AdminController::class . ':ModificarUno');
  $group->delete('/{id}', \AdminController::class . ':BorrarUno');
})->add(\ValidadorParams::class . ':ValidarParamsAdmins')->add(\Logger::class . ':VerificarAdmin')->add(\MiddlewareJWT::class . ':ValidarTokenMiembros');

$app->group("/empleados", function (RouteCollectorProxy $group) {
    $group->get('[/]', \EmpleadoController::class . ':TraerTodos');
    $group->get('/{id}', \EmpleadoController::class . ':TraerUno');
    $group->post('[/]', \EmpleadoController::class . ':CargarUno')->add(\ValidadorParams::class . ':ValidarParamsEmpleados');
    $group->post('/{id}', \EmpleadoController::class . ':CambiarEstado');
    $group->put('/{id}', \EmpleadoController::class . ':ModificarUno');
    $group->delete('/{id}', \EmpleadoController::class . ':BorrarUno');
})->add(\Logger::class . ':VerificarAdmin')->add(\MiddlewareJWT::class . ':ValidarTokenMiembros');

$app->group("/mesas", function (RouteCollectorProxy $group) {
  $group->get('[/]', \MesaController::class . ':TraerTodos');
  $group->get('/{id}', \MesaController::class . ':TraerUno');
  $group->post('[/]', \MesaController::class . ':CargarUno');
  $group->put('/{id}', \MesaController::class . ':CambiarEstado');
  $group->delete('/{id}', \MesaController::class . ':BorrarUno');
})->add(\ValidadorParams::class . ':ValidarParamsMesas')->add(\Logger::class . ':VerificarAdminOMozo')->add(\MiddleWareJWT::class . ':ValidarTokenMiembros');

$app->group("/productos", function (RouteCollectorProxy $group){
  $group->get('[/]', \ProductoController::class . ':TraerTodos')->add(\FiltrosListas::class . ':FiltrarVistaProductos');
  $group->get('/{id}', \ProductoController::class . ':TraerUno');
  $group->post('[/]', \ProductoController::class . ':CargarUno')->add(\ValidadorParams::class . ':ValidarParamsProductos')->add(\Logger::class . ':VerificarAdmin');
  $group->put('/{id}', \ProductoController::class . ':ModificarUno')->add(\Logger::class . ':VerificarAdmin');
  $group->delete('/{id}', \ProductoController::class . ':BorrarUno')->add(\Logger::class . ':VerificarAdmin');

})->add(\MiddleWareJWT::class . ':ValidarTokenMiembros');

$app->group("/pedidos", function (RouteCollectorProxy $group){
  $group->get('[/]', \PedidoController::class . ':TraerTodos')->add(\FiltrosListas::class . ':FiltrarVistaPedidos');;
  $group->get('/{id}', \PedidoController::class . ':TraerUno');
  $group->post('[/]', \PedidoController::class . ':CargarUno')->add(\ValidadorParams::class . ':ValidarParamsCargaPedidos')->add(\Logger::class . ":VerificarMozo");
  $group->put('/{id}', \PedidoController::class . ':ModificarUno')->add(\Logger::class . ":VerificarMozo");
  $group->delete('/{id}', \PedidoController::class . ':BorrarUno');
})->add(\Logger::class . ":VerificarAdminOMozo")->add(\MiddlewareJWT::class . ':ValidarTokenMiembros');

$app->group("/ordenes", function (RouteCollectorProxy $group){
  $group->get('[/]', \OrdenController::class . ':TraerTodos')->add(\FiltrosListas::class . ':FiltrarVistaOrdenes');
  $group->get('/{id}', \OrdenController::class . ':TraerUno');
  $group->post('[/]', \OrdenController::class . ':CargarUno')->add(\Logger::class . ":VerificarMozo")->add(\ValidadorParams::class . ':ValidarParamsOrdenes');
  $group->post('/{id}', \OrdenController::class . ':CambiarEstado')->add(\Logger::class . ':VerificarEmpleadoEspecifico');
  $group->put('/{id}', \OrdenController::class . ':ModificarUno');
  $group->delete('/{id}', \OrdenController::class . ':BorrarUno')->add(\Logger::class . ":VerificarMozo");;
})->add(\MiddleWareJWT::class . ':ValidarTokenMiembros');

$app->group("/encuestas", function (RouteCollectorProxy $group){
  $group->get('/todas', \EncuestaController::class . ':TraerTodos')->add(\Logger::class . ':VerificarAdmin')->add(\MiddlewareJWT::class . ':ValidarTokenMiembros');
  $group->get('/mejorestres', \EncuestaController::class . ':TraerMejoresTres')->add(\Logger::class . ':VerificarAdmin')->add(\MiddlewareJWT::class . ':ValidarTokenMiembros');
  $group->get('/peorestres', \EncuestaController::class . ':TraerPeoresTres')->add(\Logger::class . ':VerificarAdmin')->add(\MiddlewareJWT::class . ':ValidarTokenMiembros');
  $group->post('[/]', \EncuestaController::class . ':CargarUno')->add(\ValidadorParams::class . ':ValidarParamsEncuestas')->add(\MiddleWareJWT::class . ':ValidarTokenClientes');
  $group->delete('/{id}', \EncuestaController::class . ':BorrarUno')->add(\Logger::class . ':VerificarAdmin')->add(\MiddlewareJWT::class . ':ValidarTokenMiembros');
});

$app->group("/registros", function (RouteCollectorProxy $group) {
  $group->get('/todos', \RegistroController::class . ':TraerTodos');
  $group->get('/porEmpleado/{id}', \RegistroController::class . ':TraerPorEmpleado');
  $group->get('/uno/{id}', \RegistroController::class . ':TraerUno');
})->add(\Logger::class . ':VerificarAdmin')->add(\MiddlewareJWT::class . ':ValidarTokenMiembros');


$app->group('/login', function (RouteCollectorProxy $group) {
  $group->post('[/]', \LoginController::class . ':verificarUsuario');
  $group->post('/clientes', \LoginController::class . ':verificarCliente');
});

$app->group('/clientes', function (RouteCollectorProxy $group) {
  $group->get('/verpedido', \PedidoController::class . ':TraerUno_De_Cliente');
})->add(\MiddlewareJWT::class . ':ValidarTokenClientes');

$app->group('/estadisticas', function (RouteCollectorProxy $group) {
  $group->get('/general', \EstadisticasController::class . ':MostrarEstadisticaGeneral');
  $group->get('/facturadoentredosfechas', \EstadisticasController::class . ':MostrarFacturadoEntreDosFechas'); 
})->add(\Logger::class . ':VerificarAdmin')->add(\MiddlewareJWT::class . ':ValidarTokenMiembros');



//CSV

$app->group('/csv', function (RouteCollectorProxy $group) {
  $group->get('/productos', \ProductoController::class . ':CrearCsv');//http://localhost:777/csv/productos en navegador para descargar csv
  $group->post('/productos', \ProductoController::class . ':ImportarCsv');
  $group->get('/pedidos', \PedidoController::class . ':CrearCsv');//http://localhost:777/csv/pedidos en navegador para descargar csv
  $group->post('/pedidos', \PedidoController::class . ':ImportarCsv');
  $group->get('/ordenes', \OrdenController::class . ':CrearCsv');//http://localhost:777/csv/ordenes en navegador para descargar csv
  $group->post('/ordenes', \OrdenController::class . ':ImportarCsv');
  $group->get('/mesas', \MesaController::class . ':CrearCsv');//http://localhost:777/csv/mesas en navegador para descargar csv
  $group->post('/mesas', \MesaController::class . ':ImportarCsv');
  $group->get('/encuestas', \EncuestaController::class . ':CrearCsv');//http://localhost:777/csv/encuestas en navegador para descargar csv
  $group->post('/encuestas', \EncuestaController::class . ':ImportarCsv');
  $group->get('/empleados', \EmpleadoController::class . ':CrearCsv');//http://localhost:777/csv/empleados en navegador para descargar csv
  $group->post('/empleados', \EmpleadoController::class . ':ImportarCsv');
  $group->get('/admins', \AdminController::class . ':CrearCsv');//http://localhost:777/csv/admins en navegador para descargar csv
  $group->post('/admins', \AdminController::class . ':ImportarCsv');
})->add(function ($request, $handler) {
  $response = $handler->handle($request);
  return $response
          ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
          ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS');
});


//facturas

$app->group('/factura', function (RouteCollectorProxy $group) {
  $group->get('/{id}', \PedidoController::class . ':CrearPDF')->add(\ValidadorParams::class . ':ValidarPedidoParaFacturar');////http://localhost:777/factura/'id'
})->add(function ($request, $handler) {//CORS
  $response = $handler->handle($request);
  return $response
          ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
          ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS');
});



// JWT rutas TEST
$app->group('/jwt', function (RouteCollectorProxy $group) {
  $group->get('/devolverPayLoad', function (Request $request, Response $response) {
    $header = $request->getHeaderLine('Authorization');
    $token = trim(explode("Bearer", $header)[1]);

    try {
      $payload = json_encode(array('payload' => AutentificadorJWT::ObtenerPayLoad($token)));
    } catch (Exception $e) {
      $payload = json_encode(array('error' => $e->getMessage()));
    }

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  });

  $group->get('/devolverPuesto', function (Request $request, Response $response) {
    $header = $request->getHeaderLine('Authorization');
    $token = trim(explode("Bearer", $header)[1]);

    try {
      $payload = json_encode(array('puesto' => AutentificadorJWT::ObtenerPuesto($token)));
    } catch (Exception $e) {
      $payload = json_encode(array('error' => $e->getMessage()));
    }

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  });

  $group->get('/verificarToken', function (Request $request, Response $response) {
    $header = $request->getHeaderLine('Authorization');
    $token = trim(explode("Bearer", $header)[1]);
    $esValido = false;

    try {
      AutentificadorJWT::verificarToken($token);
      $esValido = true;
    } catch (Exception $e) {
      $payload = json_encode(array('error' => $e->getMessage()));
    }

    if ($esValido) {
      $payload = json_encode(array('valid' => $esValido));
    }

    $response->getBody()->write($payload);
    return $response
      ->withHeader('Content-Type', 'application/json');
  });
});




$app->post('[/]', function (Request $request, Response $response) {
    $response->getBody()->write("TP BACCHETTA");
    return $response;
});

  $app->run();

?>