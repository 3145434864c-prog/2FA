<link rel="stylesheet" href="vistas/recursos/login/login.css">

<div class="container">
    <div class="heading">Restablecer Contraseña</div>

    <?php
    require_once "Controladores/ControladorUsuarios.php";
    $controlador = new ControladorUsuarios();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        ob_start();
        $controlador->actualizarPassword();
        ob_end_flush();
    }
    ?>

    <form method="post" class="form">
        <input type="hidden" name="selector" value="<?php echo htmlspecialchars($_GET['selector'] ?? '', ENT_QUOTES); ?>">
        <input type="hidden" name="token" value="<?php echo htmlspecialchars($_GET['token'] ?? '', ENT_QUOTES); ?>">

        <input required class="input" type="password" name="password" placeholder="Nueva contraseña">
        <input required class="input" type="password" name="confirmar" placeholder="Confirmar contraseña">

        <input class="login-button" type="submit" value="Restablecer">
        <a href="ingreso" class="volver-principal">Volver al login</a>
    </form>
</div>
