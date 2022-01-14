import { useClientes, useVendedores, useVentas } from "../../hooks/apiCalls";
import React, { useState } from "react";
import { Container, Card, CardHeader, CardBody } from "shards-react";
import { Table, Spin, Row, Col } from "antd";

import PageTitle from "../../components/common/PageTitle";
import "antd/dist/antd.css";
import { columnas } from "./utils";
import Busqueda from "./Busqueda";

const Ventas = () => {
  const [busqueda, setBusqueda] = useState({
    cliente: null,
    vendedor: null,
    fechas: null
  });

  const allVentas = useVentas(busqueda);
  const allClientes = useClientes({});
  const allVendedores = useVendedores({});

  if (allVentas.isLoading || allClientes.isLoading || allVendedores.isLoading) {
    return (
      <Spin tip="Cargando" style={{ width: "100%", margin: "10% auto" }}></Spin>
    );
  }

  return (
    <Container fluid className="main-content-container px-4">
      <Row noGutters className="page-header py-4">
        <PageTitle sm="4" title="Ventas" subtitle="" className="text-sm-left" />
      </Row>
      <Row>
        <Col>
          <Card small className="mb-4">
            <CardHeader className="border-bottom">
              <Busqueda
                setBusqueda={setBusqueda}
                clientes={allClientes.data.allClientes}
                vendedores={allVendedores.data.allVendedores}
              />
            </CardHeader>
            <CardBody className="p-0 pb-3">
              <Table
                rowKey="imiID"
                columns={columnas}
                scroll={{ x: 900, y: 450 }}
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
      </Row>
    </Container>
  );
};

export default Ventas;
