<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // carga PHPMailer instalado con composer

class CorreoRecuperacion {

    public static function enviar($email, $selector, $token) {
        $mail = new PHPMailer(true);

        try {
            // Configuración SMTP
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = '3145434864c@gmail.com';
            $mail->Password   = 'ayvrxdvuugoizdna'; // app password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            // Configuración del correo
            $mail->setFrom('3145434864c@gmail.com', 'NUVA S.A.S');
            $mail->addAddress($email);
            $mail->isHTML(true);
            $mail->Subject = 'Recupera tu contraseña';

            // FIXED URL - actual path /2FA
            $url = "http://localhost/2FA/index.php?route=recuperar_confirmar&selector=" . urlencode($selector) . "&token=" . urlencode($token);

            // Cuerpo del mensaje
            $mail->Body = "
    <div style='font-family: Arial, sans-serif; background-color: #f7f7f7; padding: 20px;'>
        <div style='max-width: 600px; background-color: #fff; margin: 0 auto; border-radius: 8px; overflow: hidden; box-shadow: 0 0 10px rgba(0,0,0,0.1);'>
            <div style='background-color: #004aad; color: white; text-align: center; padding: 16px; font-size: 20px; font-weight: bold;'>
                Recuperación de contraseña - NUVA S.A.S
            </div>
            <div style='padding: 20px; color: #333;'>
                <p>Hola,</p>
               <p>Hemos recibido una solicitud para restablecer tu contraseña. Este enlace es válido solo por <strong>10 minutos</strong>. Si fuiste tú, haz clic en el botón de abajo:</p>

                <p style='text-align: center; margin: 25px 0;'>
                    <a href='$url' target='_blank' 
                       style='background-color: #004aad; color: white; padding: 12px 20px; text-decoration: none; border-radius: 6px; font-weight: bold;'>
                       Recuperar contraseña
                    </a>
                </p>
                <p>Si no realizaste esta solicitud, puedes ignorar este mensaje. Tu contraseña permanecerá segura.</p>
                <hr>
                <p style='font-size: 12px; text-align: center; color: #888;'>
                    © " . date('Y') . " NUVA S.A.S. Todos los derechos reservados.
                </p>
            </div>
        </div>
    </div>
";

            $mail->send();
            return true;

        } catch (Exception $e) {
            error_log("Error al enviar correo: {$mail->ErrorInfo}");
            return false;
        }
    }
}
?>

