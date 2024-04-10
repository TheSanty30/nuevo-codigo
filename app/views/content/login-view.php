<div class="login-container">
    <div class="login-form">
        <h2 class="text-center mb-4">Iniciar Sesión</h2>
        <form class="" action="" method="post" autocomplete="off">
            <div class="form-group">
                <label for="username">Usuario:</label>
                <input type="text" class="form-control" name="login_usuario" id="username" pattern="[a-zA-Z0-9]{4,20}" maxlength="20" required placeholder="Ingrese su usuario">
            </div>
            <div class="form-group">
                <label for="password">Contraseña:</label>
                <input type="password" class="form-control" name="login_pasword" pattern="[a-zA-Z0-9$@.-]{7,100}" maxlength="100" required id="password" placeholder="Ingrese su contraseña">
            </div>
            <button type="submit" class="btn btn-primary btn-block">Iniciar Sesión</button>
        </form>
    </div>
</div>