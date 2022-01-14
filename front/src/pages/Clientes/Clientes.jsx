import { useCambiarEstado, useClientes } from "../../hooks/apiCalls";
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
import Busqueda from "./Busqueda";
import ModalNuevoCliente from "./ModalNuevoCliente";

const Clientes = () => {
  //INFO TABLA:
  const columnas = [
    {
      title: "Nombre",
      dataIndex: ["nombre"],
      fixed: "left",
      render: text => text
    },
    {
      title: "Telefono",
      dataIndex: ["telefono"],
      render: text => text
    },
    {
      title: "Email",
      dataIndex: ["email"],
      render: text => text
    },
    {
      title: "Estado",
      dataIndex: ["activo"],
      render: (text, row) => (
        <Space>
          <Switch
            checked={text}
            checkedChildren={"Activo"}
            onChange={() => toggleEstado("Cliente", row.id, text)}
            unCheckedChildren={"Inactivo"}
          />
        </Space>
      )
    },
    {
      title: "Acciones",
      key: "action",

      render: text => (
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

  const [busqueda, setBusqueda] = useState({
    cliente: null
  });
  const [modal, setModal] = useState(false);
  const [clienteEdicion, setClienteEdicion] = useState(false);
  const allClientes = useClientes(busqueda);

  const openNotificationWithIcon = (type, message, description) => {
    notification[type]({
      message,
      description,
      placement: "bottomRight"
    });
  };

  const edicion = marca => {
    setClienteEdicion(marca);
    setModal(true);
  };

  if (allClientes.isLoading) {
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
              title="Clientes"
              subtitle=""
              className="text-sm-left"
            />
          </Col>
          <Col span={8}>
            <Button onClick={() => setModal(true)} type="primary">
              Nuevo Cliente
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
              />
            </CardHeader>
            <CardBody className="p-0 pb-3">
              <Table
                rowKey="imiID"
                columns={columnas}
                scroll={{ x: 900, y: 450 }}
                dataSource={allClientes.data.clientesFiltro}
                pagination={{
                  defaultPageSize: 10,
                  showSizeChanger: true,
                  pageSizeOptions: ["5", "10", "15", "20", "30", "50"]
                }}
              />
            </CardBody>
          </Card>
        </Col>
        <ModalNuevoCliente
          modal={modal}
          setModal={setModal}
          openNotificationWithIcon={openNotificationWithIcon}
          clienteEdicion={clienteEdicion}
          setClienteEdicion={setClienteEdicion}
        />
      </Row>
    </Container>
  );
};

export default Clientes;
