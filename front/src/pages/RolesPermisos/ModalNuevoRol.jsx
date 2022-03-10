import React from "react";
import { Container } from "shards-react";
import { Form, Input, Row, Modal, Col } from "antd";
import { api } from "../../hooks/api";

const ModalNuevoRol = ({
  modal,
  setModal,
  showNotification,
  rolEdicion,
  setRolEdicion,
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
    setRolEdicion(false);
    form.resetFields();
    setModal(false);
  };

  const onCreate = values => {
    api
      .setRol(values)
      .then(res => {
        if (res.error) {
          showNotification("error", "Ocurrio un error", res.data);
        } else {
          showNotification("success", "Rol alteado", "");
          queryClient.invalidateQueries("roles");
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
  };

  return (
    <Container fluid className="main-content-container px-4">
      <Row className="page-header py-4">
        <Modal
          visible={modal}
          title={(rolEdicion ? "Editar" : "Nuevo") + " Rol"}
          okText={rolEdicion ? "Editar" : "Crear"}
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
              <Col xs={24}>
                <Form.Item name="descripcion" label="Descripcion" rules={rules}>
                  <Input />
                </Form.Item>
              </Col>
              {/* <Row>
                  <CheckboxGroup
                    value={permisosCheck}
                    onChange={checked => setPermisosCheck(checked)}
                    options={options}
                  ></CheckboxGroup>
                </Row> */}
            </Row>
          </Form>
        </Modal>
      </Row>
    </Container>
  );
};

export default ModalNuevoRol;
