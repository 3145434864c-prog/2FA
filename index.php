<?php

/*=============================================
Configuración de errores
=============================================*/

error_reporting(E_ALL);
ini_set('display_errors', false); // No mostrar errores al usuario
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

// =======================================
// Manejo de formulario de contacto
// =======================================
if (isset($_GET['ruta']) && $_GET['ruta'] === 'contacto_enviar') {
    ControladorContacto::enviar();
    exit; // importante para que no cargue la plantilla
}



$plantilla = new ControladorPlantilla();
$plantilla -> mostrarPlantilla();

