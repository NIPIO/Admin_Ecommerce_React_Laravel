import React from "react";
import { Container } from "shards-react";
import { Input, Form, Row, Modal, Select, InputNumber, Col } from "antd";

import { api } from "../../hooks/api";
const { Option } = Select;
const { TextArea } = Input;

const ModalNuevaCaja = ({
  modal,
  setModal,
  tipoMovimientoObj,
  showNotification,
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
    form.resetFields();
    setModal(false);
  };

  const onCreate = values => {
    api
      .setNuevaCaja(values)
      .then(res => {
        if (res.error) {
          showNotification("error", "Ocurrio un error", res.data);
        } else {
          showNotification("success", "Movimiento alteado", "");
          queryClient.invalidateQueries("caja");
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
          title={"Nueva Caja"}
          okText={"Crear"}
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
                <Form.Item
                  name="tipoMovimiento"
                  label="Tipo Mov."
                  rules={rules}
                >
                  <Select
                    allowClear
                    style={{ marginBottom: "3%", width: "100%" }}
                    placeholder="Elegí el mov."
                  >
                    {tipoMovimientoObj.map((mov, idx) => (
                      <Option key={idx} value={mov}>
                        {mov}
                      </Option>
                    ))}
                  </Select>
                </Form.Item>
              </Col>
              <Col xs={24} md={12}>
                <Form.Item name="importe" label={"Importe"} rules={rules}>
                  <InputNumber
                    formatter={value =>
                      `$ ${value}`.replace(/\B(?=(\d{3})+(?!\d))/g, ",")
                    }
                    parser={value => value.replace(/\$\s?|(,*)/g, "")}
                    style={{ width: "100%" }}
                  />
                </Form.Item>
              </Col>
              <Col xs={24}>
                <Form.Item name="observacion" label={"Observaciones"}>
                  <TextArea rows={4} />
                </Form.Item>
              </Col>
            </Row>
          </Form>
        </Modal>
      </Row>
    </Container>
  );
};

export default ModalNuevaCaja;
