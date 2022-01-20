import React, { useEffect, useState } from "react";
import { Container } from "shards-react";
import { Form, Input, Row, Modal, Col, Radio } from "antd";
import { api } from "../../hooks/api";

const ModalEdicionVendedor = ({
  modal,
  setModal,
  showNotification,
  vendedorEdicion,
  setVendedorEdicion,
  queryClient,
  roles
}) => {
  const [form] = Form.useForm();
  const [rolVendedor, setRolVendedor] = useState();

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
      values.rol = rolVendedor;
      api
        .putVendedor(values)
        .then(res => {
          if (res.error) {
            showNotification("error", "Ocurrio un error", res.data);
          } else {
            showNotification(
              "success",
              "Vendedor modificado correctamente",
              ""
            );
            queryClient.invalidateQueries("vendedores");
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
    }
  };

  useEffect(() => {
    if (vendedorEdicion) {
      setRolVendedor(vendedorEdicion.rol_id);
      form.setFieldsValue({
        nombre: vendedorEdicion.nombre,
        telefono: vendedorEdicion.telefono,
        email: vendedorEdicion.email
      });
    }
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
            <Row className="mt-2">
              <Radio.Group
                className="m-auto"
                value={rolVendedor}
                onChange={e => setRolVendedor(e.target.value)}
              >
                {roles.map(rol => (
                  <Radio value={rol.id}>{rol.nombre}</Radio>
                ))}
              </Radio.Group>
            </Row>
          </Form>
        </Modal>
      </Row>
    </Container>
  );
};

export default ModalEdicionVendedor;
