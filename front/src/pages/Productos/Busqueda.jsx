import React, { useState } from "react";
import { Space, Collapse, Row, Select, Col, Button } from "antd";
import { SearchOutlined } from "@ant-design/icons";

const { Option } = Select;

function Busqueda({ setBusqueda, marcas, productos }) {
  const [producto, setProducto] = useState(null);
  const [marca, setMarca] = useState(null);

  const limpiar = () => {
    setMarca(null);
    setProducto(null);
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
              placeholder="Buscá por productos"
              optionFilterProp="children"
              onChange={val => setProducto(val)}
              filterOption={(input, option) =>
                option.children.toLowerCase().indexOf(input.toLowerCase()) >= 0
              }
              filterSort={(optionA, optionB) =>
                optionA.children
                  .toLowerCase()
                  .localeCompare(optionB.children.toLowerCase())
              }
            >
              {productos.map(producto => (
                <Option key={producto.id} value={producto.id}>
                  {producto.nombre}
                </Option>
              ))}
            </Select>
          </Col>
          <Col>
            <Select
              showSearch
              allowClear
              style={{ width: 200 }}
              placeholder="Buscá por marcas"
              optionFilterProp="children"
              onChange={val => setMarca(val)}
              filterOption={(input, option) =>
                option.children.toLowerCase().indexOf(input.toLowerCase()) >= 0
              }
              filterSort={(optionA, optionB) =>
                optionA.children
                  .toLowerCase()
                  .localeCompare(optionB.children.toLowerCase())
              }
            >
              {marcas.map(marca => (
                <Option key={marca.id} value={marca.id}>
                  {marca.nombre}
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
                    producto,
                    marca
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
