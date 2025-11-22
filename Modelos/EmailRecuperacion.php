<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // Asegúrate de tener PHPMailer instalado con Composer

class EmailRecuperacion {

    public static function enviarLink($emailDestino, $nombreUsuario, $selector, $token) {
        $mail = new PHPMailer(true);

        try {
            // Configuración del servidor SMTP
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com'; // Cambia según tu servidor
            $mail->SMTPAuth = true;
            $mail->Username = '3145434864c@gmail.com'; // <-- tu correo
            $mail->Password = 'suidpsasoxhfwdl'; // <-- contraseña de aplicación
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            // Remitente y destinatario
            $mail->setFrom('3145434864c@gmail.com', 'Soporte NUVA');
            $mail->addAddress($emailDestino, $nombreUsuario);

            // Enlace de recuperación
            $url = "https://tusitio.com/restablecer?selector={$selector}&token={$token}";

            // Contenido del correo
            $mail->isHTML(true);
            $mail->Subject = 'Recuperación de contraseña - NUVA';
            $mail->Body = "
                <h2>Hola, {$nombreUsuario}</h2>
                <p>Has solicitado restablecer tu contraseña. Haz clic en el siguiente enlace para continuar:</p>
                <p><a href='{$url}'>{$url}</a></p>
                <p>Este enlace expirará en 1 hora.</p>
                <p>Si no solicitaste este cambio, ignora este correo.</p>
            ";

            $mail->send();
            return true;

        } catch (Exception $e) {
            error_log("Error al enviar email: {$mail->ErrorInfo}");
            return false;
        }
    }
}
