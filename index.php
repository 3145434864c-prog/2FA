<?php

/*=============================================
Configuración de errores
=============================================*/

error_reporting(E_ALL);
ini_set('display_errors', 1); // TEMP: Mostrar errores para debug
ini_set('log_errors', true);
ini_set('ignore_repeated_errors', true);

// Validar que el directorio de logs exista
$logDir = 'logs';
if (!is_dir($logDir)) {
    mkdir($logDir, 0775, true);
}

ini_set('error_log', $logDir . '/errores.log');


require_once "Controladores/ControladorPlantilla.php";

// require_once "Controladores/ControladorAdmin.php";
require_once "Controladores/ControladorUsuarios.php";
require_once "Controladores/ControladorCategorias.php";
require_once "Controladores/ControladorProductos.php";
require_once "Controladores/ControladorContacto.php";

// Antes o después de incluir la vista 2fa, controla acciones POST/GET concretas
if (isset($_GET['route']) && $_GET['route'] === '2fa' && isset($_GET['action'])) {
    require_once "Controladores/Controlador2FA.php";
    $ctrl = new Controlador2FA();

    if ($_GET['action'] === 'verificar') {
        $ctrl->verificarOTP();
        exit;
    } elseif ($_GET['action'] === 'reenviar') {
        $ctrl->reenviarOTP();
        exit;
    }
}

//para poder utilizar variables de sesión
if (session_status() !== PHP_SESSION_ACTIVE) session_start();

// ================================
// 2FA TRUST MIDDLEWARE for sensitive routes
// ================================
require_once "Modelos/ModeloUsuarios.php";
$route = $_GET['route'] ?? '';
$sensitiveRoutes = ['dashboard', 'usuarios', 'generar_reporte', 'productos', 'categorias', 'admin'];
$isSensitive = in_array($route, $sensitiveRoutes) || strpos($route, 'admin') === 0;

if (isset($_SESSION['usuario_id']) && !isset($_SESSION['rol']) && $isSensitive) {
    // Partial session on sensitive route: check trust
    $userId = $_SESSION['usuario_id'];
    $currIp = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $agentHash = hash('sha256', ($_SERVER['HTTP_USER_AGENT'] ?? '') . ($_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? ''));
    if (!ModeloUsuarios::isTrustedWithin24h($userId, $currIp, $agentHash, $isSensitive)) {
        // Not trusted: force 2FA
        $_SESSION['2fa_user_id'] = $userId;
        header('Location: index.php?route=2fa');
        exit;
    }
}

// Load user data if trusted partial session (non-sensitive)
if (isset($_SESSION['usuario_id']) && !isset($_SESSION['rol'])) {
    $user = ModeloUsuarios::findById($_SESSION['usuario_id']);
    if ($user) {
        $_SESSION['rol'] = $user['perfil_usuario'];
        $_SESSION['admin'] = 'ok';
        $_SESSION['usuario_nombre'] = $user['nombre_usuario'];
        $_SESSION['usuario_foto'] = !empty($user['foto_usuario']) ? $user['foto_usuario'] : 'vistas/recursos/img/default_user.png';
    }
}

// =======================================
// Manejo de formulario de contacto
// =======================================
if (isset($_GET['ruta']) && $_GET['ruta'] === 'contacto_enviar') {
    ControladorContacto::enviar();
    exit; // importante para que no cargue la plantilla
}



$plantilla = new ControladorPlantilla();
$plantilla -> mostrarPlantilla();

?>


