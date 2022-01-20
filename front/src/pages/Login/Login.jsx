import React, { useState } from "react";
import { Form, Input, Button, Row, Card, Col } from "antd";
import { Container } from "shards-react";
import { signin } from "./../../hooks/api";
import { showNotification } from "./../notificacion";

const Login = () => {
  const [nuevoUsuario, setNuevoUsuario] = useState(false);
  const [form] = Form.useForm();
  const [loading, setLoading] = useState(false);
  let rules = [
    {
      required: true,
      message: "Campo necesario!"
    }
  ];

  const onCreate = values => {
    setLoading(true);

    if (nuevoUsuario) {
      signin
        .postRegistro(values)
        .then(res => {
          setLoading(false);
          if (res.error) {
            showNotification("error", "Ocurrió un error", res.data);
          } else {
            localStorage.setItem("logueado", JSON.stringify(res.data));
            window.location.href = "/ventas"; //relative to domain
          }
        })
        .catch(err => {
          alert("algo pasó");
          setLoading(false);
        });
    } else {
      signin
        .postLogin(values)
        .then(res => {
          setLoading(false);

          if (res.error) {
            showNotification("error", "Ocurrió un error", res.data);
          } else {
            localStorage.setItem("logueado", JSON.stringify(res.data));
            window.location.href = "/ventas"; //relative to domain
          }
        })
        .catch(err => {
          alert("algo pasó");
          setLoading(false);
        });
    }
  };

  return (
    <Container fluid className="main-content-container px-4 ">
      <Row className="page-header py-4 mt-5  pt-5 ">
        <Card className="m-auto ">
          <Form
            name="login"
            onFinish={() => {
              form
                .validateFields()
                .then(values => {
                  onCreate(values);
                })
                .catch(info => {
                  console.log("Validate Failed:", info);
                });
            }}
            form={form}
            labelCol={{
              span: 8
            }}
            wrapperCol={{
              span: 16
            }}
            autoComplete="off"
          >
            {nuevoUsuario && (
              <>
                <Form.Item label="Nombre" name="nombre" rules={rules}>
                  <Input />
                </Form.Item>
                <Form.Item
                  label="Email"
                  name="email"
                  rules={[
                    ...rules,
                    {
                      type: "email",
                      message: "Esto no es un mail"
                    }
                  ]}
                >
                  <Input />
                </Form.Item>
              </>
            )}

            <Form.Item label="Usuario" name="usuario" rules={rules}>
              <Input />
            </Form.Item>

            <Form.Item label="Clave" name="password" rules={rules}>
              <Input.Password />
            </Form.Item>

            <Col>
              <Button type="primary" block htmlType="submit" loading={loading}>
                {nuevoUsuario ? "Crear" : "Ingresar"}
              </Button>
              {nuevoUsuario ? (
                <Button
                  className="mt-1"
                  block
                  onClick={() => setNuevoUsuario(false)}
                >
                  Tengo usuario
                </Button>
              ) : (
                <Button
                  className="mt-1"
                  block
                  onClick={() => setNuevoUsuario(true)}
                >
                  Nuevo usuario
                </Button>
              )}
            </Col>
          </Form>
        </Card>
      </Row>
    </Container>
  );
};

export default Login;
