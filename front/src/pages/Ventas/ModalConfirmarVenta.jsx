import React, { useEffect, useState } from "react";
import { Container } from "shards-react";
import { Row, Modal, Spin, Form, Col, InputNumber, Typography } from "antd";
import { api } from "../../hooks/api";
import { useQuery } from "react-query";
import { showNotification } from "./../notificacion";

const { Title } = Typography;

const ModalNuevaVenta = ({ modal, setModal, id }) => {
  const [modalAlerta, setModalAlerta] = useState(false);
  const [form] = Form.useForm();

  const confirmarVenta = () => {
    form.validateFields().then(res => {
      if (res.precioFinal !== detallesVenta.data.venta[0].precio_total) {
        setModalAlerta({
          pago: res.precioFinal,
          deberiaPagar: detallesVenta.data.venta[0].precio_total
        });
      } else {
        confirmaAlerta(res.precioFinal, 0);
      }
    });
  };

  const confirmaAlerta = (pago, diferencia) => {
    api
      .confirmarVenta(pago, id, diferencia)
      .then(res => {
        if (res.error) {
          showNotification("error", "Ocurrio un error", res.data);
        } else {
          showNotification("success", "Venta confirmada!", "");
          setModal(false);
          setModalAlerta(false);
        }
      })
      .catch(err =>
        showNotification("error", "Ocurrio un error", err.response.data.message)
      );
  };

  const detallesVenta = useQuery(["ventaId", id], () => {
    if (id !== null) return api.getVenta(id);
  });

  useEffect(() => {
    if (detallesVenta.data !== undefined) {
      form.setFieldsValue({
        precioFinal: detallesVenta.data.venta[0].precio_total
      });
    }
  }, [id, detallesVenta.data]);

  if (detallesVenta.isLoading || detallesVenta.data === undefined) {
    return (
      <Spin tip="Cargando" style={{ width: "100%", display: "none" }}></Spin>
    );
  }

  return (
    <Container fluid className="main-content-container px-4">
      <Row className="page-header py-4">
        <Modal
          width={800}
          visible={modal}
          title={"Confirmar venta"}
          okText={"Confirmar"}
          cancelText="Cancelar"
          onCancel={() => setModal(false)}
          onOk={() => confirmarVenta()}
        >
          <Row>
            Según la venta cargada, estos son los productos que deberían haber
            retirado. Si acepta, está confirmando que se vendieron todos, sino
            edite la venta.
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
                {detallesVenta.data.venta[0].detalle_venta.map((item, idx) => (
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
                ))}
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
                  {detallesVenta.data.venta[0].precio_total}. Confirme en el
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
              modalAlerta.deberiaPagar - modalAlerta.pago
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
                  ${modalAlerta.pago - modalAlerta.deberiaPagar} con el cliente.
                </span>
              </>
            )}
          </Row>
        </Modal>
      </Row>
    </Container>
  );
};

export default ModalNuevaVenta;
