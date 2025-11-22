<?php
require_once "../../../Modelos/Conexion.php";

// Obtener parámetros del enlace
$selector = $_GET['selector'] ?? '';
$token = $_GET['token'] ?? '';

if (empty($selector) || empty($token)) {
    exit("Enlace inválido o incompleto.");
}

// Conectar a la base de datos
$pdo = Conexion::pdo();

// Buscar el registro del token
$stmt = $pdo->prepare("SELECT * FROM password_resets WHERE selector = ? AND used = 0 AND expires_at > NOW()");
$stmt->execute([$selector]);
$reset = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$reset) {
    exit("⚠️ Este enlace no es válido o ha expirado.");
}

// Validar token (comparar el hash con el token real)
if (!password_verify(hex2bin($token), $reset['token_hash'])) {
    exit("❌ Token inválido. Solicita nuevamente el restablecimiento de contraseña.");
}

// Si el token es válido, mostrar formulario
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Restablecer contraseña</title>
    <link rel="stylesheet" href="../../recursos/login/login.css">
</head>
<body>
<div class="container">
    <div class="heading">Nueva contraseña</div>

    <form method="post" class="form" action="procesar_reset.php">
        <input type="hidden" name="selector" value="<?php echo htmlspecialchars($selector); ?>">
        <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">

        <input required class="input" type="password" name="password" placeholder="Nueva contraseña">
        <input required class="input" type="password" name="password_confirm" placeholder="Confirmar contraseña">

        <input class="login-button" type="submit" value="Actualizar contraseña">
    </form>
</div>
</body>
</html>
