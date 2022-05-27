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

require __DIR__ . '/../vendor/autoload.php';

require_once './db/AccesoDatos.php';
// require_once './middlewares/Logger.php';

require_once './controllers/UsuarioController.php';

// Load ENV
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

// Instantiate App
$app = AppFactory::create();

// Set base path
//$app->setBasePath('/app');

// Add error middleware
$app->addErrorMiddleware(true, true, true);

// Add parse body
$app->addBodyParsingMiddleware();

// Routes
$app->group('/usuarios', function (RouteCollectorProxy $group) {
    $group->get('[/]', \UsuarioController::class . ':TraerTodos');
    $group->get('/{usuario}', \UsuarioController::class . ':TraerUno');
    $group->post('[/]', \UsuarioController::class . ':CargarUno');
  });

$app->get('[/]', function (Request $request, Response $response) {    
    $response->getBody()->write("Slim Framework 4 PHP");
    return $response;

});

$app->run();

/*EXPLICACION DE POR QUE SE PUEDE HACER ESO

Static class methods can also be passed without instantiating an object of that class by passing the class name instead of an object at index 0.
  // Only from inside the same class
  $this->processSomething([__CLASS__, 'myStaticCallback']);
  // From either inside or outside the same class
  $myObject->processSomething(['\Namespace\MyClass', 'myStaticCallback']);
  $myObject->processSomething(['\Namespace\MyClass::myStaticCallback']); // PHP 5.2.3+
  $myObject->processSomething([MyClass::class, 'myStaticCallback']); // PHP 5.5.0+

*/