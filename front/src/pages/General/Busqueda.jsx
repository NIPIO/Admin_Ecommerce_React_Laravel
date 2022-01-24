import React, { useState } from "react";
import { Space, Collapse, Row, Select, Col, Button, DatePicker } from "antd";
import { SearchOutlined } from "@ant-design/icons";

import locale from "antd/es/date-picker/locale/es_ES";
const { Option } = Select;
const { RangePicker } = DatePicker;

function Busqueda({ setBusqueda, allVendedores }) {
  const [fechas, setFechas] = useState(null);
  const [usuario, setUsuario] = useState(null);
  const [tipoMovimiento, setTipoMovimiento] = useState(null);

  const tiposMovimientos = [
    "CONFIRMACION",
    "MODIFICACION",
    "ESTADO",
    "ALTA",
    "BAJA",
    "INGRESO",
    "EGRESO"
  ];

  const limpiar = () => {
    setFechas(null);
    setUsuario(null);
    setTipoMovimiento(null);
    setBusqueda({});
  };
  return (
    <Collapse expandIconPosition="right">
      <Collapse.Panel
        key={1}
        header={
          <Space>
            <SearchOutlined style={{ color: "#aaa" }} />
            Búsqueda
          </Space>
        }
      >
        <Row gutter={24}>
          <Col>
            <Select
              allowClear
              style={{ width: 200 }}
              placeholder="Buscá por usuario"
              onChange={val => setUsuario(val)}
            >
              {allVendedores.map((vendedor, idx) => (
                <Option key={idx} value={vendedor.id}>
                  {vendedor.nombre}
                </Option>
              ))}
            </Select>
          </Col>
          <Col>
            <Select
              allowClear
              style={{ width: 200 }}
              placeholder="Buscá por movimiento"
              onChange={val => setTipoMovimiento(val)}
            >
              {tiposMovimientos.map((tipo, idx) => (
                <Option key={idx} value={tipo}>
                  {tipo}
                </Option>
              ))}
            </Select>
          </Col>

          <Col md={6}>
            <RangePicker
              style={{ width: "100%" }}
              locale={locale}
              allowClear
              onChange={val => setFechas(val)}
            />
          </Col>
          <Col>
            <Space>
              <Button
                type="primary"
                icon={<SearchOutlined />}
                onClick={() => {
                  setBusqueda({
                    usuario,
                    fechas,
                    tipoMovimiento
                  });
                }}
              >
                Buscar
              </Button>
              <Button onClick={() => limpiar()}>Limpiar</Button>
            </Space>
          </Col>
        </Row>
      </Collapse.Panel>
    </Collapse>
  );
}

export default Busqueda;
