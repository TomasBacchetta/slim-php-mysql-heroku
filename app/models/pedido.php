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
estados:
-Abierto (automático al crear el pedido)
-Con orden (automático al incluir al menos una orden)
-Preparado (automático cuando la ultima orden a preparar se marco como preparada)
-Entregado (lo pone el mozo cuando entrega el pedido a la mesa)
-Pagado (hace falta? automático cuando se cierra la mesa)
*/


namespace App\Models;

use \Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use TCPDF;
use Illuminate\Database\Capsule\Manager as Capsule;

class pedido extends Model{
   
    public $incrementing = true;

    protected $fillable = [
        'mesa_id',
        'mozo_id',
        'total',
        'tiempo_estimado',
        'estado',
        'con_retraso',
        'foto_mesa'

    ];

    use SoftDeletes;

    public static function ObtenerPedidosDelMozo($mozo_id){
        
        $pedidos = pedido::where("mozo_id", $mozo_id)->where("estado", "!=", "Pagado")->get();
        $arrayPedidos = array();
        foreach ($pedidos as $ePedido){
            $ordenesDelPedido = orden::where("pedido_id", $ePedido->id)->orderBy("id", "desc")->get();
            $jsonPedido = json_encode($ePedido);  
            $jsonOrdenesDelPedido = json_encode($ordenesDelPedido);
            $arrayCombinado = array("pedido" => json_decode($jsonPedido, true),
                        "ordenes" => json_decode($jsonOrdenesDelPedido, true)
        );
        array_push($arrayPedidos, $arrayCombinado);
        }

        return $arrayPedidos;
    }

    public static function existePedido_PorId($id){
        $pedido = pedido::where("id", "=", $id)->withTrashed()->first();
        if (isset($pedido)){
            return true;
        } else {
            return false;
        }
        
    }

    public static function existePedido_PorId_SinBorrados($id){
        $pedido = pedido::where("id", "=", $id)->first();
        if (isset($pedido)){
            return true;
        } else {
            return false;
        }
        
    }

    public static function PedidoTieneCocinero($pedido_id){
        $ordenes = orden::ObtenerOrdenesPorSector("Cocina");
        foreach ($ordenes as $eOrden){
            if ($eOrden->pedido_id == $pedido_id){
                return true;
            }
        }
        $ordenes = orden::ObtenerOrdenesPorSector("Candy_Bar");
        foreach ($ordenes as $eOrden){
            if ($eOrden->pedido_id == $pedido_id){
                return true;
            }
        }
        return false;

    }

    public static function PedidoTieneCervecero($pedido_id){
        $ordenes = orden::ObtenerOrdenesPorSector("Barra_Choperas");
        foreach ($ordenes as $eOrden){
            if ($eOrden->pedido_id == $pedido_id){
                return true;
            }
        }
        return false;
    }

    public static function PedidoTieneBartender($pedido_id){
        $ordenes = orden::ObtenerOrdenesPorSector("Barra_Tragos");
        foreach ($ordenes as $eOrden){
            if ($eOrden->pedido_id == $pedido_id){
                return true;
            }
        }
        return false;
    }

    public static function MesaDelPedidoEstaCerrada($pedido_id){
        $mesa = Capsule::table('mesas')
        ->join('pedidos', 'pedidos.mesa_id', '=', 'mesas.id')
        ->select('mesas.*')
        ->where('mesas.estado', "Cerrada")->first();

        if (isset($mesa)){
            return true;
        } else {
            return false;
        }
        
    }

    public static function CrearFacturaPDF($id){
        $pedido = pedido::where("id", $id)->first();
        $ordenes = orden::where("pedido_id", $id)->get();
        $mozo = empleado::where("id", $pedido->mozo_id)->first();

        $textoOrdenes = "<dl>";

        foreach ($ordenes as $eOrden){
            $producto = producto::where("id", $eOrden->producto_id)->first();
            $textoOrdenes .= '<dt> x' . $eOrden->cantidad . ' ' . $producto->descripcion . '-------$' . $producto->precio . '</dt>';
            $textoOrdenes .= '<dd>Subtotal: $' . $eOrden->subtotal . '</dd>';
        }

        $textoOrdenes .= "</dl>";


        
        $pdf = new TCPDF('P', 'mm', 'A5', true, 'UTF-8', false, true);
    
        $pdf->addPage();
        $texto = '<img src="./logo/logo.png" alt="test alt attribute" width="60" height="60" border="0" />
                    <h1>Factura</h1> <br>
                    Fecha: ' . date("y-m-d") . '<br>
                    Pedido N°: ' . $id . '<br>
                    Mesa N°: ' . $pedido->mesa_id .'<br> 
                    Mozo: ' . $mozo->nombre .'<br> 
                    <ol>
                        
                        
                        
                    ' . $textoOrdenes . '<br><br> Total:$' . $pedido->total;
                    $pdf->writeHTML($texto, true, false, true, false, '');

        
        ob_end_clean();

        return $pdf;
    }
    /*

    public static function ObtenerPedidosAbiertosPorSector($sector){
        $pedidos = pedido::where("sector", $sector)->where("estado", "Abierto")->get();
        $arrayPedidos = array();
        foreach ($pedidos as $ePedido){
            $ordenesDelPedido = orden::where("pedido_id", $ePedido->id)->get();
            $jsonPedido = json_encode($ePedido);  
            $jsonOrdenesDelPedido = json_encode($ordenesDelPedido);
            $arrayCombinado = array("pedido" => json_decode($jsonPedido, true),
                        "ordenes" => json_decode($jsonOrdenesDelPedido, true)
        );
        array_push($arrayPedidos, $arrayCombinado);
        }

        return $arrayPedidos;
    }
    */
    /*
    public function orden()
    {
        return $this->hasMany(Comment::class);
    }
    */
    
    

    

    

    
}



?>