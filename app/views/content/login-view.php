<div class="login-container">
    <div class="login-form">
        <h2 class="text-center mb-4">Iniciar Sesión</h2>
        <form class="" action="" method="post" autocomplete="off">
            <div class="form-group">
                <label for="username">Usuario:</label>
                <input type="text" class="form-control" name="login_usuario" id="username" pattern="[a-zA-Z0-9]{4,20}" maxlength="20" placeholder="Ingrese su usuario">
            </div>
            <div class="form-group">
                <label for="password">Contraseña:</label>
                <input type="password" class="form-control" name="login_password" pattern="[a-zA-Z0-9$@.-]{7,100}" maxlength="100" id="password" placeholder="Ingrese su contraseña">
            </div>
            <button type="submit" class="btn btn-primary btn-block">Iniciar Sesión</button>
        </form>
    </div>
</div>
<script>
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
    }
</script>






<?php
/*
if (isset($_POST['login_usuario']) && isset($_POST["login_password"])) {
    $insLogin->iniciarSesionControlador();
}
print_r($_POST);
*/
?>