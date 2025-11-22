<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once "vendor/autoload.php"; // PHPMailer
require_once "Modelos/Conexion.php";

class ControladorContacto {

    // =======================================
    // Enviar mensaje de contacto
    // =======================================
    public static function enviar(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            // Solo permitir POST
            echo json_encode([
                'status' => 'error',
                'titulo' => 'Método no permitido',
                'mensaje' => 'Este endpoint solo acepta solicitudes POST'
            ]);
            exit;
        }

        if (headers_sent()) ob_end_clean();

        // Recibir datos
        $nombre  = trim($_POST['nombre'] ?? '');
        $email   = trim($_POST['email'] ?? '');
        $mensaje = trim($_POST['mensaje'] ?? '');

        // Validaciones
        if ($nombre === '' || $email === '' || $mensaje === '') {
            echo json_encode([
                'status' => 'error',
                'titulo' => 'Campos vacíos',
                'mensaje' => 'Todos los campos son obligatorios'
            ]);
            exit;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo json_encode([
                'status' => 'error',
                'titulo' => 'Correo inválido',
                'mensaje' => 'El correo ingresado no es válido'
            ]);
            exit;
        }

        $mail = new PHPMailer(true);

        try {
            // Configuración SMTP
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = '3145434864c@gmail.com';
            $mail->Password   = 'ayvrxdvuugoizdna'; 
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            // Destinatario y remitente
            $mail->setFrom($email, $nombre);
            $mail->addAddress('3145434864c@gmail.com', 'NUVA S.A.S');

            // Contenido del correo
            $mail->isHTML(true);
            $mail->Subject = "Nuevo mensaje de contacto de $nombre";
            $mail->Body    = "
                <h3>Nuevo mensaje desde tu web</h3>
                <p><strong>Nombre:</strong> $nombre</p>
                <p><strong>Email:</strong> $email</p>
                <p><strong>Mensaje:</strong><br>$mensaje</p>
            ";

            $mail->send();

            echo json_encode([
                'status' => 'success',
                'titulo' => 'Mensaje enviado',
                'mensaje' => 'Gracias por contactarnos. Te responderemos pronto.'
            ]);
            exit;

        } catch (Exception $e) {
            error_log("Error PHPMailer: " . $mail->ErrorInfo);

            echo json_encode([
                'status' => 'error',
                'titulo' => 'Error al enviar',
                'mensaje' => 'No se pudo enviar tu mensaje. Intenta más tarde.'
            ]);
            exit;
        }
    }
}
?>
