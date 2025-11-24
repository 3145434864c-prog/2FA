<?php
// src/Controllers/enroll_2fa.php
require_once __DIR__ . '/../../Modelos/Conexion.php'; // Conexion::pdo() devuelve PDO
require_once __DIR__ . '/../Services/FirebaseTOTPService.php';

header('Content-Type: application/json');

$conn = Conexion::pdo(); // PDO

$isCli = (php_sapi_name() === 'cli');
if ($isCli) {
    $_SERVER['REQUEST_METHOD'] = 'POST';
    if (!isset($_POST['user_id'])) {
        echo json_encode(['ok' => false, 'message' => 'CLI: falta user_id.']);
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['ok' => false, 'message' => 'Método no permitido.']);
    exit;
}

if (!isset($_POST['user_id'])) {
    echo json_encode(['ok' => false, 'message' => 'Falta user_id.']);
    exit;
}

$userId = intval($_POST['user_id']);

// Obtener usuario
$stmt = $conn->prepare("SELECT id_usuario AS id, email_usuario AS email, firebase_uid, firebase_totp_id FROM usuarios WHERE id_usuario = :id");
$stmt->bindValue(':id', $userId, PDO::PARAM_INT);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo json_encode(['ok' => false, 'message' => 'Usuario no encontrado.']);
    exit;
}

if (empty($user['firebase_uid'])) {
    echo json_encode([
        'ok' => false,
        'message' => 'El usuario no tiene firebase_uid asignado.'
    ]);
    exit;
}

// Inicializar FirebaseTOTPService
try {
    FirebaseTOTPService::init(__DIR__ . '/../secrets/fa-nuva-firebase-adminsdk-fbsvc-093a035cfd.json');
} catch (Exception $e) {
    echo json_encode(['ok' => false, 'message' => $e->getMessage()]);
    exit;
}

// Crear secreto TOTP
try {
    $totpData = FirebaseTOTPService::createTotpSecret($user['firebase_uid']);
} catch (Throwable $e) {
    echo json_encode(['ok' => false, 'message' => 'Error creando secreto TOTP: ' . $e->getMessage()]);
    exit;
}

$firebaseTotpId = $totpData['firebase_totp_id'];
$qrUri = $totpData['qr_uri'];

// Guardar firebase_totp_id y activar totp_enabled
$update = $conn->prepare("UPDATE usuarios SET firebase_totp_id = :totp_id, totp_enabled = 1 WHERE id_usuario = :id");
$update->bindValue(':totp_id', $firebaseTotpId, PDO::PARAM_STR);
$update->bindValue(':id', $userId, PDO::PARAM_INT);
$update->execute();

// Respuesta JSON
echo json_encode([
    'ok' => true,
    'message' => 'TOTP habilitado exitosamente.',
    'data' => [
        'firebase_totp_id' => $firebaseTotpId,
        'qr_uri' => $qrUri,
        'raw_response' => $totpData['raw_response'] ?? null
    ]
]);
exit;
