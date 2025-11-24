<?php
require_once __DIR__ . '/../Services/FirebaseTOTPService.php';
require_once __DIR__ . '/../Models/User.php';

class EnrollmentController {
    public function enrollTotp(User $user) {
        // Inicializar servicio (JSON opcional para simulación)
        FirebaseTOTPService::init(__DIR__ . '/../../secrets/firebase-service-account.json');

        // Crear secreto TOTP (simulado)
        $result = FirebaseTOTPService::createTotpSecret($user->firebase_uid);

        // Guardar QR/ID en base de datos cifrada (simulado)
        $user->totp_secret_meta = $result['firebase_totp_id'];
        $user->save();

        // Retornar URI para generar QR en frontend
        return [
            'qr_uri' => $result['qr_uri'],
            'firebase_totp_id' => $result['firebase_totp_id']
        ];
    }
}
