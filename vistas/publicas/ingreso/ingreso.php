<link rel="stylesheet" href="vistas/recursos/login/login.css">

<div class="container">
    <div class="heading">NUVA</div>
    
    <form method="post" class="form">
        <!-- Inputs críticos: name y id conservados -->
        <input required class="input" type="email" name="email" id="email" placeholder="Correo electrónico">
        <input required class="input" type="password" name="password" id="password" placeholder="Contraseña">
        
        <!-- Mensaje de validación PHP -->
      <?php
$login = new ControladorUsuarios();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    ob_start(); // Evita que se envíe salida antes de header()
    $login->ingresoUsuario();
    ob_end_flush();
}
?>


        <!-- Botón de login -->
        <input class="login-button" type="submit" value="Ingresar">

        <!-- Links importantes -->
        <span class="forgot-password">
            <a href="recuperar">¿Olvidaste tu contraseña?</a>
        </span>
        
        <a href="inicio" class="volver-principal">Volver al inicio</a>
    </form>

    <!-- Opcional: Social Login y acuerdo -->
    <div class="social-account-container">
        
    </div>
   
</div>