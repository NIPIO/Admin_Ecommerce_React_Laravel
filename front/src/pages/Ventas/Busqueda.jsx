import React, { useState } from "react";
import { Space, Collapse, Row, Select, Col, Button, DatePicker } from "antd";
import { SearchOutlined } from "@ant-design/icons";
import locale from "antd/es/date-picker/locale/es_ES";

const { Option } = Select;
const { RangePicker } = DatePicker;

function Busqueda({ setBusqueda, clientes, vendedores }) {
  const [cliente, setCliente] = useState(null);
  const [vendedor, setVendedor] = useState(null);
  const [fechas, setFechas] = useState(null);

  const limpiar = () => {
    setCliente(null);
    setFechas(null);
    setVendedor(null);
    setBusqueda({});
  };
  return (
    <Collapse expandIconPosition="right">
      <Collapse.Panel
        key={1}
        header={
          <Space>
            <SearchOutlined style={{ color: "#aaa" }} />
            Búsqueda (Por defecto las ventas son las del día de hoy)
          </Space>
        }
      >
        <Row gutter={24}>
          <Col>
            <Select
              showSearch
              allowClear
              style={{ width: 200 }}
              placeholder="Buscá por vendedor"
              optionFilterProp="children"
              onChange={val => setVendedor(val)}
              filterOption={(input, option) =>
                option.children.toLowerCase().indexOf(input.toLowerCase()) >= 0
              }
              filterSort={(optionA, optionB) =>
                optionA.children
                  .toLowerCase()
                  .localeCompare(optionB.children.toLowerCase())
              }
            >
              {vendedores.map(vendedor => (
                <Option key={vendedor.id} value={vendedor.id}>
                  {vendedor.nombre}
                </Option>
              ))}
            </Select>
          </Col>
          <Col>
            <Select
              showSearch
              allowClear
              style={{ width: 200 }}
              placeholder="Buscá por cliente"
              optionFilterProp="children"
              onChange={val => setCliente(val)}
              filterOption={(input, option) =>
                option.children.toLowerCase().indexOf(input.toLowerCase()) >= 0
              }
              filterSort={(optionA, optionB) =>
                optionA.children
                  .toLowerCase()
                  .localeCompare(optionB.children.toLowerCase())
              }
            >
              {clientes.map(cliente => (
                <Option key={cliente.id} value={cliente.id}>
                  {cliente.nombre}
                </Option>
              ))}
            </Select>
          </Col>

          <Col>
            <Space direction="vertical" size={12}>
              <RangePicker
                locale={locale}
                allowClear
                onChange={val => setFechas(val)}
              />
            </Space>
            ,
          </Col>
          <Col>
            <Space>
              <Button
                type="primary"
                icon={<SearchOutlined />}
                onClick={() => {
                  setBusqueda({
                    cliente,
                    vendedor,
                    fechas
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
