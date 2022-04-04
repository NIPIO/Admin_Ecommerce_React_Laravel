import React, { useEffect } from "react";
import { Container } from "shards-react";
import { Form, Input, Row, Modal, Select, InputNumber, Col } from "antd";

import { api } from "./../../hooks/api";

const { Option } = Select;

const ModalNuevoProducto = ({
  modal,
  setModal,
  marcas,
  showNotification,
  productoEdicion,
  setProductoEdicion,
  queryClient
}) => {
  const [form] = Form.useForm();

  let rules = [
    {
      required: true,
      message: "Campo necesario!"
    }
  ];

  const onReset = () => {
    setProductoEdicion(false);
    form.resetFields();
    setModal(false);
  };

  const enviar = values => {
    if (productoEdicion) {
      values.id = productoEdicion.id;
      api
        .putProducto(values)
        .then(res => {
          if (res.error) {
            showNotification("error", "Ocurrio un error", res.data);
          } else {
            showNotification(
              "success",
              "Producto modificado correctamente",
              ""
            );
            queryClient.invalidateQueries("productos");
            onReset();
          }
        })
        .catch(err => {
          showNotification(
            "error",
            "Ocurrio un error",
            err.response.data.message
          );
        });
    } else {
      api
        .setNuevoProducto(values)
        .then(res => {
          if (res.error) {
            showNotification("error", "Ocurrio un error", res.data);
          } else {
            showNotification("success", "Producto alteado", "");
            queryClient.invalidateQueries("productos");
            setModal(false);
            form.resetFields();
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

  useEffect(() => {
    form.setFieldsValue({
      nombre: productoEdicion.nombre,
      marca: productoEdicion.marca,
      stock: productoEdicion ? productoEdicion.stock : 0,
      costo: productoEdicion ? productoEdicion.costo : 0
    });
  }, [productoEdicion]);

  return (
    <Container fluid className="main-content-container px-4">
      <Row className="page-header py-4">
        <Modal
          visible={modal}
          title={(productoEdicion ? "Editar" : "Nuevo") + " Producto"}
          okText={productoEdicion ? "Editar" : "Crear"}
          cancelText="Cancelar"
          onCancel={() => onReset()}
          onOk={() => {
            form
              .validateFields()
              .then(values => {
                enviar(values);
              })
              .catch(info => {
                console.log("Validate Failed:", info);
              });
          }}
        >
          <Form form={form} layout="vertical" name="form_in_modal">
            <Row gutter={24}>
              <Col xs={24} md={12}>
                <Form.Item name="nombre" label="Nombre" rules={rules}>
                  <Input disabled={productoEdicion} />
                </Form.Item>
              </Col>
              <Col xs={24} md={12}>
                <Form.Item name="marca" label="Marca" rules={rules}>
                  <Select
                    showSearch
                    allowClear
                    style={{ marginBottom: "3%", width: "100%" }}
                    placeholder="Elegí la marca"
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
                    {marcas.map((marca, idx) => (
                      <Option key={idx} value={marca.id}>
                        {marca.nombre}
                      </Option>
                    ))}
                  </Select>
                </Form.Item>
              </Col>
            </Row>
            <Row gutter={24}>
              <Col xs={24} md={12}>
                <Form.Item name="stock" label="Stock" rules={rules}>
                  <InputNumber style={{ width: "100%" }} />
                </Form.Item>
              </Col>
              <Col xs={24} md={12}>
                <Form.Item name="costo" label="Costo" rules={rules}>
                  <InputNumber
                    formatter={value =>
                      `$ ${value}`.replace(/\B(?=(\d{3})+(?!\d))/g, ",")
                    }
                    parser={value => value.replace(/\$\s?|(,*)/g, "")}
                    style={{ width: "100%" }}
                  />
                </Form.Item>
              </Col>
            </Row>
          </Form>
        </Modal>
      </Row>
    </Container>
  );
};

export default ModalNuevoProducto;
