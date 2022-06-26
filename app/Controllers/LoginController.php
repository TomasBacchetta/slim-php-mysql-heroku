<?php

use \App\Models\empleado as empleado;
use \App\Models\admin as admin;
use \App\Models\registro as registro;
use \App\Models\pedido as pedido;
use \App\Models\encuesta as encuesta;

//use \App\Middleware\AutentificadorJWT as AutentificadorJWT;
use GuzzleHttp\Psr7\Stream;

class LoginController {
    public function VerificarUsuario($request, $response, $args){
        $parametros = $request->getParsedBody();

        $nombre = $parametros['nombre'];
        $clave = $parametros['clave'];


        if (empleado::verificarEmpleado($nombre, $clave)){
            $empleado = empleado::where("nombre", $nombre)->first();
            if ($empleado->estado == "Activo"){
                $tokenNuevo = AutentificadorJWT::CrearToken(
                    empleado::where("nombre", $nombre)->value("puesto"), 
                    empleado::where("nombre", $nombre)->value("id")
                );

                registro::CrearRegistro($empleado->id, "Login");

                $response->getBody()->write(json_encode(array("token"=>$tokenNuevo)));
    
                return $response;
            } else {
                $response->getBody()->write("Empleado inactivo. Consulte al administrador");
                return $response->withStatus(403);
            }
            
        }
        if (admin::verificarAdmin($nombre, $clave)){
            $tokenNuevo = AutentificadorJWT::CrearToken("admin", 999);
            $response->getBody()->write(json_encode(array("token"=>$tokenNuevo)));

            return $response;
        }
        
        $response->getBody()->write("Datos erróneos");
        return $response->withStatus(403);
        
    }

    public function VerificarCliente($request, $response, $args){
        $parametros = $request->getParsedBody();

        $mesa_id = $parametros['mesa_id'];
        $pedido_id = $parametros['pedido_id'];

        if (!pedido::existePedido_PorId($pedido_id)){
            $response->getBody()->write("El pedido no existe");
            return $response->withStatus(403);
        }
        if (encuesta::YaHayEncuestaParaEsePedido($pedido_id)){
            $response->getBody()->write("Ya hizo la encuesta. No puede loguearse");
            return $response->withStatus(403);
        }
        
        $tokenNuevo = AutentificadorJWT_Clientes::CrearToken($mesa_id, $pedido_id);
        $response->getBody()->write(json_encode(array("token"=>$tokenNuevo)));

        return $response;

        
        
    }
}

?>