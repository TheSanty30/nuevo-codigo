<?php

namespace app\controllers;

use app\models\mainModel;

class loginController extends mainModel
{
    /*private $data;

    public function __construct()
    {
        $requestBody = file_get_contents('php://input');
        $requestBody = json_decode($requestBody);

        $request =  ($requestBody) ?? (object) $_REQUEST;
        //print_r($request);
        //controlador         
        $this->swicht_usuario($request);
    }

    private function swicht_usuario($request)
    {
        //print_r($request);
        switch ($request->modulo_usuario) {
            case 'login':
                //print_r($request);
                $request = self::iniciarSesionControlador_api($request);
                echo json_encode($request);
                break;
        }
        //print_r($this->data);
    }


    public function validacion_usuario($request)
    {
        $request->resp = (bool) true;

        if ($request->login_usuario === "" || $request->login_password === "") return (object) ['resp' => 'vacio'];
        if ($this->verificarDatos("[a-zA-Z0-9]{4,20}", $request->login_usuario)) return (object) ['resp' => 'usuario_invalido'];

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
    }*/

    /*----------  Controlador iniciar sesion  ----------*/
    public function iniciarSesionControlador()
    {
        $usuario = $this->limpiarCadena($_POST['login_usuario']);
        $password = $this->limpiarCadena($_POST['login_password']);

        $token = $this->limpiarCadena($_POST['token']);

        include "../../config/app.php";
        $url = 'https://wwww.google.com/recaptcha/api/siteverify';

        $ruta = file_get_contents($url . '?secret=' . RECAPTCHA_PUBLIC_KEY . '&response=' . $token);

        $json = json_decode($ruta, true);
        $ok = $json['success'];

        if (!$ok) {
            echo "
                <script>
                    Swal.fire({
                        icon: 'error',
                        title: 'Ocur   un error inesperado',
                        text: 'El token no es valido',
                        confirmButtonText: 'Aceptar'
                    })
                </script>
            ";
            exit();
        }

        if ($usuario == "" || $password == "") {
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
            # Verificando integridad de los datos #
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
                # Verificando integridad de los datos #
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
                    # Verificando usuario #
                    $check_usuario = $this->ejecutarConsulta("SELECT * FROM usuario WHERE usuario_usuario ='$usuario'");
                    if ($check_usuario->rowCount() == 1) {
                        $check_usuario = $check_usuario->fetch();

                        if ($check_usuario['usuario_usuario'] == $usuario && $check_usuario['usuario_clave'] == $password) {
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

    /*----------  Controlador cerrar sesion  ----------*/
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
