import { useQuery } from "react-query";
import { api } from "./api";

export function useVentas({
  cliente = undefined,
  vendedor = undefined,
  fechas = undefined
}) {
  return useQuery(["ventas", cliente, vendedor, fechas], () =>
    api.getVentas({
      cliente,
      vendedor,
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

export function useCompras() {
  return useQuery("compras", () => api.getCompras());
}

export function useCuentas({ proveedor = undefined }) {
  return useQuery(["cuentas", proveedor], () =>
    api.getCuentasCorrientes({
      proveedor
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

export function useCambiarEstado(tabla, id, estado) {
  return api.patchEstado(tabla, id, estado).then(res => res);
}
