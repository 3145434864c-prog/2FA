<?php
// Controladores/contacto_enviar.php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/../vendor/phpmailer/phpmailer/src/PHPMailer.php';
require __DIR__ . '/../vendor/phpmailer/phpmailer/src/SMTP.php';
require __DIR__ . '/../vendor/phpmailer/phpmailer/src/Exception.php';

// Configuración de cabecera para JSON
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $mensaje = trim($_POST['mensaje'] ?? '');

    if (empty($nombre) || empty($email) || empty($mensaje)) {
        echo json_encode([
            'status' => 'error',
            'titulo' => 'Error',
            'mensaje' => 'Todos los campos son obligatorios.'
        ]);
        exit;
    }

    $mail = new PHPMailer(true);

    try {
        // Configuración SMTP
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com'; // tu servidor SMTP
        $mail->SMTPAuth   = true;
        $mail->Username   = 'tu_correo@gmail.com'; // tu correo
        $mail->Password   = 'tu_contraseña_app'; // contraseña de aplicación
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port       = 465;

        // Destinatarios
        $mail->setFrom('tu_correo@gmail.com', 'Contacto Nuva S.A.S');
        $mail->addAddress('destinatario@dominio.com', 'Destinatario'); // destinatario final

        // Contenido
        $mail->isHTML(true);
        $mail->Subject = 'Nuevo mensaje de contacto';
        $mail->Body    = "<strong>Nombre:</strong> {$nombre}<br>
                          <strong>Email:</strong> {$email}<br>
                          <strong>Mensaje:</strong><br>{$mensaje}";
        $mail->AltBody = "Nombre: {$nombre}\nEmail: {$email}\nMensaje:\n{$mensaje}";

        $mail->send();

        echo json_encode([
            'status' => 'success',
            'titulo' => 'Mensaje enviado',
            'mensaje' => 'Gracias por contactarnos, te responderemos pronto.'
        ]);
    } catch (Exception $e) {
        echo json_encode([
            'status' => 'error',
            'titulo' => 'Error',
            'mensaje' => 'No se pudo enviar el mensaje. Intenta nuevamente.'
        ]);
    }
} else {
    echo json_encode([
        'status' => 'error',
        'titulo' => 'Error',
        'mensaje' => 'Método no permitido.'
    ]);
}
