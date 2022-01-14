import React, { useEffect } from "react";
import { Container } from "shards-react";
import { Form, Input, Row, Modal, Col } from "antd";

import { api } from "../../hooks/api";

const ModalEdicionVendedor = ({
  modal,
  setModal,
  openNotificationWithIcon,
  vendedorEdicion,
  setVendedorEdicion
}) => {
  const [form] = Form.useForm();

  let rules = [
    {
      required: true,
      message: "Campo necesario!"
    }
  ];

  const onReset = () => {
    setVendedorEdicion(false);
    form.resetFields();
    setModal(false);
  };

  const onCreate = values => {
    if (vendedorEdicion) {
      values.id = vendedorEdicion.id;
      api
        .putVendedor(values)
        .then(res => {
          if (res.error) {
            openNotificationWithIcon("error", "Ocurrio un error", res.data);
          } else {
            openNotificationWithIcon(
              "success",
              "Cliente modificado correctamente",
              ""
            );
            onReset();
          }
        })
        .catch(err => {
          openNotificationWithIcon(
            "error",
            "Ocurrio un error",
            err.response.data.message
          );
        });
    }
  };

  useEffect(() => {
    form.setFieldsValue({
      nombre: vendedorEdicion.nombre,
      telefono: vendedorEdicion.telefono,
      email: vendedorEdicion.email
    });
  }, [vendedorEdicion]);

  return (
    <Container fluid className="main-content-container px-4">
      <Row className="page-header py-4">
        <Modal
          visible={modal}
          title={"Editar Vendedor"}
          okText={"Editar"}
          cancelText="Cancelar"
          onCancel={() => onReset()}
          onOk={() => {
            form
              .validateFields()
              .then(values => {
                onCreate(values);
              })
              .catch(info => {
                console.log("Validate Failed:", info);
              });
          }}
        >
          <Form
            form={form}
            layout="vertical"
            name="form_in_modal"
            initialValues={{
              modifier: "public"
            }}
          >
            <Row gutter={24}>
              <Col xs={24} md={12}>
                <Form.Item name="nombre" label="Nombre" rules={rules}>
                  <Input />
                </Form.Item>
              </Col>
              <Col xs={24} md={12}>
                <Form.Item name="telefono" label="Telefono">
                  <Input />
                </Form.Item>
              </Col>
            </Row>
            <Row gutter={24}>
              <Col xs={24}>
                <Form.Item name="email" label="Email">
                  <Input />
                </Form.Item>
              </Col>
            </Row>
          </Form>
        </Modal>
      </Row>
    </Container>
  );
};

export default ModalEdicionVendedor;
