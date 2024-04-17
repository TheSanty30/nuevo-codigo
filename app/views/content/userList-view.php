<?php

use app\controllers\userController;

$insUsuario = new userController();

echo $insUsuario->listarUsuariosControlador($url[1], 15, $url[0], "");
