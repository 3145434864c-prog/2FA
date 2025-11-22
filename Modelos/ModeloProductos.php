<?php
require_once "Modelos/Conexion.php";

class ModeloProductos
{
   public static function crear(array $data): ?int
{
    try {
        $pdo = Conexion::pdo();

        // 1️⃣ Obtener el mayor número de referencia existente
$stmtRef = $pdo->query("SELECT MAX(CAST(SUBSTRING(referencia, 6) AS UNSIGNED)) AS max_ref FROM productos");
$ultimo = $stmtRef->fetch(PDO::FETCH_ASSOC);

// 2️⃣ Generar nueva referencia automática
if ($ultimo && isset($ultimo['max_ref']) && $ultimo['max_ref'] !== null) {
    $nuevaReferencia = 'PROD-' . str_pad(((int)$ultimo['max_ref']) + 1, 4, '0', STR_PAD_LEFT);
} else {
    $nuevaReferencia = 'PROD-0001';
}


        // 3️⃣ Insertar producto con referencia generada
        $sql = "INSERT INTO productos (referencia, id_categoria, nombre, descripcion, precio, stock, imagen)
                VALUES (:referencia, :id_categoria, :nombre, :descripcion, :precio, :stock, :imagen)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':referencia', $nuevaReferencia, PDO::PARAM_STR);
        $stmt->bindValue(':id_categoria', (int)$data['id_categoria'], PDO::PARAM_INT);
        $stmt->bindValue(':nombre', trim($data['nombre']), PDO::PARAM_STR);
        $stmt->bindValue(':descripcion', $data['descripcion'] ?? null, PDO::PARAM_STR);
        $stmt->bindValue(':precio', (string)($data['precio'] ?? '0'), PDO::PARAM_STR);
        $stmt->bindValue(':stock', (int)($data['stock'] ?? 0), PDO::PARAM_INT);
        $stmt->bindValue(':imagen', $data['imagen'] ?? null, PDO::PARAM_STR);

        if (!$stmt->execute()) return null;
        $id = (int)$pdo->lastInsertId();
        return $id > 0 ? $id : null;

    } catch (PDOException $e) {
        error_log("ModeloProductos::crear => ".$e->getMessage());
        return null;
    }
}


    public static function actualizar(int $id, array $data): bool
    {
        $campos = [
            'id_categoria = :id_categoria',
            'nombre = :nombre',
            'descripcion = :descripcion',
            'precio = :precio',
            'stock = :stock'
        ];
        $conImagen = array_key_exists('imagen', $data);
        if ($conImagen) $campos[] = 'imagen = :imagen';

        $sql = "UPDATE productos SET ".implode(', ', $campos)." WHERE id_producto = :id";
        try {
            $pdo = Conexion::pdo();
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->bindValue(':id_categoria', (int)$data['id_categoria'], PDO::PARAM_INT);
            $stmt->bindValue(':nombre', trim($data['nombre']), PDO::PARAM_STR);
            $stmt->bindValue(':descripcion', $data['descripcion'] ?? null, PDO::PARAM_STR);
            $stmt->bindValue(':precio', (string)($data['precio'] ?? '0'), PDO::PARAM_STR);
            $stmt->bindValue(':stock', (int)($data['stock'] ?? 0), PDO::PARAM_INT);
            if ($conImagen) $stmt->bindValue(':imagen', $data['imagen'] ?: null, PDO::PARAM_STR);

            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("ModeloProductos::actualizar => ".$e->getMessage());
            return false;
        }
    }

    public static function eliminar(int $id): bool
    {
        $sql = "DELETE FROM productos WHERE id_producto = :id";
        try {
            $pdo = Conexion::pdo();
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            throw $e; // el controlador decide el mensaje (FK, etc.)
        }
    }

    public static function obtenerPorId(int $id): ?array
    {
        $sql = "SELECT * FROM productos WHERE id_producto = :id LIMIT 1";
        try {
            $pdo = Conexion::pdo();
            $stmt = $pdo->prepare($sql);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row ?: null;
        } catch (PDOException $e) {
            error_log("ModeloProductos::obtenerPorId => ".$e->getMessage());
            return null;
        }
    }

    public static function listar(): array
    {
        $sql = "SELECT p.*, c.nombre_categoria
                FROM productos p
                JOIN categorias c ON c.id_categoria = p.id_categoria
                ORDER BY p.id_producto DESC";
        try {
            $pdo = Conexion::pdo();
            return $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (PDOException $e) {
            error_log("ModeloProductos::listar => ".$e->getMessage());
            return [];
        }
    }
}
