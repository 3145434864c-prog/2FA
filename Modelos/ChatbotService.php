<?php
/**
 * ChatbotService.php - Keyword detection (no Gemini)
 */
require_once __DIR__ . '/ModeloInventario.php';

class ChatbotService {

    public function __construct() {
        // Empty - no config needed
    }

    public function procesar(string $mensaje): array {
        $lower = strtolower(trim($mensaje));
        $original = trim($mensaje);
        
        $intencion = $this->detectarIntencion($lower, $original);
        if (!$intencion) {
            return [
                'respuesta' => "🤔 No entendí tu consulta.\n\nIntenta con:\n• \"buscar [nombre]\"\n• \"stock [producto]\"\n• \"categoria [nombre]\"\n• \"prod-0001\"\n• \"stock bajo\"\n• \"valor total\"",
            ];
        }

        $data = $this->dispatchAction($intencion);
        $respuesta = $this->format($intencion['accion'], $data);

        return ['respuesta' => $respuesta, 'accion' => $intencion['accion']];
    }

    private function detectarIntencion(string $lower, string $original): ?array {
        // Saludos
        if (preg_match('/\b(hola|buenos dias|buenos días|buenas tardes|buenas noches|hey|hi|saludos|que tal|qué tal|como estas|cómo estás)\b/i', $lower)) {
            return ['accion' => 'saludo'];
        }
        // Stock bajo
        if (preg_match('/\b(stock\s+(bajo|productos\s+bajos|bajo stock))/i', $lower)) {
            return ['accion' => 'stock_bajo'];
        }
        // Valor total
        if (preg_match('/\b(valor\s+total|valor\s+inventario|total\s+inventario)/i', $lower)) {
            return ['accion' => 'valor_inventario'];
        }
        // Movimientos
        if (preg_match('/\b(movimientos|recientes)\b/i', $lower)) {
            return ['accion' => 'movimientos_recientes'];
        }
        // Referencia
        if (preg_match('/\breferencia\s+(.+)/i', $original, $m)) {
            return ['accion' => 'buscar_por_referencia', 'parametros' => ['nombre_producto' => trim($m[1])]];
        }
        // Referencia directa tipo PROD-0001 sin necesidad de escribir "referencia"
        if (preg_match('/\\b(PROD-\\d+)\\b/i', $original, $m)) {
            return ['accion' => 'buscar_por_referencia', 'parametros' => ['nombre_producto' => strtoupper($m[1])]];
        }
        // Categoría
        if (preg_match('/\b(categoria|categoría)\s+(.+)/i', $original, $m)) {
            return ['accion' => 'buscar_por_categoria', 'parametros' => ['nombre_producto' => trim($m[2])]];
        }
        // Buscar/busca
        if (preg_match('/\b(buscar|busca)\s+(.+)/i', $original, $m)) {
            return ['accion' => 'buscar_producto', 'parametros' => ['nombre_producto' => trim($m[2])]];
        }
        // Stock producto
        if (preg_match('/\bstock\s+(.+)/i', $original, $m)) {
            return ['accion' => 'stock_producto', 'parametros' => ['nombre_producto' => trim($m[1])]];
        }
        // Agotados / sin stock
        if (preg_match('/\b(agotado|agotados|sin stock|stock cero|sin existencia)\b/i', $lower)) {
            return ['accion' => 'productos_agotados'];
        }
        // Todos los productos
        if (preg_match('/\b(todos|todo|listar|lista|mostrar todo|ver todo|todos los productos)\b/i', $lower)) {
            return ['accion' => 'todos_los_productos'];
        }
        // Precio máximo — detecta números con o sin puntos/comas
        if (preg_match('/\b(menos de|maximo|máximo|hasta|menor a|precio max)\s*([\d.,]+)/i', $lower, $m)) {
            $precio = (float) str_replace(['.', ','], ['', '.'], $m[2]);
            return ['accion' => 'productos_por_precio', 'parametros' => ['precio' => $precio, 'orden' => 'ASC']];
        }
        // Más baratos
        if (preg_match('/\b(mas barato|más barato|mas baratos|más baratos|economico|económico|baratos)\b/i', $lower)) {
            return ['accion' => 'productos_por_precio', 'parametros' => ['precio' => 99999999, 'orden' => 'ASC']];
        }
        // Más caros
        if (preg_match('/\b(mas caro|más caro|mas caros|más caros|costoso|costosos|caros)\b/i', $lower)) {
            return ['accion' => 'productos_por_precio', 'parametros' => ['precio' => 99999999, 'orden' => 'DESC']];
        }
        // Búsqueda libre — último recurso, busca directamente en DB
        if (strlen(trim($lower)) >= 3) {
            return ['accion' => 'buscar_producto', 'parametros' => ['nombre_producto' => trim($original)]];
        }
        return null;
    }

    private function dispatchAction(array $intencion): array {
        $params = $intencion['parametros'] ?? [];
        $accion = $intencion['accion'];
        
        return match($accion) {
            'saludo' => ['data' => null],
            'stock_producto' => ['data' => ModeloInventario::stock_producto($params['nombre_producto'] ?? '')],
            'stock_bajo' => ['data' => ModeloInventario::stock_bajo()],
            'movimientos_recientes' => ['data' => ModeloInventario::movimientos_recientes()],
            'buscar_producto' => ['data' => ModeloInventario::buscar_producto($params['nombre_producto'] ?? '')],
            'buscar_por_categoria' => ['data' => ModeloInventario::buscar_por_categoria($params['nombre_producto'] ?? '')],
            'buscar_por_referencia' => ['data' => ModeloInventario::buscar_por_referencia($params['nombre_producto'] ?? '')],
            'valor_inventario' => ['data' => ModeloInventario::valor_inventario()],
            'productos_agotados' => ['data' => ModeloInventario::productos_agotados()],
            'todos_los_productos' => ['data' => ModeloInventario::todos_los_productos()],
            'productos_por_precio' => ['data' => ModeloInventario::productos_por_precio(
                $params['precio'] ?? 99999999,
                $params['orden'] ?? 'ASC'
            )],
            default => ['error' => 'noaction']
        };
    }

    private function format(string $accion, array $data): string {
        return match($accion) {
            'saludo' => "👋 ¡Hola! Soy tu asistente de inventario.\n\nPuedo ayudarte con:\n• 🔍 Buscar productos por nombre o referencia\n• 📦 Consultar stock disponible\n• 📂 Filtrar por categoría\n• 💰 Ver valor total del inventario\n• ⚠️ Ver productos con stock bajo\n• 🚨 Ver productos agotados\n\n¿Qué necesitas consultar?",
            'stock_producto' => $data['data'] 
                ? "📦 *" . $data['data']['nombre'] . "*\n─────────────────\n• Categoría: " . $data['data']['nombre_categoria'] . "\n• Stock actual: " . $data['data']['stock'] . " unidades\n• Precio unitario: $" . number_format($data['data']['precio'], 0, ',', '.') 
                : "❌ No encontré ningún producto con ese nombre.",
            'stock_bajo' => empty($data['data']) 
                ? "✅ ¡Todo en orden! Ningún producto tiene stock crítico." 
                : "⚠️ *Productos con stock bajo*\n─────────────────\n" . implode("\n", array_map(fn($p) => "• " . $p['nombre'] . " → " . $p['stock'] . " und. (" . $p['nombre_categoria'] . ")", $data['data'])),
            'movimientos_recientes' => empty($data['data']) 
                ? "📋 No hay movimientos registrados recientemente." 
                : "📋 *Últimos movimientos*\n─────────────────\n" . implode("\n", array_map(fn($m) => "• " . ucfirst($m['tipo']) . " — " . $m['producto'] . " x" . $m['cantidad'], $data['data'])),
            'buscar_producto', 'buscar_por_categoria', 'todos_los_productos', 'productos_por_precio' => $this->formatLista($data['data']),
            'buscar_por_referencia' => $data['data'] 
                ? $this->formatProductoDetallado($data['data']) 
                : "❌ No encontré ningún producto con esa referencia.",
            'productos_agotados' => empty($data['data']) 
                ? "✅ ¡Excelente! No tienes productos agotados." 
                : "🚨 *Productos agotados*\n─────────────────\n" . implode("\n", array_map(fn($p) => "• " . $p['nombre'] . " (" . $p['referencia'] . ") — " . $p['nombre_categoria'], $data['data'])),
            'valor_inventario' => "💰 *Valor total del inventario*\n─────────────────\n$ " . number_format($data['data'], 0, ',', '.') . " COP\n\nEste valor representa el costo total de todos los productos en stock.",
            default => "🤔 No entendí tu consulta. Puedes preguntarme sobre stock, productos, categorías o valor del inventario."
        };
    }

    private function formatLista(array $productos): string {
        if (empty($productos)) {
            return "🔍 No encontré productos con ese criterio de búsqueda.";
        }
        $count = count($productos);
        $header = "🔍 *" . $count . " producto" . ($count > 1 ? 's' : '') . " encontrado" . ($count > 1 ? 's' : '') . "*\n─────────────────\n";
        $items = array_map(fn($p) => 
            "• " . $p['nombre'] . "\n  Ref: " . $p['referencia'] . " | Stock: " . $p['stock'] . " und. | $" . number_format($p['precio'], 0, ',', '.'),
            array_slice($productos, 0, 5)
        );
        $footer = $count > 5 ? "\n_...y " . ($count - 5) . " más_" : '';
        return $header . implode("\n", $items) . $footer;
    }

    private function formatProductoDetallado(array $p): string {
        return "📋 *" . $p['nombre'] . "*\n─────────────────\n• Referencia: " . $p['referencia'] . "\n• Categoría: " . $p['nombre_categoria'] . "\n• Stock: " . $p['stock'] . " unidades\n• Precio: $" . number_format($p['precio'], 0, ',', '.') . " COP\n• Registrado: " . date('d/m/Y', strtotime($p['creado_en'] ?? 'now'));
    }
}
?>

