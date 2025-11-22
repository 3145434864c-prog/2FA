<?php
class RecoveryCodeService
{
    /**
     * Genera $count códigos únicos alfanuméricos de 8 caracteres.
     */
    public static function generateCodes(int $count = 10): array
    {
        $codes = [];
        for ($i = 0; $i < $count; $i++) {
            $codes[] = substr(bin2hex(random_bytes(4)), 0, 8); // 8 caracteres alfanuméricos
        }
        return $codes;
    }

    /**
     * Hashea cada código usando Argon2i.
     * Retorna un array de hashes listos para guardar en la BD.
     */
    public static function hashCodes(array $codes): array
    {
        $hashes = [];
        foreach ($codes as $code) {
            $hashes[] = password_hash($code, PASSWORD_ARGON2I);
        }
        return $hashes;
    }

    /**
     * Revoca códigos previos para un usuario.
     * (Simulación: se podría eliminar o marcar como inválidos en la BD)
     */
    public static function revokeCodes(int $userId, PDO $db): void
    {
        $sql = "UPDATE users SET recovery_codes_hash = '[]' WHERE id = ?";
        $stmt = $db->prepare($sql);
        $stmt->execute([$userId]);
    }
}
