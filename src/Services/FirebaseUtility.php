<?php
class FirebaseUtility
{
    private static $credentialsPath;

    /**
     * Inicializa la clase con la ruta de credenciales de Firebase
     */
    public static function init(string $credentialsPath)
    {
        if (!file_exists($credentialsPath)) {
            throw new Exception("Archivo de credenciales de Firebase no encontrado: {$credentialsPath}");
        }
        self::$credentialsPath = $credentialsPath;
    }

    /**
     * Genera un secreto TOTP simulado para un usuario.
     * Se asegura la integridad del firebase_uid.
     */
    public static function generateTotpSecret(string $firebaseUid): array
    {
        if (empty($firebaseUid)) {
            throw new InvalidArgumentException("El firebase_uid no puede estar vacío.");
        }

        // Simulación de la respuesta de Firebase
        return [
            'totp_secret' => bin2hex(random_bytes(10)), // 20 caracteres hexadecimales
            'qr_code_url' => "https://fake.qr.code/{$firebaseUid}"
        ];
    }
}
