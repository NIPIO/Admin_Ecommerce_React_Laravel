import React, { useEffect, useState } from "react";
import { Container } from "shards-react";
import { Row, Modal, Spin, Form, Col, InputNumber, Typography } from "antd";
import { api } from "../../hooks/api";
import { useQuery } from "react-query";
import { showNotification } from "./../notificacion";

const { Title } = Typography;

const ModalConfirmarCompra = ({ modal, setModal, id }) => {
  const [modalAlerta, setModalAlerta] = useState(false);
  const [form] = Form.useForm();

  const confirmarCompra = () => {
    form.validateFields().then(res => {
      if (res.precioFinal !== detallesCompra.data.compra[0].precio_total) {
        setModalAlerta({
          pago: res.precioFinal,
          deberiaPagar: detallesCompra.data.compra[0].precio_total
        });
      } else {
        confirmaAlerta(res.precioFinal, 0);
      }
    });
  };

  const confirmaAlerta = (pago, diferencia) => {
    api
      .confirmarCompra(pago, id, diferencia)
      .then(res => {
        if (res.error) {
          showNotification("error", "Ocurrio un error", res.data);
        } else {
          showNotification("success", "Compra confirmada!", "");
          setModal(false);
          setModalAlerta(false);
        }
      })
      .catch(err =>
        showNotification("error", "Ocurrio un error", err.response.data.message)
      );
  };

  const detallesCompra = useQuery(["compraId", id], () => {
    if (id !== null) return api.getCompra(id);
  });

  useEffect(() => {
    if (detallesCompra.data !== undefined) {
      form.setFieldsValue({
        precioFinal: detallesCompra.data.compra[0].precio_total
      });
    }
  }, [id, detallesCompra.data]);

  if (detallesCompra.isLoading || detallesCompra.data === undefined) {
    return (
      <Spin tip="Cargando" style={{ width: "100%", margin: "10% auto" }}></Spin>
    );
  }

  return (
    <Container fluid className="main-content-container px-4">
      <Row className="page-header py-4">
        <Modal
          width={800}
          visible={modal}
          title={"Confirmar compra"}
          okText={"Confirmar"}
          cancelText="Cancelar"
          onCancel={() => setModal(false)}
          onOk={() => confirmarCompra()}
        >
          <Row>
            Según la compra cargada, estos son los productos que deberían haber
            llegado. Si acepta, está confirmando que llegaron todos, sino edite
            la compra.
            <span style={{ color: "red", fontStyle: "bold" }}>
              Los productos ingresados pasarán de "En transito" a "Stock"
            </span>
          </Row>
          <Row className="page-header py-4">
            <table className="table table-bordered table-hover">
              <thead>
                <tr>
                  <th className="text-center"> Producto </th>
                  <th className="text-center"> Cantidad </th>
                  <th className="text-center"> </th>
                </tr>
              </thead>
              <tbody>
                {detallesCompra.data.compra[0].detalle_compra.map(
                  (item, idx) => (
                    <tr key={idx}>
                      <td>
                        <input
                          type="text"
                          value={item.producto.nombre}
                          className="form-control"
                          disabled
                        />
                      </td>
                      <td>
                        <input
                          type="number"
                          value={item.cantidad}
                          className="form-control"
                          disabled
                        />
                      </td>
                    </tr>
                  )
                )}
              </tbody>
            </table>
            <Row style={{ margin: "auto" }}>
              <Form
                form={form}
                layout="vertical"
                name="form_in_modal"
                style={{ width: "100%" }}
              >
                <Title
                  level={5}
                  style={{
                    width: "100%",
                    textAlign: "center",
                    marginTop: "3%"
                  }}
                >
                  El precio final a abonar era $
                  {detallesCompra.data.compra[0].precio_total}. Confirme en el
                  siguiente campo si se abonó el total o cuánto se abonó.
                </Title>
                <Col xs={24} md={12} style={{ margin: "auto" }}>
                  <Form.Item
                    name="precioFinal"
                    style={{
                      width: "40%",
                      textAlign: "center",
                      margin: "auto"
                    }}
                  >
                    <InputNumber
                      formatter={value =>
                        `$ ${value}`.replace(/\B(?=(\d{3})+(?!\d))/g, ",")
                      }
                      parser={value => value.replace(/\$\s?|(,*)/g, "")}
                      style={{ width: "100%" }}
                    />
                  </Form.Item>
                </Col>
              </Form>
            </Row>
          </Row>
        </Modal>
        <Modal
          width={800}
          visible={modalAlerta}
          title={"Confirmar"}
          okText={"Está bien"}
          cancelText="No"
          onCancel={() => setModalAlerta(false)}
          onOk={() =>
            confirmaAlerta(
              modalAlerta.pago,
              modalAlerta.pago - modalAlerta.deberiaPagar
            )
          }
        >
          <Row>
            El precio ingresado difiere con los datos anteriores. <br /> Esta
            diferencia se verá impactada en la cuenta corriente con el
            proveedor.
            {modalAlerta.deberiaPagar !== modalAlerta.pago && (
              <>
                <span
                  style={{
                    color:
                      modalAlerta.deberiaPagar > modalAlerta.pago
                        ? "red"
                        : "green"
                  }}
                >
                  {modalAlerta.deberiaPagar > modalAlerta.pago
                    ? "Se registrará una deuda de "
                    : "Se registrará un saldo positivo de "}
                  ${modalAlerta.pago - modalAlerta.deberiaPagar} con el
                  proveedor.
                </span>
              </>
            )}
          </Row>
        </Modal>
      </Row>
    </Container>
  );
};

export default ModalConfirmarCompra;
