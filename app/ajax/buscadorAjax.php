<?php

require_once "../../config/app.php";
require_once "../views/inc/session_start.php";
require_once "../../autoload.php";

use app\controllers\searchController;

if (isset($_POST['modulo_buscador'])) {

    $insBuscador = new searchController();

    switch ($_POST['modulo_usuario']) {
        case "buscar":
            echo $insBuscador->iniciarBuscadorControlador();
            break;
        case "eliminar":
            echo $insBuscador->eliminarBuscadorControlador();
            break;
        default:
            // Acción por defecto si no se encuentra ninguna coincidencia
            break;
    }
} else {
    session_destroy();
    header("Location: " . APP_URL . "login/");
}
