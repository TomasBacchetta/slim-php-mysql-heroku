<?php
/*
BACCHETTA, TOMÁS
TP PROGRAMACION 3 "LA COMANDA"
SPRINT 1
ALTA
VISUALIZACION
BASE DE DATOS

*/
use \App\Models\empleado as empleado;
use \App\Models\registro as registro;
use GuzzleHttp\Psr7\Stream;


class EmpleadoController {


    public function CargarUno($request, $response, $args){
        $param = $request->getParsedBody();

        $nombre = $param["nombre"];
        $clave = $param["clave"];
        $puesto = $param["puesto"];
        

        $empleadoNuevo = new empleado();
        $empleadoNuevo->nombre = $nombre;
        $empleadoNuevo->clave = $clave;
        $empleadoNuevo->puesto = $puesto;
        $empleadoNuevo->estado = "Activo";
        $empleadoNuevo->puntaje = 0;
        
        $empleadoNuevo->save();

        $payload = json_encode(array("mensaje" => $puesto . " creado con éxito"));
        $response->getBody()->write($payload);

        return $response->withHeader("Content-Type", "application/json");

        
    }

    public function TraerUno($request, $response, $args){
        $id = $args["id"];
        //$empleado = empleado::where('id', $id)->first();
        $empleado = empleado::ObtenerEmpleadosDeUnPedido($id);
        $payload = json_encode($empleado);

        $response->getBody()->write($payload);

        return $response->withHeader("Content-Type", "application/json");
        
    }

    public function TraerTodos($request, $response, $args){
        $empleados = empleado::all();
        $payload = json_encode(array("listaEmpleado" => $empleados));

        $response->getBody()->write($payload);

        return $response->withHeader("Content-Type", "application/json");

    }

    public function ModificarUno($request, $response, $args){
        $param = $request->getParsedBody();

        $id = $param["id"];
        $nombre = $param["nombre"];
        $clave = $param["clave"];
        $puesto = $param["puesto"];
        $dni = $param["dni"];

        $empleadoModificado = empleado::where('id', $id)->first();
        $empleadoModificado->nombre = $nombre;
        $empleadoModificado->clave = $clave;
        $empleadoModificado->puesto = $puesto;
        $empleadoModificado->dni = $dni;

        $empleadoModificado->update();

        $payload = json_encode(array("mensaje" => "Empleado modificado exitosamente"));

        $response->getBody()->write($payload);

        return $response->withHeader("Content-Type", "application/json");

        
    }

    public function CambiarEstado($request, $response, $args){
        $id = $args["id"];
        $param = $request->getParsedBody();

        
        $empleado = empleado::where("id", $id)->first();
        $empleado->estado = $param["estado"];
        $empleado->save();

        $payload = json_encode(array("mensaje"=> "Empleado cambiado de estado a: " . $param["estado"]));
        registro::CrearRegistro($id, "Estado pasado a " . $param["estado"]);
        $response->getBody()->write($payload);

        return $response->withHeader("Content-Type", "application/json");

    }

    public function BorrarUno($request, $response, $args){

        $id = $args["id"];


        $empleadoABorrar = empleado::where("id", $id);
        $empleadoABorrar->estado = "Inactivo";
        $empleadoABorrar->delete();

        $payload = json_encode(array("mensaje"=> "Empleado eliminado exitosamente"));

        $response->getBody()->write($payload);

        return $response->withHeader("Content-Type", "application/json");

    }

    public function CrearCsv($request, $response, $args){

        
        $data = empleado::withTrashed()->get();
        $csv = fopen('php://memory', 'w');
        
        foreach ($data as $row) {
	        fputcsv($csv, $row->toArray(), ';');
        }

        $stream = new Stream($csv); // create a stream instance for the response body
        rewind($csv);

        return $response->withHeader('Content-Type', 'application/force-download')
                        ->withHeader('Content-Type', 'application/octet-stream')
                        ->withHeader('Content-Type', 'application/download')
                        ->withHeader('Content-Description', 'File Transfer')
                        ->withHeader('Content-Transfer-Encoding', 'binary')
                        ->withHeader('Content-Disposition', 'attachment; filename="empleados.csv"')
                        ->withHeader('Expires', '0')
                        ->withHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0')
                        ->withHeader('Pragma', 'public')
                        ->withBody($stream); // all stream contents will be sent to the response
        
       
    }


    public function ImportarCsv($request, $response, $args){
        $tmpName = $_FILES['csv']['tmp_name'];
        $csvAsArray = array_map('str_getcsv', file($tmpName));
        var_dump($csvAsArray);
        
        foreach ($csvAsArray as $eObj){
            $empleado = new empleado();
            $array = explode(';', $eObj[0]);
            if (!empleado::existeEmpleado_PorId($array[0])){
                $empleado->id = $array[0];
                $empleado->nombre = $array[1];
                $empleado->clave = $array[2];
                $empleado->puesto = $array[3];
                $empleado->puntaje = $array[4];
                $empleado->estado = $array[5];
                $empleado->created_at = $array[6];
                $empleado->updated_at = $array[7];
                

            } else {
                
                $empleado = empleado::where("id", $array[0])->withTrashed()->first();
                if ((!isset($empleado->deleted_at) || $empleado->deleted_at != '') &&
                    ($array[8] == null || $array[8] == '')){
                    $empleado->deleted_at = null;
                }

                
            }
            
            $empleado->save();
        }
        

        $payload = json_encode(array("mensaje" => "Csv importado con exito"));

        $response->getBody()->write($payload);

        return $response->withHeader("Content-Type", "application/json");
    }


    

    

}



?>