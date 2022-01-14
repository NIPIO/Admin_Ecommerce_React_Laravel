import React, { useState } from "react";
import { Space, Collapse, Row, Select, Col, Button } from "antd";
import { SearchOutlined } from "@ant-design/icons";

const { Option } = Select;

function Busqueda({ setBusqueda, proveedores, productos }) {
  const [proveedor, setProveedor] = useState(null);
  const [producto, setProducto] = useState(null);

  const limpiar = () => {
    setProveedor(null);
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
              {proveedores.map(prov => (
                <Option key={prov.id} value={prov.id}>
                  {prov.nombre}
                </Option>
              ))}
            </Select>
          </Col>
          <Col>
            <Select
              showSearch
              allowClear
              style={{ width: 200 }}
              placeholder="Buscá por producto"
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
              {productos.map(prod => (
                <Option key={prod.id} value={prod.id}>
                  {prod.nombre}
                </Option>
              ))}
            </Select>
          </Col>

          <Col>
            <Button
              type="primary"
              icon={<SearchOutlined />}
              onClick={() => {
                setBusqueda({
                  proveedor,
                  producto
                });
              }}
            >
              Buscar
            </Button>
            <Button onClick={() => limpiar()}>Limpiar</Button>
          </Col>
        </Row>
      </Collapse.Panel>
    </Collapse>
  );
}

export default Busqueda;
