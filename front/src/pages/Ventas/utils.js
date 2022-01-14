import { Space, Button, Switch } from "antd";
import React from "react";
import { notification } from "antd";
import { api } from "./../../hooks/api";
export const columnas = [
  {
    title: "Cliente",
    dataIndex: ["cliente", "nombre"],
    fixed: "left",
    render: text => text
  },
  {
    title: "Vendedor",
    dataIndex: ["vendedor", "nombre"],
    render: text => text
  },
  {
    title: "Total",
    dataIndex: ["precio_total"],
    render: text => text,
    sorter: (a, b) => a.precio_total - b.precio_total
  },
  {
    title: "Fecha",
    dataIndex: ["fecha_venta"],
    render: text => text
  },
  {
    title: "Estado",
    dataIndex: ["activo"],
    render: (text, row) => (
      <Space>
        <Switch
          checked={text}
          onChange={() => cambiarEstado("Venta", row.id, text)}
          checkedChildren={"Activo"}
          unCheckedChildren={"Inactivo"}
        />
      </Space>
    )
  },
  {
    title: "Acciones",
    key: "action",

    render: (text, record) => (
      <Space size="middle">
        <Button>Editar</Button>
      </Space>
    )
  }
];

const cambiarEstado = (tabla, id, estado) => {
  api
    .patchEstado(tabla, id, estado)
    .then(res => {
      if (res.error) {
        openNotificationWithIcon("error", "Ocurrio un error", res.data);
      } else {
        openNotificationWithIcon("success", "Cambio realizado!", "");
      }
    })
    .catch(err => {
      openNotificationWithIcon(
        "error",
        "Ocurrio un error",
        err.response.data.message
      );
    });
};

const openNotificationWithIcon = (type, message, description) => {
  notification[type]({
    message,
    description,
    placement: "bottomRight"
  });
};
