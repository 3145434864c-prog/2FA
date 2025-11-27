<?php
// Controladores/Controlador2FA.php

require_once __DIR__ . '/../Modelos/Conexion.php';
require_once __DIR__ . '/../Modelos/Funciones2FA.php'; // sendOtpEmail()
session_start();

class Controlador2FA
{
    // Configuraciones de seguridad / política (ajustables)
    private int $MAX_RESEND = 3;           // máximo reenvíos por sesión
    private int $RESEND_COOLDOWN = 60;     // segundos entre reenvíos
    private int $MAX_FAILED_ATTEMPTS = 5;  // fallos antes de bloqueo
    private int $BLOCK_MINUTES = 10;       // bloqueo en minutos
    private int $OTP_EXPIRY_SECONDS = 300; // 5 minutos (comprobación redundante)

    public function __construct()
    {
        // Aseguramos que exista la sesión intermedia
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
    }

    /**
     * Acción pública para verificar OTP
     * Debe invocarse por POST desde la vista 2fa: index.php?route=2fa&action=verificar
     */
    public function verificarOTP(): void
    {
        // Asegurar método POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirectToLogin();
            return;
        }

        // Comprobar sesión intermedia
        if (empty($_SESSION['2fa_user_id'])) {
            $this->redirectToLogin();
            return;
        }

        $userId = (int)$_SESSION['2fa_user_id'];

        // Control de bloqueo por fallos almacenado en sesión
        $blockedUntil = $_SESSION['2fa_blocked_until'] ?? null;
        if ($blockedUntil && (time() < (int)$blockedUntil)) {
            $secsLeft = (int)$blockedUntil - time();
            $minutes = ceil($secsLeft / 60);
            $this->swalAndExit('error', 'Acceso bloqueado', "Demasiados intentos fallidos. Intenta de nuevo en {$minutes} minutos.");
            return;
        }

        // Recibir OTP: puede venir como array (6 inputs) o campo único 'otp'
        $otp = '';
        if (!empty($_POST['otp']) && is_array($_POST['otp'])) {
            $otp = implode('', array_map('trim', $_POST['otp']));
        } elseif (!empty($_POST['otp']) && is_string($_POST['otp'])) {
            $otp = trim($_POST['otp']);
        } elseif (!empty($_POST['codigo'])) {
            $otp = trim($_POST['codigo']);
        }

        // Sanitizar y validar formato
        $otp = preg_replace('/\D/', '', $otp);
        if (strlen($otp) !== 6) {
            $this->incrementFailAndRespond($userId, 'Código inválido', 'El código debe tener 6 dígitos.');
            return;
        }

        $pdo = Conexion::pdo();

        // Obtener el registro más reciente para este usuario que no esté usado
        $stmt = $pdo->prepare("
            SELECT * FROM email_2fa
            WHERE user_id = :user_id
            ORDER BY id DESC
            LIMIT 1
        ");
        $stmt->execute([':user_id' => $userId]);
        $record = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$record) {
            $this->incrementFailAndRespond($userId, 'Código no encontrado', 'No existe un código solicitado para esta cuenta. Solicita un nuevo código.');
            return;
        }

        // Verificar si ya fue usado
        if ((int)$record['used'] === 1) {
            $this->incrementFailAndRespond($userId, 'Código usado', 'El código ya fue utilizado. Solicita uno nuevo.');
            return;
        }

        // Verificar expiración
        $expiresAt = strtotime($record['expires_at']);
        if ($expiresAt < time()) {
            $this->incrementFailAndRespond($userId, 'Código expirado', 'El código ha expirado. Solicita uno nuevo.');
            return;
        }

        // Comparar OTP
        if (!hash_equals((string)$record['otp'], (string)$otp)) {
            $this->incrementFailAndRespond($userId, 'Código incorrecto', 'El código es incorrecto. Verifica e intenta de nuevo.');
            return;
        }

        // OK: OTP correcto -> marcar usado y completar login dentro de transacción
        try {
            $pdo->beginTransaction();

            $update = $pdo->prepare("UPDATE email_2fa SET used = 1 WHERE id = :id");
            $update->execute([':id' => $record['id']]);

            // Cargar usuario para poblar SESSION exactamente como hace tu login normal
            $uStmt = $pdo->prepare("SELECT * FROM usuarios WHERE id_usuario = :id_usuario LIMIT 1");
            $uStmt->execute([':id_usuario' => $userId]);
            $usuario = $uStmt->fetch(PDO::FETCH_ASSOC);

            if (!$usuario) {
                $pdo->rollBack();
                $this->swalAndExit('error', 'Usuario no encontrado', 'No fue posible completar el inicio de sesión.');
                return;
            }

            // Setear las mismas variables de sesión que tu login normal
            // $_SESSION['admin'] = $usuario['perfil_usuario'] === 'administrador' ? 'ok' : '';
            $_SESSION['admin'] = 'ok';
            $_SESSION['rol'] = $usuario['perfil_usuario'];
            $_SESSION['usuario_id'] = $usuario['id_usuario'];
            $_SESSION['usuario_nombre'] = $usuario['nombre_usuario'];
            $_SESSION['usuario_foto'] = !empty($usuario['foto_usuario'])
                ? $usuario['foto_usuario']
                : 'vistas/recursos/img/default_user.png';

            // Limpiar variables temporales relacionadas con 2FA
            unset($_SESSION['2fa_user_id']);
            unset($_SESSION['2fa_resend_count']);
            unset($_SESSION['2fa_last_resend']);
            unset($_SESSION['2fa_failed_attempts']);
            unset($_SESSION['2fa_blocked_until']);

            $pdo->commit();

            // Redirigir EXACTAMENTE como pediste
            header('Location: index.php?route=dashboard');
            exit;
        } catch (Exception $e) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            error_log("Error al completar 2FA: " . $e->getMessage());
            $this->swalAndExit('error', 'Error interno', 'No fue posible completar el proceso. Intenta de nuevo.');
            return;
        }
    }

    /**
     * Acción para reenviar OTP
     * Invocar: index.php?route=2fa&action=reenviar
     */
    public function reenviarOTP(): void
    {
        if (empty($_SESSION['2fa_user_id'])) {
            $this->redirectToLogin();
            return;
        }

        $userId = (int)$_SESSION['2fa_user_id'];

        // Inicializar contadores en sesión si no existen
        if (!isset($_SESSION['2fa_resend_count'])) {
            $_SESSION['2fa_resend_count'] = 0;
            $_SESSION['2fa_last_resend'] = 0;
        }

        // Comprobar límite de reenvío
        if ((int)$_SESSION['2fa_resend_count'] >= $this->MAX_RESEND) {
            $this->swalAndExit('error', 'Límite de reenvíos alcanzado', 'Has alcanzado el máximo de reenvíos permitidos en esta sesión.');
            return;
        }

        // Cooldown
        $last = (int)($_SESSION['2fa_last_resend'] ?? 0);
        if (time() - $last < $this->RESEND_COOLDOWN) {
            $secsLeft = $this->RESEND_COOLDOWN - (time() - $last);
            $this->swalAndExit('warning', 'Espera un momento', "Puedes reenviar en {$secsLeft} segundos.");
            return;
        }

        // Obtener email del usuario
        $pdo = Conexion::pdo();
        $stmt = $pdo->prepare("SELECT email_usuario FROM usuarios WHERE id_usuario = :id_usuario LIMIT 1");
        $stmt->execute([':id_usuario' => $userId]);
        $u = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$u) {
            $this->swalAndExit('error', 'Usuario no encontrado', 'No fue posible reenviar el código.');
            return;
        }

        // Generar y enviar nuevo OTP usando tu Funciones2FA::sendOtpEmail
        $sent = sendOtpEmail($userId, $u['email_usuario']);
        if ($sent) {
            // incrementar contador y marcar tiempo
            $_SESSION['2fa_resend_count'] = (int)($_SESSION['2fa_resend_count'] ?? 0) + 1;
            $_SESSION['2fa_last_resend'] = time();

            $this->swalAndExit('success', 'Enviado', 'Se ha reenviado el código al correo registrado.');
            return;
        } else {
            $this->swalAndExit('error', 'Error', 'No fue posible enviar el código. Intenta más tarde.');
            return;
        }
    }

    // ------------------------------
    // Helpers privados
    // ------------------------------

    private function redirectToLogin(): void
    {
        header('Location: index.php?route=ingreso'); // asumiendo 'ingreso' es tu ruta de login
        exit;
    }

    /**
     * Incrementa contador de fallos, aplica bloqueo si aplica y responde con SweetAlert + redirect back to 2fa view
     */
    private function incrementFailAndRespond(int $userId, string $title, string $message): void
    {
        // Incrementar intentos en sesión
        if (!isset($_SESSION['2fa_failed_attempts'])) {
            $_SESSION['2fa_failed_attempts'] = 0;
        }
        $_SESSION['2fa_failed_attempts']++;

        // Si excede máximo, bloquear
        if ((int)$_SESSION['2fa_failed_attempts'] >= $this->MAX_FAILED_ATTEMPTS) {
            $_SESSION['2fa_blocked_until'] = time() + ($this->BLOCK_MINUTES * 60);
            $this->swalAndExit('error', 'Bloqueado', "Has excedido el número de intentos. Intenta de nuevo en {$this->BLOCK_MINUTES} minutos.");
            return;
        }

        $remaining = $this->MAX_FAILED_ATTEMPTS - (int)$_SESSION['2fa_failed_attempts'];
        $this->swalAndExit('error', $title, $message . " Te quedan {$remaining} intentos.");
    }

    /**
     * Envía una respuesta simple usando SweetAlert2 y redirige a la vista 2fa (misma ruta).
     */
    private function swalAndExit(string $icon, string $title, string $text): void
    {
        // Mostramos alert y regresamos a la vista 2fa
        // Renderizamos una página mínima para ejecutar el script y redirigir a la vista 2fa
        echo "<!doctype html><html><head><meta charset='utf-8'><title>{$title}</title>
            <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
            </head><body>
            <script>
                Swal.fire({icon: '{$icon}', title: \"{$title}\", text: \"{$text}\", confirmButtonText: 'OK'}).then(() => {
                    window.location = 'index.php?route=2fa';
                });
            </script>
        </body></html>";
        exit;
    }
}
