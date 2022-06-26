<?php

use GuzzleHttp\Psr7\Response;
use Psr7Middlewares\Middleware\Payload;
use App\Models\orden as orden;
use App\Models\empleado as empleado;

class Logger {

    public static function VerificarAdmin($request, $handler)
    {
        $header = $request->getHeaderLine('Authorization');
        $token = trim(explode("Bearer", $header)[1]);

        $response = new Response();
        

        if (AutentificadorJWT::ObtenerPuesto($token) == "admin"){
            $response = $handler->handle($request); //ejecuta la funcion del controller
            return $response;
        } else {
            $payload = json_encode(array("Mensaje" => "Usted no es admin. Acceso denegado"));
            $response->getBody()->write($payload);
            return $response->withStatus(403);
        }
        

        
        
    }

    

    public static function VerificarAdminOMozo($request, $handler)
    {
        $header = $request->getHeaderLine('Authorization');
        $token = trim(explode("Bearer", $header)[1]);

        $response = new Response();
        

        if (AutentificadorJWT::ObtenerPuesto($token) == "admin" ||
            (AutentificadorJWT::ObtenerPuesto($token) == "Mozo" && empleado::withTrashed()->where("id", AutentificadorJWT::ObtenerId($token))->withTrashed()->first()->estado != "Inactivo")){
            $response = $handler->handle($request); //ejecuta la funcion del controller
            return $response;
        } else {
            $payload = json_encode(array("Mensaje" => "Usted no es admin o mozo. Acceso denegado"));
            $response->getBody()->write($payload);
            
            return $response->withStatus(403);
        }
        

        
        
    }

   

    public static function VerificarMozo($request, $handler)
    {
        $header = $request->getHeaderLine('Authorization');
        $token = trim(explode("Bearer", $header)[1]);

        $response = new Response();
        

        if (AutentificadorJWT::ObtenerPuesto($token) == "Mozo" && empleado::withTrashed()->where("id", AutentificadorJWT::ObtenerId($token))->first()->estado != "Inactivo"){
            $response = $handler->handle($request); //ejecuta la funcion del controller
            return $response;
        } else {
            $payload = json_encode(array("Mensaje" => "Usted no es mozo. Acceso denegado"));
            $response->getBody()->write($payload);
            
            return $response->withStatus(403);
        }
        
        
    }

    public static function VerificarNoMozo($request, $handler)
    {
        $header = $request->getHeaderLine('Authorization');
        $token = trim(explode("Bearer", $header)[1]);

        $response = new Response();
        

        if (AutentificadorJWT::ObtenerPuesto($token) != "Mozo"){
            $response = $handler->handle($request); //ejecuta la funcion del controller
            return $response;
        } else {
            $payload = json_encode(array("Mensaje" => "Los mozos no pueden acceder a las ordenes. Acceso denegado"));
            $response->getBody()->write($payload);
            
            return $response->withStatus(403);
        }
        

        
        
    }


    public static function VerificarEmpleadoEspecifico($request, $handler){
        $header = $request->getHeaderLine('Authorization');
        $token = trim(explode("Bearer", $header)[1]);
        $url = $request->getUri()->getPath();
        $id_orden = explode('/', $url)[2];
        $response = new Response();
        $id_empleado = AutentificadorJWT::ObtenerId($token);        

        if (orden::SiOrdenEsDelEmpleado($id_orden, $id_empleado) ||
            (orden::ObtenerEstadoPorId($id_orden) == "Abierta" && 
             orden::SiOrdenEsDelSectorDelEmpleado($id_orden, $id_empleado) && 
             empleado::where("id", $id_empleado)->first()->estado != "Inactivo")){
            $response = $handler->handle($request); //ejecuta la funcion del controller
            return $response;
        } else {
            $payload = json_encode(array("Mensaje" => "Esta orden no es suya, o no es de su sector, o el empleado esta inactivo"));
            $response->getBody()->write($payload);
            return $response->withStatus(403);
        }
    }


  

    
   
    
    
    

    

    
}



?>