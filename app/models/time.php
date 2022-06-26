<?php

use Illuminate\Support\Facades\Date;

///clase propia para trabajar con los timestamps de laravel
class Time{

    public static function StringToSeconds($strTime){
        $parseado = explode(':', $strTime);
        $horas = $parseado[0];
        $minutos = $parseado[1];
        $segundos = $parseado[2];

        return $horas * 3600 + $minutos * 60 + $segundos;

    }

    public static function DiferenciaEntreTimestamps($tiempoIncial, $tiempoFinal){
        $ts1 = strtotime($tiempoIncial);
        $ts2 = strtotime($tiempoFinal);
        
       
        $diferencia = $ts2 - $ts1; 
        return $diferencia;

    }
    
    //dd-mm-yyyy
    public static function StrFechaToTimestamp($strFecha){
        $parseado = explode('-', $strFecha);
        $dia = $parseado[0];
        $mes = $parseado[1];
        $año = $parseado[2];
        //2022-06-26 03:07:44.000000
        
        $fecha = $año . "-" . $mes . "-" . $dia . " 00:00:00.000000";
        
        return $fecha;


    }
}


?>