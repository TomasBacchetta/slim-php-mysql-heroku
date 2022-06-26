<?php


use \App\Models\registro as registro;
use \App\Models\empleado as empleado;


class RegistroController{

    

    public function TraerUno($request, $response, $args){
        $id = $args["id"];
        
        $log = registro::where("id", $id);
        $payload = json_encode($log);

        $response->getBody()->write($payload);

        return $response->withHeader("Content-Type", "application/json");
        
    }

    public function TraerTodos($request, $response, $args){
        $logs = registro::orderBy("id", "desc")->get();
        $payload = json_encode(array("listaLogs" => $logs));

        $response->getBody()->write($payload);

        return $response->withHeader("Content-Type", "application/json");

    }

    public function TraerPorEmpleado($request, $response, $args){
        $nombre = $args["nombre"];

        $logs = registro::where("empleado", empleado::where("nombre", $nombre)->first());
        $payload = json_encode(array("listaLogs" => $logs));

        $response->getBody()->write($payload);

        return $response->withHeader("Content-Type", "application/json");

    }
}



?>