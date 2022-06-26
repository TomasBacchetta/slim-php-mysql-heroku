<?php
/*
BACCHETTA, TOMÁS
TP PROGRAMACION 3 "LA COMANDA"
SPRINT 1
ALTA
VISUALIZACION
BASE DE DATOS

*/
use \App\Models\admin as admin;
use GuzzleHttp\Psr7\Stream;


class AdminController {


    public function CargarUno($request, $response, $args){
        $param = $request->getParsedBody();

        $nombre = $param["nombre"];
        $clave = $param["clave"];
     
        

        $adminNuevo = new admin();
        $adminNuevo->nombre = $nombre;
        $adminNuevo->clave = $clave;
        
        $adminNuevo->save();

        $payload = json_encode(array("mensaje" => "Administrador creado con éxito"));
        $response->getBody()->write($payload);

        return $response->withHeader("Content-Type", "application/json");

        
    }

    public function TraerUno($request, $response, $args){
        $id = $args["id"];
        $admin = admin::where('id', $id)->first();
        $payload = json_encode($admin);

        $response->getBody()->write($payload);

        return $response->withHeader("Content-Type", "application/json");
        
    }

    public function TraerTodos($request, $response, $args){
        $admins = admin::all();
        $payload = json_encode(array("listaAdmin" => $admins));

        $response->getBody()->write($payload);

        return $response->withHeader("Content-Type", "application/json");

    }

    public function ModificarUno($request, $response, $args){
        $param = $request->getParsedBody();

        $id = $args["id"];

        $nombre = $param["nombre"];
        $clave = $param["clave"];
        

        $adminModificado = admin::where('id', $id)->first();
        $adminModificado->nombre = $nombre;
        $adminModificado->clave = $clave;

        $adminModificado->save();

        $payload = json_encode(array("mensaje" => "Admin modificado exitosamente"));

        $response->getBody()->write($payload);

        return $response->withHeader("Content-Type", "application/json");

        
    }

    public function BorrarUno($request, $response, $args){

        $id = $args["id"];

       

        $adminABorrar = admin::where("id", $id)->first();
        
        $adminABorrar->delete();

        $payload = json_encode(array("mensaje"=> "Admin eliminado exitosamente"));

        $response->getBody()->write($payload);

        return $response->withHeader("Content-Type", "application/json");

    }

    public function CrearCsv($request, $response, $args){

        
        $data = admin::withTrashed()->get();
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
                        ->withHeader('Content-Disposition', 'attachment; filename="admins.csv"')
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
            $admin = new admin();
            $array = explode(';', $eObj[0]);
            if (!admin::existeEncuesta_PorId($array[0])){
                admin::where("id", $array[0])->forceDelete();//esto es por si hay un id con softdelete, la prioridad la tiene el csv
                $admin->id = $array[0];
                $admin->nombre = $array[1];
                $admin->clave = $array[2];
                $admin->created_at = $array[3];
                $admin->updated_at = $array[4];

                
            } else {
                
                $admin = admin::where("id", $array[0])->withTrashed()->first();
                if ((!isset($admin->deleted_at) || $admin->deleted_at != '') &&
                    ($array[5] == null || $array[5] == '')){
                    $admin->deleted_at = null;
                }

                
            }

            $admin->save();
            

        }
        

        $payload = json_encode(array("mensaje" => "Csv importado con exito"));

        $response->getBody()->write($payload);

        return $response->withHeader("Content-Type", "application/json");
    }

    

}



?>