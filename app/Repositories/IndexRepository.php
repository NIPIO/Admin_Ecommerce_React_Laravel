<?php

namespace App\Repositories;

use App\Interfaces\RepositoryInterface;
use App\Models\Caja;
use App\Models\Clientes;
use App\Models\Compras;
use App\Models\CtaCte;
use App\Models\Marcas;
use App\Models\Movimientos;
use App\Models\Productos;
use App\Models\Proveedores;
use App\Models\Vendedores;
use App\Models\Ventas;
use Carbon\Carbon;

class IndexRepository implements RepositoryInterface 
{

    //DEVUELVE TODAS LOS INDEX FILTRADOS. 
    public function indexVentas($req) {
        
        $cliente = isset($req['cliente']) ? $req['cliente'] : null;
        $vendedor = isset($req['vendedor']) ? $req['vendedor'] : null;
        $producto = isset($req['producto']) ? $req['producto'] : null;
        $fechas = isset($req['fechas']) ? $req['fechas'] : null;
        $estado = isset($req['estado']) ? $req['estado'] : null;
        
        $ventas = Ventas::orderBy('id', 'DESC')->with(['cliente', 'vendedor']);

        if ($cliente) {
            $ventas->whereClienteId((int) $cliente);
        }
        if ($vendedor) {
            $ventas->whereVendedorId((int) $vendedor);
        }

        //Si hay filtro busco
        if (!is_null($estado)) {
            $ventas->whereActivo((int) $estado);
        } else {
        //Por defecto devuelvo las activas.
            $ventas->whereActivo(1);
        }
        if ($producto) {
            $ventas->whereHas('detalleVenta', function($innerQuery) use ($producto) {
                $innerQuery->where('producto_id', (int) $producto);
            });
        }
        if ($fechas) {
            $ventas->whereBetween('fecha_venta', [Carbon::parse(substr($fechas[0], 1, -1))->format('Y-m-d'), Carbon::parse(substr($fechas[1], 1, -1))->format('Y-m-d')]);
        } else {
            //Si no filtra por fecha, le devuelvo solo las ventas de hoy.
            $ventas->whereDate('fecha_venta', '<=', Carbon::now()->format('Y-m-d'));
        }

        return $ventas;
    }
    
    public function indexCaja($tipoMovimiento, $fechas) {
        $caja = Caja::orderBy('id', 'DESC')->with('usuario');

        if ($tipoMovimiento) {
            $caja->whereTipoMovimiento($tipoMovimiento);
        }
    
        if ($fechas) {
            $caja->whereBetween('created_at', [Carbon::parse(substr($fechas[0], 1, -1))->format('Y-m-d'), Carbon::parse(substr($fechas[1], 1, -1))->format('Y-m-d')]);
        }

        return $caja;
    }

    public function indexClientes($cliente) {
        $clientes = Clientes::orderBy('id', 'DESC');

        if ($cliente) {
            $clientes->whereId((int) $cliente);
        }

        return $clientes;
    }
    
    public function indexCompras($proveedor, $producto) {
        $compras = Compras::orderBy('id', 'DESC')->with(['proveedor', 'producto']);
        
        if ($proveedor) {
            $compras->where('proveedor_id', (int) $proveedor);
        }
        if ($producto) {
            $compras->whereHas('detalleCompra', function($innerQuery) use ($producto) {
                $innerQuery->where('producto_id', (int) $producto);
            });
        }

        return $compras;
    }

    public function indexCuentas($req) {
                
        $cliente = isset($req['cliente']) ? $req['cliente'] : null;
        $proveedor = isset($req['proveedor']) ? $req['proveedor'] : null;

        $cuentas = CtaCte::orderBy('id', 'DESC')->with(['proveedor','cliente']);

        if ($proveedor) {
            $cuentas->where('proveedor_id', (int) $proveedor);
        } elseif ($cliente) {
            $cuentas->where('cliente_id', (int) $cliente);
        }

        return $cuentas;
    }

    public function indexMovimientos($req) {
        $usuario = isset($req['usuario']) ? $req['usuario'] : null;
        $tipoMovimiento = isset($req['tipoMovimiento']) ? $req['tipoMovimiento'] : null;
        $fechas = isset($req['fechas']) ? $req['fechas'] : null;
        $seccion = isset($req['seccion']) ? $req['seccion'] : null;

        $movimientos = Movimientos::orderBy('id', 'DESC')->with('usuario');

        if ($usuario) {
            $movimientos->whereUsuario((int) $usuario);
        }

        if ($tipoMovimiento) {
            $movimientos->whereTipoMovimiento($tipoMovimiento);
        }

        if ($seccion) {
            $movimientos->whereTabla($seccion);
        }
        
        
        if ($fechas) {
            $movimientos->whereBetween('created_at', [Carbon::parse(substr($fechas[0], 1, -1))->format('Y-m-d 00:00:00'), Carbon::parse(substr($fechas[1], 1, -1))->format('Y-m-d 23:59:59')]);
        }

        return $movimientos;
    }

    public function indexProductos($producto, $marca) {
        $productos = Productos::orderBy('id', 'ASC')->with(['marcas']);

        if ($producto) {
            $productos->whereId((int) $producto);
        }
        if ($marca) {
            $productos->whereMarca([(int) $marca]);
        }

        return $productos;
    }

    public function indexProveedores($proveedor) {
        $proveedores = Proveedores::orderBy('id', 'DESC');

        if ($proveedor) {
            $proveedores->whereId((int) $proveedor);
        }

        return $proveedores;
    }

    public function indexVendedores($vendedor) {
        $vendedores = Vendedores::orderBy('id', 'DESC')->with(['rol']);

        if ($vendedor) {
            $vendedores->whereId((int) $vendedor);
        }

        return $vendedores;
    }

    public function indexMarcas($marca) {
        $marcas = Marcas::orderBy('id', 'ASC');

        if ($marca) {
            $marcas->whereId((int) $marca);
        }

        return $marcas->get();
    }
}