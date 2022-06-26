<?php

use GuzzleHttp\Psr7\Response;
use Psr7Middlewares\Middleware\Payload;
use \App\Models\admin as admin;
use App\Models\empleado as empleado;
use App\Models\producto as producto;
use App\Models\pedido as pedido;
use App\Models\mesa as mesa;
use App\Models\encuesta as encuesta;


class ValidadorParams {

    public static function ValidarParamsAdmins($request, $handler){
        $method = $request->getMethod();
        
        $response = new Response();

        if ($method == "POST" || $method == "PUT"){
            $dato = $request->getParsedBody();
            $error = false;
            $mensaje = "";
            
            
            if (!isset($dato)){
                $payload = json_encode(array("Mensaje" => "Faltan todos los parametros"));
                $response->getBody()->write($payload);
                return $response->withStatus(403);
                
            } else {
                if (!array_key_exists("nombre", $dato)){
                    $mensaje .= "Falta el parametro de nombre. ";
                    $error = true;   
                } else {
                    if ($method == "POST"){
                        if (admin::existeAdmin($dato["nombre"])){
                            $mensaje .= "Ya existe ese admin. ";
                            $error = true;
                        }
                    }
                    
                    if ($dato["nombre"] == ""){
                        $mensaje .= "Nombre invalido. ";
                        $error = true;
                    }
                }
                if (!array_key_exists("clave", $dato)){
                    $mensaje .= "Falta el parametro de clave";
                    $error = true;  
                } else {
                    if ($dato["clave"] == "" || is_nan($dato["clave"])){
                        $mensaje .= "Clave invalida. ";
                        $error = true;
                    } 
                }
                
               
                
                if ($error){
                    
                    $payload = json_encode(array("Mensaje" => $mensaje));
                    $response->getBody()->write($payload);
                    return $response->withStatus(403); 
                }
                
                    
            }

            
        }
        

        $response = $handler->handle($request);
        return $response;
        
          
    }


    public static function ValidarParamsEmpleados($request, $handler){
        $method = $request->getMethod();
        
        $response = new Response();

        
        $dato = $request->getParsedBody();
        $error = false;
        $mensaje = "";
        
        
        if (!isset($dato)){
            $payload = json_encode(array("Mensaje" => "Faltan todos los parametros"));
            $response->getBody()->write($payload);
            return $response->withStatus(403);
            
        } else {
            if (!array_key_exists("nombre", $dato)){
                $mensaje .= "Falta el parametro de nombre. ";
                $error = true;   
            } else {//si existe el parametro
                if ($method == "POST"){
                    if (empleado::existeEmpleado($dato["nombre"])){
                        $mensaje .= "Ya existe ese empleado. ";
                        $error = true;
                    }
                }
                if ($dato["nombre"] == ""){
                    $mensaje .= "Nombre invalido. ";
                    $error = true;
                }
            }
            if (!array_key_exists("clave", $dato)){
                $mensaje .= "Falta el parametro de clave";
                $error = true;  
            } else {//si existe el parametro
                if ($dato["clave"] == "" || is_nan($dato["clave"])){
                    $mensaje .= "Clave invalida. ";
                    $error = true;
                } 
            }
            if (!array_key_exists("puesto", $dato)){
                $mensaje .= "Falta el parametro de puesto";
                $error = true;  
            } else {//si existe el parametro
                if ($dato["puesto"] == "" || (
                    $dato["puesto"] != "Mozo" &&
                    $dato["puesto"] != "Cocinero" &&
                    $dato["puesto"] != "Bartender" &&
                    $dato["puesto"] != "Cervecero"

                )){
                    $mensaje .= "Puesto invalido. ";
                    $error = true;
                } 
            }
            
            
            
            if ($error){
                
                $payload = json_encode(array("Mensaje" => $mensaje));
                $response->getBody()->write($payload);
                return $response->withStatus(403); 
            }
            
                
        }

            
        
        

        $response = $handler->handle($request);
        return $response;
        
          
    }

    public static function ValidarParamsMesas($request, $handler){
        $method = $request->getMethod();
       
        
        $response = new Response();

        if ($method == "PUT"){
            $dato = $request->getParsedBody();
            $error = false;
            $mensaje = "";
            
            
            if (!isset($dato)){
                $payload = json_encode(array("Mensaje" => "Faltan todos los parametros"));
                $response->getBody()->write($payload);
                return $response->withStatus(403);
                
            } else {
                if (!array_key_exists("estado", $dato)){
                    $mensaje .= "Falta el parametro de estado. ";
                    $error = true;   
                } else {//si existe el parametro
                    if ($dato["estado"] == "" || (
                        $dato["estado"] != "Con cliente esperando pedido" &&
                        $dato["estado"] != "Con cliente comiendo" &&
                        $dato["estado"] != "Con cliente pagando" &&
                        $dato["estado"] != "Cerrada"
                        )){
                        $mensaje .= "Estado invalido. ";
                        $error = true;
                    } else {
                        $header = $request->getHeaderLine('Authorization');
                        $token = trim(explode("Bearer", $header)[1]);
                        if ($dato["estado"] == "Cerrada" && AutentificadorJWT::ObtenerPuesto($token) != "admin"){
                            $payload = json_encode(array("Mensaje" => "Solo los admins pueden cerrar la mesa"));
                                    $response->getBody()->write($payload);
                                    return $response->withStatus(403);
                        }
                        if ($dato["estado"] != "Cerrada" && AutentificadorJWT::ObtenerPuesto($token) == "admin"){
                            $payload = json_encode(array("Mensaje" => "Solo los mozos pueden hacer eso"));
                                    $response->getBody()->write($payload);
                                    return $response->withStatus(403);
                        }
                    }
                }
                
                
                if ($error){
                    
                    $payload = json_encode(array("Mensaje" => $mensaje));
                    $response->getBody()->write($payload);
                    return $response->withStatus(403); 
                }
                
                    
            }

            
        }
        

        $response = $handler->handle($request);
        return $response;
        
          
    }
    

    public static function ValidarParamsProductos($request, $handler){
        $method = $request->getMethod();
        
        $response = new Response();

        if ($method == "POST" || $method == "PUT"){
            $dato = $request->getParsedBody();
            $error = false;
            $mensaje = "";
            
            
            if (!isset($dato)){
                $payload = json_encode(array("Mensaje" => "Faltan todos los parametros"));
                $response->getBody()->write($payload);
                return $response->withStatus(403);
                
            } else {
                if (!array_key_exists("descripcion", $dato)){
                    $mensaje .= "Falta el parametro de la descripcion. ";
                    $error = true;   
                } else {//si existe el parametro
                    if ($method == "POST"){//esto es porque en put es solo modificacion
                        if (producto::existeProducto($dato["descripcion"])){
                            $mensaje .= "Ya existe ese producto. ";
                            $error = true;
                        }
                    }
                    if ($dato["descripcion"] == ""){
                        $mensaje .= "Descripcion invalida. ";
                        $error = true;
                    }
                }
                if (!array_key_exists("precio", $dato)){
                    $mensaje .= "Falta el parametro de precio";
                    $error = true;  
                } else {//si existe el parametro
                    if ($dato["precio"] == "" || is_nan($dato["precio"])){
                        $mensaje .= "Precio invalido. ";
                        $error = true;
                    } 
                }
                if (!array_key_exists("stock", $dato)){
                    $mensaje .= "Falta el parametro de stock. ";
                    $error = true;  
                } else {//si existe el parametro
                    if ($dato["stock"] == "" || 
                        $dato["stock"] < 0){
                        $mensaje .= "Stock invalido. ";
                        $error = true;
                    } 
                }
                if (!array_key_exists("sector", $dato)){
                    $mensaje .= "Falta el parametro de sector. ";
                    $error = true;  
                } else {//si existe el parametro
                    if ($dato["sector"] == "" || (
                        $dato["sector"] != "Cocina" &&
                        $dato["sector"] != "Candy_Bar" &&
                        $dato["sector"] != "Barra_Choperas" &&
                        $dato["sector"] != "Barra_Tragos"

                    )){
                        $mensaje .= "Sector invalido. ";
                        $error = true;
                    } 
                }
                if (!array_key_exists("tiempo_estimado", $dato)){
                    $mensaje .= "Falta el parametro de tiempo estimado. ";
                    $error = true;  
                }
                
               
                
                
                
                    
            }

            
        }
        

        $response = $handler->handle($request);
        return $response;
        
          
    }

    public static function ValidarParamsOrdenes($request, $handler){
        $dato = $request->getParsedBody();
        $pedido_id = $dato["pedido_id"];
        $producto_id = $dato["producto_id"];

        $response = new Response();

        if (!pedido::existePedido_PorId_SinBorrados($pedido_id)){
                $payload = json_encode(array("Mensaje" => "No existe ese pedido"));
                $response->getBody()->write($payload);
                return $response->withStatus(403); 
        }

        if (!producto::existeProducto_PorIdSinBorrados($producto_id)){
            $payload = json_encode(array("Mensaje" => "No existe ese producto"));
            $response->getBody()->write($payload);
            return $response->withStatus(403); 
        }

        if (pedido::where("id", $pedido_id)->first()->estado == "Pagado"){
            $payload = json_encode(array("Mensaje" => "El producto ya fue pagado. No pueden agregarse mas ordenes"));
            $response->getBody()->write($payload);
            return $response->withStatus(403); 
        }

        $response = $handler->handle($request);
        return $response;

    
    }


    public static function ValidarParamsCargaPedidos($request, $handler){
        $response = new Response();
        $mensaje = "";
        $error = false;

        $dato = $request->getParsedBody();
        
        
        
        if (!isset($dato)){
            $payload = json_encode(array("Mensaje" => "Faltan todos los parametros"));
            $response->getBody()->write($payload);
            return $response->withStatus(403);
            
        } else {
            if (!array_key_exists("mesa_id", $dato)){
                $mensaje .= "Falta la id de la mesa. ";
                $error = true;   
            } else {//si existe el parametro
                if (!mesa::existeMesa($dato["mesa_id"])){
                    $mensaje .= "No existe esa mesa. ";
                    $error = true;
                }
                if ($dato["mesa_id"] == "" || is_nan($dato["mesa_id"])){
                    $mensaje .= "Id de mesa invalido. ";
                    $error = true;
                }
            }         
            
            //foto
            if (isset($_FILES["archivo"])){
            
            $destino = "archivos/" . $_FILES["archivo"]["name"];
            $tipoArchivo = pathinfo($destino, PATHINFO_EXTENSION);

            if ($_FILES["archivo"]["size"] > 5000000) {//5mb
                $mensaje .= "Superado el tamaño maximo de archivo (5mb) ";
                $error = true;
            }
            
            $esImagen = getimagesize($_FILES["archivo"]["tmp_name"]);

            if($esImagen == true) {
                if($tipoArchivo != "jpg" && $tipoArchivo != "jpeg" && $tipoArchivo != "png") {
                    $mensaje .= "Formato de la imagen invalido. Solo se admite .jpg, .jpeg y .png";
                    $error = true;
                }
            }
            else {
                    $mensaje .= "El archivo no es una imagen";
                    $error = true;
            
            }
            } else {
                $mensaje .= "No se subio la foto. ";
                $error = true;
            }

            if ($error){
                    
            $payload = json_encode(array("Mensaje" => $mensaje));
            $response->getBody()->write($payload);
            return $response->withStatus(403); 
        } 
        
        }

        

        $response = $handler->handle($request);
        return $response;

            
            
        
    }

    public function ValidarPedidoParaFacturar($request, $handler){
        $url = $request->getUri()->getPath();
        $pedido_id = explode('/', $url)[2];
        $response = new Response();
        
        if (pedido::existePedido_PorId($pedido_id)){
            if (pedido::where("id", $pedido_id)->first()->estado != "Servido" &&
            pedido::where("id", $pedido_id)->first()->estado != "Pagado"){
                $payload = json_encode(array("Mensaje" => "El pedido no esta servido aun"));
                $response->getBody()->write($payload);
                return $response->withStatus(403); 
            }
            $response = $handler->handle($request);
            return $response;
        }

        $payload = json_encode(array("Mensaje" => "No existe el pedido"));
        $response->getBody()->write($payload);
        return $response->withStatus(403); 

    }


    public function ValidarParamsEncuestas($request, $handler){
        $dato = $request->getParsedBody();
        $header = $request->getHeaderLine('Authorization');
        $token = trim(explode("Bearer", $header)[1]);
        $mesa_id = AutentificadorJWT_Clientes::ObtenerIdMesa($token);
        $pedido_id = AutentificadorJWT_Clientes::ObtenerIdPedido($token);
        $response = new Response();

        if (encuesta::YaHayEncuestaParaEsePedido($pedido_id)){
            $payload = json_encode(array("Mensaje" => "Ya se hizo la encuesta"));
            $response->getBody()->write($payload);
            return $response->withStatus(403);
        }

        if (mesa::where("id", $mesa_id)->first()->estado != "Con cliente pagando" &&
            mesa::where("id", $mesa_id)->first()->estado != "Cerrada"){
            $payload = json_encode(array("Mensaje" => "Aun no puede realizarse la encuesta"));
            $response->getBody()->write($payload);
            return $response->withStatus(403);
        }

        if (!isset($dato)){
            $payload = json_encode(array("Mensaje" => "Faltan todos los parametros"));
            $response->getBody()->write($payload);
            return $response->withStatus(403);
        }
        
        if (!array_key_exists("calificacion_mesa", $dato) ||
            !array_key_exists("calificacion_restaurante", $dato) ||
            !array_key_exists("calificacion_mozo", $dato) ||
            !array_key_exists("calificacion_cocinero", $dato) ||
            !array_key_exists("calificacion_cervecero", $dato) ||
            !array_key_exists("calificacion_bartender", $dato)){

            $payload = json_encode(array("Mensaje" => "Falta algun parametro"));
            $response->getBody()->write($payload);
            return $response->withStatus(403);
        }

        if (($dato["calificacion_mesa"] < 1 || $dato["calificacion_mesa"] > 10) ||
            ($dato["calificacion_restaurante"] < 1 || $dato["calificacion_restaurante"] > 10) ||
            ($dato["calificacion_mozo"] < 1 || $dato["calificacion_mozo"] > 10) ||
            (($dato["calificacion_cocinero"] < 1 || $dato["calificacion_cocinero"] > 10) && pedido::PedidoTieneCocinero($pedido_id)) ||
            (($dato["calificacion_cervecero"] < 1 || $dato["calificacion_cervecero"] > 10) && pedido::PedidoTieneCervecero($pedido_id)) ||
            (($dato["calificacion_bartender"] < 1 || $dato["calificacion_bartender"] > 10) && pedido::PedidoTieneBartender($pedido_id))){
                
                $payload = json_encode(array("Mensaje" => "Error en alguna calificacion"));
                $response->getBody()->write($payload);
                return $response->withStatus(403);
            

        }

        if (strlen($dato["comentario"]) < 10){
            $payload = json_encode(array("Mensaje" => "Comentario demasiado corto (Por lo menos 10 caracteres)"));
            $response->getBody()->write($payload);
            return $response->withStatus(403);
        }

        if (strlen($dato["comentario"]) > 66){
            $payload = json_encode(array("Mensaje" => "Comentario demasiado largo (hasta 66 caracteres)"));
            $response->getBody()->write($payload);
            return $response->withStatus(403);
        }


        $response = $handler->handle($request);
        return $response;
    }

    
   
    
}
?>