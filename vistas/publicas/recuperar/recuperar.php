<link rel="stylesheet" href="vistas/recursos/login/login.css">

<div class="container">
    <div class="heading">Recuperar Contraseña</div>
    
    <form method="post" class="form">
        <input required class="input" type="email" name="email_recuperacion" id="email_recuperacion" placeholder="Ingresa tu correo registrado">
        
        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $recuperacion = new ControladorUsuarios();
            $recuperacion->solicitarRecuperacion();
        }
        ?>

        <input class="login-button" type="submit" value="Enviar enlace de recuperación">
        <a href="ingreso" class="volver-principal">Volver al inicio de sesión</a>
    </form>
</div>
