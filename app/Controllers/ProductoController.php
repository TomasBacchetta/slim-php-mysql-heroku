<?php
/*
BACCHETTA, TOMÁS
TP PROGRAMACION 3 "LA COMANDA"
SPRINT 1
ALTA
VISUALIZACION
BASE DE DATOS

*/



use \App\Models\producto as producto;
use GuzzleHttp\Psr7\Stream;
use Slim\Psr7\Stream as Psr7Stream;

class ProductoController {


    public function CargarUno($request, $response, $args){
        
        $param = $request->getParsedBody();

        $descripcion = $param["descripcion"];
        $precio = $param["precio"];
        $stock = $param["stock"];
        $sector = $param["sector"];
        $tiempo_estimado = $param["tiempo_estimado"];


        $productoNuevo = new producto();

        $productoNuevo->descripcion = $descripcion; 
        $productoNuevo->precio = $precio; 
        $productoNuevo->stock = $stock; 
        $productoNuevo->sector = $sector; 
        $productoNuevo->tiempo_estimado = $tiempo_estimado; 

        $productoNuevo->save();
    
        

        $payload = json_encode(array("mensaje" => "Producto cargado con éxito para ser usado en " . $sector));
        $response->getBody()->write($payload);

        return $response->withHeader("Content-Type", "application/json");

        
    }

    public function TraerUno($request, $response, $args){
        $id_producto = $args["id"];
        //otra forma usando instancia
        //$producto = new producto();
        //$producto = $producto->find($id_producto);
        $producto = producto::where('id', '=', $id_producto)->first();
        $payload = json_encode($producto);

        $response->getBody()->write($payload);

        return $response->withHeader("Content-Type", "application/json");
        
    }

    public function TraerTodos($request, $response, $args){
        $productos = producto::all();
        $payload = json_encode(array("listaProductos" => $productos));

        $response->getBody()->write($payload);

        return $response->withHeader("Content-Type", "application/json");

    }
   

    public function ModificarUno($request, $response, $args){
        $param = $request->getParsedBody();
        $id = $args["id"];
        $descripcion = $param["descripcion"];
        $precio = $param["precio"];
        $stock = $param["stock"];
        $sector = $param["sector"];
        $tiempo_estimado = $param["tiempo_estimado"];

        $productoAModificar = producto::where('id', $id)->first();
        $productoAModificar->descripcion = $descripcion;
        $productoAModificar->precio = $precio;
        $productoAModificar->stock = $stock;
        $productoAModificar->sector = $sector;
        $productoAModificar->tiempo_estimado = $tiempo_estimado;
        

        $productoAModificar->save();

        $payload = json_encode(array("mensaje" => "Producto modificado exitosamente"));

        $response->getBody()->write($payload);

        return $response->withHeader("Content-Type", "application/json");

        
    }

    public function BorrarUno($request, $response, $args){
        $id = $args["id"];
        

        $productoABorrar = producto::where("id", $id)->first();
        $productoABorrar->delete();
        


        $payload = json_encode(array("mensaje"=> "Producto eliminado exitosamente"));

        $response->getBody()->write($payload);

        return $response->withHeader("Content-Type", "application/json");

    }

    public function CrearCsv($request, $response, $args){
        $data = producto::withTrashed()->get();
        $csv = fopen('php://memory', 'w');
        
        foreach ($data as $row) {
	        fputcsv($csv, $row->toArray(), ';');
        }

        $stream = new Stream($csv); 
        rewind($csv);

        return $response->withHeader('Content-Type', 'application/force-download')
                        ->withHeader('Content-Type', 'application/octet-stream')
                        ->withHeader('Content-Type', 'application/download')
                        ->withHeader('Content-Description', 'File Transfer')
                        ->withHeader('Content-Transfer-Encoding', 'binary')
                        ->withHeader('Content-Disposition', 'attachment; filename="productos.csv"')
                        ->withHeader('Expires', '0')
                        ->withHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0')
                        ->withHeader('Pragma', 'public')
                        ->withBody($stream);
        
        
    }



    public function ImportarCsv($request, $response, $args){
        $tmpName = $_FILES['csv']['tmp_name'];
        $csvAsArray = array_map('str_getcsv', file($tmpName));
        var_dump($csvAsArray);
        
        foreach ($csvAsArray as $eObj){
            $producto = new producto();
            $array = explode(';', $eObj[0]);
            if (!producto::existeProducto_PorId($array[0])){
                producto::where("id", $array[0])->forceDelete();//esto es por si hay un id con softdelete
                $producto->id = $array[0];
                $producto->descripcion = $array[1];
                $producto->precio = $array[2];
                $producto->stock = $array[3];
                $producto->sector = $array[4];
                $producto->tiempo_estimado = $array[5];
                $producto->created_at = $array[6];
                $producto->updated_at = $array[7];

                
            } else {
                
                $producto = producto::where("id", $array[0])->withTrashed()->first();
                if ((!isset($producto->deleted_at) || $producto->deleted_at != '') &&
                    ($array[8] == null || $array[8] == '')){
                    $producto->deleted_at = null;
                }

                
            }

                $producto->save();
            

        }
        

        $payload = json_encode(array("mensaje" => "Csv importado con exito"));

        $response->getBody()->write($payload);

        return $response->withHeader("Content-Type", "application/json");
    }
    

    

    

}



?>