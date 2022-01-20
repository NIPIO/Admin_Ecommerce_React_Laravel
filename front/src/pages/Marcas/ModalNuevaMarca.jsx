import React, { useEffect } from "react";
import { Container } from "shards-react";
import { Form, Input, Row, Modal, Col } from "antd";

import { api } from "./../../hooks/api";
const ModalNuevaMarca = ({
  modal,
  setModal,
  showNotification,
  marcaEdicion,
  setMarcaEdicion,
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
    setMarcaEdicion(false);
    form.resetFields();
    setModal(false);
  };

  const onCreate = values => {
    if (marcaEdicion) {
      values.id = marcaEdicion.id;
      api
        .putMarca(values)
        .then(res => {
          if (res.error) {
            showNotification("error", "Ocurrio un error", res.data);
          } else {
            showNotification("success", "Marca modificado correctamente", "");
            queryClient.invalidateQueries("marcas");
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
        .setNuevaMarca(values)
        .then(res => {
          if (res.error) {
            showNotification("error", "Ocurrio un error", res.data);
          } else {
            showNotification("success", "Marca alteada", "");
            queryClient.invalidateQueries("marcas");
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
      nombre: marcaEdicion.nombre,
      marca: marcaEdicion.marca,
      stock: marcaEdicion.stock,
      en_transito: marcaEdicion.en_transito
    });
  }, [marcaEdicion]);

  return (
    <Container fluid className="main-content-container px-4">
      <Row className="page-header py-4">
        <Modal
          visible={modal}
          title={(marcaEdicion ? "Editar" : "Nueva") + " Marca"}
          okText={marcaEdicion ? "Editar" : "Crear"}
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

export default ModalNuevaMarca;
