import React, { useEffect, useState } from "react";
import { Container } from "shards-react";
import { Form, Row, Modal, Col, Alert, Select } from "antd";
import VerTablaItemsCompra from "./VerTablaItemsCompra";
import { showNotification } from "../../notificacion";

import { api } from "../../../hooks/api";
import { useQueryClient } from "react-query";

const { Option } = Select;

const ModalVerEditarCompra = ({
  modal,
  setModal,
  verCompra,
  setVerCompra,
  productos,
  proveedores
}) => {
  const [form] = Form.useForm();
  const queryClient = useQueryClient();
  const [editarCompra, setEditarCompra] = useState(false);
  const [filas, setFilas] = useState([]);
  const [error, setError] = useState(false);
  const [mostrarDetalles, setMostrarDetalles] = useState(false);
  const [proveedor, setProveedor] = useState(false);

  const onCreate = () => {
    if (filas.length < 1 || !proveedor) {
      setError("Falta proveedor o productos");
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
        .putCompra(verCompra.id, filas, proveedor)
        .then(res => {
          if (res.error) {
            showNotification("error", "Ocurrio un error", res.data);
          } else {
            showNotification("success", "Compra modificada", "");
            queryClient.invalidateQueries("compras");
            setModal(false);
            setEditarCompra(false);
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
    if (!editarCompra) {
      setEditarCompra(true);
    }
  };

  useEffect(() => {
    let compra = api.getCompra(verCompra.id);
    compra.then(res => {
      form.setFieldsValue({
        proveedor: res.compra[0].proveedor_id
      });
      setMostrarDetalles(true);
      setProveedor(res.compra[0].proveedor_id);
      setFilas(res.compra[0].detalle_compra);
    });
  }, [verCompra.id]);

  return (
    <Container fluid className="main-content-container px-4">
      <Row className="page-header py-4">
        <Modal
          width={800}
          visible={modal}
          title="Ver compra"
          okText={editarCompra ? "Guardar" : "Editar"}
          cancelText={editarCompra ? "Cancelar" : "Cerrar"}
          onCancel={() => {
            setModal(false);
            setEditarCompra(false);
            setVerCompra(false);
          }}
          okButtonProps={{
            style: { display: verCompra.confirmada ? "none" : "inline" }
          }}
          onOk={() => (!editarCompra ? activarEdicion() : onCreate())}
        >
          <Form form={form} layout="vertical" name="form_in_modal">
            <Row gutter={24}>
              <Col xs={24} md={12}>
                <Form.Item name="proveedor" label="Proveedor">
                  <Select
                    showSearch
                    allowClear
                    style={{ marginBottom: "3%", width: "100%" }}
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
                    disabled={!editarCompra}
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
            {mostrarDetalles && (
              <Row gutter={24}>
                <VerTablaItemsCompra
                  setError={setError}
                  filas={filas}
                  setFilas={setFilas}
                  editarCompra={editarCompra}
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

export default ModalVerEditarCompra;
