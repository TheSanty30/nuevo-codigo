<div class="login-container">
    <div class="login-form">
        <h2 class="text-center mb-4">Iniciar Sesión</h2>
        <form id="login-form" action="" method="post" autocomplete="off">
            <div class="form-group">
                <label for="username">Usuario:</label>
                <input type="text" class="form-control" name="login_usuario" id="username" maxlength="20" placeholder="Ingrese su usuario">
            </div>
            <div class="form-group">
                <label for="password">Contraseña:</label>
                <input type="password" class="form-control" name="login_password" maxlength="100" id="password" placeholder="Ingrese su contraseña">
            </div>
            <button type="submit" class="btn btn-primary btn-block">Iniciar Sesión</button>
        </form>
    </div>
</div>
<script>
    /*
    fetch('http://localhost/lev/login.controller.php?modulo_usuario=login&login_usuario=Sany')
        .then((res) => {
            return res.json();
        })
        .then((res) => {
            console.log(res);
            validateForm(res.resp)

        })


    function validateForm(resp) {
        if (resp === 'vacio')
            Swal.fire({
                icon: 'error',
                title: 'Ocurrió un error inesperado',
                text: 'Usuario o clave incorrecto',
                confirmButtonText: 'Aceptar'
            })
    }*/


    /*
        var formLogin = document.getElementById('login-form');

        formLogin.addEventListener('submit', function(e) {
            e.preventDefault();
            var datos = new FormData(formLogin);

            fetch('./app/ajax/loginAjax.php', {
                    method: 'POST',
                    body: datos
                })
                .then((res) => {
                    return res.json();
                })
                .then((res) => {
                    validateForm(res.resp);
                })

            function validateForm(resp) {
                switch (resp) {
                    case 'vacio':
                        Swal.fire({
                            icon: 'error',
                            title: 'Ocurrió un error inesperado',
                            text: 'Todos los campos son obligatorios',
                            confirmButtonText: 'Aceptar'
                        });
                        break;
                    case 'usuario_invalido':
                        Swal.fire({
                            icon: 'error',
                            title: 'Ocurrió un error inesperado',
                            text: 'Usuario incorrecto',
                            confirmButtonText: 'Aceptar'
                        });
                        break;
                    case 'login_true':
                        window.location.href = './app/views/content/dashboard-view.php';
                        break;
                    default:
                        // Manejar cualquier otro caso no previsto
                        break;
                }
            }
        })*/
</script>






<?php

if (isset($_POST['login_usuario']) && isset($_POST["login_password"])) {
    $insLogin->iniciarSesionControlador();
}
print_r($_POST);

?>