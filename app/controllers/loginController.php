<?php

namespace app\controllers;

use app\models\mainModel;

class loginController extends mainModel
{
    private $data;


    public function __construct()
    {
        $request = (object) $_REQUEST;
        /**
         * controlador
         */
        $this->swicht_usuario($request);
    }

    private function swicht_usuario($request)
    {
        switch ($request->modulo_usuario) {
            case 'login':
                $request = self::iniciarSesionControlador_api($request);
                echo json_encode($request);
                break;
        }
        //print_r($this->data);
    }


    public function validacion_usuario($request)
    {
        $request->resp = (bool) true;

        if ($request->login_usuario == "" || $request->login_password == "") $request->resp = 'vacio';
        if ($this->verificarDatos("[a-zA-Z0-9]{4,20}", $request->login_usuario)) $request->resp = 'usaurio_invalido';
        return $request;
    }

    public function iniciarSesionControlador_api($request)
    {
        $request = $this->validacion_usuario($request);

        if ($request->resp === true) {
            $sql = "SELECT * FROM usuario 
            WHERE usuario_usuario ='{$request->login_usuario}' ";

            $check_usuario = $this->ejecutarConsulta($sql);
            $request->count = $check_usuario->rowCount();

            if ($request->count == 1) {
                //$request->clave = password_hash('123456789', PASSWORD_BCRYPT, ["cost" => 10]);
                $check_usuario = $check_usuario->fetchAll();
                $_SESSION['id'] = $check_usuario['usuario_id'];
                $_SESSION['nombre'] = $check_usuario['usuario_nombre'];
                $_SESSION['apellido'] = $check_usuario['usuario_apellido'];
                $_SESSION['usuario'] = $check_usuario['usuario_usuario'];
                $request->resp = 'login_true';
                $request->usuario = $check_usuario;
            } else {
                $request->resp = 'no_data';
            }
        }

        return $request;
    }

    #Controllador iniciar session

    /**
     * 
     */
    public function iniciarSesionControlador()
    {
        $usuario = $this->limpiarCadena($_POST['login_usuario']);
        $password = $this->limpiarCadena($_POST['login_password']);

        if ($usuario == "" && $password == "") {
            echo "
                <script>
                    Swal.fire({
                        icon: 'error',
                        title: 'Ocurrió un error inesperado',
                        text: 'Existen campos vacios',
                        confirmButtonText: 'Aceptar'
                    })
                </script>
            ";
        } else {
            if ($this->verificarDatos("[a-zA-Z0-9]{4,20}", $usuario)) {
                echo "
                    <script>
                        Swal.fire({
                            icon: 'error',
                            title: 'Ocurrió un error inesperado',
                            text: 'El usuario no coinicide con el formato solicitado',
                            confirmButtonText: 'Aceptar'
                        })
                    </script>
                ";
            } else {
                if ($this->verificarDatos("[a-zA-Z0-9$@.-]{7,100}", $password)) {
                    echo "
                        <script>
                            Swal.fire({
                                icon: 'error',
                                title: 'Ocurrió un error inesperado',
                                text: 'La contraseña no coinicide con el formato solicitado',
                                confirmButtonText: 'Aceptar'
                            })
                        </script>
                    ";
                } else {
                    $check_usuario = $this->ejecutarConsulta("SELECT * FROM usuario WHERE usuario_usuario ='$usuario'");
                    if ($check_usuario->rowCount() == 1) {
                        $check_usuario = $check_usuario->fetch();

                        if ($check_usuario['usuario_usuario'] == $usuario && password_verify($password, $check_usuario['usuario_clave'])) {
                            $_SESSION['id'] = $check_usuario['usuario_id'];
                            $_SESSION['nombre'] = $check_usuario['usuario_nombre'];
                            $_SESSION['apellido'] = $check_usuario['usuario_apellido'];
                            $_SESSION['usuario'] = $check_usuario['usuario_usuario'];

                            if (headers_sent()) {
                                echo "
                                    <script>
                                        window.location.href='" . APP_URL . "dashboard/'
                                    </script>
                                ";
                            } else {
                                header("Location: " . APP_URL . "dashboard/");
                            }
                        } else {
                            echo "
                                <script>
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Ocurrió un error inesperado',
                                        text: 'Usuario o clave incorrecto',
                                        confirmButtonText: 'Aceptar'
                                    })
                                </script>
                            ";
                        }
                    } else {
                        echo "
                            <script>
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Ocurrió un error inesperado',
                                    text: 'Usuario o clave incorrecto',
                                    confirmButtonText: 'Aceptar'
                                })
                            </script>
                        ";
                    }
                }
            }
        }
    }
    /** */




    public function cerrarSesionControlador()
    {
        session_destroy();

        if (headers_sent()) {
            echo "
                <script>
                    window.location.href='" . APP_URL . "login/'
                </script>
            ";
        } else {
            header("Location: " . APP_URL . "login/");
        }
    }
}

$testing = new loginController();
