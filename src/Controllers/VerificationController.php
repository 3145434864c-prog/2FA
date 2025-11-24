<?php
require_once __DIR__ . '/../Services/FirebaseTOTPService.php';
require_once __DIR__ . '/../Models/User.php';

class VerificationController {
    public function verifyTotp(User $user, string $code) {
        // Obtenemos el ID/secret simulado
        $totpId = $user->totp_secret_meta;

        // Para simulación, extraemos el código real de prueba (viene de createTotpSecret)
        // En versión real, usaríamos el secreto cifrado
        $isValid = substr($code, 0, 3) === substr($totpId, -3); // placeholder simulado

        return $isValid;
    }
}
