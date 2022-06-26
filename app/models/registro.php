<?php

namespace App\Models;

use \Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class registro extends Model{

     

    protected $fillable = [
        'empleado',
        'area',
        'descripcion'
    ];

    use SoftDeletes;

    public static function CrearRegistro($empleado_id, $descripcion){
        $log = new registro();
        $empleado = empleado::where("id", $empleado_id)->first();
        
        
        $log->empleado = $empleado->nombre;
        $log->puesto = $empleado->puesto;
        $log->descripcion = $descripcion;

        $log->save();

    }

} 



?>