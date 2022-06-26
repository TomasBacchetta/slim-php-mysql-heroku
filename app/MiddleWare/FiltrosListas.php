<?php

use GuzzleHttp\Psr7\Response;
use Psr7Middlewares\Middleware\Payload;
use \App\Models\admin as admin;
use App\Models\empleado as empleado;
use App\Models\producto as producto;
use App\Models\pedido as pedido;
use App\Models\orden as orden;

class FiltrosListas {

    public static function FiltrarVistaProductos($request, $handler){
        
        $response = new Response();

        $header = $request->getHeaderLine('Authorization');
        $token = trim(explode("Bearer", $header)[1]);
        $puesto = AutentificadorJWT::ObtenerPuesto($token);
        switch($puesto){
            case "admin":
            case "Mozo":
                $response = $handler->handle($request);
                return $response->withHeader("Content-Type", "application/json");
                break;
            case "Cocinero":
                $productos1 =producto::ObtenerProductosPorSector("Candy_Bar");
                $productos2 =producto::ObtenerProductosPorSector("Cocina");
                $productos = $productos1->concat($productos2);
                break;
            case "Cervecero":
                $productos = producto::ObtenerProductosPorSector("Barra_Choperas");
                break;
            case "Bartender":
                $productos = producto::ObtenerProductosPorSector("Barra_Tragos");
                break;

        }


        $payload = json_encode(array("listaProductos" => $productos));

        $response->getBody()->write($payload);

        return $response->withHeader("Content-Type", "application/json");


        
    }

    public static function FiltrarVistaPedidos($request, $handler){
        
        $response = new Response();

        $header = $request->getHeaderLine('Authorization');
        $token = trim(explode("Bearer", $header)[1]);
        $puesto = AutentificadorJWT::ObtenerPuesto($token);
        

        if ($puesto == "admin"){
            $response = $handler->handle($request);
                return $response->withHeader("Content-Type", "application/json");
        } else {
            $id = AutentificadorJWT::ObtenerId($token);
            $pedidos = pedido::ObtenerPedidosDelMozo($id);
        }
        


        $payload = json_encode(array("listaPedidos" => $pedidos));

        $response->getBody()->write($payload);

        return $response->withHeader("Content-Type", "application/json");


        
    }


    public static function FiltrarVistaOrdenes($request, $handler){
        
        $response = new Response();

        $header = $request->getHeaderLine('Authorization');
        $token = trim(explode("Bearer", $header)[1]);
        $puesto = AutentificadorJWT::ObtenerPuesto($token);
        switch($puesto){
            case "admin":
            case "Mozo":
                $response = $handler->handle($request);
                return $response->withHeader("Content-Type", "application/json");
                break;
            case "Cocinero":
                $ordenes1 =orden::ObtenerOrdenesPorSector("Candy_Bar");
                $ordenes2 =orden::ObtenerOrdenesPorSector("Cocina");
                $ordenes = $ordenes1->concat($ordenes2);
                break;
            case "Cervecero":
                $ordenes = orden::ObtenerOrdenesPorSector("Barra_Choperas");
                break;
            case "Bartender":
                $ordenes = orden::ObtenerOrdenesPorSector("Barra_Tragos");
                break;

        }


        $payload = json_encode(array("listaOrdenes" => $ordenes));

        $response->getBody()->write($payload);

        return $response->withHeader("Content-Type", "application/json");


        
    }

    

        



    

    

    

    
}



?>