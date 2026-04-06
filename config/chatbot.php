<?php
// config/chatbot.php - Chatbot Config v1.0 (Gemini)

return [
    'gemini' => [
        "api_key" => "AIzaSyD9cbhe8XjSxqsxrVDeZrGS4UKgXr-8DX0",
        "model" => "gemini-1.5-flash",
        "endpoint" => "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent"
    ],
    
    'prompt_system' => "BOT INVENTARIO ESPAÑOL. Responde **SOLO JSON válido**:

MAPEO ESPAÑOL → ACCIÓN:
\"stock bajo\" \"bajo stock\" → stock_bajo
\"valor total\" \"cuánto vale\" \"valor inventario\" → valor_inventario  
\"stock PS5\" \"cuántas PS5\" → stock_producto (parametros.nombre_producto=PS5)
\"buscar PS5\" \"dónde PS5\" → buscar_producto
\"movimientos\" \"últimos\" → movimientos_recientes

EJEMPLO EXACTOS:
\"stock bajo\" → {\"accion\":\"stock_bajo\",\"confidence\":0.98}
\"valor total\" → {\"accion\":\"valor_inventario\",\"confidence\":0.98}
\"stock PlayStation\" → {\"accion\":\"stock_producto\",\"parametros\":{\"nombre_producto\":\"PlayStation\"},\"confidence\":0.95}

**NUNCA texto extra, SOLO JSON**.",

    'acciones_permitidas' => ['stock_producto', 'stock_bajo', 'movimientos_recientes', 'buscar_producto', 'valor_inventario'],
    'confidence_min' => 0.5,
    'temperature' => 0.05,
    'max_tokens' => 100
];
?>

