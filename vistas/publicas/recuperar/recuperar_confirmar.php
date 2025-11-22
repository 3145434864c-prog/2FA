<link rel="stylesheet" href="vistas/recursos/login/login.css">

<div class="container">
    <div class="heading">Restablecer contraseña</div>

    <?php
    // Capturar selector y token de la URL
    $selector = $_GET['selector'] ?? '';
    $token = $_GET['token'] ?? '';

    if (empty($selector) || empty($token)) {
        echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'Enlace inválido',
                text: 'El enlace de recuperación no es válido o está incompleto.',
                confirmButtonColor: '#d33'
            }).then(() => {
                window.location = 'ingreso';
            });
        </script>";
        exit;
    }
    ?>

    <form method="post" class="form">
        <input type="hidden" name="selector" value="<?php echo htmlspecialchars($selector); ?>">
        <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">

        <input required class="input" type="password" name="password" placeholder="Nueva contraseña">
        <input required class="input" type="password" name="confirmar" placeholder="Confirmar contraseña">

        <input class="login-button" type="submit" value="Actualizar contraseña">

        <a href="ingreso" class="volver-principal">Volver al inicio</a>
    </form>

    <?php
    // Procesar cambio de contraseña
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $controlador = new ControladorUsuarios();
        $controlador->actualizarPassword();
    }
    ?>
</div>
