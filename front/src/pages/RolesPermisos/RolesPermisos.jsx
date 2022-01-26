import { useRoles, usePermisos } from "../../hooks/apiCalls";
import React, { useState } from "react";
import { Container, Card, CardHeader, CardBody } from "shards-react";
import { Table, Space, Spin, Row, Col, Button } from "antd";
import { showNotification } from "./../notificacion";

import PageTitle from "../../components/common/PageTitle";
import { useQueryClient } from "react-query";
import ModalNuevoRol from "./ModalNuevoRol";
import Busqueda from "./Busqueda";
const RolesPermisos = () => {
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
      title: "Descripcion",
      dataIndex: ["descripcion"],
      render: text => text
    },

    {
      title: "Acciones",
      key: "action",
      width: "18%",
      render: (text, record) => (
        <Space size="middle">
          <Button disabled onClick={() => edicion(text)}>
            Editar
          </Button>
        </Space>
      )
    }
  ];

  //FIN INFO TABLA.
  const queryClient = useQueryClient();
  const [modal, setModal] = useState(false);
  const [busqueda, setBusqueda] = useState({
    rol: null
  });
  const [rolEdicion, setRolEdicion] = useState(false);
  const allRoles = useRoles(busqueda);
  const allPermisos = usePermisos();

  const edicion = rol => {
    setRolEdicion(rol);
    setModal(true);
  };

  if (allRoles.isLoading || allPermisos.isLoading) {
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
              title="Roles"
              subtitle=""
              className="text-sm-left"
            />
          </Col>
          <Col span={8}>
            <Button onClick={() => setModal(true)} type="primary">
              Nuevo Rol
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
                permisos={allPermisos.data.allPermisos}
                roles={allRoles.data.allRoles}
              />
            </CardHeader>
            <CardBody className="p-0 pb-3">
              <Table
                rowKey="imiID"
                columns={columnas}
                scroll={{ x: 700, y: 450 }}
                dataSource={allRoles.data.rolesFiltro}
                pagination={{
                  defaultPageSize: 10,
                  showSizeChanger: true,
                  pageSizeOptions: ["5", "10", "15", "20", "30", "50"]
                }}
              />
            </CardBody>
          </Card>
        </Col>
        <ModalNuevoRol
          modal={modal}
          setModal={setModal}
          showNotification={showNotification}
          rolEdicion={rolEdicion}
          setRolEdicion={setRolEdicion}
          permisos={allPermisos.data.allPermisos}
          queryClient={queryClient}
        />
      </Row>
    </Container>
  );
};

export default RolesPermisos;
