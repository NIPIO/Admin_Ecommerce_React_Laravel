import React, { useState } from "react";
import { Space, Collapse, Row, Select, Col, Button } from "antd";
import { SearchOutlined } from "@ant-design/icons";

const { Option } = Select;

function Busqueda({ setBusqueda, clientes }) {
  const [cliente, setCliente] = useState(null);

  const limpiar = () => {
    setCliente(null);
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
              {clientes.map((cliente, idx) => (
                <Option key={idx} value={cliente.id}>
                  {cliente.nombre}
                </Option>
              ))}
            </Select>
          </Col>

          <Col>
            <Space>
              <Button
                type="primary"
                icon={<SearchOutlined />}
                onClick={() => {
                  setBusqueda({
                    cliente
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
