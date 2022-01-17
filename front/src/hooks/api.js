import axios from "axios";

export const client = axios.create({
  headers: {
    Accept: "application/json"
  }
});

const API_PORT = "http://localhost:8000";

export const api = {
  ///GETTERS
  getProductos: ({ producto, marca }) =>
    client
      .get(API_PORT + "/api/productos", {
        params: {
          producto,
          marca
        }
      })
      .then(res => res.data),

  getMarcas: ({ marca }) =>
    client
      .get(API_PORT + "/api/marcas", {
        params: {
          marca
        }
      })
      .then(res => res.data),

  getVendedores: ({ vendedor }) =>
    client
      .get(API_PORT + "/api/vendedores", {
        params: {
          vendedor
        }
      })
      .then(res => res.data),

  getClientes: ({ cliente }) =>
    client
      .get(API_PORT + "/api/clientes", {
        params: {
          cliente
        }
      })
      .then(res => res.data),

  getProveedores: ({ proveedor }) =>
    client
      .get(API_PORT + "/api/proveedores", {
        params: {
          proveedor
        }
      })
      .then(res => res.data),

  getVentas: ({ cliente, vendedor, fechas, producto }) => {
    return client
      .get(API_PORT + "/api/ventas", {
        params: {
          cliente,
          vendedor,
          fechas,
          producto
        }
      })
      .then(res => res.data);
  },

  getVenta: id =>
    client.get(API_PORT + `/api/venta/${id}`, id).then(res => res.data),

  getCompras: ({ proveedor, producto }) =>
    client
      .get(API_PORT + "/api/compras", {
        params: {
          proveedor,
          producto
        }
      })
      .then(res => res.data),

  getCompra: id =>
    client.get(API_PORT + `/api/compra/${id}`, id).then(res => res.data),

  getCuentasCorrientes: ({ proveedor, cliente }) =>
    client
      .get(API_PORT + "/api/cuentas-corrientes", {
        params: {
          proveedor,
          cliente
        }
      })
      .then(res => res.data),

  confirmarCompra: (pago, id, diferencia) =>
    client
      .get(API_PORT + "/api/confirmarCompra", {
        params: {
          id,
          pago,
          diferencia
        }
      })
      .then(res => res.data),

  confirmarVenta: (pago, id, diferencia) =>
    client
      .get(API_PORT + "/api/confirmarVenta", {
        params: {
          id,
          pago,
          diferencia
        }
      })
      .then(res => res.data),

  ///SETTERS
  setNuevoProducto: data =>
    client.post(API_PORT + "/api/producto", data).then(res => res.data),

  setNuevaMarca: data =>
    client.post(API_PORT + "/api/marca", data).then(res => res.data),

  setNuevaVenta: (productos, cliente, vendedor) =>
    client
      .post(API_PORT + "/api/venta", { productos, cliente, vendedor })
      .then(res => res.data),

  setNuevaCompra: (productos, proveedor) =>
    client
      .post(API_PORT + "/api/compra", { productos, proveedor })
      .then(res => res.data),

  setNuevoCliente: data =>
    client.post(API_PORT + "/api/cliente", data).then(res => res.data),

  setNuevoVendedor: data =>
    client.post(API_PORT + "/api/vendedor", data).then(res => res.data),

  setNuevoProveedor: data =>
    client.post(API_PORT + "/api/proveedor", data).then(res => res.data),

  setNuevaCtaCte: data =>
    client
      .post(API_PORT + "/api/cuentas-corrientes", data)
      .then(res => res.data),

  //PUTTERS
  putProducto: data =>
    client
      .put(API_PORT + `/api/producto/${data.id}`, data)
      .then(res => res.data),
  putMarca: data =>
    client.put(API_PORT + `/api/marca/${data.id}`, data).then(res => res.data),
  putCliente: data =>
    client
      .put(API_PORT + `/api/cliente/${data.id}`, data)
      .then(res => res.data),
  putProveedor: data =>
    client
      .put(API_PORT + `/api/proveedor/${data.id}`, data)
      .then(res => res.data),
  putCtaCte: data =>
    client
      .put(API_PORT + `/api/cuentas-corrientes/${data.id}`, data)
      .then(res => res.data),

  putVenta: data =>
    client.put(API_PORT + `/api/venta/${data.id}`, data).then(res => res.data),

  putVendedor: data =>
    client
      .put(API_PORT + `/api/vendedor/${data.id}`, data)
      .then(res => res.data),

  //DELETERS
  deleteProducto: id =>
    client.delete(API_PORT + `/api/producto/${id}`, id).then(res => res.data),

  deleteMarca: id =>
    client.delete(API_PORT + `/api/marca/${id}`, id).then(res => res.data),

  deleteVendedor: id =>
    client.delete(API_PORT + `/api/vendedor/${id}`, id).then(res => res.data),

  //PATCHERS
  patchEstado: (tabla, id, estado) =>
    client
      .patch(API_PORT + `/api/toggleEstado/${id}`, { tabla, id, estado })
      .then(res => res.data)
};

export const signin = {
  postLogin: data =>
    client.post(API_PORT + "/api/login", data).then(res => res.data),

  postRegistro: data =>
    client.post(API_PORT + "/api/registro", data).then(res => res.data)
};
