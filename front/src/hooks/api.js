import axios from "axios";

export const client = axios.create({
  headers: {
    Accept: "application/json"
  }
});

const API_PORT = "http://localhost:8000";

export const getStorage = () => {
  let localSto = localStorage.getItem("logueado");
  localSto = JSON.parse(localSto);
  return localSto;
};

export const getUsuario = () => {
  return getStorage().id;
};

export const api = {
  ///GETTERS

  getMovimientos: ({ usuario, fechas, tipoMovimiento, seccion }) =>
    client
      .get(API_PORT + "/api/movimientos", {
        params: {
          usuario,
          fechas,
          tipoMovimiento,
          seccion
        }
      })
      .then(res => res.data),

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

  getVentas: ({ cliente, vendedor, fechas, producto }) =>
    client
      .get(API_PORT + "/api/ventas", {
        params: {
          cliente,
          vendedor,
          fechas,
          producto
        }
      })
      .then(res => res.data),

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

  getCaja: ({ tipoMovimiento, fechas }) =>
    client
      .get(API_PORT + "/api/caja", {
        params: {
          tipoMovimiento,
          fechas
        }
      })
      .then(res => res.data),

  getRoles: ({ rol }) =>
    client
      .get(API_PORT + "/api/roles", {
        params: {
          rol
        }
      })
      .then(res => res.data),

  getPermisos: () =>
    client.get(API_PORT + "/api/permisos").then(res => res.data),

  ///SETTERS
  setNuevoProducto: data =>
    client
      .post(API_PORT + "/api/producto", { data, usuario: getUsuario() })
      .then(res => res.data),

  setNuevaMarca: data =>
    client
      .post(API_PORT + "/api/marca", { data, usuario: getUsuario() })
      .then(res => res.data),

  setNuevaVenta: (filas, cliente, vendedor) =>
    client
      .post(API_PORT + "/api/venta", {
        filas,
        cliente,
        vendedor,
        usuario: getUsuario()
      })
      .then(res => res.data),

  setNuevaCompra: (productos, proveedor) =>
    client
      .post(API_PORT + "/api/compra", {
        productos,
        proveedor,
        usuario: getUsuario()
      })
      .then(res => res.data),

  setNuevaCaja: data =>
    client
      .post(API_PORT + "/api/caja", { data, usuario: getUsuario() })
      .then(res => res.data),

  setNuevoCliente: data =>
    client
      .post(API_PORT + "/api/cliente", { data, usuario: getUsuario() })
      .then(res => res.data),

  setNuevoVendedor: data =>
    client
      .post(API_PORT + "/api/vendedor", { data, usuario: getUsuario() })
      .then(res => res.data),

  setNuevoProveedor: data =>
    client
      .post(API_PORT + "/api/proveedor", { data, usuario: getUsuario() })
      .then(res => res.data),

  setNuevaCtaCte: data =>
    client
      .post(API_PORT + "/api/cuentas-corrientes", {
        data,
        usuario: getUsuario()
      })
      .then(res => res.data),

  setRol: data =>
    client
      .post(API_PORT + "/api/roles", { data, usuario: getUsuario() })
      .then(res => res.data),

  confirmarCompra: data =>
    client
      .post(API_PORT + "/api/confirmarCompra", { data, usuario: getUsuario() })
      .then(res => res.data),

  confirmarVenta: data =>
    client
      .post(API_PORT + "/api/confirmarVenta", { data, usuario: getUsuario() })
      .then(res => res.data),

  //PUTTERS
  putProducto: data =>
    client
      .put(API_PORT + `/api/producto/${data.id}`, {
        data,
        usuario: getUsuario()
      })
      .then(res => res.data),
  putMarca: data =>
    client
      .put(API_PORT + `/api/marca/${data.id}`, { data, usuario: getUsuario() })
      .then(res => res.data),

  putCliente: data =>
    client
      .put(API_PORT + `/api/cliente/${data.id}`, {
        data,
        usuario: getUsuario()
      })
      .then(res => res.data),

  putCuenta: data =>
    client
      .put(API_PORT + `/api/cuentas-corrientes/${data.id}`, {
        data,
        usuario: getUsuario()
      })
      .then(res => res.data),

  putProveedor: data =>
    client
      .put(API_PORT + `/api/proveedor/${data.id}`, {
        data,
        usuario: getUsuario()
      })
      .then(res => res.data),

  putCtaCte: data =>
    client
      .put(API_PORT + `/api/cuentas-corrientes/${data.id}`, {
        data,
        usuario: getUsuario()
      })
      .then(res => res.data),

  putVenta: (id, filas, cliente, vendedor) =>
    client
      .put(API_PORT + `/api/venta/${id}`, {
        id,
        filas,
        cliente,
        vendedor,
        usuario: getUsuario()
      })
      .then(res => res.data),

  putVendedor: data =>
    client
      .put(API_PORT + `/api/vendedor/${data.id}`, {
        data,
        usuario: getUsuario()
      })
      .then(res => res.data),

  putRol: data =>
    client
      .put(API_PORT + `/api/rol/${data.id}`, { data, usuario: getUsuario() })
      .then(res => res.data),

  //DELETERS
  deleteProducto: id =>
    client.delete(API_PORT + `/api/producto/${id}`, id).then(res => res.data),

  deleteMarca: id =>
    client.delete(API_PORT + `/api/marca/${id}`, id).then(res => res.data),

  deleteVendedor: id =>
    client.delete(API_PORT + `/api/vendedor/${id}`, id).then(res => res.data),

  //PATCHERS
  patchEstado: (tabla, id, estado, usuario) =>
    client
      .patch(API_PORT + `/api/toggleEstado/${id}`, {
        tabla,
        id,
        estado,
        usuario
      })
      .then(res => res.data)
};

export const signin = {
  postLogin: data =>
    client.post(API_PORT + "/api/login", data).then(res => res.data),

  postRegistro: data =>
    client.post(API_PORT + "/api/registro", data).then(res => res.data)
};
