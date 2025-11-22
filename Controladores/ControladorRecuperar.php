<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . "/../vendor/autoload.php";
require_once __DIR__ . "/../Modelos/Conexion.php";

class ControladorRecuperar {

    /**
     * Envía un correo de recuperación de contraseña.
     *
     * @param string $email   Correo del usuario
     * @param string $selector Identificador único (parte pública del token)
     * @param string $token   Token secreto (hex codificado)
     * @return bool           True si se envió correctamente, false si falló
     */
    public static function enviarCorreoRecuperacion($email, $selector, $token) {
        $mail = new PHPMailer(true);

        try {
            // === Configuración del servidor SMTP ===
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = '3145434864c@gmail.com'; // 👉 tu correo
            $mail->Password = 'ayvrxdvuugoizdna'; // 👉 contraseña de aplicación (NO la personal)
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            // === Datos del remitente y destinatario ===
            $mail->setFrom('3145434864c@gmail.com', 'Soporte NUVA');
            $mail->addAddress($email);

            // === Asunto ===
            $mail->Subject = 'Recuperación de contraseña - NUVA';

            // === Construir el enlace correcto ===
            // Importante: usamos "public.php?route=recuperar_confirmar"
            $url = "http://localhost/entregable-main/public.php?route=recuperar_confirmar"
                 . "&selector=" . urlencode($selector)
                 . "&token=" . urlencode($token);

            // === Cuerpo del correo en HTML ===
            $mail->isHTML(true);
            $mail->Body = "
                <div style='font-family: Arial, sans-serif; color:#333;'>
                    <h2>Recuperación de contraseña</h2>
                    <p>Hemos recibido una solicitud para restablecer tu contraseña.</p>
                    <p>Haz clic en el siguiente botón para continuar:</p>
                    <p>
                        <a href='{$url}' 
                           style='display:inline-block;background-color:#007bff;color:white;
                                  padding:10px 20px;text-decoration:none;border-radius:5px;'>
                           Restablecer contraseña
                        </a>
                    </p>
                    <p>Este enlace expirará en 30 minutos. Si no realizaste esta solicitud, puedes ignorar este mensaje.</p>
                    <hr>
                    <p><strong>NUVA S.A.S</strong><br>Soporte técnico</p>
                </div>
            ";

            // === Enviar correo ===
            $mail->send();
            return true;

        } catch (Exception $e) {
            error_log("Error al enviar correo de recuperación: {$mail->ErrorInfo}");
            return false;
        }
    }
}
