<?php

namespace app\controllers;

use app\models\mainModel;
use app\models\viewsModel;

class userController extends mainModel
{
    #Controlador para registra usuario#
    public function registarUsuarioControlador()
    {
        //Almacenado los datos
        $nombre = $this->limpiarCadena($_POST['usuario_nombre']);
        $apellido = $this->limpiarCadena($_POST['usuario_apellido']);
        $usuario = $this->limpiarCadena($_POST['usuario_usuario']);
        $email = $this->limpiarCadena($_POST['usuario_email']);
        $clave1 = $this->limpiarCadena($_POST['usuario_clave_1']);
        $clave2 = $this->limpiarCadena($_POST['usuario_clave_2']);

        //Verificando campos obligatorios
        /*if ($nombre == "" || $apellido == "" || $usuario == "" || $clave1 == "" || $clave2 == "") {
            $alerta = [
                "tipo" => "simple",
                "titulo" => "Ocurrior un error inesperado",
                "texto" => "Existen campos vacios",
                "icono" => "error"
            ];
            return json_encode($alerta);
            exit();
        }*/

        //Verificando integridad de los datos
        if ($this->verificarDatos("[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{3,40}", $nombre)) {
            $alerta = [
                "tipo" => "simple",
                "titulo" => "Ocurrior un error inesperado",
                "texto" => "El nombre no coincide con el formato solicitado",
                "icono" => "error"
            ];
            return json_encode($alerta);
            exit();
        }
    }
}
