import { useCambiarEstado, useProveedores } from "../../hooks/apiCalls";
import React, { useState } from "react";
import { Container, Card, CardHeader, CardBody } from "shards-react";
import {
  Table,
  Space,
  Spin,
  Row,
  Col,
  Button,
  Switch,
  notification
} from "antd";

import PageTitle from "../../components/common/PageTitle";
import "antd/dist/antd.css";
import ModalNuevoProveedor from "./ModalNuevoProveedor";
import Busqueda from "./Busqueda";
const Proveedores = () => {
  //INFO TABLA:
  const columnas = [
    {
      title: "Nombre",
      dataIndex: ["nombre"],
      fixed: "left",
      render: text => text
    },

    {
      title: "Estado",
      dataIndex: ["activo"],
      render: (text, row) => (
        <Space>
          <Switch
            checked={text}
            onChange={() => toggleEstado("Proveedor", row.id, text)}
            checkedChildren={"Activo"}
            unCheckedChildren={"Inactivo"}
          />
        </Space>
      )
    },
    {
      title: "Acciones",
      key: "action",

      render: (text, record) => (
        <Space size="middle">
          <Button onClick={() => edicion(text)}>Editar</Button>
        </Space>
      )
    }
  ];

  const toggleEstado = (tabla, id, estado) => {
    useCambiarEstado(tabla, id, estado)
      .then(res => {
        if (res.error) {
          openNotificationWithIcon("error", "Ocurrio un error", res.data);
        } else {
          openNotificationWithIcon("success", "Cambio realizado!", "");
        }
      })
      .catch(err => {
        openNotificationWithIcon(
          "error",
          "Ocurrio un error",
          err.response.data.message
        );
      });
  };
  //FIN INFO TABLA.
  const [modal, setModal] = useState(false);
  const [busqueda, setBusqueda] = useState({
    proveedor: null
  });
  const [proveedorEdicion, setProveedorEdicion] = useState(false);
  const allProveedores = useProveedores(busqueda);

  const openNotificationWithIcon = (type, message, description) => {
    notification[type]({
      message,
      description,
      placement: "bottomRight"
    });
  };

  const edicion = marca => {
    setProveedorEdicion(marca);
    setModal(true);
  };

  if (allProveedores.isLoading) {
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
              title="Proveedores"
              subtitle=""
              className="text-sm-left"
            />
          </Col>
          <Col span={8}>
            <Button onClick={() => setModal(true)} type="primary">
              Nuevo Proveedor
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
              />
            </CardHeader>
            <CardBody className="p-0 pb-3">
              <Table
                rowKey="imiID"
                columns={columnas}
                scroll={{ x: 700, y: 450 }}
                dataSource={allProveedores.data.proveedoresFiltro}
                pagination={{
                  defaultPageSize: 10,
                  showSizeChanger: true,
                  pageSizeOptions: ["5", "10", "15", "20", "30", "50"]
                }}
              />
            </CardBody>
          </Card>
        </Col>
        <ModalNuevoProveedor
          modal={modal}
          setModal={setModal}
          openNotificationWithIcon={openNotificationWithIcon}
          proveedorEdicion={proveedorEdicion}
          setProveedorEdicion={setProveedorEdicion}
        />
      </Row>
    </Container>
  );
};

export default Proveedores;
