import React, { useEffect, useState } from "react";
import { Container } from "shards-react";
import { Form, Input, Row, Modal, Col, Checkbox } from "antd";
import { api } from "../../hooks/api";

const CheckboxGroup = Checkbox.Group;

const ModalNuevoRol = ({
  modal,
  setModal,
  showNotification,
  permisos,
  rolEdicion,
  setRolEdicion,
  queryClient
}) => {
  const [form] = Form.useForm();
  const [options, setOptions] = useState([]);
  const [permisosCheck, setPermisosCheck] = useState([]);
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
    // if (rolEdicion) {
    //   api
    //     .putRol({ id: rolEdicion.id, permisos: permisosCheck })
    //     .then(res => {
    //       if (res.error) {
    //         showNotification("error", "Ocurrio un error", res.data);
    //       } else {
    //         showNotification("success", "Rol modificado correctamente", "");
    //         queryClient.invalidateQueries("roles");
    //         onReset();
    //       }
    //     })
    //     .catch(err => {
    //       showNotification(
    //         "error",
    //         "Ocurrio un error",
    //         err.response.data.message
    //       );
    //     });
    // } else {
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
    // }
  };

  // const prepararOpciones = () => {
  //   let arrayPermisos = [];
  //   permisos.map(permiso => {
  //     arrayPermisos.push({
  //       label: permiso.nombre,
  //       value: permiso.id
  //     });
  //   });

  //   const permisosId = rolEdicion.permisos.map(permiso => permiso.id);

  //   setPermisosCheck(permisosId);
  //   setOptions(arrayPermisos);
  // };

  // useEffect(() => {
  //   setPermisosCheck([]);
  //   if (rolEdicion) {
  //     form.setFieldsValue({
  //       nombre: rolEdicion.nombre
  //     });
  //     prepararOpciones();
  //   }
  // }, [rolEdicion]);

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
                <Form.Item name="descripcion" label="Descripcion">
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
