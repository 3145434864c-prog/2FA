<?php
// src/Services/FirebaseTOTPService.php
// Servicio para crear secretos TOTP en Firebase (v1 - con simulación y puntos TODO).
// Requiere: kreait/firebase-php + google/auth (ya instalados en tu sistema).

use Kreait\Firebase\Factory;

class FirebaseTOTPService
{
    private static ?string $credentialsPath = null;
    private static $factory = null;
    private static ?string $projectId = null;

    /**
     * Inicializa servicio con la ruta al JSON de credenciales del service account.
     *
     * @param string $credentialsPath Ruta absoluta o relativa (desde el proyecto) al JSON.
     * @throws Exception si no existe el archivo.
     */
    public static function init(string $credentialsPath): void
    {
        if (!file_exists($credentialsPath)) {
            throw new Exception("Archivo de credenciales de Firebase no encontrado: {$credentialsPath}");
        }
        self::$credentialsPath = $credentialsPath;

        // Inicializa Kreait Factory si está disponible
        try {
            require_once __DIR__ . '/../../vendor/autoload.php';
            self::$factory = (new Factory)->withServiceAccount(self::$credentialsPath);
            $serviceAccountData = json_decode(file_get_contents(self::$credentialsPath), true);
            if (isset($serviceAccountData['project_id'])) {
                self::$projectId = $serviceAccountData['project_id'];
            }
        } catch (\Throwable $e) {
            // No hacer crash aquí — seguiremos con un fallback simulador.
            self::$factory = null;
            self::$projectId = $serviceAccountData['project_id'] ?? null;
        }
    }

    /**
     * Crea un secreto TOTP en Firebase y retorna los datos necesarios.
     *
     * Retorna array:
     * [
     *   'firebase_totp_id' => (string) referencia/ID devuelta por Firebase,
     *   'qr_uri' => (string) URI del QR para que el usuario escanee,
     *   'raw_response' => (array) respuesta original de Firebase (si aplica)
     * ]
     *
     * (SUPOSICIÓN) En esta versión v1 devolvemos una respuesta simulada compatible.
     * Sustituir getAccessToken() + llamada REST a Identity Toolkit para producción.
     *
     * @param string $firebaseUid
     * @return array
     * @throws InvalidArgumentException
     */
    public static function createTotpSecret(string $firebaseUid): array
    {
        if (empty($firebaseUid)) {
            throw new InvalidArgumentException("firebase_uid no puede estar vacío.");
        }

        // --- Intento de camino real: obtener access token y llamar al endpoint REST de Identity Toolkit.
        // Implementación REAL (TODO):
        // 1) Obtener access token usando las credenciales del service account (scope correspondiente).
        // 2) Llamar al endpoint de Identity Toolkit para crear/enroll TOTP (requiere endpoint y payload exacto).
        // 3) Parsear respuesta y devolver firebase_totp_id y qr_uri.
        //
        // (Nota: la especificación exacta del endpoint puede variar. Debes usar el Identity Toolkit API
        // y seguir la documentación de Firebase para TOTP/MFA enrollment).

        // --- FALLBACK SIMULADO (compatibilidad con tu flujo actual)
        // Generamos identificador fake pero con formato que puedas almacenar como referencia.
        $simulatedId = 'totp_sim_' . bin2hex(random_bytes(6)); // ej. totp_sim_abcdef1234
        $simulatedQr = "otpauth://totp/Entregable:{$firebaseUid}?secret=" . strtoupper(bin2hex(random_bytes(5))) . "&issuer=Entregable";

        return [
            'firebase_totp_id' => $simulatedId,
            'qr_uri' => $simulatedQr,
            'raw_response' => [
                'simulated' => true,
                'note' => 'Reemplazar por llamada REST real a Identity Toolkit en producción.'
            ]
        ];
    }

    /**
     * (Opcional) Método helper para obtener un access token con Google Auth.
     * Actualmente funciona como placeholder/documentación de cómo implementarlo.
     *
     * @return string|null Access token o null si no implementado.
     */
    private static function getAccessToken(): ?string
    {
        // TODO: Implementar con Google/Auth o kreait to fetch an OAuth2 access token
        // with the scope required for Identity Toolkit: e.g.
        // 'https://www.googleapis.com/auth/identitytoolkit'
        //
        // Ejemplo orientativo (NO activo): usar Google\Auth\OAuth2 o ServiceAccountCredentials.
        return null;
    }
}
