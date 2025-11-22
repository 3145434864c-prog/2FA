<?php
require_once "Modelos/Conexion.php";

class ModeloUsuarios {

    // =======================================
    // Crear usuario
    // =======================================
    public static function crear(array $datos): ?int {

        // Validar campos mínimos
        $nombre  = trim($datos['nombre_usuario'] ?? '');
        $email   = trim($datos['email_usuario'] ?? '');
        $password = $datos['password_usuario'] ?? '';
        $perfilValido = ['administrador', 'editor', 'usuario'];
        $perfil  = in_array($datos['perfil_usuario'] ?? 'usuario', $perfilValido)
                    ? $datos['perfil_usuario']
                    : 'usuario';

        $estado  = isset($datos['estado_usuario']) ? (int)$datos['estado_usuario'] : 0;
        $foto    = $datos['foto_usuario'] ?? null;

        if ($nombre === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || $password === '') {
            return null;
        }

        // Validar email único
        if (self::findByEmail($email)) {
            return null;
        }

        // Ya viene hasheado desde el controlador, pero validamos que sea seguro
        if (!str_starts_with($password, '$argon2id$')) {
            $password = password_hash($password, PASSWORD_ARGON2ID, [
                'memory_cost' => 1 << 17,
                'time_cost'   => 4,
                'threads'     => 2
            ]);
        }

        // Insertar en DB
        $sql = "INSERT INTO usuarios 
                (nombre_usuario, email_usuario, password_usuario, perfil_usuario, estado_usuario, foto_usuario, fyh_creacion_usuario) 
                VALUES 
                (:nombre, :email, :password, :perfil, :estado, :foto, NOW())";

        try {
            $pdo = Conexion::pdo();
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':nombre', $nombre, PDO::PARAM_STR);
            $stmt->bindValue(':email', $email, PDO::PARAM_STR);
            $stmt->bindValue(':password', $password, PDO::PARAM_STR);
            $stmt->bindValue(':perfil', $perfil, PDO::PARAM_STR);
            $stmt->bindValue(':estado', $estado, PDO::PARAM_INT);
            $stmt->bindValue(':foto', $foto, PDO::PARAM_STR);
            if (!$stmt->execute()) return null;

            return (int)$pdo->lastInsertId();
        } catch (PDOException $e) {
            error_log("Error en ModeloUsuarios::crear - " . $e->getMessage());
            return null;
        }
    }

    // =======================================
    // Actualizar usuario
    // =======================================
    public static function actualizar(int $id, array $datos): bool {
        $usuarioActual = self::findById($id);
        if (!$usuarioActual) return false;

        $nombre = trim($datos['nombre_usuario'] ?? $usuarioActual['nombre_usuario']);
        $email  = trim($datos['email_usuario'] ?? $usuarioActual['email_usuario']);
        $perfil = $datos['perfil_usuario'] ?? $usuarioActual['perfil_usuario'];
        $estado = isset($datos['estado_usuario']) ? (int)$datos['estado_usuario'] : $usuarioActual['estado_usuario'];
        $foto   = $datos['foto_usuario'] ?? $usuarioActual['foto_usuario'];

        // Validar email único (excepto el propio usuario)
        $otro = self::findByEmail($email);
        if ($otro && $otro['id_usuario'] != $id) return false;

        // Contraseña opcional
        $password = $datos['password_usuario'] ?? null;
        $campos = "nombre_usuario = :nombre, email_usuario = :email, perfil_usuario = :perfil, estado_usuario = :estado, foto_usuario = :foto";
        if ($password) {
            // Si no está hasheada, la hasheamos
            if (!str_starts_with($password, '$argon2id$')) {
                $password = password_hash($password, PASSWORD_ARGON2ID);
            }
            $campos .= ", password_usuario = :password";
        }

        $sql = "UPDATE usuarios SET $campos WHERE id_usuario = :id";
        try {
            $stmt = Conexion::pdo()->prepare($sql);
            $stmt->bindValue(':nombre', $nombre, PDO::PARAM_STR);
            $stmt->bindValue(':email', $email, PDO::PARAM_STR);
            $stmt->bindValue(':perfil', $perfil, PDO::PARAM_STR);
            $stmt->bindValue(':estado', $estado, PDO::PARAM_INT);
            $stmt->bindValue(':foto', $foto, PDO::PARAM_STR);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            if ($password) $stmt->bindValue(':password', $password, PDO::PARAM_STR);

            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error en ModeloUsuarios::actualizar - " . $e->getMessage());
            return false;
        }
    }

    // =======================================
    // Eliminar usuario
    // =======================================
    public static function eliminar(int $id): bool {
        $usuario = self::findById($id);
        if (!$usuario) return false;

        if ($usuario['perfil_usuario'] === 'administrador') {
            $sql = "SELECT COUNT(*) FROM usuarios WHERE perfil_usuario = 'administrador'";
            $count = (int)Conexion::pdo()->query($sql)->fetchColumn();
            if ($count <= 1) return false; // último admin
        }

        $sql = "DELETE FROM usuarios WHERE id_usuario = :id";
        try {
            $stmt = Conexion::pdo()->prepare($sql);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error en ModeloUsuarios::eliminar - " . $e->getMessage());
            return false;
        }
    }

    // =======================================
    // Obtener usuario por ID
    // =======================================
    public static function findById(int $id): ?array {
        $sql = "SELECT * FROM usuarios WHERE id_usuario = :id LIMIT 1";
        try {
            $stmt = Conexion::pdo()->prepare($sql);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
            return $usuario ?: null;
        } catch (PDOException $e) {
            error_log("Error en ModeloUsuarios::findById - " . $e->getMessage());
            return null;
        }
    }

    // =======================================
    // Buscar usuario por email
    // =======================================
    public static function findByEmail(string $email): ?array {
        $sql = "SELECT * FROM usuarios WHERE email_usuario = :email LIMIT 1";
        try {
            $stmt = Conexion::pdo()->prepare($sql);
            $stmt->bindValue(':email', $email, PDO::PARAM_STR);
            $stmt->execute();
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
            return $usuario ?: null;
        } catch (PDOException $e) {
            error_log("Error en ModeloUsuarios::findByEmail - " . $e->getMessage());
            return null;
        }
    }

    // =======================================
    // Listar usuarios
    // =======================================
    public static function listar(): array {
        $sql = "SELECT id_usuario, nombre_usuario, email_usuario, perfil_usuario, estado_usuario, foto_usuario, fyh_creacion_usuario 
                FROM usuarios ORDER BY id_usuario DESC";
        try {
            return Conexion::pdo()->query($sql)->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (PDOException $e) {
            error_log("Error en ModeloUsuarios::listar - " . $e->getMessage());
            return [];
        }
    }

    // =======================================
    // Funciones adicionales (recuperación)
    // =======================================
    public static function mdlBuscarUsuarioPorEmail($email) {
        $stmt = Conexion::pdo()->prepare("SELECT * FROM usuarios WHERE email_usuario = :email LIMIT 1");
        $stmt->bindParam(":email", $email, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetch();
    }

    public static function mdlRegistrarSolicitudRecuperacion($data) {
        $stmt = Conexion::pdo()->prepare("
            INSERT INTO password_resets 
            (user_id, selector, token_hash, expires_at, request_ip, user_agent)
            VALUES (:user_id, :selector, :token_hash, :expires_at, :request_ip, :user_agent)
        ");
        $stmt->bindParam(":user_id", $data["user_id"], PDO::PARAM_INT);
        $stmt->bindParam(":selector", $data["selector"], PDO::PARAM_STR);
        $stmt->bindParam(":token_hash", $data["token_hash"], PDO::PARAM_STR);
        $stmt->bindParam(":expires_at", $data["expires_at"], PDO::PARAM_STR);
        $stmt->bindParam(":request_ip", $data["request_ip"], PDO::PARAM_STR);
        $stmt->bindParam(":user_agent", $data["user_agent"], PDO::PARAM_STR);
        return $stmt->execute();
    }
}
