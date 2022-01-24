import { useClientes } from "../../hooks/apiCalls";
import React, { useState } from "react";
import { Container, Card, CardHeader, CardBody } from "shards-react";
import { Table, Space, Spin, Row, Col, Button, Switch } from "antd";
import PageTitle from "../../components/common/PageTitle";
import { showNotification, toggleEstado } from "./../notificacion";
import Busqueda from "./Busqueda";
import ModalNuevoCliente from "./ModalNuevoCliente";
import { useQueryClient } from "react-query";

const Clientes = () => {
  //INFO TABLA:
  const columnas = [
    {
      title: "Id",
      dataIndex: ["id"],
      width: "5%",
      render: text => text
    },
    {
      title: "Nombre",
      dataIndex: ["nombre"],
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
            onChange={() =>
              toggleEstado("clientes", "clientes", row.id, text, queryClient)
            }
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

  //FIN INFO TABLA.
  const queryClient = useQueryClient();
  const [busqueda, setBusqueda] = useState({
    cliente: null
  });
  const [modal, setModal] = useState(false);
  const [clienteEdicion, setClienteEdicion] = useState(false);
  const allClientes = useClientes(busqueda);

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
          showNotification={showNotification}
          clienteEdicion={clienteEdicion}
          setClienteEdicion={setClienteEdicion}
          queryClient={queryClient}
        />
      </Row>
    </Container>
  );
};

export default Clientes;
