import {
  useClientes,
  useVendedores,
  useVentas,
  useProductos
} from "../../hooks/apiCalls";
import { api } from "./../../hooks/api";
import React, { useState } from "react";
import { Container, Card, CardHeader, CardBody } from "shards-react";
import { Table, Spin, Row, Col, Space, Button, Switch, Popconfirm } from "antd";
import PageTitle from "../../components/common/PageTitle";
import { toggleEstado, showNotification } from "./../notificacion";
import ModalNuevaVenta from "./Nueva/ModalNuevaVenta";
import ModalConfirmarVenta from "./Nueva/ModalConfirmarVenta";
import Busqueda from "./Busqueda";
import { useQueryClient } from "react-query";
import ModalVerEditarVenta from "./VerEditar/ModalVerEditarVenta";

const Ventas = () => {
  let isAdmin = JSON.parse(localStorage.getItem("logueado")).rol_id === 1;

  //INFO TABLA:
  const columnas = [
    {
      title: "Nro",
      dataIndex: ["id"],
      width: "7%",
      render: text => text
    },
    {
      title: "Cliente",
      dataIndex: ["cliente", "nombre"],
      render: text => text
    },
    {
      title: "Vendedor",
      dataIndex: ["vendedor", "nombre"],
      render: text => text
    },
    {
      title: "Total",
      dataIndex: ["precio_total"],
      render: text => `$ ${text.toLocaleString()}`,
      sorter: (a, b) => a.precio_total - b.precio_total
    },
    {
      title: "Costo",
      dataIndex: ["costo"],
      render: text => `$ ${text.toLocaleString()}`,
      sorter: (a, b) => a.costo - b.costo
    },

    {
      title: "Fecha",
      dataIndex: ["fecha_venta"],
      render: text => text
    },
    {
      title: "Estado",
      dataIndex: ["activo"],
      render: (text, row) => (
        <Space>
          <Popconfirm
            title="Si cambia el estado de esta venta modificará el stock de los productos (van a pasar a reservados o disponibles según corresponda). Seguimos?"
            onConfirm={() =>
              toggleEstado("ventas", "ventas", row.id, row.activo, queryClient)
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
              setModalVentaConfirmada(true);
              setIdVentaConfirmada(row.id);
            }}
            success={row.confirmada}
            disabled={row.confirmada || !row.activo}
          >
            {row.confirmada ? "Confirmada" : " Confirmar "}
          </Button>
          <Button onClick={() => verEditar(text)}>Ver</Button>
          <Popconfirm
            title="Eliminamos de forma definitiva esta venta?"
            onConfirm={() =>
              api.deleteVenta(row.id).then(res => {
                if (res.error) {
                  showNotification("error", "Hubo un error", res.error);
                } else {
                  showNotification("success", "Venta eliminada", "");
                  queryClient.invalidateQueries("ventas");
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

  // Muestro la utilidad si es admin.
  if (isAdmin) {
    columnas.splice(5, 0, {
      title: "Utilidad",
      dataIndex: ["utilidad"],
      render: text => `$ ${text.toLocaleString()}`,
      sorter: (a, b) => a.utilidad - b.utilidad
    });
  }

  //FIN INFO TABLA.

  const queryClient = useQueryClient();
  const [busqueda, setBusqueda] = useState({
    cliente: null,
    vendedor: null,
    estado: null,
    producto: null,
    fechas: null
  });

  const [idVentaConfirmada, setIdVentaConfirmada] = useState(null);
  const [modalVentaConfirmada, setModalVentaConfirmada] = useState(false);
  const [verVenta, setVerVenta] = useState(null);
  const [modal, setModal] = useState(false);
  const [modalVerEditar, setModalVerEditar] = useState(false);

  const allVentas = useVentas(busqueda);
  const allClientes = useClientes({});
  const allVendedores = useVendedores({});
  const allProductos = useProductos({});

  const verEditar = idVenta => {
    setModalVerEditar(true);
    setVerVenta(idVenta);
  };

  if (
    allVentas.isLoading ||
    allClientes.isLoading ||
    allVendedores.isLoading ||
    allProductos.isLoading
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
              title="Ventas"
              subtitle=""
              className="text-sm-left"
            />
          </Col>
          <Col span={8}>
            <Button onClick={() => setModal(true)} type="primary">
              Nueva Venta
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
                clientes={allClientes.data.allClientes}
                vendedores={allVendedores.data.allVendedores}
                productos={allProductos.data.allProductos}
              />
            </CardHeader>
            <CardBody className="p-0 pb-3">
              <Table
                rowKey="imiID"
                scroll={{ x: 900, y: 450 }}
                columns={columnas}
                dataSource={allVentas.data.ventasFiltro}
                pagination={{
                  defaultPageSize: 10,
                  showSizeChanger: true,
                  pageSizeOptions: ["5", "10", "15", "20", "30", "50"]
                }}
              />
            </CardBody>
          </Card>
        </Col>
        <ModalNuevaVenta
          modal={modal}
          setModal={setModal}
          clientes={allClientes.data.allClientes}
          productos={allProductos.data.allProductos}
          vendedores={allVendedores.data.allVendedores}
          queryClient={queryClient}
        />
        {verVenta && (
          <ModalVerEditarVenta
            modal={modalVerEditar}
            setModal={setModalVerEditar}
            verVenta={verVenta}
            setVerVenta={setVerVenta}
            clientes={allClientes.data.allClientes}
            productos={allProductos.data.allProductos}
            vendedores={allVendedores.data.allVendedores}
          />
        )}
        {idVentaConfirmada && (
          <ModalConfirmarVenta
            modal={modalVentaConfirmada}
            setModal={setModalVentaConfirmada}
            id={idVentaConfirmada}
            queryClient={queryClient}
          />
        )}
      </Row>
    </Container>
  );
};

export default Ventas;
