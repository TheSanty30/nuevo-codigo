<?php

namespace app\controllers;

use app\models\mainModel;
use app\models\viewsModel;

class userController extends mainModel
{
    /*----------  Controlador registrar usuario  ----------*/
    public function registrarUsuarioControlador()
    {
        //Almacenado los datos
        $nombre = $this->limpiarCadena($_POST['usuario_nombre']);
        $apellido = $this->limpiarCadena($_POST['usuario_apellido']);
        $usuario = $this->limpiarCadena($_POST['usuario_usuario']);
        $email = $this->limpiarCadena($_POST['usuario_email']);
        $clave1 = $this->limpiarCadena($_POST['usuario_clave_1']);
        $clave2 = $this->limpiarCadena($_POST['usuario_clave_2']);

        //Verificando campos obligatorios
        if ($nombre == "" || $apellido == "" || $usuario == "" || $clave1 == "" || $clave2 == "") {
            $alerta = [
                "tipo" => "simple",
                "titulo" => "Ocurrió un error inesperado",
                "text" => "Existen campos vacios",
                "icono" => "error"
            ];
            return json_encode($alerta);
            exit();
        }

        //Verificando integridad de los datos
        if ($this->verificarDatos("[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{3,40}", $nombre)) {
            $alerta = [
                "tipo" => "simple",
                "titulo" => "Ocurrió un error inesperado",
                "text" => "El nombre no coincide con el formato solicitado",
                "icono" => "error"
            ];
            return json_encode($alerta);
            exit();
        }

        if ($this->verificarDatos("[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{3,40}", $apellido)) {
            $alerta = [
                "tipo" => "simple",
                "titulo" => "Ocurrió un error inesperado",
                "text" => "El apellido no coincide con el formato solicitado",
                "icono" => "error"
            ];
            return json_encode($alerta);
            exit();
        }

        if ($this->verificarDatos("[a-zA-Z0-9]{4,20}", $usuario)) {
            $alerta = [
                "tipo" => "simple",
                "titulo" => "Ocurrió un error inesperado",
                "text" => "El usuario no coincide con el formato solicitado",
                "icono" => "error"
            ];
            return json_encode($alerta);
            exit();
        }

        if ($this->verificarDatos("[a-zA-Z0-9$@.-]{7,100}", $clave1) || $this->verificarDatos("[a-zA-Z0-9$@.-]{7,100}", $clave2)) {
            $alerta = [
                "tipo" => "simple",
                "titulo" => "Ocurrió un error inesperado",
                "text" => "Las claves no coinciden con el formato solicitado",
                "icono" => "error"
            ];
            return json_encode($alerta);
            exit();
        }

        if ($email != "") {
            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $check_email = $this->ejecutarConsulta("SELECT usuario_email FROM usuario WHERE usuario_email ='$email'");
                if ($check_email->rowCount() > 0) {
                    $alerta = [
                        "tipo" => "simple",
                        "titulo" => "Ocurrió un error inesperado",
                        "text" => "El correo ingresado ya se encuentra registrado",
                        "icono" => "error"
                    ];
                    return json_encode($alerta);
                    exit();
                }
            } else {
                $alerta = [
                    "tipo" => "simple",
                    "titulo" => "Ocurrió un error inesperado",
                    "text" => "Ha ingresado un email no valido",
                    "icono" => "error"
                ];
                return json_encode($alerta);
                exit();
            }
        }

        if ($clave1 != $clave2) {
            $alerta = [
                "tipo" => "simple",
                "titulo" => "Ocurrió un error inesperado",
                "text" => "Las claves no coinciden",
                "icono" => "error"
            ];
            return json_encode($alerta);
            exit();
        } else {
            $clave = password_hash($clave1, PASSWORD_BCRYPT, ["cost" => 10]);
        }

        $check_usuario = $this->ejecutarConsulta("SELECT usuario_usuario FROM usuario WHERE usuario_usuario ='$usuario'");
        if ($check_usuario->rowCount() > 0) {
            $alerta = [
                "tipo" => "simple",
                "titulo" => "Ocurrió un error inesperado",
                "text" => "El usuario ya esta en uso, cambie a uno diferente",
                "icono" => "error"
            ];
            return json_encode($alerta);
            exit();
        }

        $usuario_datos_reg = [
            [
                "campo_nombre" => "usuario_nombre",
                "campo_marcador" => ":Nombre",
                "campo_valor" => $nombre
            ],
            [
                "campo_nombre" => "usuario_apellido",
                "campo_marcador" => ":Apellido",
                "campo_valor" => $apellido
            ],
            [
                "campo_nombre" => "usuario_email",
                "campo_marcador" => ":Email",
                "campo_valor" => $email
            ],
            [
                "campo_nombre" => "usuario_usuario",
                "campo_marcador" => ":Usuario",
                "campo_valor" => $usuario
            ],
            [
                "campo_nombre" => "usuario_clave",
                "campo_marcador" => ":Clave",
                "campo_valor" => $clave
            ],
            [
                "campo_nombre" => "usuario_creado",
                "campo_marcador" => ":Creado",
                "campo_valor" => date("Y-m-d H:i:s")
            ],
            [
                "campo_nombre" => "usuario_actualizado",
                "campo_marcador" => ":Actualizado",
                "campo_valor" => date("Y-m-d H:i:s")
            ]
        ];

        $registrar_usuario = $this->guardarDatos("usuario", $usuario_datos_reg);
        if ($registrar_usuario->rowCount() == 1) {
            $alerta = [
                "tipo" => "limpiar",
                "titulo" => "Usuario Registrado",
                "text" => "El usuario " . $nombre . " " . $apellido . " se registro con exito",
                "icono" => "success"
            ];
        } else {
            $alerta = [
                "tipo" => "simple",
                "titulo" => "Ocurrió un error inesperado",
                "text" => "No se pudo registrar el usuario, por favor intentelo nuevamente",
                "icono" => "error"
            ];
        }
        return json_encode($alerta);
    }

    /*----------  Controlador listar usuario  ----------*/
    public function listarUsuariosControlador($pagina, $registros, $url, $busqueda)
    {
        $pagina = $this->limpiarCadena($pagina);
        $registros = $this->limpiarCadena($registros);

        $url = $this->limpiarCadena($url);
        $url = APP_URL . $url . "/";

        $busqueda = $this->limpiarCadena($busqueda);
        $tabla = "";

        $pagina = (isset($pagina) && $pagina > 0) ? (int)$pagina : 1;
        $inicio = ($pagina > 0) ? (($pagina * $registros) - $registros) : 0;

        if (isset($busqueda) && $busqueda != "") {
            $consulta_datos = "SELECT * FROM usuario WHERE ((usuario_id != '" . $_SESSION['id'] . "' AND usuario_id != '1') AND (usuario_nombre LIKE '%$busqueda%' OR usuario_email LIKE '%$busqueda%' OR usuario_nombreapellido LIKE '%$busqueda%' OR usuario_usuario LIKE '%$busqueda%')) ORDER BY usuario_nombre ASC LIMIT $inicio, $registros";

            $consulta_total = "SELECT COUNT(usuario_id) FROM usuario WHERE ((usuario_id != '" . $_SESSION['id'] . "' AND usuario_id != '1') AND (usuario_nombre LIKE '%$busqueda%' OR usuario_email LIKE '%$busqueda%' OR usuario_nombreapellido LIKE '%$busqueda%' OR usuario_usuario LIKE '%$busqueda%'))";
        } else {
            $consulta_datos = "SELECT * FROM usuario WHERE usuario_id != '" . $_SESSION['id'] . "' AND usuario_id != '1' ORDER BY usuario_nombre ASC LIMIT $inicio, $registros";

            $consulta_total = "SELECT COUNT(usuario_id) FROM usuario WHERE usuario_id != '" . $_SESSION['id'] . "' AND usuario_id != '1'";
        }

        $datos = $this->ejecutarConsulta($consulta_datos);
        $datos = $datos->fetchAll();

        $total = $this->ejecutarConsulta($consulta_total);
        $total = (int)$total->fetchColumn();

        $numeroPaginas = ceil($total / $registros);

        $tabla .= '
            <div class="container mt-5">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover">
                        <thead>
                            <tr>
                                <th class="text-center">#</th>
                                <th class="text-center">Nombre</th>
                                <th class="text-center">Usuario</th>
                                <th class="text-center">Email</th>
                                <th class="text-center">Creado</th>
                                <th class="text-center">Actualizado</th>
                                <th class="text-center" colspan="3">Opciones</th>
                            </tr>
                        </thead>
                        <tbody>
        ';

        if ($total >= 1 && $pagina <= $numeroPaginas) {
            $contador = $inicio + 1;
            $pag_inicio = $inicio + 1;
            foreach ($datos as $rows) {
                $tabla .= '
                <tr class="text-center">
                    <td>' . $contador . '</td>
                    <td>
                        ' . $rows['usuario_nombre'] . ' ' . $rows['usuario_apellido'] . '
                    </td>
                    <td>' . $rows['usuario_usuario'] . '</td>
                    <td>' . $rows['usuario_email'] . '</td>
                    <td>
                        ' . date("d-m-Y h:i:s A", strtotime($rows['usuario_creado'])) . '
                    </td>
                    <td>
                        ' . date("d-m-Y h:i:s A", strtotime($rows['usuario_actualizado'])) . '
                    </td>

                    <td>
                        <a href="' . APP_URL . 'userUpdate/' . $rows['usuario_id'] . '" class="btn btn-success btn-sm rounded-pill">Actualizar</a>
                    </td>
                    <td>
                        <form class="FormularioAjax" action="' . APP_URL . 'app/ajax/usuarioAjax.php" method="POST" autocomplete="off">

                            <input type="hidden" name="modulo_usuario" value="eliminar">
                            <input type="hidden" name="usuario_id" value="' . $rows['usuario_id'] . '">

                            <button type="submit" class="btn btn-danger btn-sm rounded-pill">Eliminar</button>
                        </form>
                    </td>
                </tr>
                ';
                $contador++;
            }
            $pag_final = $contador - 1;
        } else {
            if ($total >= 1) {
                $tabla .= '
                    <tr class="text-center">
                        <td colspan="12">
                            <a href="' . $url . '1/" class="btn btn-link rounded-pill mt-4 mb-4">
                                Haga clic acá para recargar el listado
                            </a>
                        </td>
                    </tr>
                ';
            } else {
                $total .= '
                <tr class="text-center">
                    <td colspan="12">
                        No hay registros en el sistema
                    </td>
                </tr>
                ';
            }
        }

        $tabla .= '
                    </tbody>
                </table>
            </div>
        </div>
        ';

        if ($total >= 1 && $pagina <= $numeroPaginas) {
            $tabla .= '
                <div class="container py-4">
                    <p style="text-align:right">Mostrando usuarios <strong>' . $pag_inicio . '</strong> al <strong>' . $pag_final . '</strong> de un <strong>total de ' . $total . '</strong></p>
                </div>
            ';

            $tabla .= $this->paginadorTablas($pagina, $numeroPaginas, $url, 10);
        }

        return $tabla;
    }

    /*----------  Controlador eliminar usuario  ----------*/
    public function eliminarUsuarioControlador()
    {
        $id = $this->limpiarCadena(($_POST['usuario_id']));

        if ($id == 1) {
            $alerta = [
                "tipo" => "simple",
                "titulo" => "Ocurrió un error inesperado",
                "text" => "No podemos eliminar el usuario principal",
                "icono" => "error"
            ];
            return json_encode($alerta);
            exit();
        }

        $datos = $this->ejecutarConsulta("SELECT * FROM usuario WHERE usuario_id ='$id'");

        if ($datos->rowCount() <= 0) {
            $alerta = [
                "tipo" => "simple",
                "titulo" => "Ocurrió un error inesperado",
                "text" => "No podemos encontrado el usuario en el sistema",
                "icono" => "error"
            ];
            return json_encode($alerta);
            exit();
        } else {
            $datos = $datos->fetch();
        }

        $eliminarUsuario = $this->eliminarDatos("usuario", "usuario_id", $id);

        if ($eliminarUsuario->rowCount() == 1) {
            $alerta = [
                "tipo" => "recargar",
                "titulo" => "Usuario eliminado",
                "text" => "EL usuario" . $datos['usuario_nombre'] . $datos['usuario_apellido'] . " se eliminó con exito",
                "icono" => "success"
            ];
        } else {
            $alerta = [
                "tipo" => "simple",
                "titulo" => "Ocurrió un error inesperado",
                "text" => "No se pudo eliminar el usuario, intente nuevamente",
                "icono" => "error"
            ];
        }
        return json_encode($alerta);
    }

    /*----------  Controlador actualizar usuario  ----------*/
    public function actualizarUsuarioControlador()
    {
        $id = $this->limpiarCadena(($_POST['usuario_id']));

        $datos = $this->ejecutarConsulta("SELECT * FROM usuario WHERE usuario_id ='$id'");

        if ($datos->rowCount() <= 0) {
            $alerta = [
                "tipo" => "simple",
                "titulo" => "Ocurrió un error inesperado",
                "text" => "No podemos encontrado el usuario en el sistema",
                "icono" => "error"
            ];
            return json_encode($alerta);
            exit();
        } else {
            $datos = $datos->fetch();
        }

        $admin_usuario = $this->limpiarCadena(($_POST['administrador_usuario']));
        $admin_password = $this->limpiarCadena(($_POST['administrador_clave']));

        if ($admin_usuario == "" || $admin_password == "") {
            $alerta = [
                "tipo" => "simple",
                "titulo" => "Ocurrió un error inesperado",
                "text" => "Es necesario ingresar tu Usuario y Contraseña logueada",
                "icono" => "error"
            ];
            return json_encode($alerta);
            exit();
        }

        if ($this->verificarDatos("[a-zA-Z0-9 ]{4,20}", $admin_usuario)) {
            $alerta = [
                "tipo" => "simple",
                "titulo" => "Ocurrió un error inesperado",
                "text" => "Su USUARIO no conicide con el formato seleccionado",
                "icono" => "error"
            ];
            return json_encode($alerta);
            exit();
        }

        if ($this->verificarDatos("[a-zA-Z0-9$@.-]{7,100}", $admin_password)) {
            $alerta = [
                "tipo" => "simple",
                "titulo" => "Ocurrió un error inesperado",
                "text" => "Su PASSWORD no conicide con el formato seleccionado",
                "icono" => "error"
            ];
            return json_encode($alerta);
            exit();
        }

        $check_admin = $this->ejecutarConsulta("SELECT * FROM usuario WHERE usuario_usuario = '$admin_usuario' AND usuario_id = '" . $_SESSION['id'] . "'");
        if ($check_admin->rowCount() == 1) {
            $check_admin = $check_admin->fetch();
            if ($check_admin['usuario_usuario'] != $admin_usuario || !password_verify($admin_password, $check_admin['usuario_clave'])) {
                $alerta = [
                    "tipo" => "simple",
                    "titulo" => "Ocurrió un error inesperado",
                    "text" => "Usuario o Password Administrador Incorrectos",
                    "icono" => "error"
                ];
                return json_encode($alerta);
                exit();
            }
        } else {
            $alerta = [
                "tipo" => "simple",
                "titulo" => "Ocurrió un error inesperado",
                "text" => "Usuario o Password Incorrectos",
                "icono" => "error"
            ];
            return json_encode($alerta);
            exit();
        }

        $nombre = $this->limpiarCadena($_POST['usuario_nombre']);
        $apellido = $this->limpiarCadena($_POST['usuario_apellido']);
        $usuario = $this->limpiarCadena($_POST['usuario_usuario']);
        $email = $this->limpiarCadena($_POST['usuario_email']);
        $clave1 = $this->limpiarCadena($_POST['usuario_clave_1']);
        $clave2 = $this->limpiarCadena($_POST['usuario_clave_2']);

        //Verificando campos obligatorios
        if ($nombre == "" || $apellido == "" || $usuario == "") {
            $alerta = [
                "tipo" => "simple",
                "titulo" => "Ocurrió un error inesperado",
                "text" => "Existen campos vacios",
                "icono" => "error"
            ];
            return json_encode($alerta);
            exit();
        }

        //Verificando integridad de los datos
        if ($this->verificarDatos("[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{3,40}", $nombre)) {
            $alerta = [
                "tipo" => "simple",
                "titulo" => "Ocurrió un error inesperado",
                "text" => "El nombre no coincide con el formato solicitado",
                "icono" => "error"
            ];
            return json_encode($alerta);
            exit();
        }

        if ($this->verificarDatos("[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{3,40}", $apellido)) {
            $alerta = [
                "tipo" => "simple",
                "titulo" => "Ocurrió un error inesperado",
                "text" => "El apellido no coincide con el formato solicitado",
                "icono" => "error"
            ];
            return json_encode($alerta);
            exit();
        }

        if ($this->verificarDatos("[a-zA-Z0-9]{4,20}", $usuario)) {
            $alerta = [
                "tipo" => "simple",
                "titulo" => "Ocurrió un error inesperado",
                "text" => "El usuario no coincide con el formato solicitado",
                "icono" => "error"
            ];
            return json_encode($alerta);
            exit();
        }

        if ($email != "" && $datos['usuario_email'] != $email) {
            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $check_email = $this->ejecutarConsulta("SELECT usuario_email FROM usuario WHERE usuario_email ='$email'");
                if ($check_email->rowCount() > 0) {
                    $alerta = [
                        "tipo" => "simple",
                        "titulo" => "Ocurrió un error inesperado",
                        "text" => "El correo ingresado ya se encuentra registrado",
                        "icono" => "error"
                    ];
                    return json_encode($alerta);
                    exit();
                }
            } else {
                $alerta = [
                    "tipo" => "simple",
                    "titulo" => "Ocurrió un error inesperado",
                    "text" => "Ha ingresado un email no valido",
                    "icono" => "error"
                ];
                return json_encode($alerta);
                exit();
            }
        }

        if ($clave1 != "" || $clave2 != "") {
            if ($this->verificarDatos("[a-zA-Z0-9$@.-]{7,100}", $clave1) || $this->verificarDatos("[a-zA-Z0-9$@.-]{7,100}", $clave2)) {
                $alerta = [
                    "tipo" => "simple",
                    "titulo" => "Ocurrió un error inesperado",
                    "text" => "Las claves no coinciden con el formato solicitado",
                    "icono" => "error"
                ];
                return json_encode($alerta);
                exit();
            } else {
                if ($clave1 != $clave2) {
                    $alerta = [
                        "tipo" => "simple",
                        "titulo" => "Ocurrió un error inesperado",
                        "text" => "Las claves no coinciden",
                        "icono" => "error"
                    ];
                    return json_encode($alerta);
                    exit();
                } else {
                    $clave = password_hash($clave1, PASSWORD_BCRYPT, ["cost" => 10]);
                }
            }
        } else {
            $clave = $datos['usuario_clave'];
        }

        if ($datos['usuario_usuario'] != $usuario) {
            $check_usuario = $this->ejecutarConsulta("SELECT usuario_usuario FROM usuario WHERE usuario_usuario ='$usuario'");
            if ($check_usuario->rowCount() > 0) {
                $alerta = [
                    "tipo" => "simple",
                    "titulo" => "Ocurrió un error inesperado",
                    "text" => "El usuario ya esta en uso, cambie a uno diferente",
                    "icono" => "error"
                ];
                return json_encode($alerta);
                exit();
            }
        }

        $usuario_datos_up = [
            [
                "campo_nombre" => "usuario_nombre",
                "campo_marcador" => ":Nombre",
                "campo_valor" => $nombre
            ],
            [
                "campo_nombre" => "usuario_apellido",
                "campo_marcador" => ":Apellido",
                "campo_valor" => $apellido
            ],
            [
                "campo_nombre" => "usuario_email",
                "campo_marcador" => ":Email",
                "campo_valor" => $email
            ],
            [
                "campo_nombre" => "usuario_usuario",
                "campo_marcador" => ":Usuario",
                "campo_valor" => $usuario
            ],
            [
                "campo_nombre" => "usuario_clave",
                "campo_marcador" => ":Clave",
                "campo_valor" => $clave
            ],
            [
                "campo_nombre" => "usuario_actualizado",
                "campo_marcador" => ":Actualizado",
                "campo_valor" => date("Y-m-d H:i:s")
            ]
        ];

        $condicion = [
            "condicion_campo" => "usuario_id",
            "condicion_marcador" => ":Id",
            "condicion_valor" => $id
        ];

        if ($this->actualizarDatos("usuario", $usuario_datos_up, $condicion)) {
            if ($id == $_SESSION['id']) {
                $_SESSION['nombre'] = $nombre;
                $_SESSION['apellido'] = $apellido;
                $_SESSION['usuario'] = $usuario;
            }
            $alerta = [
                "tipo" => "recargar",
                "titulo" => "Usuario Actualizado",
                "text" => "El usuario " . $datos['usuario_nombre'] . " " . $datos['usuario_apellido'] . " se actualizó con exito",
                "icono" => "success"
            ];
        } else {
            $alerta = [
                "tipo" => "simple",
                "titulo" => "Ocurrió un error inesperado",
                "text" => "No se pudo actualizar el usuario, por favor intentelo nuevamente",
                "icono" => "error"
            ];
        }
        return json_encode($alerta);
    }
}
