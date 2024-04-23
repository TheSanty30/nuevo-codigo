<div id="layoutSidenav">
    <?php include "./app/views/inc/sidebar.php"; ?>
    <div id="layoutSidenav_content">
        <?php

        use app\controllers\userController;

        $insUsuario = new userController();

        echo $insUsuario->listarUsuariosControlador($url[1], 15, $url[0], "");
        ?>
        <?php include "./app/views/inc/footer.php"; ?>
    </div>
</div>