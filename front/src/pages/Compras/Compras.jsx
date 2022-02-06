import { useCompras, useProductos, useProveedores } from "../../hooks/apiCalls";
import React, { useState } from "react";
import { Container, Card, CardHeader, CardBody } from "shards-react";
import PageTitle from "../../components/common/PageTitle";
import { Table, Spin, Row, Col, Space, Button, Switch, Popconfirm } from "antd";
import { api } from "./../../hooks/api";
import ModalNuevaCompra from "./Nueva/ModalNuevaCompra";
import ModalConfirmarCompra from "./Nueva/ModalConfirmarCompra";
import Busqueda from "./Busqueda";
import { useQueryClient } from "react-query";
import { toggleEstado, showNotification } from "../notificacion";
import ModalVerEditarCompra from "./VerEditar/ModalVerEditarCompra";

const Compras = () => {
  //INFO TABLA:
  const columnas = [
    {
      title: "Nro",
      dataIndex: ["id"],
      width: "7%",
      render: text => text
    },
    {
      title: "Proveedor",
      dataIndex: ["proveedor", "nombre"],
      render: text => text
    },
    {
      title: "Total",
      dataIndex: ["precio_total"],
      render: text => `$ ${text.toLocaleString()}`
    },
    {
      title: "Fecha",
      dataIndex: ["created_at"],
      render: text => text
    },
    {
      title: "Estado",
      dataIndex: ["activo"],
      render: (text, row) => (
        <Space>
          <Popconfirm
            title="Si cambia el estado de esta compra modificará el stock en transito de los productos. Seguimos?"
            onConfirm={() =>
              toggleEstado(
                "compras",
                "compras",
                row.id,
                row.activo,
                queryClient
              )
            }
            onCancel={() => console.log("ta")}
            okText="Sí"
            cancelText="No"
          >
            <Switch
              checked={text}
              checkedChildren={"Activa"}
              unCheckedChildren={"Cancelada"}
            />
          </Popconfirm>
        </Space>
      )
    },
    {
      title: "Acciones",
      key: "action",

      render: (text, row, id) => (
        <Space size="middle">
          <Button
            type="primary"
            onClick={() => {
              setModalCompraConfirmada(true);
              setIdCompraConfirmada(row.id);
            }}
            success={row.confirmada}
            disabled={row.confirmada || !row.activo}
          >
            {row.confirmada ? "Confirmada" : " Confirmar "}
          </Button>
          <Button onClick={() => verEditar(text)}>Ver</Button>
          <Popconfirm
            title="Eliminamos de forma definitiva esta compra?"
            onConfirm={() =>
              api.deleteCompra(row.id).then(res => {
                if (res.error) {
                  showNotification("error", "Hubo un error", res.error);
                } else {
                  showNotification("success", "Compra eliminada", "");
                  queryClient.invalidateQueries("compras");
                }
              })
            }
            onCancel={() => console.log("ta")}
            okText="Sí"
            cancelText="No"
          >
            <Button danger style={{ display: !row.activo ? "block" : "none" }}>
              Eliminar
            </Button>
          </Popconfirm>
        </Space>
      )
    }
  ];
  //FIN INFO TABLA.

  const [busqueda, setBusqueda] = useState({
    proveedor: null,
    producto: null
  });

  const queryClient = useQueryClient();
  const [idCompraConfirmada, setIdCompraConfirmada] = useState(null);
  const [modalCompraConfirmada, setModalCompraConfirmada] = useState(false);
  const [modalVerEditar, setModalVerEditar] = useState(false);
  const [modal, setModal] = useState(false);
  const [verCompra, setVerCompra] = useState(null);

  const allProveedores = useProveedores({});
  const allProductos = useProductos({});
  const allCompras = useCompras(busqueda);

  const verEditar = idVenta => {
    setModalVerEditar(true);
    setVerCompra(idVenta);
  };

  if (
    allProveedores.isLoading ||
    allProductos.isLoading ||
    allCompras.isLoading
  ) {
    return (
      <Spin tip="Cargando" style={{ width: "100%", margin: "10% auto" }}></Spin>
    );
  }

  return (
    <Container fluid className="main-content-container px-4">
      <Row className="page-header py-4">
        <Space
          direction="horizontal"
          style={{ width: "100%", justifyContent: "space-between" }}
        >
          <Col span={8}>
            <PageTitle
              sm="4"
              title="Compras"
              subtitle=""
              className="text-sm-left"
            />
          </Col>
          <Col span={8}>
            <Button onClick={() => setModal(true)} type="primary">
              Nueva Compra
            </Button>
          </Col>
        </Space>
      </Row>

      <Row>
        <Col>
          <Card small className="mb-4">
            <CardHeader className="border-bottom">
              <Busqueda
                setBusqueda={setBusqueda}
                proveedores={allProveedores.data.allProveedores}
                productos={allProductos.data.allProductos}
              />
            </CardHeader>
            <CardBody className="p-0 pb-3">
              <Table
                rowKey="id"
                columns={columnas}
                scroll={{ x: 900, y: 300 }}
                dataSource={allCompras.data.comprasFiltro}
                pagination={{
                  defaultPageSize: 10,
                  showSizeChanger: true,
                  pageSizeOptions: ["5", "10", "15", "20", "30", "50"]
                }}
              />
            </CardBody>
          </Card>
        </Col>
        <ModalNuevaCompra
          modal={modal}
          setModal={setModal}
          proveedores={allProveedores.data.allProveedores}
          productos={allProductos.data.allProductos}
          queryClient={queryClient}
        />
        {verCompra && (
          <ModalVerEditarCompra
            modal={modalVerEditar}
            setModal={setModalVerEditar}
            verCompra={verCompra}
            setVerCompra={setVerCompra}
            productos={allProductos.data.allProductos}
            proveedores={allProveedores.data.allProveedores}
          />
        )}

        {idCompraConfirmada && (
          <ModalConfirmarCompra
            modal={modalCompraConfirmada}
            setModal={setModalCompraConfirmada}
            id={idCompraConfirmada}
            queryClient={queryClient}
          />
        )}
      </Row>
    </Container>
  );
};

export default Compras;
