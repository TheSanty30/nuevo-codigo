<?php

require_once "../../config/app.php";
require_once "../views/inc/session_start.php";
require_once "../../autoload.php";

use app\controllers\userController;

$request = (object)$_REQUEST;

if (isset($request->modulo_usuario)) {
    $insUsuario = new userController();

    switch ($request->modulo_usuario) {
        case "registrar":
            echo $insUsuario->registrarUsuarioControlador($request);
            break;
        case "eliminar":
            echo $insUsuario->eliminarUsuarioControlador();
            break;
        case "actualizar":
            echo $insUsuario->actualizarUsuarioControlador();
            break;
        default:
            // Acción por defecto si no se encuentra ninguna coincidencia
            break;
    }
} else {
    session_destroy();
    header("Location: " . APP_URL . "login/");
}
