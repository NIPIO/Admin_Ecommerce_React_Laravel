import React from "react";
import { Row, Form, Select, Col, Button } from "antd";
import { Container } from "shards-react";
const { Option } = Select;

const VerTablaItemsCompra = ({
  filas,
  productos,
  editarCompra,
  setFilas,
  setError
}) => {
  const handleAddRow = () => {
    setError(false);
    let filasCopia = [...filas];
    const item = {
      producto: 1,
      cantidad: 0,
      costo: 0
    };
    filasCopia.push(item);
    setFilas([...filasCopia]);
  };
  // const handleRemoveRow = idx => {
  //   setError(false);

  //   let filasCopia = [...filas];
  //   filasCopia.splice(idx, 1);
  //   setFilas([...filasCopia]);
  // };

  const setearDato = (val, type, id) => {
    setError(false);

    let filasCopia = [...filas];
    filasCopia[id][type] = val;
    setFilas([...filasCopia]);
  };

  return (
    <Container fluid className="main-content-container px-4">
      <Row>
        <Col xs={24} span={8}>
          <Button
            onClick={() => handleAddRow()}
            type="primary"
            disabled={!editarCompra}
          >
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
              <th className="text-center"> Costo U. </th>
              {/* <th className="text-center"> </th> */}
            </tr>
          </thead>
          <tbody>
            {filas.map((item, idx) => (
              <tr key={idx}>
                <td>
                  <Form.Item>
                    <Select
                      showSearch
                      style={{ marginBottom: "3%", width: "100%" }}
                      placeholder="Elegí el producto"
                      optionFilterProp="children"
                      value={item.producto_id}
                      disabled={!editarCompra}
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
                      {productos.map((producto, idx) => (
                        <Option key={idx} value={producto.id}>
                          {producto.nombre}
                        </Option>
                      ))}
                    </Select>
                  </Form.Item>
                </td>
                <td>
                  <input
                    value={item.cantidad}
                    type="number"
                    disabled={!editarCompra}
                    onChange={val =>
                      setearDato(val.target.value, "cantidad", idx)
                    }
                    className="form-control"
                  />
                </td>
                <td>
                  <input
                    value={item.costo}
                    disabled={!editarCompra}
                    type="number"
                    onChange={val => setearDato(val.target.value, "costo", idx)}
                    className="form-control"
                  />
                </td>
                {/* <td>
                  <Button
                    type="success"
                    onClick={() => handleRemoveRow(idx)}
                    disabled={!editarCompra}
                  >
                    Eliminar
                  </Button>
                </td> */}
              </tr>
            ))}
          </tbody>
        </table>
      </Row>
    </Container>
  );
};

export default VerTablaItemsCompra;
