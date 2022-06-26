<?php

use GuzzleHttp\Psr7\Response;

//require_once "AutentificadorJWT.php";

class MiddlewareJWT
{
    public static function ValidarTokenMiembros($request, $handler)
    {
        $token = null;
        $response = new Response();
        try{
            $headerPeticion = $request->getHeaderLine('Authorization');

        
            if ($headerPeticion != ""){
                $token = trim(explode("Bearer", $headerPeticion)[1]);
            }
            AutentificadorJWT::VerificarToken($token);
        }
        catch (Exception $e){
            $response->getBody()->write("Token inválido");
            return $response->withStatus(403);
        }
        $response = $handler->handle($request);

        return $response;

        
    }

    public static function ValidarTokenClientes($request, $handler)
    {
        $token = null;
        $response = new Response();
        try{
            $headerPeticion = $request->getHeaderLine('Authorization');

        
            if ($headerPeticion != ""){
                $token = trim(explode("Bearer", $headerPeticion)[1]);
            }
            AutentificadorJWT_Clientes::VerificarToken($token);
        }
        catch (Exception $e){
            $response->getBody()->write("Token inválido");
            return $response->withStatus(403);
        }
        $response = $handler->handle($request);

        return $response;

        
    }
}

?>