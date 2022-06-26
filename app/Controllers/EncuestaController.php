<?php
/*
BACCHETTA, TOMÁS
TP PROGRAMACION 3 "LA COMANDA"
SPRINT 1
ALTA
VISUALIZACION
BASE DE DATOS

*/
use \App\Models\encuesta as encuesta;
use \App\Models\empleado as empleado;
use GuzzleHttp\Psr7\Stream;


class EncuestaController {


    public function CargarUno($request, $response, $args){
        $param = $request->getParsedBody();
        $header = $request->getHeaderLine('Authorization');
        $token = trim(explode("Bearer", $header)[1]);

        $pedido_id = AutentificadorJWT_Clientes::ObtenerIdPedido($token);
        $calificacion_mesa = $param["calificacion_mesa"];
        $calificacion_restaurante = $param["calificacion_restaurante"];
        $calificacion_mozo = $param["calificacion_mozo"];
        $calificacion_cocinero = $param["calificacion_cocinero"];
        $calificacion_cervecero = $param["calificacion_cervecero"];
        $calificacion_bartender = $param["calificacion_bartender"];
        $comentario = $param["comentario"];
        
        

        $encuestaNueva = new encuesta();
        $encuestaNueva->pedido_id = $pedido_id;
        $encuestaNueva->calificacion_mesa = $calificacion_mesa;
        $encuestaNueva->calificacion_restaurante = $calificacion_restaurante;
        $encuestaNueva->calificacion_mozo = $calificacion_mozo;
        $encuestaNueva->calificacion_cocinero = $calificacion_cocinero;
        $encuestaNueva->calificacion_cervecero = $calificacion_cervecero;
        $encuestaNueva->calificacion_bartender = $calificacion_bartender;
        $encuestaNueva->comentario = $comentario;
   
        
        $encuestaNueva->save();

        empleado::actualizarPuntajeEmpleadosDeUnPedido($pedido_id);
        //actualizar puntaje mesa

        $payload = json_encode(array("mensaje" => "Encuesta generada. Gracias por su participacion"));
        $response->getBody()->write($payload);

        return $response->withHeader("Content-Type", "application/json");

        
    }

    public function TraerUno($request, $response, $args){
        $id = $args["id"];
        $encuesta = encuesta::where('id', $id)->first();
        $payload = json_encode($encuesta);

        $response->getBody()->write($payload);

        return $response->withHeader("Content-Type", "application/json");
        
    }

    public function TraerTodos($request, $response, $args){
        $encuestas = encuesta::all();
        $payload = json_encode(array("listaEncuestas" => $encuestas));

        $response->getBody()->write($payload);

        return $response->withHeader("Content-Type", "application/json");

    }

    public function TraerMejoresTres($request, $response, $args){
        $encuestas = encuesta::orderBy("calificacion_restaurante", "DESC")->limit(3)->get();
        $payload = json_encode(array("listaEncuestas" => $encuestas));

        $response->getBody()->write($payload);

        return $response->withHeader("Content-Type", "application/json");

    }

    public function TraerPeoresTres($request, $response, $args){
        $encuestas = encuesta::orderBy("calificacion_restaurante", "ASC")->limit(3)->get();
        $payload = json_encode(array("listaEncuestas" => $encuestas));

        $response->getBody()->write($payload);

        return $response->withHeader("Content-Type", "application/json");

    }

    

    public function BorrarUno($request, $response, $args){

        $id = $args["id"];

        $empleadoABorrar = encuesta::where("id", $id);
        
        $empleadoABorrar->delete();

        $payload = json_encode(array("mensaje"=> "Usuario eliminado exitosamente"));

        $response->getBody()->write($payload);

        return $response->withHeader("Content-Type", "application/json");

    }

    public function CrearCsv($request, $response, $args){

        
        $data = encuesta::withTrashed()->get();
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
                        ->withHeader('Content-Disposition', 'attachment; filename="encuestas.csv"')
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
            $encuesta = new encuesta();
            $array = explode(';', $eObj[0]);
            if (!encuesta::existeEncuesta_PorId($array[0])){
                encuesta::where("id", $array[0])->forceDelete();//esto es por si hay un id con softdelete, la prioridad la tiene el csv
                $encuesta->id = $array[0];
                $encuesta->pedido_id = $array[1];
                $encuesta->calificacion_mesa = $array[2];
                $encuesta->calificacion_restaurante = $array[3];
                $encuesta->calificacion_mozo = $array[4];
                $encuesta->calificacion_cocinero = $array[5];
                $encuesta->calificacion_cervecero = $array[6];
                $encuesta->calificacion_bartender = $array[7];
                $encuesta->comentario = $array[8];
                $encuesta->created_at = $array[9];
                $encuesta->updated_at = $array[10];
                

                
            } else {
                
                $encuesta = encuesta::where("id", $array[0])->withTrashed()->first();
                if ((!isset($encuesta->deleted_at) || $encuesta->deleted_at != '') &&
                    ($array[11] == null || $array[11] == '')){
                    $encuesta->deleted_at = null;
                }

                
            }

                $encuesta->save();
            

        }
        

        $payload = json_encode(array("mensaje" => "Csv importado con exito"));

        $response->getBody()->write($payload);

        return $response->withHeader("Content-Type", "application/json");
    }

    

}



?>