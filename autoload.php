<?php

//obtener las clases que estammos utilizando em el sistema
spl_autoload_register(function ($clase) {
    //Obtener el directorio actual donde se encuentra el archivo que se esta ejecutando
    $archivo = __DIR__ . "/" . $clase . ".php";
    $archivo = str_replace("\\", "/", $archivo);

    if (is_file($archivo)) {
        require_once $archivo;
    }
});
