import React, { useState } from "react";
import { Space, Collapse, Row, Select, Col, Button, DatePicker } from "antd";
import { SearchOutlined } from "@ant-design/icons";
import locale from "antd/es/date-picker/locale/es_ES";

const { Option } = Select;
const { RangePicker } = DatePicker;

function Busqueda({ setBusqueda, clientes, vendedores, productos }) {
  const [producto, setProducto] = useState(null);
  const [cliente, setCliente] = useState(null);
  const [vendedor, setVendedor] = useState(null);
  const [estado, setEstado] = useState(null);
  const [fechas, setFechas] = useState(null);

  const limpiar = () => {
    setCliente(null);
    setFechas(null);
    setVendedor(null);
    setProducto(null);
    setEstado(null);
    setBusqueda({});
  };

  const estados = [
    {
      nombre: "activa",
      id: 1
    },
    {
      nombre: "inactiva",
      id: 0
    }
  ];

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
        <Space direction="vertical" size={30} style={{ width: "100%" }}>
          <Row gutter={24}>
            <Col xs={24} md={5}>
              <Select
                showSearch
                allowClear
                style={{ marginBottom: "3%", width: "100%" }}
                placeholder="Buscá por vendedor"
                optionFilterProp="children"
                onChange={val => setVendedor(val)}
                filterOption={(input, option) =>
                  option.children.toLowerCase().indexOf(input.toLowerCase()) >=
                  0
                }
                filterSort={(optionA, optionB) =>
                  optionA.children
                    .toLowerCase()
                    .localeCompare(optionB.children.toLowerCase())
                }
              >
                {vendedores.map((vendedor, idx) => (
                  <Option key={idx} value={vendedor.id}>
                    {vendedor.nombre}
                  </Option>
                ))}
              </Select>
            </Col>
            <Col xs={24} md={5}>
              <Select
                showSearch
                allowClear
                style={{ marginBottom: "3%", width: "100%" }}
                placeholder="Buscá por cliente"
                optionFilterProp="children"
                onChange={val => setCliente(val)}
                filterOption={(input, option) =>
                  option.children.toLowerCase().indexOf(input.toLowerCase()) >=
                  0
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
            <Col xs={24} md={5}>
              <Select
                showSearch
                allowClear
                style={{ marginBottom: "3%", width: "100%" }}
                placeholder="Buscá por producto"
                optionFilterProp="children"
                onChange={val => setProducto(val)}
                filterOption={(input, option) =>
                  option.children.toLowerCase().indexOf(input.toLowerCase()) >=
                  0
                }
                filterSort={(optionA, optionB) =>
                  optionA.children
                    .toLowerCase()
                    .localeCompare(optionB.children.toLowerCase())
                }
              >
                {productos.map((producto, idx) => (
                  <Option key={idx} value={producto.id}>
                    {producto.nombre}
                  </Option>
                ))}
              </Select>
            </Col>
            <Col xs={24} md={5}>
              <RangePicker
                style={{ marginBottom: "3%", width: "100%" }}
                locale={locale}
                allowClear
                onChange={val => setFechas(val)}
              />
            </Col>
            <Col xs={24} md={4}>
              <Select
                showSearch
                allowClear
                defaultValue={1}
                style={{ marginBottom: "3%", width: "100%" }}
                placeholder="Buscá por estado"
                optionFilterProp="children"
                onChange={val => setEstado(val)}
                filterOption={(input, option) =>
                  option.children.toLowerCase().indexOf(input.toLowerCase()) >=
                  0
                }
                filterSort={(optionA, optionB) =>
                  optionA.children
                    .toLowerCase()
                    .localeCompare(optionB.children.toLowerCase())
                }
              >
                {estados.map((estado, idx) => (
                  <Option key={idx} value={estado.id}>
                    {estado.nombre}
                  </Option>
                ))}
              </Select>
            </Col>
          </Row>
        </Space>
        <Space direction="vertical" size={30} style={{ width: "100%" }}>
          <Row>
            <Col md={8}>
              <Button
                type="primary"
                icon={<SearchOutlined />}
                onClick={() => {
                  setBusqueda({
                    cliente,
                    vendedor,
                    producto,
                    fechas,
                    estado
                  });
                }}
              >
                Buscar
              </Button>
              <Button onClick={() => limpiar()}>Limpiar</Button>
            </Col>
          </Row>
        </Space>
      </Collapse.Panel>
    </Collapse>
  );
}

export default Busqueda;
