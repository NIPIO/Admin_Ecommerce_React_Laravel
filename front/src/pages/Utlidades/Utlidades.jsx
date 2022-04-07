import React, { useState } from "react";
import { Container } from "shards-react";
import { Space, Spin, Row, Col } from "antd";
import PageTitle from "../../components/common/PageTitle";
import UtlidadesOverview from "../../views/UtlidadesOverview";
import { useUtilidades } from "../../hooks/apiCalls";

const Utlidades = () => {
  const [busqueda, setBusqueda] = useState({
    fechas: null
  });

  const allUtilidades = useUtilidades(busqueda);

  if (allUtilidades.isLoading) {
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
            <PageTitle sm="12" title="Utlidades" className="text-sm-left" />
          </Col>
        </Space>
      </Row>
      {/* <UtlidadesOverview allUtilidades={allUtilidades} /> */}
    </Container>
  );
};

export default Utlidades;
