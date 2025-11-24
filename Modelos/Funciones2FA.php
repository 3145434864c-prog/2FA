<?php
// Funciones2FA.php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once 'Conexion.php'; // Tu clase de conexión PDO
require 'C:\xampp\htdocs\Entregable-main\vendor\autoload.php'; // PHPMailer via Composer

function sendOtpEmail(int $userId, string $userEmail): bool {
    $pdo = Conexion::pdo();

    // Generar OTP de 6 dígitos
    $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    $expiresAt = date('Y-m-d H:i:s', strtotime('+5 minutes'));

    // Marcar OTP previos como usados
    $stmt = $pdo->prepare("UPDATE email_2fa SET used=1 WHERE user_id=:user_id AND used=0");
    $stmt->execute([':user_id' => $userId]);

    // Insertar OTP nuevo
    $stmt = $pdo->prepare("
        INSERT INTO email_2fa (user_id, otp, expires_at) 
        VALUES (:user_id, :otp, :expires_at)
    ");
    $stmt->execute([
        ':user_id' => $userId,
        ':otp' => $otp,
        ':expires_at' => $expiresAt
    ]);

    // Configuración de PHPMailer
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com'; // Cambia si es otro servidor
        $mail->SMTPAuth   = true;
        $mail->Username   = '3145434864c@gmail.com'; // Tu correo emisor
        $mail->Password   = 'vuimcvpzvayurgmo';   // App password de Gmail
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;

        $mail->setFrom('3145434864c@gmail.com', 'Equipo de Soporte');
        $mail->addAddress($userEmail);

        $mail->isHTML(true);
        $mail->Subject = 'Tu código OTP de verificación';
        $mail->Body    = "Tu código OTP es: <b>$otp</b>. Expira en 5 minutos.";

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Error al enviar OTP: " . $mail->ErrorInfo);
        return false;
    }
}
