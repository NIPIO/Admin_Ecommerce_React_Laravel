import { useCompras, useProductos, useProveedores } from "../../hooks/apiCalls";
import React, { useState } from "react";
import { Container, Card, CardHeader, CardBody } from "shards-react";
import PageTitle from "../../components/common/PageTitle";
import { Table, Spin, Row, Col, Space, Button } from "antd";

import ModalNuevaCompra from "./ModalNuevaCompra";
import ModalConfirmarCompra from "./ModalConfirmarCompra";
import Busqueda from "./Busqueda";
import { useQueryClient } from "react-query";

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
      render: text => text
    },
    {
      title: "Fecha",
      dataIndex: ["created_at"],
      render: text => text
    },
    {
      title: "Acciones",
      key: "action",

      render: (text, value, id) => (
        <Space size="middle">
          <Button
            type="primary"
            onClick={() => {
              setModalCompraConfirmada(true);
              setIdCompraConfirmada(value.id);
            }}
            success={value.confirmada}
            disabled={value.confirmada}
          >
            {value.confirmada ? "Confirmada" : " Confirmar "}
          </Button>
          <Button onClick={() => edicion(text)} disabled>
            Editar (En desarrollo)
          </Button>
        </Space>
      )
    }
  ];
  //FIN INFO TABLA.

  const [busqueda, setBusqueda] = useState({
    provedor: null,
    producto: null
  });
  const queryClient = useQueryClient();
  const [idCompraConfirmada, setIdCompraConfirmada] = useState(null);
  const [modalCompraConfirmada, setModalCompraConfirmada] = useState(false);
  const [modal, setModal] = useState(false);
  const allProveedores = useProveedores({});
  const allProductos = useProductos({});
  const allCompras = useCompras(busqueda);

  const edicion = () => {
    setModal(true);
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
        <ModalConfirmarCompra
          modal={modalCompraConfirmada}
          setModal={setModalCompraConfirmada}
          id={idCompraConfirmada}
          queryClient={queryClient}
        />
      </Row>
    </Container>
  );
};

export default Compras;
