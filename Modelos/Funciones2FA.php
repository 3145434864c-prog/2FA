<?php
// Funciones2FA.php - FIXED for Linux/LAMPP
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once 'Conexion.php'; // Tu clase de conexión PDO
require 'vendor/autoload.php'; // FIXED: PHPMailer via Composer - Linux path

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
        $mail->Body = '
<!DOCTYPE html>
<html lang="es">
<body style="margin:0; padding:0; background:#f4f7fb; font-family:Arial, sans-serif;">

<table width="100%" cellpadding="0" cellspacing="0" style="background:#f4f7fb; padding:40px 0;">
<tr>
<td align="center">

    <table width="480" cellpadding="0" cellspacing="0" 
        style="background:#ffffff; border-radius:14px; padding:35px; 
        box-shadow:0 4px 20px rgba(0,0,0,0.08);">

        <tr>
            <td align="center" style="font-size:26px; font-weight:800; 
            color:#1089d3; padding-bottom:10px;">
                Verificación en dos pasos
            </td>
        </tr>

        <tr>
            <td style="font-size:16px; color:#333; text-align:center; 
            padding-bottom:25px;">
                Hola,<br>
                Usa el siguiente código para completar tu inicio de sesión:
            </td>
        </tr>

        <tr>
            <td align="center" style="padding-bottom:25px;">
                <div style="
                    font-size:40px;
                    font-weight:800;
                    letter-spacing:8px;
                    color:#1089d3;
                    background:#eef6ff;
                    padding:15px 20px;
                    border-radius:12px;
                    display:inline-block;
                    border:2px solid #cfe7ff;">
                    ' . $otp . '
                </div>
            </td>
        </tr>

        <tr>
            <td align="center" style="font-size:14px; color:#555; padding-bottom:25px;">
                Este código es válido por <strong>5 minutos</strong>.
            </td>
        </tr>

        <tr>
            <td align="center" 
            style="font-size:12px; color:#777; line-height:1.5; padding-top:10px;">
                Si no solicitaste este código, ignora este mensaje.<br>
                © 2025 — Nuva S.A.S
            </td>
        </tr>

    </table>

</td>
</tr>
</table>

</body>
</html>';

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Error al enviar OTP: " . $mail->ErrorInfo);
        return false;
    }
}
?>

