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

class encuesta extends Model{
    
    public $incrementing = true;

    protected $fillable = [
        'pedido_id',
        'calificacion_mesa',
        'calificacion_restaurante',
        'calificacion_mozo',
        'calificacion_cocinero',
        'calificacion_cervecero',
        'calificacion_bartender',
        'comentario'
    ];

    use SoftDeletes;

    public static function existeEncuesta_PorId($id){
        $encuesta = encuesta::where("id", "=", $id)->withTrashed()->first();
        if (isset($encuesta)){
            return true;
        } else {
            return false;
        }
        
    }

    public static function YaHayEncuestaParaEsePedido($pedido_id){
        $encuesta = encuesta::where("pedido_id", $pedido_id)->first();
        if (isset($encuesta)){
            return true;
        } else {
            return false;
        }
    }
    
}



?>