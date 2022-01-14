import React, { useEffect, useState } from "react";
import { Container } from "shards-react";
import { Row, Modal, Spin } from "antd";
import { api } from "../../hooks/api";
import { useQuery } from "react-query";

const ModalConfirmarCompra = ({ modal, setModal, id }) => {
  const confirmarCompra = () => {
    setModal(false);
  };

  const detallesCompra = useQuery(["compraId", id], () => {
    if (id !== null) return api.getCompra(id);
  });

  if (detallesCompra.isLoading) {
    return (
      <Spin tip="Cargando" style={{ width: "100%", margin: "10% auto" }}></Spin>
    );
  }

  return (
    <Container fluid className="main-content-container px-4">
      <Row className="page-header py-4">
        <Modal
          width={800}
          visible={modal}
          title={"Confirmar compra"}
          okText={"Confirmar"}
          cancelText="Cancelar"
          onCancel={() => setModal(false)}
          onOk={() => confirmarCompra()}
        >
          ID {JSON.stringify(detallesCompra.data)}
        </Modal>
      </Row>
    </Container>
  );
};

export default ModalConfirmarCompra;
