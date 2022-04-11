import React, { useState } from "react";
import { Container } from "shards-react";
import { Form, Row, Modal, Col, Alert, Select } from "antd";
import TablaItemsVenta from "./TablaItemsVenta";
import { showNotification } from "../../notificacion";

import { api } from "../../../hooks/api";

const { Option } = Select;

const ModalNuevaVenta = ({
  modal,
  setModal,
  vendedores,
  clientes,
  productos,
  queryClient
}) => {
  const [filas, setFilas] = useState([]);
  const [error, setError] = useState(false);
  const [cliente, setCliente] = useState(false);
  const [vendedor, setVendedor] = useState(false);
  const [tipoVenta, setTipoVenta] = useState(false);
  const [tipoStock, setTipoStock] = useState(false);

  const onCreate = (filas, cliente) => {
    if (filas.length < 1 || !cliente || !vendedor) {
      setError("Falta cliente o productos");
    } else if (
      filas.some(prod => {
        return (
          prod.cantidad === "" ||
          prod.producto === undefined ||
          prod.precioUnitario === ""
        );
      })
    ) {
      setError("Productos incompletos");
    } else {
      setError(false);
      api
        .setNuevaVenta(filas, cliente, vendedor, tipoVenta, tipoStock)
        .then(res => {
          if (res.error) {
            showNotification("error", "Ocurrio un error", res.data);
          } else {
            showNotification("success", "Venta alteada", "");
            queryClient.invalidateQueries("ventas");
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
          title="Nueva Venta"
          okText="Crear"
          cancelText="Cancelar"
          onCancel={() => setModal(false)}
          onOk={() => onCreate(filas, cliente)}
        >
          <Form layout="vertical" name="form_in_modal">
            <Row gutter={24}>
              <Col xs={24} md={7}>
                <Form.Item name="cliente" label="Cliente">
                  <Select
                    showSearch
                    allowClear
                    style={{ marginBottom: "3%", width: "100%" }}
                    onChange={e => {
                      setCliente(e);
                      setError(false);
                    }}
                    placeholder="Elegí el cliente"
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
                    {clientes.map((cliente, idx) => (
                      <Option key={idx} value={cliente.id}>
                        {cliente.nombre}
                      </Option>
                    ))}
                  </Select>
                </Form.Item>
              </Col>
              <Col xs={24} md={7}>
                <Form.Item name="vendedor" label="Vendedor">
                  <Select
                    showSearch
                    allowClear
                    style={{ marginBottom: "3%", width: "100%" }}
                    onChange={e => {
                      setVendedor(e);
                      setError(false);
                    }}
                    placeholder="Elegí el vendedor"
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
                    {vendedores.map((vendedor, idx) => (
                      <Option key={idx} value={vendedor.id}>
                        {vendedor.nombre}
                      </Option>
                    ))}
                  </Select>
                </Form.Item>
              </Col>

              <Col xs={24} md={5}>
                <Form.Item name="tipoVenta" label="Tipo Venta">
                  <Select
                    style={{ marginBottom: "3%", width: "100%" }}
                    onChange={e => {
                      setTipoVenta(e);
                      setError(false);
                    }}
                    placeholder="Elegí el tipo de venta"
                    optionFilterProp="children"
                  >
                    {["Minorista", "Mayorista"].map((tv, idx) => (
                      <Option key={idx} value={tv}>
                        {tv}
                      </Option>
                    ))}
                  </Select>
                </Form.Item>
              </Col>

              <Col xs={24} md={5}>
                <Form.Item name="tipoStock" label="Tipo Stock">
                  <Select
                    style={{ marginBottom: "3%", width: "100%" }}
                    onChange={e => {
                      setTipoStock(e);
                      setError(false);
                    }}
                    placeholder="Elegí el tipo de stock"
                    optionFilterProp="children"
                  >
                    {["Fisico", "En transito"].map((ts, idx) => (
                      <Option key={idx} value={ts}>
                        {ts}
                      </Option>
                    ))}
                  </Select>
                </Form.Item>
              </Col>
            </Row>
            <Row gutter={24}>
              {cliente && vendedor && tipoVenta && tipoStock && (
                <TablaItemsVenta
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

export default ModalNuevaVenta;
