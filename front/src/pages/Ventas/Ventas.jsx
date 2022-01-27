import {
  useClientes,
  useVendedores,
  useVentas,
  useProductos
} from "../../hooks/apiCalls";
import React, { useState } from "react";
import { Container, Card, CardHeader, CardBody } from "shards-react";
import { Table, Spin, Row, Col, Space, Button, Switch, Popconfirm } from "antd";
import PageTitle from "../../components/common/PageTitle";
import { toggleEstado } from "./../notificacion";
import ModalNuevaVenta from "./ModalNuevaVenta";
import ModalConfirmarVenta from "./ModalConfirmarVenta";
import Busqueda from "./Busqueda";
import { useQueryClient } from "react-query";

const Ventas = () => {
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
          <Button onClick={() => edicion(text)} disabled>
            Editar (En desarrollo)
          </Button>
        </Space>
      )
    }
  ];
  //FIN INFO TABLA.
  const queryClient = useQueryClient();
  const [busqueda, setBusqueda] = useState({
    cliente: null,
    vendedor: null,
    fechas: null
  });

  const [idVentaConfirmada, setIdVentaConfirmada] = useState(null);
  const [modalVentaConfirmada, setModalVentaConfirmada] = useState(false);
  const [modal, setModal] = useState(false);

  const allVentas = useVentas(busqueda);
  const allClientes = useClientes({});
  const allVendedores = useVendedores({});
  const allProductos = useProductos({});

  const edicion = () => {
    setModal(true);
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
