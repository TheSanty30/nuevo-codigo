<?php

namespace app\controllers;

use app\models\mainModel;

class userController extends mainModel
{

    /*----------  Controlador registrar usuario  ----------*/
    public function registrarUsuarioControlador()
    {
        $alertas = [
            "campos_vacios" => [
                "tipo" => "simple",
                "titulo" => "Ocurrió un error inesperado",
                "text" => "Existen campos vacíos",
                "icono" => "error"
            ],
            "email_existente" => [
                "tipo" => "simple",
                "titulo" => "Ocurrió un error inesperado",
                "text" => "El correo ingresado ya se encuentra registrado",
                "icono" => "error"
            ],
            "email_invalido" => [
                "tipo" => "simple",
                "titulo" => "Ocurrió un error inesperado",
                "text" => "Ha ingresado un email no valido",
                "icono" => "error"
            ],
            "claves_diferentes" => [
                "tipo" => "simple",
                "titulo" => "Ocurrió un error inesperado",
                "text" => "Las claves no coinciden",
                "icono" => "error"
            ],
            "usuario_existente" => [
                "tipo" => "simple",
                "titulo" => "Ocurrió un error inesperado",
                "text" => "El usuario ya esta en uso, cambie a uno diferente",
                "icono" => "error"
            ],
            // Agrega más tipos de alertas según sea necesario
        ];

        // Verificar campos vacíos
        $campos_obligatorios = ['usuario_nombre', 'usuario_apellido', 'usuario_usuario', 'usuario_clave_1', 'usuario_clave_2'];
        foreach ($campos_obligatorios as $campo) {
            if (empty($_POST[$campo])) {
                return json_encode($alertas["campos_vacios"]);
            }
        }

        // Verificar datos
        $verificaciones = [
            "usuario_nombre" => ["mensaje" => "El nombre no coincide con el formato solicitado", "patron" => "[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{3,40}"],
            "usuario_apellido" => ["mensaje" => "El apellido no coincide con el formato solicitado", "patron" => "[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{3,40}"],
            "usuario_usuario" => ["mensaje" => "El usuario no coincide con el formato solicitado", "patron" => "[a-zA-Z0-9]{4,20}"],
            "usuario_clave_1" => ["mensaje" => "Las claves no coinciden con el formato solicitado", "patron" => "[a-zA-Z0-9$@.-]{7,100}"],
            "usuario_clave_2" => ["mensaje" => "Las claves no coinciden con el formato solicitado", "patron" => "[a-zA-Z0-9$@.-]{7,100}"]
        ];

        foreach ($verificaciones as $campo => $info) {
            if ($this->verificarDatos($info["patron"], $_POST[$campo])) {
                return json_encode(["tipo" => "simple", "titulo" => "Ocurrió un error inesperado", "text" => $info["mensaje"], "icono" => "error"]);
            }
        }

        // Verificar email
        if (!empty($_POST['usuario_email'])) {
            if (!filter_var($_POST['usuario_email'], FILTER_VALIDATE_EMAIL)) {
                return json_encode($alertas["email_invalido"]);
            }
            $check_email = $this->ejecutarConsulta("SELECT usuario_email FROM usuario WHERE usuario_email = '{$_POST['usuario_email']}'");
            if ($check_email->rowCount() > 0) {
                return json_encode($alertas["email_existente"]);
            }
        }

        // Verificar claves
        if ($_POST['usuario_clave_1'] != $_POST['usuario_clave_2']) {
            return json_encode($alertas["claves_diferentes"]);
        } else {
            $clave = $_POST['usuario_clave_1'];
        }

        // Verificar usuario
        $check_usuario = $this->ejecutarConsulta("SELECT usuario_usuario FROM usuario WHERE usuario_usuario = '{$_POST['usuario_email']}'");
        if ($check_usuario->rowCount() > 0) {
            return json_encode($alertas["usuario_existente"]);
        }

        // Registrar usuario
        $usuario_datos_reg = [
            [
                "campo_nombre" => "usuario_nombre",
                "campo_marcador" => ":Nombre",
                "campo_valor" => $_POST['usuario_nombre']
            ],
            [
                "campo_nombre" => "usuario_apellido",
                "campo_marcador" => ":Apellido",
                "campo_valor" => $_POST['usuario_apellido']
            ],
            [
                "campo_nombre" => "usuario_email",
                "campo_marcador" => ":Email",
                "campo_valor" => $_POST['usuario_email']
            ],
            [
                "campo_nombre" => "usuario_usuario",
                "campo_marcador" => ":Usuario",
                "campo_valor" => $_POST['usuario_usuario']
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
            // Agregar otros campos aquí...
        ];

        $registrar_usuario = $this->guardarDatos("usuario", $usuario_datos_reg);

        if ($registrar_usuario->rowCount() == 1) {
            return json_encode(["tipo" => "limpiar", "titulo" => "Usuario Registrado", "text" => "El usuario se registró con éxito", "icono" => "success"]);
        } else {
            return json_encode(["tipo" => "simple", "titulo" => "Ocurrió un error inesperado", "text" => "No se pudo registrar el usuario, por favor inténtelo nuevamente", "icono" => "error"]);
        }
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

        ### Paginacion ###
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

        # Verificando usuario #
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
        $alertas = [
            "campos_vacios" => [
                "tipo" => "simple",
                "titulo" => "Ocurrió un error inesperado",
                "text" => "Existen campos vacíos",
                "icono" => "error"
            ],
            "email_existente" => [
                "tipo" => "simple",
                "titulo" => "Ocurrió un error inesperado",
                "text" => "El correo ingresado ya se encuentra registrado",
                "icono" => "error"
            ],
            "email_invalido" => [
                "tipo" => "simple",
                "titulo" => "Ocurrió un error inesperado",
                "text" => "Ha ingresado un email no valido",
                "icono" => "error"
            ],
            "claves_diferentes" => [
                "tipo" => "simple",
                "titulo" => "Ocurrió un error inesperado",
                "text" => "Las claves no coinciden",
                "icono" => "error"
            ],
            "usuario_existente" => [
                "tipo" => "simple",
                "titulo" => "Ocurrió un error inesperado",
                "text" => "El usuario ya esta en uso, cambie a uno diferente",
                "icono" => "error"
            ],
            "session_invalido" => [
                "tipo" => "simple",
                "titulo" => "Ocurrió un error inesperado",
                "text" => "Usuario o Password Administrador Incorrectos",
                "icono" => "error"
            ],
            "no_session" => [
                "tipo" => "simple",
                "titulo" => "Ocurrió un error inesperado",
                "text" => "Es necesario ingresar tu Usuario y Contraseña logueada",
                "icono" => "error"
            ],
            "usuario_inexistente" => [
                "tipo" => "simple",
                "titulo" => "Ocurrió un error inesperado",
                "text" => "No se encontró el usuario en el sistema",
                "icono" => "error"
            ],
            // Agrega más tipos de alertas según sea necesario
        ];

        $id = $this->limpiarCadena(($_POST['usuario_id']));

        $datos = $this->ejecutarConsulta("SELECT * FROM usuario WHERE usuario_id ='$id'");

        if ($datos->rowCount() <= 0) {
            return json_encode($alertas["usuario_inexistente"]);
        } else {
            $datos = $datos->fetch();
        }

        $admin_usuario = $this->limpiarCadena(($_POST['administrador_usuario']));
        $admin_password = $this->limpiarCadena(($_POST['administrador_clave']));

        if ($admin_usuario == "" || $admin_password == "") {
            return json_encode($alertas["no_session"]);
        }

        // Verificar datos
        $verificaciones = [
            "usuario_nombre" => ["mensaje" => "El nombre no coincide con el formato solicitado", "patron" => "[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{3,40}"],
            "usuario_apellido" => ["mensaje" => "El apellido no coincide con el formato solicitado", "patron" => "[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{3,40}"],
            "usuario_usuario" => ["mensaje" => "El usuario no coincide con el formato solicitado", "patron" => "[a-zA-Z0-9]{4,20}"],
            "administrador_usuario" => ["mensaje" => "Su USUARIO no conicide con el formato seleccionado", "patron" => "[a-zA-Z0-9 ]{4,20}"],
            "administrador_clave" => ["mensaje" => "Su PASSWORD no conicide con el formato seleccionado", "patron" => "[a-zA-Z0-9$@.-]{7,100}"]
        ];

        foreach ($verificaciones as $campo => $info) {
            if ($this->verificarDatos($info["patron"], $_POST[$campo])) {
                return json_encode(["tipo" => "simple", "titulo" => "Ocurrió un error inesperado", "text" => $info["mensaje"], "icono" => "error"]);
            }
        }

        $check_admin = $this->ejecutarConsulta("SELECT * FROM usuario WHERE usuario_usuario = '$admin_usuario' AND usuario_id = '" . $_SESSION['id'] . "'");
        if ($check_admin->rowCount() == 1) {
            $check_admin = $check_admin->fetch();
            if ($check_admin['usuario_usuario'] != $admin_usuario || $check_admin['usuario_clave'] != $admin_password) {
                return json_encode($alertas["session_invalido"]);
            }
        } else {
            return json_encode($alertas["session_invalido"]);
        }

        $campos_obligatorios = ['usuario_nombre', 'usuario_apellido', 'usuario_usuario'];
        foreach ($campos_obligatorios as $campo) {
            if (empty($_POST[$campo])) {
                return json_encode($alertas["campos_vacios"]);
            }
        }

        if ($_POST['usuario_email'] != "" && $datos['usuario_email'] != $_POST['usuario_email']) {
            if (filter_var($_POST['usuario_email'], FILTER_VALIDATE_EMAIL)) {
                $check_email = $this->ejecutarConsulta("SELECT usuario_email FROM usuario WHERE usuario_email ='{$_POST['usuario_email']}'");
                if ($check_email->rowCount() > 0) {
                    return json_encode($alertas["email_existente"]);
                }
            } else {
                return json_encode($alertas["email_invalido"]);
            }
        }

        if ($_POST['usuario_clave_1'] != "" || $_POST['usuario_clave_2'] != "") {
            if ($this->verificarDatos("[a-zA-Z0-9$@.-]{7,100}", $_POST['usuario_clave_1']) || $this->verificarDatos("[a-zA-Z0-9$@.-]{7,100}", $_POST['usuario_clave_2'])) {
                $alerta = [
                    "tipo" => "simple",
                    "titulo" => "Ocurrió un error inesperado",
                    "text" => "Las claves no coinciden con el formato solicitado",
                    "icono" => "error"
                ];
                return json_encode($alerta);
                exit();
            } else {
                if ($_POST['usuario_clave_1'] != $_POST['usuario_clave_2']) {
                    $alerta = [
                        "tipo" => "simple",
                        "titulo" => "Ocurrió un error inesperado",
                        "text" => "Las claves no coinciden",
                        "icono" => "error"
                    ];
                    return json_encode($alerta);
                    exit();
                } else {
                    $clave = $_POST['usuario_clave_1'];
                }
            }
        } else {
            $clave = $datos['usuario_clave'];
        }

        if ($datos['usuario_usuario'] != $_POST['usuario_usuario']) {
            $check_usuario = $this->ejecutarConsulta("SELECT usuario_usuario FROM usuario WHERE usuario_usuario ='{$_POST['usuario_usuario']}) {'");
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
                "campo_valor" => $_POST['usuario_nombre']
            ],
            [
                "campo_nombre" => "usuario_apellido",
                "campo_marcador" => ":Apellido",
                "campo_valor" => $_POST['usuario_apellido']
            ],
            [
                "campo_nombre" => "usuario_email",
                "campo_marcador" => ":Email",
                "campo_valor" => $_POST['usuario_email']
            ],
            [
                "campo_nombre" => "usuario_usuario",
                "campo_marcador" => ":Usuario",
                "campo_valor" => $_POST['usuario_usuario']
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
            // Agregar otros campos aquí...
        ];

        $condicion = [
            "condicion_campo" => "usuario_id",
            "condicion_marcador" => ":Id",
            "condicion_valor" => $id
        ];

        if ($this->actualizarDatos("usuario", $usuario_datos_up, $condicion)) {
            if ($id == $_SESSION['id']) {
                $_SESSION['nombre'] = $_POST['usuario_nombre'];
                $_SESSION['apellido'] = $_POST['usuario_apellido'];
                $_SESSION['usuario'] = $_POST['usuario_usuario'];
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
