import React, { useState } from "react";
import { Container } from "shards-react";
import { Form, Row, Modal, Col, Alert, Select } from "antd";
import TablaItemsCompra from "./TablaItemsCompra";
import { showNotification } from "./../notificacion";

import { api } from "../../hooks/api";

const { Option } = Select;

const ModalNuevaCompra = ({
  modal,
  setModal,
  compraEdicion,
  proveedores,
  productos,
  queryClient
}) => {
  const [filas, setFilas] = useState([]);
  const [error, setError] = useState(false);

  const [proveedor, setProveedor] = useState(false);

  const onCreate = (filas, proveedor) => {
    if (filas.length < 1 || !proveedor) {
      setError("Falta Proveedor o productos");
    } else if (
      filas.some(prod => {
        return (
          prod.cantidad === "" ||
          prod.cantidad < 1 ||
          prod.producto === undefined ||
          prod.precioUnitario === ""
        );
      })
    ) {
      setError("Productos incompletos");
    } else {
      setError(false);
      api
        .setNuevaCompra(filas, proveedor)
        .then(res => {
          if (res.error) {
            showNotification("error", "Ocurrio un error", res.data);
          } else {
            showNotification("success", "Compra alteada", "");
            queryClient.invalidateQueries("compras");
            setModal(false);
          }
        })
        .catch(err => {
          showNotification(
            "error",
            "Ocurrio un error",
            err.response.data.message
          );
        });
    }
  };

  return (
    <Container fluid className="main-content-container px-4">
      <Row className="page-header py-4">
        <Modal
          width={800}
          visible={modal}
          title={(compraEdicion ? "Editar" : "Nueva") + " Compra"}
          okText={compraEdicion ? "Editar" : "Crear"}
          cancelText="Cancelar"
          onCancel={() => setModal(false)}
          onOk={() => onCreate(filas, proveedor)}
        >
          <Form layout="vertical" name="form_in_modal">
            <Row gutter={24}>
              <Col xs={24} md={12}>
                <Form.Item name="proveedor" label="Proveedor">
                  <Select
                    showSearch
                    allowClear
                    style={{ width: 200 }}
                    onChange={e => {
                      setProveedor(e);
                      setError(false);
                    }}
                    placeholder="Elegí el proveedor"
                    optionFilterProp="children"
                    filterOption={(input, option) =>
                      option.children
                        .toLowerCase()
                        .indexOf(input.toLowerCase()) >= 0
                    }
                    filterSort={(optionA, optionB) =>
                      optionA.children
                        .toLowerCase()
                        .localeCompare(optionB.children.toLowerCase())
                    }
                  >
                    {proveedores.map((proveedor, idx) => (
                      <Option key={idx} value={proveedor.id}>
                        {proveedor.nombre}
                      </Option>
                    ))}
                  </Select>
                </Form.Item>
              </Col>
            </Row>
            <Row gutter={24}>
              {proveedor && (
                <TablaItemsCompra
                  setError={setError}
                  filas={filas}
                  setFilas={setFilas}
                  productos={productos}
                />
              )}
            </Row>
          </Form>
          {error && (
            <Alert
              message="Error"
              closable
              description={error}
              type="error"
              showIcon
            />
          )}
        </Modal>
      </Row>
    </Container>
  );
};

export default ModalNuevaCompra;
