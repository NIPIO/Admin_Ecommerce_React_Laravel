import {
  useCambiarEstado,
  useMarcas,
  useProductos
} from "../../hooks/apiCalls";
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
import ModalNuevoProducto from "./ModalNuevoProducto";

const Productos = () => {
  //INFO TABLA:
  const columnas = [
    {
      title: "Nombre",
      dataIndex: ["nombre"],
      fixed: "left",
      render: text => text
    },
    {
      title: "Marca",
      dataIndex: ["marcas", "nombre"],
      render: text => text
    },
    {
      title: "Precio",
      dataIndex: ["precio"],
      render: text => text,
      sorter: (a, b) => a.precio - b.precio
    },
    {
      title: "Costo",
      dataIndex: ["costo"],
      render: text => text,
      sorter: (a, b) => a.costo - b.costo
    },
    {
      title: "Stock",
      dataIndex: ["stock"],
      render: text => text,
      sorter: (a, b) => a.stock - b.stock
    },
    {
      title: "Reservado",
      dataIndex: ["stock_reservado"],
      render: text => text,
      sorter: (a, b) => a.stock_reservado - b.stock_reservado
    },
    {
      title: "En Transito",
      dataIndex: ["en_transito"],
      render: text => text,
      sorter: (a, b) => a.en_transito - b.en_transito
    },
    {
      title: "ET Reservado",
      dataIndex: ["en_transito_reservado"],
      render: text => text,
      sorter: (a, b) => a.en_transito_reservado - b.en_transito_reservado
    },
    {
      title: "Estado",
      dataIndex: ["activo"],
      render: (text, row) => (
        <Space>
          <Switch
            checked={text}
            onChange={() => toggleEstado("Producto", row.id, text)}
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
  //FIN INFO TABLA.

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

  const [busqueda, setBusqueda] = useState({
    producto: null,
    marca: null
  });

  const [productoEdicion, setProductoEdicion] = useState(false);
  const [modal, setModal] = useState(false);
  const allMarcas = useMarcas({});
  const allProductos = useProductos(busqueda);

  const openNotificationWithIcon = (type, message, description) => {
    notification[type]({
      message,
      description,
      placement: "bottomRight"
    });
  };

  const edicion = producto => {
    setProductoEdicion(producto);
    setModal(true);
  };

  if (allProductos.isLoading || allMarcas.isLoading) {
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
              title="Productos"
              subtitle=""
              className="text-sm-left"
            />
          </Col>
          <Col span={8}>
            <Button onClick={() => setModal(true)} type="primary">
              Nuevo Producto
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
                productos={allProductos.data.allProductos}
              />
            </CardHeader>
            <CardBody className="p-0 pb-3">
              <Table
                rowKey="imiID"
                columns={columnas}
                scroll={{ x: 900, y: 450 }}
                dataSource={allProductos.data.productosFiltro}
                pagination={{
                  defaultPageSize: 10,
                  showSizeChanger: true,
                  pageSizeOptions: ["5", "10", "15", "20", "30", "50"]
                }}
              />
            </CardBody>
          </Card>
        </Col>
        <ModalNuevoProducto
          modal={modal}
          setModal={setModal}
          marcas={allMarcas.data.allMarcas}
          openNotificationWithIcon={openNotificationWithIcon}
          productoEdicion={productoEdicion}
          setProductoEdicion={setProductoEdicion}
        />
      </Row>
    </Container>
  );
};

export default Productos;
