<?php
/*
BACCHETTA, TOMÁS
TP PROGRAMACION 3 "LA COMANDA"
SPRINT 1
ALTA
VISUALIZACION
BASE DE DATOS

*/
use \App\Models\mesa as mesa;
use \App\Models\pedido as pedido;
use \App\Models\registro as registro;

use GuzzleHttp\Psr7\Stream;


class MesaController {


    public function CargarUno($request, $response, $args){
        
        $mesaNueva = new mesa();

        $mesaNueva->estado = "Cerrada";
        $mesaNueva->puntaje = 0;
        
        $mesaNueva->save();

        $payload = json_encode(array("mensaje" => "Mesa cargada con éxito"));
        $response->getBody()->write($payload);

        return $response->withHeader("Content-Type", "application/json");

        
    }

    public function TraerUno($request, $response, $args){
        $id = $args["id"];
        
        $mesa = mesa::where("id", $id)->first();
        $payload = json_encode($mesa);

        $response->getBody()->write($payload);

        return $response->withHeader("Content-Type", "application/json");
        
    }

    public function TraerTodos($request, $response, $args){
        $mesas = mesa::all();
        $payload = json_encode(array("listaMesas" => $mesas));

        $response->getBody()->write($payload);

        return $response->withHeader("Content-Type", "application/json");

    }

    public function CambiarEstado($request, $response, $args){
        $param = $request->getParsedBody();
        $header = $request->getHeaderLine('Authorization');
        $token = trim(explode("Bearer", $header)[1]);
        $id = $args["id"];
        $estado = $param["estado"];
        $mesaACerrar = mesa::where("id", $id)->first();
        $mesaACerrar->estado = $estado;
        
        $mesaACerrar->save();

        if ($estado == "Con cliente comiendo"){
            registro::CrearRegistro(AutentificadorJWT::ObtenerId($token), "Sirvio el pedido");
            $pedido = pedido::where("mesa_id", $id)->where("estado", "Listo para servir")->first();
            $pedido->estado = "Servido";
            $pedido->save();
            
        }

        if ($estado == "Con cliente pagando"){
            registro::CrearRegistro(AutentificadorJWT::ObtenerId($token), "Cobro el pedido");
        }

        if ($estado == "Cerrada"){
            $pedido = pedido::where("mesa_id", $id)->where("estado", "Servido")->first();
            if (isset($pedido)){
                $pedido->estado = "Pagado";
                $pedido->save();
            }
           
        }
        

        $payload = json_encode(array("Mensaje" => "Estado de mesado pasado a: " . $estado));

        $response->getBody()->write($payload);

        return $response->withHeader("Content-Type", "application/json");

    }


    public function BorrarUno($request, $response, $args){

        $id = $args["id"];
        

        $mesaABorrar = mesa::where("id", $id)->first();
        

        $mesaABorrar->delete();

        $payload = json_encode(array("mensaje"=> "Mesa eliminada exitosamente"));

        $response->getBody()->write($payload);

        return $response->withHeader("Content-Type", "application/json");

    }

    public function CrearCsv($request, $response, $args){

        
        $data = mesa::withTrashed()->get();
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
                        ->withHeader('Content-Disposition', 'attachment; filename="mesas.csv"')
                        ->withHeader('Expires', '0')
                        ->withHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0')
                        ->withHeader('Pragma', 'public')
                        ->withBody($stream);
        

        
        
    }


    public function ImportarCsv($request, $response, $args){
        $tmpName = $_FILES['csv']['tmp_name'];
        $csvAsArray = array_map('str_getcsv', file($tmpName));
        
        foreach ($csvAsArray as $eObj){
            $mesa = new mesa();
            $array = explode(';', $eObj[0]);
            if (!mesa::existeMesa_PorId($array[0])){
                $mesa->id = $array[0];
                $mesa->estado = $array[1];
                $mesa->puntaje = $array[2];
                $mesa->created_at = $array[3];
                $mesa->updated_at = $array[4];
                

                
            } else {
                
                $mesa = mesa::where("id", $array[0])->withTrashed()->first();
                if ((!isset($mesa->deleted_at) || $mesa->deleted_at != '') &&
                    ($array[5] == null || $array[5] == '')){
                    $mesa->deleted_at = null;
                    
                }

                
            }
            
            $mesa->save();
        }
        

        $payload = json_encode(array("mensaje" => "Csv importado con exito"));

        $response->getBody()->write($payload);

        return $response->withHeader("Content-Type", "application/json");
    }

    

    

}



?>