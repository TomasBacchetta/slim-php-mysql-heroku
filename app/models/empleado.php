<?php
/*
BACCHETTA, TOMÁS
TP PROGRAMACION 3 "LA COMANDA"
SPRINT 1
ALTA
VISUALIZACION
BASE DE DATOS


*/

namespace App\Models;


use \Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Capsule\Manager as Capsule;

class empleado extends Model{

    public $incrementing = true;
    
    protected $fillable = [
        'nombre',
        'clave',
        'puesto',
        'puntaje',
        'estado'
    ];

    use SoftDeletes;

    public static function verificarEmpleado($nombre, $clave){
        $empleado = empleado::where("nombre", "=", $nombre)->where("clave", "=", $clave)->first();

        if (isset($empleado)){
            return true;
        } else {
            return false;
        }
    }

    public static function existeEmpleado($nombre){
        $empleado = empleado::where("nombre", "=", $nombre)->first();
        if (isset($empleado)){
            return true;
        } else {
            return false;
        }
        
    }

    public static function existeMozo($id){
        $empleado = empleado::where("id", $id)->where("puesto", "Mozo")->first();
        if (isset($empleado)){
            return true;
        } else {
            return false;
        }
        
    }

    public static function existeEmpleado_PorId($id){
        $empleado = empleado::where("id", "=", $id)->withTrashed()->first();
        if (isset($empleado)){
            return true;
        } else {
            return false;
        }
        
    }

    public static function existeEmpleado_PorIdSinBorrar($id){
        $empleado = empleado::where("id", "=", $id)->first();
        if (isset($empleado)){
            return true;
        } else {
            return false;
        }
        
    }

    public static function ObtenerEmpleadosDeUnPedido($pedido_id){
        $empleados = Capsule::table('empleados')
        ->join('ordens', 'ordens.empleado_id', '=', 'empleados.id')
        ->join('pedidos', 'pedidos.id', '=', 'ordens.pedido_id')
        ->select('empleados.*')
        ->where('pedidos.id', $pedido_id)->where("empleados.deleted_at", null)->orWhere("empleados.deleted_at", "!=", null)->get();

        return $empleados->unique('id');

    }

    public static function ObtenerMozoDeUnPedido($pedido_id){
        $mozo_id = pedido::where("id", $pedido_id)->first()->mozo_id;
        $mozo = empleado::where("id", $mozo_id)->withTrashed()->first();
        return $mozo;
    }

    public static function actualizarPuntajeEmpleadosDeUnPedido($pedido_id){

        $empleados = empleado::ObtenerEmpleadosDeUnPedido($pedido_id);
        foreach ($empleados as $eEmpleado){
            $sumatoriaPuntajes = 0;
            switch ($eEmpleado->puesto){
                case "Cocinero":
                    $tipoDePuntaje = "calificacion_cocinero";
                    break;
                case "Bartender":
                    $tipoDePuntaje = "calificacion_bartender";
                    break;
                case "Cervecero":
                    $tipoDePuntaje = "calificacion_cervecero";
                    break;
            }
            $encuestas = Capsule::table('encuestas')
            ->join('pedidos', 'pedidos.id', '=', 'encuestas.pedido_id')
            ->join('ordens', 'ordens.pedido_id', '=', 'pedidos.id')
            ->select('encuestas.*')
            ->where('ordens.empleado_id', $eEmpleado->id)->get();


            $encuestas = $encuestas->unique('id');


            if (count($encuestas) > 0){
                foreach ($encuestas as $eEncuesta){
                    $sumatoriaPuntajes += $eEncuesta->$tipoDePuntaje;
                }
                
                $empleadoAModificar = empleado::where("id", $eEmpleado->id)->first();
                $puntaje = $sumatoriaPuntajes/count($encuestas);
                $empleadoAModificar->puntaje = $puntaje;
                $empleadoAModificar->save();
            }


            //mozo (siemrpre sera uno)
            $sumatoriaPuntajesMozo = 0;
            $mozo = empleado::ObtenerMozoDeUnPedido($pedido_id);
            $mozo_id = $mozo->id;
            $mozoAModificar = empleado::where("id", $mozo_id)->first();

            $encuestasMozo = Capsule::table('encuestas')
            ->join('pedidos', 'pedidos.id', '=', 'encuestas.pedido_id')
            ->select('encuestas.*')
            ->where('pedidos.mozo_id', $mozo_id)->get();

            $encuestasMozo = $encuestasMozo->unique('id');

            if (count($encuestasMozo) > 0){
                foreach ($encuestasMozo as $eEncuesta){
                    $sumatoriaPuntajesMozo += $eEncuesta->calificacion_mozo;
                }
                
                $puntaje = $sumatoriaPuntajesMozo/count($encuestasMozo);
                $mozoAModificar->puntaje = $puntaje;
                $mozoAModificar->save();
            }

            
        }
        

        
    }
    
}



?>