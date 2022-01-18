import React, { useEffect } from "react";
import { Container } from "shards-react";
import { Form, Input, Row, Modal, Col } from "antd";

import { api } from "./../../hooks/api";

const ModalNuevoProveedor = ({
  modal,
  setModal,
  showNotification,
  proveedorEdicion,
  setProveedorEdicion
}) => {
  const [form] = Form.useForm();

  let rules = [
    {
      required: true,
      message: "Campo necesario!"
    }
  ];

  const onReset = () => {
    setProveedorEdicion(false);
    form.resetFields();
    setModal(false);
  };

  const onCreate = values => {
    if (proveedorEdicion) {
      values.id = proveedorEdicion.id;
      api
        .putProveedor(values)
        .then(res => {
          if (res.error) {
            showNotification("error", "Ocurrio un error", res.data);
          } else {
            showNotification(
              "success",
              "Proveedor modificado correctamente",
              ""
            );
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
        .setNuevoProveedor(values)
        .then(res => {
          if (res.error) {
            showNotification("error", "Ocurrio un error", res.data);
          } else {
            showNotification("success", "Proveedor alteado", "");
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
      nombre: proveedorEdicion.nombre
    });
  }, [proveedorEdicion]);

  return (
    <Container fluid className="main-content-container px-4">
      <Row className="page-header py-4">
        <Modal
          visible={modal}
          title={(proveedorEdicion ? "Editar" : "Nueva") + " Proveedor"}
          okText={proveedorEdicion ? "Editar" : "Crear"}
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
              <Col xs={24}>
                <Form.Item name="nombre" label="Nombre" rules={rules}>
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

export default ModalNuevoProveedor;
