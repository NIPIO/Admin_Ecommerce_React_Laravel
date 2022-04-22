import React, { useEffect, useState } from "react";
import { Row, Modal, Col, Table } from "antd";
import { Container, Card, CardBody } from "shards-react";

import { api } from "./../../hooks/api";

const ModalHistorialCuenta = ({
  cuenta,
  modalHistorial,
  setModalHistorial
}) => {
  const [cuentaDetalle, setCuentaDetalle] = useState();

  useEffect(() => {
    api.getHistorialCuentaCorriente(cuenta).then(res => setCuentaDetalle(res));
  }, [cuenta]);

  const columnas = [
    {
      title: "Movimiento",
      dataIndex: ["tipo_movimiento"],
      render: text => (text === "CONFIRMACION" ? "COMPRA" : text)
    },
    {
      title: "Cantidad",
      dataIndex: ["diferencia"],
      render: text => `$ ${text}`
    },
    {
      title: "Fecha",
      dataIndex: ["updated_at"],
      render: text => (text ? text : "-")
    }
  ];

  return (
    <Container fluid className="main-content-container px-4">
      <Row className="page-header py-4">
        <Modal
          width={700}
          visible={modalHistorial}
          title="Historial cuenta"
          cancelText="Cancelar"
          onCancel={() => setModalHistorial(false)}
        >
          <Col>
            <Card small className="mb-4">
              <CardBody className="p-0 pb-3">
                <Table
                  rowKey="imiID"
                  columns={columnas}
                  scroll={{ x: 900, y: 450 }}
                  dataSource={cuentaDetalle}
                  pagination={{
                    defaultPageSize: 50,
                    showSizeChanger: true,
                    pageSizeOptions: ["5", "10", "15", "20", "30", "50"]
                  }}
                />
              </CardBody>
            </Card>
          </Col>
        </Modal>
      </Row>
    </Container>
  );
};

export default ModalHistorialCuenta;
