import React, { useState } from "react";
import { Space, Collapse, Row, Select, Col, Button } from "antd";
import { SearchOutlined } from "@ant-design/icons";

const { Option } = Select;

function Busqueda({ setBusqueda, proveedores }) {
  const [proveedor, setProveedor] = useState(null);

  const limpiar = () => {
    setProveedor(null);
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
              placeholder="Buscá por proveedor"
              optionFilterProp="children"
              onChange={val => setProveedor(val)}
              filterOption={(input, option) =>
                option.children.toLowerCase().indexOf(input.toLowerCase()) >= 0
              }
              filterSort={(optionA, optionB) =>
                optionA.children
                  .toLowerCase()
                  .localeCompare(optionB.children.toLowerCase())
              }
            >
              {proveedores.map((proveedor, idx) => (
                <Option key={idx} value={proveedor.id}>
                  {proveedor.nombre}
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
                    proveedor
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
