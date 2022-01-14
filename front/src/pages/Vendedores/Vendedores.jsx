import { useCambiarEstado, useVendedores } from "../../hooks/apiCalls";
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
import ModalEdicionVendedor from "./ModalEdicionVendedor";

const Vendedores = () => {
  //INFO TABLA:
  const columnas = [
    {
      title: "Nombre",
      dataIndex: ["nombre"],
      fixed: "left",
      render: text => text
    },
    {
      title: "Usuario",
      dataIndex: ["usuario"],
      render: text => text
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
      render: text => text
    },

    {
      title: "Estado",
      dataIndex: ["activo"],
      render: (text, row) => (
        <Space>
          <Switch
            checked={text}
            onChange={() => toggleEstado("Vendedor", row.id, text)}
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
    vendedor: null
  });
  const [modal, setModal] = useState(false);
  const [vendedorEdicion, setVendedorEdicion] = useState(false);
  const allVendedores = useVendedores(busqueda);

  const openNotificationWithIcon = (type, message, description) => {
    notification[type]({
      message,
      description,
      placement: "bottomRight"
    });
  };

  const edicion = marca => {
    setVendedorEdicion(marca);
    setModal(true);
  };

  if (allVendedores.isLoading) {
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
                  defaultPageSize: 10,
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
          openNotificationWithIcon={openNotificationWithIcon}
          vendedorEdicion={vendedorEdicion}
          setVendedorEdicion={setVendedorEdicion}
        />
      </Row>
    </Container>
  );
};

export default Vendedores;
