<?php


use GuzzleHttp\Psr7\Stream;

    class EstadisticasController{

        public static function MostrarEstadisticaGeneral($request, $response, $args){
            estadisticas::ImprimirEstadisticasGenerales()->Output(date("y-m-d") .'.pdf', 'I');
            return $response->withHeader("Content-Type", "application/pdf");
        }

        public static function MostrarFacturadoEntreDosFechas($request, $response, $args){
            $params = $request->getQueryParams();

        
            //$desde = $params["desde"] . " 00:00:00";
            //$hasta = $params["hasta"] . " 00:00:00";
            $desde = $params["desde"];
            $hasta = $params["hasta"];
            
            $facturado = estadisticas::ObtenerFacturadoEntreDosFechas($desde, $hasta);

            $response->getBody()->write(json_encode(array("mensaje" => "Se facturaron $" . $facturado ." entre las fechas " . $desde . " y " . $hasta )));
            return $response->withHeader("Content-Type", "application/json");
        }

    }



?>