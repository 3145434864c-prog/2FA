<?php
/**
 * ModeloInventario.php - Queries seguras para chatbot (PDO prepared statements)
 */
require_once 'Conexion.php';

class ModeloInventario {
    
    private static function pdo(): PDO {
        return Conexion::pdo();
    }

    // Stock específico producto (LIKE insensitive)
    public static function stock_producto(string $nombre): ?array {
        if (empty(trim($nombre))) return null;
        $sql = "SELECT p.nombre, p.stock, p.precio, c.nombre_categoria 
                FROM productos p LEFT JOIN categorias c ON p.id_categoria = c.id_categoria
                WHERE LOWER(p.nombre) LIKE LOWER(:nombre) ORDER BY p.stock DESC LIMIT 1";
        try {
            $stmt = self::pdo()->prepare($sql);
            $stmt->bindValue(':nombre', "%$nombre%", PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetch() ?: null;
        } catch (PDOException $e) {
            error_log("stock_producto: " . $e->getMessage());
            return null;
        }
    }

    // Stock bajo (<=limite, default 5)
    public static function stock_bajo(int $limite = 5): array {
        $sql = "SELECT p.nombre, p.stock, c.nombre_categoria 
                FROM productos p LEFT JOIN categorias c ON p.id_categoria = c.id_categoria
                WHERE p.stock <= :limite ORDER BY p.stock ASC LIMIT 10";
        try {
            $stmt = self::pdo()->prepare($sql);
            $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("stock_bajo: " . $e->getMessage());
            return [];
        }
    }

    // Productos con stock cero o agotados
    public static function productos_agotados(): array {
        $sql = "SELECT p.*, c.nombre_categoria FROM productos p 
                JOIN categorias c ON p.id_categoria = c.id_categoria
                WHERE p.stock = 0 ORDER BY p.nombre";
        try {
            $stmt = self::pdo()->query($sql);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("productos_agotados: " . $e->getMessage());
            return [];
        }
    }

    // Todos los productos
    public static function todos_los_productos(): array {
        $sql = "SELECT p.*, c.nombre_categoria FROM productos p 
                JOIN categorias c ON p.id_categoria = c.id_categoria
                ORDER BY p.nombre";
        try {
            $stmt = self::pdo()->query($sql);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("todos_productos: " . $e->getMessage());
            return [];
        }
    }

    // Productos filtrados por precio máximo
    public static function productos_por_precio(float $max, string $orden = 'ASC'): array {
        $sql = "SELECT p.*, c.nombre_categoria FROM productos p 
                JOIN categorias c ON p.id_categoria = c.id_categoria
                WHERE p.precio <= :max ORDER BY p.precio $orden LIMIT 10";
        try {
            $stmt = self::pdo()->prepare($sql);
            $stmt->bindValue(':max', $max);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("productos_por_precio: " . $e->getMessage());
            return [];
        }
    }

    // Movimientos recientes (últ 10)
    public static function movimientos_recientes(int $limit = 10): array {
        $sql = "SELECT m.id, m.tipo, m.cantidad, m.fecha, p.nombre AS producto, 
                       c.nombre_categoria, u.nombre_usuario 
                FROM movimientos_inventario m 
                LEFT JOIN productos p ON m.id_producto = p.id_producto 
                LEFT JOIN categorias c ON p.id_categoria = c.id_categoria 
                LEFT JOIN usuarios u ON m.usuario_id = u.id_usuario 
                ORDER BY m.fecha DESC LIMIT :limit";
        try {
            $stmt = self::pdo()->prepare($sql);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("movimientos_recientes: " . $e->getMessage());
            return [];
        }
    }

    // Buscar por nombre, referencia, descripción
    public static function buscar_producto(string $termino): array {
        if (empty(trim($termino))) return [];
        $sql = "SELECT p.*, c.nombre_categoria 
                FROM productos p JOIN categorias c ON p.id_categoria = c.id_categoria
                WHERE LOWER(p.nombre) LIKE LOWER(:termino) 
                   OR LOWER(p.referencia) LIKE LOWER(:termino)
                   OR LOWER(p.descripcion) LIKE LOWER(:termino)
                ORDER BY p.nombre LIMIT 10";
        try {
            $stmt = self::pdo()->prepare($sql);
            $stmt->bindValue(':termino', "%$termino%", PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("buscar_producto: " . $e->getMessage());
            return [];
        }
    }

    // Buscar por categoría
    public static function buscar_por_categoria(string $categoria): array {
        if (empty(trim($categoria))) return [];
        $sql = "SELECT p.*, c.nombre_categoria 
                FROM productos p JOIN categorias c ON p.id_categoria = c.id_categoria
                WHERE LOWER(c.nombre_categoria) LIKE LOWER(:categoria)
                ORDER BY p.nombre";
        try {
            $stmt = self::pdo()->prepare($sql);
            $stmt->bindValue(':categoria', "%$categoria%", PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("buscar_por_categoria: " . $e->getMessage());
            return [];
        }
    }

    // Buscar por referencia exacta (case-insensitive)
    public static function buscar_por_referencia(string $ref): ?array {
        if (empty(trim($ref))) return null;
        $sql = "SELECT p.*, c.nombre_categoria 
                FROM productos p JOIN categorias c ON p.id_categoria = c.id_categoria
                WHERE UPPER(p.referencia) = UPPER(:ref) LIMIT 1";
        try {
            $stmt = self::pdo()->prepare($sql);
            $stmt->bindValue(':ref', trim($ref), PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetch() ?: null;
        } catch (PDOException $e) {
            error_log("buscar_por_referencia: " . $e->getMessage());
            return null;
        }
    }

    // Valor total inventario SUM(stock*precio)
    public static function valor_inventario(): float {
        $sql = "SELECT COALESCE(SUM(p.stock * p.precio), 0) AS total FROM productos p";
        try {
            $stmt = self::pdo()->query($sql);
            $row = $stmt->fetch();
            return (float)($row['total'] ?? 0);
        } catch (PDOException $e) {
            error_log("valor_inventario: " . $e->getMessage());
            return 0.0;
        }
    }
}
?>

