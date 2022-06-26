<?php
/*
BACCHETTA, TOMÁS
TP PROGRAMACION 3 "LA COMANDA"
SPRINT 1
ALTA
VISUALIZACION
BASE DE DATOS

*/

/*
sectores:
Barra_Tragos (bartender)
Barra_Choperas (cervecero)
Cocina (cocinero)
Candy_Bar (coinero)

*/

namespace App\Models;

use \Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class producto extends Model{
    
    public $incrementing = true;

    protected $fillable = [
        'descripcion', 
        'precio', 
        'stock', 
        'sector', 
        'tiempo_estimado'
    ]; 
    
    use SoftDeletes;

    /* public function orden()
    {
        return $this->belongsTo(orden::class);
    }  */

    public static function ObtenerProductosPorSector($sector){
        return producto::where("sector", $sector)->get();

    }

    public static function existeProducto($descripcion){
        $producto = producto::where("descripcion", "=", $descripcion)->first();
        if (isset($producto)){
            return true;
        } else {
            return false;
        }
        
    }

    public static function existeProducto_PorId($id){
        $producto = producto::where("id", "=", $id)->withTrashed()->first();
        if (isset($producto)){
            return true;
        } else {
            return false;
        }
        
    }

    public static function existeProducto_PorIdSinBorrados($id){
        $producto = producto::where("id", "=", $id)->first();
        if (isset($producto)){
            return true;
        } else {
            return false;
        }
        
    }

    
    
    
}



?>