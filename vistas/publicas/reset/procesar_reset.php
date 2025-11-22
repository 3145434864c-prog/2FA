<?php
require_once "../../../Modelos/Conexion.php";

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    exit("Acceso no permitido.");
}

$selector = $_POST['selector'] ?? '';
$token = $_POST['token'] ?? '';
$password = $_POST['password'] ?? '';
$password_confirm = $_POST['password_confirm'] ?? '';

if (empty($selector) || empty($token) || empty($password) || empty($password_confirm)) {
    exit("Faltan datos del formulario.");
}

if ($password !== $password_confirm) {
    exit("Las contraseñas no coinciden.");
}

// Conectar base de datos
$pdo = Conexion::pdo();

// Buscar token en la base de datos
$stmt = $pdo->prepare("SELECT * FROM password_resets WHERE selector = ? AND used = 0 AND expires_at > NOW()");
$stmt->execute([$selector]);
$reset = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$reset) {
    exit("⚠️ Enlace inválido o caducado.");
}

// Verificar que el token sea correcto
if (!password_verify(hex2bin($token), $reset['token_hash'])) {
    exit("❌ Token no válido.");
}

// Si el token es correcto → actualizar contraseña del usuario
$hashedPassword = password_hash($password, PASSWORD_ARGON2ID);

$updateUser = $pdo->prepare("UPDATE usuarios SET password = ? WHERE id_usuario = ?");
$updateUser->execute([$hashedPassword, $reset['user_id']]);

// Marcar el token como usado
$markUsed = $pdo->prepare("UPDATE password_resets SET used = 1 WHERE id = ?");
$markUsed->execute([$reset['id']]);

// Redirigir con mensaje de éxito
echo "
    <script>
        alert('✅ Tu contraseña ha sido actualizada correctamente.');
        window.location.href = '../../publicas/ingreso/ingreso.php';
    </script>
";
?>
