<div class="container mt-5">
    <h2 class="mb-4">Registro de Usuario</h2>
    <form class="FormularioAjax" action="<?php echo APP_URL; ?>app/ajax/usuarioAjax.php" method="POST" autocomplete="off" enctype="multipart/form-data">
        <input type="hidden" name="modulo_usuario" value="registrar">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="nombres">Nombres:</label>
                    <input type="text" class="form-control" id="nombres" placeholder="Ingrese sus nombres" name="usuario_nombre">
                </div>
                <div class="form-group">
                    <label for="apellidos">Apellidos:</label>
                    <input type="text" class="form-control" id="apellidos" placeholder="Ingrese sus apellidos" name="usuario_apellido">
                </div>
                <div class="form-group">
                    <label for="usuario">Usuario:</label>
                    <input type="text" class="form-control" id="usuario" placeholder="Ingrese su usuario" name="usuario_usuario">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" class="form-control" id="email" placeholder="Ingrese su correo electrónico" name="usuario_email">
                </div>
                <div class="form-group">
                    <label for="password">Contraseña:</label>
                    <input type="password" class="form-control" id="password" placeholder="Ingrese su contraseña" name="usuario_clave_1">
                </div>
                <div class="form-group">
                    <label for="repeat-password">Repetir Contraseña:</label>
                    <input type="password" class="form-control" id="repeat-password" placeholder="Repita su contraseña" name="usuario_clave_2">
                </div>
            </div>
        </div>
        <div class="row mt-4 text-center">
            <div class="col">
                <button type="reset" class="btn btn-secondary mr-2">Limpiar</button>
                <button type="submit" class="btn btn-primary">Guardar</button>
            </div>
        </div>
    </form>
</div>