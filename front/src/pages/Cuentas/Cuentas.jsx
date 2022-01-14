import {
  useCuentas,
  useProveedores,
  useCambiarEstado
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
import ModalNuevaCuenta from "./ModalNuevaCuenta";

const Cuentas = () => {
  //INFO TABLA:
  const columnas = [
    {
      title: "Proveedor",
      dataIndex: ["proveedor", "nombre"],
      fixed: "left",
      render: text => text
    },
    {
      title: "Saldo",
      dataIndex: ["saldo"],
      render: text => text,
      sorter: (a, b) => a.precio - b.precio
    },
    {
      title: "Estado",
      dataIndex: ["activo"],
      render: (text, row) => (
        <Space>
          <Switch
            checked={text}
            onChange={() => toggleEstado("Cuenta", row.id, text)}
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

  const [busqueda, setBusqueda] = useState({
    proveedor: null
  });

  const [modal, setModal] = useState(false);
  const [cuentaEdicion, setCuentaEdicion] = useState(false);
  const allCuentas = useCuentas(busqueda);
  const allProveedores = useProveedores({});

  const openNotificationWithIcon = (type, message, description) => {
    notification[type]({
      message,
      description,
      placement: "bottomRight"
    });
  };

  const edicion = marca => {
    setCuentaEdicion(marca);
    setModal(true);
  };

  if (allCuentas.isLoading || allProveedores.isLoading) {
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
          openNotificationWithIcon={openNotificationWithIcon}
          cuentaEdicion={cuentaEdicion}
          setCuentaEdicion={setCuentaEdicion}
        />
      </Row>
    </Container>
  );
};

export default Cuentas;
