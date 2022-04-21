import React, { useState } from "react";
import { Container } from "shards-react";
import { Space, Spin, Row, Col, DatePicker } from "antd";
import PageTitle from "../../components/common/PageTitle";
import UtilidadesOverview from "../../views/UtilidadesOverview";
import { api } from "../../hooks/api";

const Utilidades = () => {
  const [cargando, setCargando] = useState(false);
  const [utilidades, setUtilidades] = useState([]);

  const traerUtilidades = mes => {
    setCargando(true);
    api
      .getUtilidades({ mes })
      .then(res => setUtilidades(res))
      .finally(() => setCargando(false));
  };

  return (
    <Container fluid className="main-content-container px-4">
      <Row className="page-header py-4">
        <Space
          direction="horizontal"
          style={{ width: "100%", justifyContent: "space-between" }}
        >
          <Col span={24}>
            <PageTitle sm="12" title="Utilidades" className="text-sm-left" />
          </Col>
        </Space>
      </Row>
      <Row>
        <DatePicker
          onChange={e => traerUtilidades(e.month() + 1)}
          picker="month"
          placeholder="Seleccioná mes"
        />
      </Row>
      {cargando ? (
        <Spin
          tip="Cargando"
          style={{ width: "100%", margin: "10% auto" }}
        ></Spin>
      ) : (
        <UtilidadesOverview allUtilidades={utilidades} />
      )}
    </Container>
  );
};

export default Utilidades;
