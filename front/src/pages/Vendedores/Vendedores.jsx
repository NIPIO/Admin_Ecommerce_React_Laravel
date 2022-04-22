import { useRoles, useVendedores, esAdmin } from "../../hooks/apiCalls";
import React, { useState } from "react";
import { Container, Card, CardHeader, CardBody } from "shards-react";
import { Table, Space, Spin, Row, Col, Button, Switch, Tag } from "antd";
import PageTitle from "../../components/common/PageTitle";

import Busqueda from "./Busqueda";
import ModalEdicionVendedor from "./ModalEdicionVendedor";
import { showNotification, toggleEstado } from "./../notificacion";
import { useQueryClient } from "react-query";

const Vendedores = () => {
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
      title: "Usuario",
      dataIndex: ["usuario"],
      render: text => text
    },
    {
      title: "Rol",
      dataIndex: ["rol"],
      key: "rol",
      render: rol => (
        <span>
          <Tag color={rol.id === 1 ? "black" : rol.id === 2 ? "green" : "red"}>
            {rol.nombre.toUpperCase()}
          </Tag>
        </span>
      )
    },
    {
      title: "Email",
      dataIndex: ["email"],
      render: text => text
    },
    {
      title: "Telefono",
      dataIndex: ["telefono"],
      render: text => text
    },
    {
      title: "Comision",
      dataIndex: ["comision"],
      sorter: (a, b) => a.comision - b.comision,
      render: text => (
        <Space
          size="middle"
          style={{ visibility: !esAdmin() ? "hidden" : "visible" }}
        >
          ${text}
        </Space>
      )
    },

    {
      title: "Estado",
      dataIndex: ["activo"],
      render: (text, row) => (
        <Space>
          <Switch
            checked={text}
            disabled={!esAdmin()}
            onChange={() =>
              toggleEstado(
                "vendedores",
                "vendedores",
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

      render: text => (
        <Space size="middle">
          <Button disabled={!esAdmin()} onClick={() => edicion(text)}>
            Editar
          </Button>
        </Space>
      )
    }
  ];

  //FIN INFO TABLA.
  const queryClient = useQueryClient();
  const [busqueda, setBusqueda] = useState({
    vendedor: null
  });
  const [modal, setModal] = useState(false);
  const [vendedorEdicion, setVendedorEdicion] = useState(false);
  const allVendedores = useVendedores(busqueda);
  const allRoles = useRoles({});

  const edicion = marca => {
    setVendedorEdicion(marca);
    setModal(true);
  };

  if (allVendedores.isLoading || allRoles.isLoading) {
    return (
      <Spin tip="Cargando" style={{ width: "100%", margin: "10% auto" }}></Spin>
    );
  }

  return (
    <Container fluid className="main-content-container px-4">
      <Row noGutters className="page-header py-4">
        <PageTitle
          sm="4"
          title="Vendedores"
          subtitle=""
          className="text-sm-left"
        />
      </Row>
      <Row>
        <Col>
          <Card small className="mb-4">
            <CardHeader className="border-bottom">
              <Busqueda
                setBusqueda={setBusqueda}
                vendedores={allVendedores.data.allVendedores}
              />
            </CardHeader>
            <CardBody className="p-0 pb-3">
              <Table
                rowKey="imiID"
                columns={columnas}
                scroll={{ x: 900, y: 450 }}
                dataSource={allVendedores.data.vendedoresFiltro}
                pagination={{
                  defaultPageSize: 50,
                  showSizeChanger: true,
                  pageSizeOptions: ["5", "10", "15", "20", "30", "50"]
                }}
              />
            </CardBody>
          </Card>
        </Col>
        <ModalEdicionVendedor
          modal={modal}
          setModal={setModal}
          showNotification={showNotification}
          vendedorEdicion={vendedorEdicion}
          setVendedorEdicion={setVendedorEdicion}
          queryClient={queryClient}
          roles={allRoles.data.allRoles}
        />
      </Row>
    </Container>
  );
};

export default Vendedores;
