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
    public function iniciarSesionControlador($request)
    {
        // Obtener datos del formulario
        $usuario = $this->limpiarCadena($request->login_usuario ?? '');
        $password = $this->limpiarCadena($request->login_password ?? '');
        $captcha = $this->limpiarCadena($_POST['g-recaptcha-response'] ?? '');

        // Verificar si hay campos vacíos
        if (empty($usuario) || empty($password)) {
            return json_encode([
                'success' => false,
                'message' => 'Existen campos vacíos'
            ]);
        }

        // Verificar el formato del usuario y la contraseña
        if ($this->verificarDatos("[a-zA-Z0-9]{4,20}", $usuario) || $this->verificarDatos("[a-zA-Z0-9$@.-]{7,100}", $password)) {
            return json_encode([
                'success' => false,
                'message' => 'El usuario o la contraseña no coinciden con el formato solicitado'
            ]);
        }

        // Verificar Captcha
        $ip = $_SERVER['REMOTE_ADDR'];
        $response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=" . RECAPTCHA_PRIVATE_KEY_V2 . "&response=$captcha&remoteip=$ip");
        $atributos = json_decode($response, true);

        if (!$atributos['success']) {
            return json_encode([
                'success' => false,
                'message' => 'Error en el Captcha'
            ]);
        }

        // Verificar usuario
        $check_usuario = $this->ejecutarConsulta("SELECT * FROM usuario WHERE usuario_usuario ='$usuario'");
        if ($check_usuario->rowCount() == 1) {
            $check_usuario = $check_usuario->fetch();

            if ($check_usuario['usuario_clave'] == $password) {
                // Iniciar sesión
                $_SESSION['id'] = $check_usuario['usuario_id'];
                $_SESSION['nombre'] = $check_usuario['usuario_nombre'];
                $_SESSION['apellido'] = $check_usuario['usuario_apellido'];
                $_SESSION['usuario'] = $check_usuario['usuario_usuario'];

                return json_encode([
                    'success' => true,
                    'redirect' => APP_URL . "dashboard/"
                ]);
            }
        }

        return json_encode([
            'success' => false,
            'message' => 'Usuario o contraseña incorrectos'
        ]);
    }



    /*----------  Controlador cerrar sesion  ----------*/
    public function cerrarSesionControlador()
    {
        session_destroy();

        if (headers_sent()) {
            echo "
                <script>
                    window.location.href='" . APP_URL . "'
                </script>
            ";
        } else {
            header("Location: " . APP_URL);
        }
    }
}
