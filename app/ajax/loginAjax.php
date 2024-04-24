<?php

require_once "../../config/app.php";
require_once "../views/inc/session_start.php";
require_once "../../autoload.php";

use app\controllers\loginController;

$request = (object)$_REQUEST;

if (isset($request->modulo_usuario)) {
    $insLogin = new loginController();

    switch ($request->modulo_usuario) {
        case "login":
            echo $insLogin->iniciarSesionControlador($request);
            break;
        default:
            // Acción por defecto si no se encuentra ninguna coincidencia
            break;
    }
} else {
    session_destroy();
    header("Location: " . APP_URL);
}
