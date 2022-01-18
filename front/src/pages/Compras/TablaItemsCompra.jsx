import React from "react";
import { Row, Form, Select, Col, Button } from "antd";
import { Container } from "shards-react";
const { Option } = Select;

const TablaItemsCompra = ({ productos, filas, setFilas, setError }) => {
  const handleAddRow = () => {
    setError(false);
    const item = {
      producto: null,
      cantidad: null,
      precioUnitario: null
    };
    setFilas([...filas, item]);
  };
  const handleRemoveRow = idx => {
    const rows = [...filas];
    rows.splice(idx, 1);
    setFilas([...rows]);
  };

  const setearDato = (val, type, id) => {
    const filasCopia = [...filas];
    filasCopia[id][type] = val;

    if (type === "producto") {
      let precioProd = buscarPrecioProd(val);
      filasCopia[id]["precioUnitario"] = precioProd.precio;
    }

    setFilas([...filasCopia]);
  };

  const buscarPrecioProd = id => {
    let prod = productos.filter(prod => prod.id === id);
    return prod[0];
  };

  return (
    <Container fluid className="main-content-container px-4">
      <Row>
        <Col xs={24} span={8}>
          <Button onClick={() => handleAddRow()} type="primary">
            Agregar
          </Button>
        </Col>
      </Row>
      <Row className="page-header py-4">
        <table className="table table-bordered table-hover">
          <thead>
            <tr>
              <th className="text-center"> Producto </th>
              <th className="text-center"> Cantidad </th>
              <th className="text-center"> Precio U. </th>
              <th className="text-center"> </th>
            </tr>
          </thead>
          <tbody>
            {filas.map((item, idx) => (
              <tr key={idx}>
                <td>
                  <Form.Item>
                    <Select
                      showSearch
                      allowClear
                      style={{ width: 200 }}
                      placeholder="Elegí el producto"
                      optionFilterProp="children"
                      initialValue={null}
                      onChange={val => setearDato(val, "producto", idx)}
                      filterOption={(input, option) =>
                        option.children
                          .toLowerCase()
                          .indexOf(input.toLowerCase()) >= 0
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
                  </Form.Item>
                </td>
                <td>
                  <input
                    type="number"
                    onChange={val =>
                      setearDato(val.target.value, "cantidad", idx)
                    }
                    className="form-control"
                  />
                </td>
                <td>
                  <input
                    type="number"
                    placeholder={filas[idx].precioUnitario}
                    onChange={val =>
                      setearDato(val.target.value, "precioUnitario", idx)
                    }
                    className="form-control"
                  />
                </td>
                <td>
                  <Button
                    type="success"
                    onClick={() => handleRemoveRow(idx)}
                    disabled
                  >
                    Eliminar (En desarrollo)
                  </Button>
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      </Row>
    </Container>
  );
};

export default TablaItemsCompra;
