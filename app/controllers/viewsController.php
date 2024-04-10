<?php

namespace app\controllers;

use app\models\viewsModel;

class ViewsController extends viewsModel
{
    public function obtenerVistasControlador($vista)
    {
        if ($vista != "") {
            $respuesta = $this->obtenerVistasModelos($vista);
        } else {
            $respuesta = "login";
        }
        return $respuesta;
    }
}
