import { useQuery } from "react-query";
import { api, getStorage } from "./api";

export const esAdmin = () => {
  return getStorage().rol_id === 1;
};

export function useMovimientos({
  usuario = undefined,
  fechas = undefined,
  tipoMovimiento = undefined,
  seccion = undefined
}) {
  return useQuery(
    ["movimientos", usuario, fechas, tipoMovimiento, seccion],
    () =>
      api.getMovimientos({
        usuario,
        fechas,
        tipoMovimiento,
        seccion
      })
  );
}

export function useVentas({
  cliente = undefined,
  vendedor = undefined,
  producto = undefined,
  estado = undefined,
  fechas = undefined
}) {
  return useQuery(["ventas", cliente, producto, estado, vendedor, fechas], () =>
    api.getVentas({
      cliente,
      vendedor,
      producto,
      estado,
      fechas
    })
  );
}

export function useProductos({ producto = undefined, marca = undefined }) {
  return useQuery(["productos", producto, marca], () =>
    api.getProductos({
      producto,
      marca
    })
  );
}

export function useClientes({ cliente = undefined }) {
  return useQuery(["clientes", cliente], () =>
    api.getClientes({
      cliente
    })
  );
}

export function useVendedores({ vendedor = undefined }) {
  return useQuery(["vendedores", vendedor], () =>
    api.getVendedores({
      vendedor
    })
  );
}

export function useCompras({ proveedor = undefined, producto = undefined }) {
  return useQuery(["compras", proveedor, producto], () =>
    api.getCompras({
      proveedor,
      producto
    })
  );
}

export function useCuentas({ proveedor = undefined, cliente = undefined }) {
  return useQuery(["cuentas", proveedor, cliente], () =>
    api.getCuentasCorrientes({
      proveedor,
      cliente
    })
  );
}

export function useCaja(
  { tipoMovimiento = undefined, fechas = undefined },
  tipo
) {
  return useQuery(["caja", tipoMovimiento, fechas, tipo], () =>
    api.getCaja({
      tipoMovimiento,
      fechas,
      tipo
    })
  );
}

export function useProveedores({ proveedor = undefined }) {
  return useQuery(["proveedores", proveedor], () =>
    api.getProveedores({
      proveedor
    })
  );
}

export function useMarcas({ marca = undefined }) {
  return useQuery(["marcas", marca], () =>
    api.getMarcas({
      marca
    })
  );
}

export function useRoles({ rol = undefined }) {
  return useQuery(["roles", rol], () =>
    api.getRoles({
      rol
    })
  );
}

export function usePermisos() {
  return useQuery(["permisos"], () => api.getPermisos());
}

export function useCambiarEstado(tabla, id, estado, usuario) {
  return api.patchEstado(tabla, id, estado, usuario).then(res => res);
}
