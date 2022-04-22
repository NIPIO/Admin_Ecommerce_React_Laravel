import { useMovimientos, useVendedores } from "../../hooks/apiCalls";
import React, { useState } from "react";
import { Container, Card, CardHeader, CardBody } from "shards-react";
import { Table, Space, Spin, Row, Col } from "antd";
import PageTitle from "../../components/common/PageTitle";
import Busqueda from "./Busqueda";
import BlogOverview from "../../views/BlogOverview";

const General = () => {
  //INFO TABLA:
  const columnas = [
    {
      title: "Nro",
      dataIndex: ["id"],
      render: text => text
    },
    {
      title: "Sección",
      dataIndex: ["tabla"],
      render: text => text
    },
    {
      title: "Tipo Mov.",
      dataIndex: ["tipo_movimiento"],
      render: text => text
    },
    {
      title: "Id de sección",
      dataIndex: ["item_id"],
      render: text => text
    },
    {
      title: "Usuario",
      dataIndex: ["usuario", "nombre"],
      render: text => text
    },
    {
      title: "Estado Anterior",
      dataIndex: ["estado_viejo"],
      render: text => text
    },
    {
      title: "Estado Posterior",
      dataIndex: ["estado_nuevo"],
      render: text => text
    },
    {
      title: "Diferencia",
      dataIndex: ["diferencia"],
      render: text => text
    },
    {
      title: "Campo Modificado",
      dataIndex: ["campo_modificado"],
      render: text => text
    },
    {
      title: "Fecha Mov.",
      dataIndex: ["created_at"],
      render: text => text
    }
  ];

  //FIN INFO TABLA.
  const [busqueda, setBusqueda] = useState({
    fechas: null,
    tipoMovimiento: null,
    seccion: null
  });
  const allMovimientos = useMovimientos(busqueda);
  const allVendedores = useVendedores({});

  if (allMovimientos.isLoading || allVendedores.isLoading) {
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
          <Col span={24}>
            <PageTitle sm="12" title="General" className="text-sm-left" />
          </Col>
        </Space>
      </Row>
      <BlogOverview
        datosIniciales={allMovimientos.data.datosIniciales}
        mostrarColores={false}
      />
      <Row>
        <Col>
          <Card small className="mb-4">
            <CardHeader className="border-bottom">
              <Busqueda
                setBusqueda={setBusqueda}
                allVendedores={allVendedores.data.allVendedores}
              />
            </CardHeader>
            <CardBody className="p-0 pb-3">
              <Table
                rowKey="imiID"
                columns={columnas}
                scroll={{ x: 900, y: 450 }}
                dataSource={allMovimientos.data.movimientosFiltro}
                pagination={{
                  defaultPageSize: 50,
                  showSizeChanger: true,
                  pageSizeOptions: ["5", "10", "15", "20", "30", "50"]
                }}
              />
            </CardBody>
          </Card>
        </Col>
      </Row>
    </Container>
  );
};

export default General;
