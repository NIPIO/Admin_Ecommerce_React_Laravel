import { useCaja } from "../../hooks/apiCalls";
import React, { useState } from "react";
import { Container, Card, CardHeader, CardBody } from "shards-react";
import { Table, Space, Spin, Row, Col, Button } from "antd";
import PageTitle from "../../components/common/PageTitle";
import Busqueda from "./Busqueda";
import ModalNuevaCaja from "./NuevaCaja";
import { showNotification } from "../notificacion";
import { useQueryClient } from "react-query";
import BlogOverview from "../../views/BlogOverview";

const CajaBille = () => {
  //INFO TABLA:
  const columnas = [
    {
      title: "Id",
      dataIndex: ["id"],
      width: "5%",
      render: text => text
    },
    {
      title: "Tipo Movimiento",
      dataIndex: ["tipo_movimiento"],
      render: text => text
    },
    {
      title: "Importe",
      dataIndex: ["importe"],
      render(text, record) {
        return {
          props: {
            style: {
              color: ["COMPRA", "PAGO", "GASTO"].includes(
                record.tipo_movimiento
              )
                ? "red"
                : "lightgreen"
            }
          },
          children: <div>$ {text.toLocaleString()}</div>
        };
      },
      sorter: (a, b) => a.importe - b.importe
    },
    {
      title: "Item",
      dataIndex: ["item_id"],
      render: text => text
    },
    {
      title: "Usuario",
      dataIndex: ["usuario", "nombre"],
      render: text => text
    },
    {
      title: "Observacion",
      dataIndex: ["observacion"],
      render: text => text
    },
    {
      title: "Fecha Mov.",
      dataIndex: ["created_at"],
      render: text => text
    }
  ];

  //FIN INFO TABLA.
  const tipoMovimientoObj = ["Ingresos", "Gasto", "Pago", "Cobro"];

  const [busqueda, setBusqueda] = useState({
    tipoMovimiento: null,
    fechas: null
  });
  const queryClient = useQueryClient();
  const [modal, setModal] = useState(false);

  const allCajaBille = useCaja(busqueda, "Bille");

  if (allCajaBille.isLoading) {
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
              title="Bille"
              subtitle="Caja"
              className="text-sm-left"
            />
          </Col>
          <Col span={8}>
            <Button onClick={() => setModal(true)} type="primary">
              Nuevo Movimiento
            </Button>
          </Col>
        </Space>
      </Row>
      <BlogOverview
        datosIniciales={allCajaBille.data.datosIniciales}
        mostrarColores={true}
      />
      <Row>
        <Col>
          <Card small className="mb-4">
            <CardHeader className="border-bottom">
              <Busqueda
                setBusqueda={setBusqueda}
                tipoMovimientoObj={tipoMovimientoObj}
              />
            </CardHeader>
            <CardBody className="p-0 pb-3">
              <Table
                rowKey="imiID"
                columns={columnas}
                scroll={{ x: 900, y: 450 }}
                dataSource={allCajaBille.data.cajaFiltro}
                pagination={{
                  defaultPageSize: 50,
                  showSizeChanger: true,
                  pageSizeOptions: ["5", "10", "15", "20", "30", "50"]
                }}
              />
            </CardBody>
          </Card>
        </Col>
        <ModalNuevaCaja
          modal={modal}
          setModal={setModal}
          tipoMovimientoObj={tipoMovimientoObj}
          showNotification={showNotification}
          queryClient={queryClient}
        />
      </Row>
    </Container>
  );
};

export default CajaBille;
