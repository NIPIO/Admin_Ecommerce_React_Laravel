import { useCuentas, useProveedores, useClientes } from "../../hooks/apiCalls";
import React, { useState } from "react";
import { Container, Card, CardHeader, CardBody } from "shards-react";
import { Table, Space, Spin, Row, Col, Button, Switch } from "antd";
import PageTitle from "../../components/common/PageTitle";
import Busqueda from "./Busqueda";
import ModalNuevaCuenta from "./ModalNuevaCuenta";
import { showNotification, toggleEstado } from "./../notificacion";
import { useQueryClient } from "react-query";

const Cuentas = () => {
  //INFO TABLA:
  const columnas = [
    {
      title: "Id",
      dataIndex: ["id"],
      width: "5%",
      render: text => text
    },
    {
      title: "Proveedor",
      dataIndex: ["proveedor", "nombre"],
      render: text => (text ? text : "-")
    },
    {
      title: "Cliente",
      dataIndex: ["cliente", "nombre"],
      render: text => (text ? text : "-")
    },
    {
      title: "Saldo",
      dataIndex: ["saldo"],
      render(text, record) {
        return {
          props: {
            style: {
              color: text !== 0 ? (text > 0 ? "lightgreen" : "red") : null
            }
          },
          children: <div>$ {text.toLocaleString()}</div>
        };
      },
      sorter: (a, b) => a.precio - b.precio
    },
    {
      title: "Estado",
      dataIndex: ["activo"],
      render: (text, row) => (
        <Space>
          <Switch
            checked={text}
            onChange={() =>
              toggleEstado(
                "cuentas_corrientes",
                "cuentas",
                row.id,
                text,
                queryClient
              )
            }
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

  //FIN INFO TABLA.

  const [busqueda, setBusqueda] = useState({
    proveedor: null
  });
  const queryClient = useQueryClient();
  const [modal, setModal] = useState(false);
  const [cuentaEdicion, setCuentaEdicion] = useState(false);
  const allCuentas = useCuentas(busqueda);
  const allProveedores = useProveedores({});
  const allClientes = useClientes({});

  const edicion = marca => {
    setCuentaEdicion(marca);
    setModal(true);
  };

  if (
    allCuentas.isLoading ||
    allProveedores.isLoading ||
    allClientes.isLoading
  ) {
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
              title="Cuentas"
              subtitle=""
              className="text-sm-left"
            />
          </Col>
          <Col span={8}>
            <Button onClick={() => setModal(true)} type="primary">
              Nueva Cuenta
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
                clientes={allClientes.data.allClientes}
              />
            </CardHeader>
            <CardBody className="p-0 pb-3">
              <Table
                rowKey="imiID"
                columns={columnas}
                scroll={{ x: 900, y: 450 }}
                dataSource={allCuentas.data.cuentasFiltro}
                pagination={{
                  defaultPageSize: 10,
                  showSizeChanger: true,
                  pageSizeOptions: ["5", "10", "15", "20", "30", "50"]
                }}
              />
            </CardBody>
          </Card>
        </Col>
        <ModalNuevaCuenta
          modal={modal}
          setModal={setModal}
          proveedores={allProveedores.data.allProveedores}
          clientes={allClientes.data.allClientes}
          showNotification={showNotification}
          cuentaEdicion={cuentaEdicion}
          setCuentaEdicion={setCuentaEdicion}
          queryClient={queryClient}
        />
      </Row>
    </Container>
  );
};

export default Cuentas;
