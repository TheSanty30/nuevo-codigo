<div class="container-fluid mb-6">

    <?php

    $id = $insLogin->limpiarCadena($url[1]);

    if ($id == $_SESSION['id']) {

    ?>

        <h1 class="display-4">Mi cuenta</h1>
        <h2 class="lead">Actualizar cuenta</h2>

    <?php } else { ?>

        <h1 class="display-4">Usuarios</h1>
        <h2 class="lead">Actualizar usuario</h2>

    <?php } ?>

</div>

<div class="container pb-6 pt-6">

    <?php
    include "./app/views/inc/btn_back.php";

    $datos = $insLogin->seleccionarDatos("Unico", "usuario", "usuario_id", $id);

    if ($datos->rowCount() == 1) {
        $datos = $datos->fetch();
    ?>

        <h2 class="display-4 text-center"><?php echo $datos['usuario_nombre'] . " " . $datos['usuario_apellido'] ?></h2>

        <p class="text-center pb-6"><?php echo "<strong>Usuario creado:</strong>" . date("d-m-Y h:i:s A", strtotime($datos['usuario_creado'])) . " &nbsp; <strong>Usuario actualizado:</strong>" . date("d-m-Y h:i:s A", strtotime($datos['usuario_actualizado'])) . ""; ?></p>

        <form class="FormularioAjax" action="<?php echo APP_URL; ?>app/ajax/usuarioAjax.php" method="POST" autocomplete="off">

            <input type="hidden" name="modulo_usuario" value="actualizar">
            <input type="hidden" name="usuario_id" value="<?php echo $datos['usuario_id']; ?>">

            <div class="row">
                <div class="col">
                    <div class="form-group">
                        <label for="usuario_nombre">Nombres</label>
                        <input class="form-control" type="text" name="usuario_nombre" maxlength="40" required value="<?php echo $datos['usuario_nombre']; ?>">
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <label for="usuario_apellido">Apellidos</label>
                        <input class="form-control" type="text" name="usuario_apellido" maxlength="40" required value="<?php echo $datos['usuario_apellido']; ?>">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col">
                    <div class="form-group">
                        <label for="usuario_usuario">Usuario</label>
                        <input class="form-control" type="text" name="usuario_usuario" maxlength="20" required value="<?php echo $datos['usuario_usuario']; ?>">
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <label for="usuario_email">Email</label>
                        <input class="form-control" type="email" name="usuario_email" maxlength="70" value="<?php echo $datos['usuario_email']; ?>">
                    </div>
                </div>
            </div>

            <br><br>

            <p class="text-center">
                SI desea actualizar la clave de este usuario por favor llene los 2 campos. Si NO desea actualizar la clave deje los campos vacíos.
            </p>

            <br>

            <div class="row">
                <div class="col">
                    <div class="form-group">
                        <label for="usuario_clave_1">Nueva clave</label>
                        <input class="form-control" type="password" name="usuario_clave_1" maxlength="100">
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <label for="usuario_clave_2">Repetir nueva clave</label>
                        <input class="form-control" type="password" name="usuario_clave_2" maxlength="100">
                    </div>
                </div>
            </div>

            <br><br><br>

            <p class="text-center">
                Para poder actualizar los datos de este usuario por favor ingrese su USUARIO y CLAVE con la que ha iniciado sesión
            </p>

            <div class="row">
                <div class="col">
                    <div class="form-group">
                        <label for="administrador_usuario">Usuario</label>
                        <input class="form-control" type="text" name="administrador_usuario" maxlength="20" required>
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <label for="administrador_clave">Clave</label>
                        <input class="form-control" type="password" name="administrador_clave" maxlength="100" required>
                    </div>
                </div>
            </div>

            <p class="text-center">
                <button type="submit" class="btn btn-success btn-rounded">Actualizar</button>
            </p>
        </form>

    <?php } else {
        include "./app/views/inc/error_alert.php";
    }
    ?>
</div>