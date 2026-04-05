<?php
require_once __DIR__ . '/../Modelos/Conexion.php';

class ControladorDashboard {

    private PDO $pdo;

    public function __construct() {
        $this->pdo = Conexion::pdo(); // Inicializa la conexión PDO
    }

    // KPI: Inventario total (suma de stock)
    public function getInventarioTotal(): int {
        $stmt = $this->pdo->query("SELECT SUM(stock) AS total FROM productos");
        $row = $stmt->fetch();
        return (int) ($row['total'] ?? 0);
    }

    // Últimos 5 productos agregados
    public function getUltimosProductosAgregados(): array {
        $stmt = $this->pdo->query("
            SELECT p.nombre, c.nombre_categoria AS categoria, p.creado_en AS fecha
            FROM productos p
            LEFT JOIN categorias c ON p.id_categoria = c.id_categoria
            ORDER BY p.creado_en DESC LIMIT 5
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Últimos 5 productos eliminados (fallback empty si table missing)
    public function getUltimosProductosEliminados(): array {
        try {
            $stmt = $this->pdo->query("
                SELECT pe.nombre, c.nombre_categoria AS categoria, pe.eliminado_en AS fecha
                FROM productos_eliminados pe
                LEFT JOIN categorias c ON pe.id_categoria = c.id_categoria
                ORDER BY pe.eliminado_en DESC LIMIT 5
            ");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Dashboard: productos_eliminados missing - " . $e->getMessage());
            return [];
        }
    }

    // KPI: Usuarios activos/inactivos
    public function getUsuariosActivosInactivos(): array {
        $stmt = $this->pdo->query("
            SELECT
                SUM(CASE WHEN estado_usuario = 1 THEN 1 ELSE 0 END) AS activos,
                COUNT(*) - SUM(CASE WHEN estado_usuario = 1 THEN 1 ELSE 0 END) AS inactivos
            FROM usuarios
        ");
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: ['activos' => 0, 'inactivos' => 0];
    }

    // Gráfico: Top 5 productos con más unidades
    public function getProductosMasUnidades(): array {
        $stmt = $this->pdo->query("
            SELECT nombre, stock
            FROM productos
            ORDER BY stock DESC
            LIMIT 5
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

  public function getProductosStockCritico(int $limite = 4): array {
    $sql = "SELECT p.nombre, c.nombre_categoria AS categoria, p.stock
            FROM productos p
            LEFT JOIN categorias c ON c.id_categoria = p.id_categoria
            WHERE p.stock <= :limite
            ORDER BY p.stock ASC";

    $stmt = $this->pdo->prepare($sql);
    $stmt->bindParam(':limite', $limite, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

}
?>

