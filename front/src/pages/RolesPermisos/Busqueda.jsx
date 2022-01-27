import React, { useState } from "react";
import { Space, Collapse, Row, Select, Col, Button } from "antd";
import { SearchOutlined } from "@ant-design/icons";

const { Option } = Select;

function Busqueda({ setBusqueda, roles, permisos }) {
  const [rol, setRol] = useState(null);

  const limpiar = () => {
    setRol(null);
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
              style={{ marginBottom: "3%", width: "100%" }}
              placeholder="Buscá por rol"
              optionFilterProp="children"
              onChange={val => setRol(val)}
              filterOption={(input, option) =>
                option.children.toLowerCase().indexOf(input.toLowerCase()) >= 0
              }
              filterSort={(optionA, optionB) =>
                optionA.children
                  .toLowerCase()
                  .localeCompare(optionB.children.toLowerCase())
              }
            >
              {roles.map((rol, idx) => (
                <Option key={idx} value={rol.id}>
                  {rol.nombre}
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
                    rol
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
