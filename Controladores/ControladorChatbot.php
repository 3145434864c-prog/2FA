<?php
/**
 * ControladorChatbot.php - API 100% JSON safe, HTTP 200 forced
 */
ob_start();
ob_implicit_flush(0);
ini_set('display_errors', 0);
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
http_response_code(200);

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    ob_end_clean();
    echo json_encode(['error' => 'POST only']);
    exit;
}

$mensaje = trim($_POST['mensaje'] ?? '');
if (!$mensaje) {
    ob_end_clean();
    echo json_encode(['respuesta' => 'Mensaje vacío']);
    exit;
}

$output_buffer = '';
try {
require_once __DIR__ . '/../Modelos/ChatbotService.php';
    if (!class_exists('ChatbotService')) {
        throw new Exception('ChatbotService class not found after include. Path: ' . __DIR__ . '/../Modelos/ChatbotService.php');
    }
    $service = new ChatbotService();
    $result = $service->procesar($mensaje);
    
    // Clean response: hide debug from UI
    $response = $result;
    // unset($response['debug']);
    // unset($response['error']);
    $output_buffer = json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PARTIAL_OUTPUT_ON_ERROR);
    
} catch (Throwable $e) {
    error_log("Chatbot EXCEPTION: " . $e->getMessage());
    $output_buffer = json_encode(['error' => 'server_error', 'debug' => $e->getMessage()]);
}

ob_end_clean();
echo $output_buffer;

