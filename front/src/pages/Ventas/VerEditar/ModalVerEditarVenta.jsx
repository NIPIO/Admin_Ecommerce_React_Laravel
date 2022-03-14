import React, { useEffect, useState } from "react";
import { Container } from "shards-react";
import { Form, Row, Modal, Col, Alert, Select } from "antd";
import VerTablaItemsVenta from "../VerEditar/VerTablaItemsVenta";
import { showNotification } from "../../notificacion";

import { api } from "../../../hooks/api";
import { useQueryClient } from "react-query";

const { Option } = Select;

const ModalVerEditarVenta = ({
  modal,
  setModal,
  verVenta,
  setVerVenta,
  clientes,
  productos,
  vendedores
}) => {
  const [form] = Form.useForm();
  const queryClient = useQueryClient();
  const [editarVenta, setEditarVenta] = useState(false);
  const [filas, setFilas] = useState([]);
  const [error, setError] = useState(false);
  const [mostrarDetalles, setMostrarDetalles] = useState(false);
  const [cliente, setCliente] = useState(false);
  const [vendedor, setVendedor] = useState(false);

  const onCreate = () => {
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
        .putVenta(verVenta.id, filas, cliente, vendedor)
        .then(res => {
          if (res.error) {
            showNotification("error", "Ocurrio un error", res.data);
          } else {
            showNotification("success", "Venta modificada", "");
            queryClient.invalidateQueries("ventas");
            setModal(false);
            setEditarVenta(false);
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

  const activarEdicion = () => {
    if (!editarVenta) {
      setEditarVenta(true);
    }
  };

  useEffect(() => {
    let venta = api.getVenta(verVenta.id);
    venta.then(res => {
      form.setFieldsValue({
        cliente: res.venta[0].cliente_id,
        vendedor: res.venta[0].vendedor_id
      });
      setMostrarDetalles(true);
      setCliente(res.venta[0].cliente_id);
      setVendedor(res.venta[0].vendedor_id);
      setFilas(res.venta[0].detalle_venta);
    });
  }, [modal]);

  return (
    <Container fluid className="main-content-container px-4">
      <Row className="page-header py-4">
        <Modal
          width={800}
          visible={modal}
          title="Ver venta"
          okText={editarVenta ? "Guardar" : "Editar"}
          cancelText={editarVenta ? "Cancelar" : "Cerrar"}
          onCancel={() => {
            setModal(false);
            setEditarVenta(false);
            setVerVenta(false);
          }}
          okButtonProps={{
            style: { display: verVenta.confirmada ? "none" : "inline" }
          }}
          onOk={() => (!editarVenta ? activarEdicion() : onCreate())}
        >
          <Form form={form} layout="vertical" name="form_in_modal">
            <Row gutter={24}>
              <Col xs={24} md={12}>
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
                    disabled={!editarVenta}
                  >
                    {clientes.map((cliente, idx) => (
                      <Option key={idx} value={cliente.id}>
                        {cliente.nombre}
                      </Option>
                    ))}
                  </Select>
                </Form.Item>
              </Col>
              <Col xs={24} md={12}>
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
                    disabled={!editarVenta}
                  >
                    {vendedores.map((vendedor, idx) => (
                      <Option key={idx} value={vendedor.id}>
                        {vendedor.nombre}
                      </Option>
                    ))}
                  </Select>
                </Form.Item>
              </Col>
            </Row>
            {mostrarDetalles && (
              <Row gutter={24}>
                <VerTablaItemsVenta
                  setError={setError}
                  filas={filas}
                  setFilas={setFilas}
                  editarVenta={editarVenta}
                  productos={productos}
                />
              </Row>
            )}
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

export default ModalVerEditarVenta;
