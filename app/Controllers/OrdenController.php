<?php
/*
BACCHETTA, TOMÁS
TP PROGRAMACION 3 "LA COMANDA"
SPRINT 1
ALTA
VISUALIZACION
BASE DE DATOS

*/
use \App\Models\orden as orden;
use \App\Models\producto as producto;
use \App\Models\pedido as pedido;
use GuzzleHttp\Psr7\Stream;
use \App\Models\registro as registro;
use Illuminate\Support\Facades\Date;

require_once "./Models/time.php";

class OrdenController {


    public function CargarUno($request, $response, $args){
        $param = $request->getParsedBody();
        $header = $request->getHeaderLine('Authorization');
        $token = trim(explode("Bearer", $header)[1]);

        $ordenNueva = new Orden();
        $ordenNueva->pedido_id = $param["pedido_id"];
        $ordenNueva->producto_id = $param["producto_id"];
        $ordenNueva->cantidad = $param["cantidad"];
        
       
        $producto = producto::where("id", $ordenNueva->producto_id)->first();

        $ordenNueva->descripcion = $producto->descripcion;
        $ordenNueva->subtotal = $producto->precio * $ordenNueva->cantidad;
        $ordenNueva->tiempo_estimado = $producto->tiempo_estimado;
        $ordenNueva->estado = "Abierta";

        registro::CrearRegistro(AutentificadorJWT::ObtenerId($token), "Agrego orden a un pedido");

        $ordenNueva->save();
        

        //actualizando producto

        $producto->stock -= $ordenNueva->cantidad;

        $producto->save();
    
        
        //actualizando pedido (la orden nueva condiciona al pedido)
        
        $pedidoActualizado = pedido::where("id", $ordenNueva->pedido_id)->first();
        $pedidoActualizado->total = orden::where("pedido_id", $ordenNueva->pedido_id)->sum("subtotal");
        $pedidoActualizado->tiempo_estimado = orden::where("pedido_id", $ordenNueva->pedido_id)->max("tiempo_estimado");
        $pedidoActualizado->estado = "Con orden";
        
        $pedidoActualizado->save();


        $payload = json_encode(array("mensaje" => "Orden cargada con éxito en pedido " . $ordenNueva->num_pedido));
        $response->getBody()->write($payload);

        return $response->withHeader("Content-Type", "application/json");

        
    }

    public function TraerUno($request, $response, $args){
        $id = $args["id"];
        $orden = orden::where("id", $id)->first();
        $payload = json_encode($orden);

        $response->getBody()->write($payload);

        return $response->withHeader("Content-Type", "application/json");
        
    }

    public function TraerOrdenesPorPedido_Id($request, $response, $args){
        $pedido_id = $args["pedido_id"];
        $ordenes = orden::where("pedido_id", $pedido_id);
        $payload = json_encode(array("listaOrdenes" => $ordenes));

        $response->getBody()->write($payload);

        return $response->withHeader("Content-Type", "application/json");

    }

    public function TraerTodos($request, $response, $args){
        $ordenes = orden::orderBy("pedido_id", "desc");
        $payload = json_encode(array("listaOrdenes" => $ordenes));

        $response->getBody()->write($payload);

        return $response->withHeader("Content-Type", "application/json");

    }

    public function ModificarUno($request, $response, $args){
        $param = $request->getParsedBody();
        

        $ordenModificada = new Orden();
        $ordenModificada->pedido_id = $param["pedido_id"];
        $ordenModificada->producto_id = $param["producto_id"];

        //reincorporando stock al producto
        $producto = producto::where("id", $ordenModificada->producto_id)->first();
        $producto->stock += $ordenModificada->cantidad;

        $ordenModificada->cantidad = $param["cantidad"];
        
        
       
        $ordenModificada->descripcion = $producto->descripcion;
        $ordenModificada->subtotal = $producto->precio * $ordenModificada->cantidad;
        $ordenModificada->tiempo_estimado = $producto->tiempo_estimado;
        $ordenModificada->estado = "Iniciada";
        
        $ordenModificada->save();

        //actualizando producto

        $producto->stock -= $ordenModificada->cantidad;

        $producto->save();
    
        
        //actualizando pedido (la orden modificada condiciona al pedido)
        
        $pedidoActualizado = pedido::where("id", $ordenModificada->pedido_id);
        $pedidoActualizado->total = orden::where("id", $ordenModificada->pedido_id)->sum("subtotal");
        $pedidoActualizado->tiempo_estimado = orden::where("id", $ordenModificada->pedido_id)->max("tiempo_estimado");
        $pedidoActualizado->estado = "Con orden";

        $pedidoActualizado->save();

        $payload = json_encode(array("mensaje" => "Orden modificada exitosamente"));

        $response->getBody()->write($payload);

        return $response->withHeader("Content-Type", "application/json");

        
    }

    public function CambiarEstado($request, $response, $args){
        $param = $request->getParsedBody();
        $id = $args["id"];
        $header = $request->getHeaderLine('Authorization');
        $token = trim(explode("Bearer", $header)[1]);
        $ordenModificada = orden::where("id", $id)->first();
        $ordenModificada->estado = $param["estado"];
        

        $pedido_id = orden::where("id", $id)->value("pedido_id");

        if ($ordenModificada->estado == "En preparacion"){
            //el empleado idoneo toma una orden Abierta y la cambia a "en preparancion
            //si esta fue la ultima orden que se necesitaba preparar para el pedido
            //vinculado, el pedido pasa a estar En Preparacion
            //ademas, la orden pasa a estar vinculada al empleado

            $ordenModificada->empleado_id = AutentificadorJWT::ObtenerId($token);

            ///esto porque poner el tiempo estimado de la orden manualmente es opcional
            if (array_key_exists("tiempo_estimado", $param) && $param["tiempo_estimado"] != null && $param["tiempo_estimado"] != ""){
                $ordenModificada->tiempo_estimado = $param["tiempo_estimado"];
                $pedido = pedido::where("id", $pedido_id)->first();
                $pedido->tiempo_estimado = orden::where("pedido_id", $ordenModificada->pedido_id)->max("tiempo_estimado");
            }
            $ordenModificada->save();
            $ordenes = orden::ObtenerOrdenesAbiertasPorPedido($pedido_id);
            if (!$ordenes){//si todas las ordenes vinculadas al pedido ya se encuentran en preparacion 
                $pedido = pedido::where("id", $pedido_id)->first();
                $pedido->tiempo_estimado = orden::where("pedido_id", $ordenModificada->pedido_id)->max("tiempo_estimado");
                registro::CrearRegistro(AutentificadorJWT::ObtenerId($token), "Empezo a preparar la orden n°" . $id . ", que es la ultima del pedido n°" . pedido::where("id", $pedido_id)->first()->id);
                $pedido->estado = "En preparacion";
                $pedido->save();
            }
            
        }
        

        if ($ordenModificada->estado == "Listo para servir"){
            //el empleado idoneo toma una orden En Preparacion y la cambia a Preparada
            //si esta fue la ultima orden que se necesitaba preparar para el pedido
            //vinculado, el pedido pasa a estar En Preparacion
            registro::CrearRegistro(AutentificadorJWT::ObtenerId($token), "Termino la orden n°" . $id . "del pedido n°" . pedido::where("id", $pedido_id)->first()->id);
            $ordenes = orden::ObtenerOrdenesAbiertasPorPedido($pedido_id);
            if (!$ordenes){//si ya no hay ordenes abiertas
                $pedido = pedido::where("id", $pedido_id)->first();
                registro::CrearRegistro(AutentificadorJWT::ObtenerId($token), "Termino la orden n°" . $id . ", que es la ultima que faltaba del pedido n°" . pedido::where("id", $pedido_id)->first()->id);
                $pedido->estado = "Listo para servir";
                if (Time::DiferenciaEntreTimestamps($ordenModificada->updated_at, Date::now()->subHours(3)) > Time::StringToSeconds($pedido->tiempo_estimado)){
                    $pedido->con_retraso = "SI";
                }
                $pedido->save();
            }

            $ordenModificada->save();
            
        }

        
        
        

        $payload = json_encode(array("mensaje" => "Estado de la orden cambiado a " . $param["estado"]));

        $response->getBody()->write($payload);

        return $response->withHeader("Content-Type", "application/json");

        
    }


    public function BorrarUno($request, $response, $args){
        

        $id = $args["id"];
        
        $ordenABorrar = orden::where("id", $id)->first();
        

        //reincorporando stock al producto
        $producto = producto::where("id", $ordenABorrar->producto_id)->first();
        $producto->stock += $ordenABorrar->cantidad;

        $producto->save();
        
        
       
    
        
        //actualizando pedido (la orden borrada condiciona al pedido)
        
        $pedidoActualizado = pedido::where("id", $ordenABorrar->pedido_id);
        $pedidoActualizado->total = orden::where("id", $ordenABorrar->pedido_id)->sum("subtotal");
        $pedidoActualizado->tiempo_estimado = orden::where("id", $ordenABorrar->pedido_id)->max("tiempo_estimado");
        $pedidoActualizado->estado = "Con orden";

        $pedidoActualizado->save();

       
        $ordenABorrar->delete();
       

        $payload = json_encode(array("mensaje"=> "Orden eliminada exitosamente"));

        $response->getBody()->write($payload);

        return $response->withHeader("Content-Type", "application/json");

    }

    public function CrearCsv($request, $response, $args){

        
        $data = orden::withTrashed()->get();
        $csv = fopen('php://memory', 'w');
        
        foreach ($data as $row) {
	        fputcsv($csv, $row->toArray(), ';');
        }

        $stream = new Stream($csv); 
        rewind($csv);

        return $response->withHeader('Content-Type', 'application/force-download')
                        ->withHeader('Content-Type', 'application/octet-stream')
                        ->withHeader('Content-Type', 'application/download')
                        ->withHeader('Content-Description', 'File Transfer')
                        ->withHeader('Content-Transfer-Encoding', 'binary')
                        ->withHeader('Content-Disposition', 'attachment; filename="ordenes.csv"')
                        ->withHeader('Expires', '0')
                        ->withHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0')
                        ->withHeader('Pragma', 'public')
                        ->withBody($stream);
        
        
    }


    public function ImportarCsv($request, $response, $args){
        $tmpName = $_FILES['csv']['tmp_name'];
        $csvAsArray = array_map('str_getcsv', file($tmpName));
        var_dump($csvAsArray);
        
        foreach ($csvAsArray as $eObj){
            $orden = new orden();
            $array = explode(';', $eObj[0]);
            if (!orden::existeOrden_PorId($array[0])){
                orden::where("id", $array[0])->forceDelete();//esto es por si hay un id con softdelete, la prioridad la tiene el csv
                $orden->id = $array[0];
                $orden->pedido_id = $array[1];
                $orden->producto_id = $array[2];
                $orden->empleado_id = $array[3];
                $orden->descripcion = $array[4];
                $orden->cantidad = $array[5];
                $orden->subtotal = $array[6];
                $orden->tiempo_estimado = $array[7];
                $orden->estado = $array[8];
                $orden->created_at = $array[9];
                $orden->updated_at = $array[10];

                $orden->save();
            }
            

        }
        

        $payload = json_encode(array("mensaje" => "Csv importado con exito"));

        $response->getBody()->write($payload);

        return $response->withHeader("Content-Type", "application/json");
    }

   
    

    

}



?>