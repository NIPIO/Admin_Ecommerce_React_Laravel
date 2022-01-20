import { notification } from "antd";

import { useCambiarEstado } from "../hooks/apiCalls";

export const showNotification = (type, message, description) => {
  notification[type]({
    message,
    description,
    placement: "bottomRight"
  });
};

export const toggleEstado = (
  tabla,
  refreshQuery,
  id,
  estado,
  queryClient = undefined
) => {
  useCambiarEstado(tabla, id, estado)
    .then(res => {
      if (res.error) {
        showNotification("error", "Ocurrio un error", res.data);
      } else {
        showNotification("success", "Cambio realizado!", "");
        queryClient.invalidateQueries(refreshQuery);
      }
    })
    .catch(err => {
      showNotification("error", "Ocurrio un error", err.response.data.message);
    });
};
