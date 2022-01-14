import { useCambiarEstado, useMarcas } from "../../hooks/apiCalls";
import React, { useState } from "react";
import { Container, Card, CardHeader, CardBody } from "shards-react";
import PageTitle from "../../components/common/PageTitle";
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
import "antd/dist/antd.css";
import Busqueda from "./Busqueda";
import ModalNuevaMarca from "./ModalNuevaMarca";

const Marcas = () => {
  //INFO TABLA:
  const columnas = [
    {
      title: "Nombre",
      dataIndex: ["nombre"],
      render: text => text
    },
    {
      title: "Cantidad",
      dataIndex: ["stock"],
      sorter: (a, b) => a.stock - b.stock,
      render: text => text
    },
    {
      title: "En transito",
      dataIndex: ["en_transito"],
      sorter: (a, b) => a.en_transito - b.en_transito,
      render: text => text
    },
    {
      title: "Estado",
      dataIndex: ["activo"],
      render: (text, row) => (
        <Space>
          <Switch
            checked={text}
            onChange={() => toggleEstado("Marca", row.id, text)}
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
          <Button onClick={() => edicion(text, record)}>Editar</Button>
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
    marca: null
  });
  const [marcaEdicion, setMarcaEdicion] = useState(false);
  const [modal, setModal] = useState(false);
  const allMarcas = useMarcas(busqueda);

  const openNotificationWithIcon = (type, message, description) => {
    notification[type]({
      message,
      description,
      placement: "bottomRight"
    });
  };

  const edicion = marca => {
    setMarcaEdicion(marca);
    setModal(true);
  };

  if (allMarcas.isLoading) {
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
              title="Marcas"
              subtitle=""
              className="text-sm-left"
            />
          </Col>
          <Col span={8}>
            <Button onClick={() => setModal(true)} type="primary">
              Nueva Marca
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
                marcas={allMarcas.data.allMarcas}
              />
            </CardHeader>
            <CardBody className="p-0 pb-3">
              <Table
                rowKey="id"
                columns={columnas}
                scroll={{ x: 900, y: 300 }}
                dataSource={allMarcas.data.marcasFiltro}
                pagination={{
                  defaultPageSize: 10,
                  showSizeChanger: true,
                  pageSizeOptions: ["5", "10", "15", "20", "30", "50"]
                }}
              />
            </CardBody>
          </Card>
        </Col>
        <ModalNuevaMarca
          modal={modal}
          setModal={setModal}
          openNotificationWithIcon={openNotificationWithIcon}
          marcaEdicion={marcaEdicion}
          setMarcaEdicion={setMarcaEdicion}
        />
      </Row>
    </Container>
  );
};

export default Marcas;
