import React, { useState } from "react";
import { api } from "./../../hooks/api";
import { Alert } from "antd";

const NuevoUsuario = () => {
  const [nombre, setNombre] = useState("");
  const [usuario, setUsuario] = useState("");
  const [password, setPassword] = useState("");
  const [repassword, setRepassword] = useState("");
  const [error, setError] = useState("");

  const registrarse = () => {
    // if (repassword !== password) {
    //   setError("Las contraseñas tienen que ser la misma che");
    // } else if (nombre === "" || usuario === "") {
    //   setError("Todos los campos son necesarios");
    // } else {
    //   api
    //     .postRegistro({ usuario, password, nombre })
    //     .then(res => {
    //       if (res.status === 400 || res.status === 500) {
    //         setError(res.data);
    //       } else {
    //         localStorage.setItem("logueado", JSON.stringify(res.data));
    //         window.location.reload();
    //       }
    //     })
    //     .catch(err => {
    //       console.log(err);
    //     });
    // }
  };

  return (
    <div className="register-box mt-5 mx-auto">
      <div className="register-logo">
        <a href="../../index2.html">
          <b>Empresa</b>Harry
        </a>
      </div>
      <div className="card">
        <div className="card-body register-card-body">
          <div className="input-group mb-3">
            <input
              type="text"
              className="form-control"
              onChange={e => setNombre(e.target.value)}
              placeholder="Nombre"
            />
            <div className="input-group-append">
              <div className="input-group-text">
                <span className="fas fa-envelope" />
              </div>
            </div>
          </div>
          <div className="input-group mb-3">
            <input
              type="text"
              className="form-control"
              onChange={e => setUsuario(e.target.value)}
              placeholder="Usuario"
            />
            <div className="input-group-append">
              <div className="input-group-text">
                <span className="fas fa-user" />
              </div>
            </div>
          </div>

          <div className="input-group mb-3">
            <input
              type="password"
              className="form-control"
              onChange={e => setPassword(e.target.value)}
              placeholder="Contraseña"
            />
            <div className="input-group-append">
              <div className="input-group-text">
                <span className="fas fa-lock" />
              </div>
            </div>
          </div>
          <div className="input-group mb-3">
            <input
              type="password"
              className="form-control"
              onChange={e => setRepassword(e.target.value)}
              placeholder="Repita Contraseña"
            />
            <div className="input-group-append">
              <div className="input-group-text">
                <span className="fas fa-lock" />
              </div>
            </div>
          </div>

          <div className="row">
            <div className="col-md-6 col-sm-12">
              <button
                type="submit"
                onClick={() => registrarse()}
                className="btn btn-success btn-block"
              >
                Registrarse
              </button>
            </div>
            <div className="col-md-6 col-sm-12">
              <a type="submit" a href="/" className="btn btn-primary btn-block">
                Tengo usuario
              </a>
            </div>
          </div>
          <div className="mt-3">
            {error.length > 0 && (
              <Alert variant="danger" style={{ textAlign: "center" }}>
                {error}
              </Alert>
            )}
          </div>
        </div>
      </div>
    </div>
  );
};
export default NuevoUsuario;
