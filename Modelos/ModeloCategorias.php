<?php

require_once "Modelos/Conexion.php";

class ModeloCategorias
{
    /**
     * Registra una categoría y devuelve el ID insertado, o null si falla.
     */
    public static function registrarCategoria(string $nombre): ?int
    {
        $nombre = trim($nombre);
        if ($nombre === '') return null;

        // (Opcional) Evitar duplicados (case-insensitive)
        if (self::existeNombre($nombre)) {
            return null; // ya existe
        }

        $sql = "INSERT INTO categorias (nombre_categoria) VALUES (:nombre_categoria)";

        try {
            $pdo  = Conexion::pdo();
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':nombre_categoria', $nombre, PDO::PARAM_STR);

            if (!$stmt->execute()) return null;

            $id = (int)$pdo->lastInsertId();
            return $id > 0 ? $id : null;

        } catch (PDOException $e) {
            // 23000 suele indicar UNIQUE/FK, etc.
            error_log("Error en registrarCategoria: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Devuelve todas las categorías.
     */
    public static function mostrarCategorias(): array
    {
        try {
            $pdo = Conexion::pdo();
            $stmt = $pdo->prepare("
                SELECT id_categoria, nombre_categoria
                FROM categorias
                ORDER BY id_categoria DESC
            ");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (PDOException $e) {
            error_log("Error en mostrarCategorias: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Actualiza el nombre de una categoría. Devuelve true/false.
     */
    public static function actualizarCategoria(int $id, string $nombre): bool
    {
        $nombre = trim($nombre);
        if ($id <= 0 || $nombre === '') return false;

        // (Opcional) Evitar duplicados (excluyendo el propio ID)
        if (self::existeNombre($nombre, $id)) {
            return false;
        }

        $sql = "UPDATE categorias
                SET nombre_categoria = :nombre
                WHERE id_categoria = :id";

        try {
            $pdo  = Conexion::pdo();
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':nombre', $nombre, PDO::PARAM_STR);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);

            return $stmt->execute() && $stmt->rowCount() >= 0;
            // rowCount() puede ser 0 si el valor no cambió; lo consideramos OK.

        } catch (PDOException $e) {
            error_log("Error en actualizarCategoria: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Elimina una categoría por ID. Devuelve true/false.
     * Si hay restricción FK (productos asociados), el DELETE fallará y retornará false.
     */
    public static function eliminarCategoria(int $id): bool
    {
        if ($id <= 0) return false;

        $sql = "DELETE FROM categorias WHERE id_categoria = :id";

        try {
            $pdo  = Conexion::pdo();
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);

            return $stmt->execute() && $stmt->rowCount() > 0;

        } catch (PDOException $e) {
            // Si hay FK (RESTRICT), caerá aquí con código 23000
            error_log("Error en eliminarCategoria: " . $e->getMessage());
            return false;
        }
    }

    /* =========================
       Helpers opcionales
    ==========================*/

    /**
     * Verifica si ya existe una categoría con el mismo nombre (case-insensitive).
     * $excluirId permite excluir un ID concreto (útil en edición).
     */
    public static function existeNombre(string $nombre, ?int $excluirId = null): bool
    {
        $sql = "SELECT COUNT(*) AS total
                FROM categorias
                WHERE LOWER(nombre_categoria) = LOWER(:nombre)";

        if ($excluirId !== null) {
            $sql .= " AND id_categoria <> :excluir";
        }

        try {
            $pdo  = Conexion::pdo();
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':nombre', trim($nombre), PDO::PARAM_STR);
            if ($excluirId !== null) {
                $stmt->bindValue(':excluir', $excluirId, PDO::PARAM_INT);
            }
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return (int)($row['total'] ?? 0) > 0;

        } catch (PDOException $e) {
            error_log("Error en existeNombre: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtiene una categoría por ID (por si lo necesitas).
     */
    public static function obtenerPorId(int $id): ?array
    {
        if ($id <= 0) return null;

        try {
            $pdo  = Conexion::pdo();
            $stmt = $pdo->prepare("
                SELECT id_categoria, nombre_categoria
                FROM categorias
                WHERE id_categoria = :id
                LIMIT 1
            ");
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row ?: null;

        } catch (PDOException $e) {
            error_log("Error en obtenerPorId: " . $e->getMessage());
            return null;
        }
    }
}
