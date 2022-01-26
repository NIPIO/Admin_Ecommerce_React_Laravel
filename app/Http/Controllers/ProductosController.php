<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Responses\Json;
use App\Models\Marcas;
use App\Models\Productos;

class ProductosController extends Controller
{
    private $movimientosController;

    public function __construct(MovimientosController $movimientosController)
    {
        $this->movimientosController = $movimientosController;    
    }

    public function index() {
        $producto = request()->get('producto');
        $marca = request()->get('marca');
        $productos = Productos::orderBy('id', 'ASC')->with(['marcas']);

        if ($producto) {
            $productos->whereId((int) $producto);
        }
        if ($marca) {
            $productos->whereMarca([(int) $marca]);
        }
        
        return response()->json(['error' => false, 'allProductos' => Productos::all(), 'productosFiltro' => $productos->get()]);
    }


    public function nuevoProducto(Request $request) {
        $req = $request->all();
        $usuario = $req['usuario'];
        $req = $req['data'];

        try {

            $producto = Productos::where('nombre', $req['nombre'])->get();
            
            if (count($producto) > 0) {
                return response()->json(['error' => true, 'data' => 'Existe un producto con ese nombre']);
            }
     
            $producto = new Productos();
            $producto->nombre = $req['nombre'];
            $producto->marca = $req['marca'];
            $producto->precio = $req['precio'];
            $producto->costo = $req['costo'];
            $producto->stock= $req['stock'];
            $producto->stock_reservado = 0;
            $producto->en_transito_reservado = 0;
            $producto->save();
    
            $this->movimientosController->guardarMovimiento(
                'productos', 'ALTA', $usuario, $producto->id, null, null, null
            );

        } catch (\Exception $th) {
            throw new \Exception($th->getMessage());
        }
        
        return response()->json(['error' => false]);
    }

    public function editarProducto(Request $request) {
        $req = $request->all();
        $usuario = $req['usuario'];
        $req = $req['data'];
        
        try {
            $producto = Productos::whereId($req['id']);

            // if($this->chequearSiExiste($req['nombre'])){
            //     return response()->json(['error' => true, 'data' => 'Existe un producto con ese nombre']);
            // }
            
            $producto = Productos::whereId($req['id']);

            $cambios = $this->buscarCamposEditados($producto, $req);

            $producto->update([
                "nombre" => $req['nombre'],
                "marca" => $req['marca'],
                "stock" => $req['stock'],
                "precio" => $req['precio'],
            ]);

            if ($cambios) { //EDITÓ ALGÚN CAMPO
                $this->movimientosController->guardarMovimiento(
                    'productos', 'MODIFICACION', $usuario, $req['id'], $cambios[1], $req[$cambios[0]], null, $cambios[0]
                );
            }
     
        } catch (\Exception $th) {
            throw new \Exception($th->getMessage());;
        }
       
        return response()->json(['error' => false]);
    }
            
    private function buscarCamposEditados($producto, $req) {
        $producto = $producto->first();

        if ($producto->nombre !== $req['nombre']) {
            return ['nombre', $producto->nombre];
        }
        if ($producto->marca !== $req['marca']) {
            return ['marca', $producto->marca];
        }
        if ($producto->stock !== $req['stock']) {
            return ['stock', $producto->stock];
        }
        if ($producto->precio !== $req['precio']) {
            return ['precio', $producto->precio];
        }
    }

    // public function chequearSiExiste($nombre) {
    //     return count(Productos::where('nombre', $nombre)->get()->toArray()) > 0;
    // }

    public function getStock() {
        return [Productos::sum('stock'), Productos::sum('stock_reservado'), Productos::sum('en_transito'), Productos::sum('en_transito_reservado')];
    }
}
