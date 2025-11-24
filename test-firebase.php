<?php
require __DIR__ . '/vendor/autoload.php';

use Dotenv\Dotenv;
use Kreait\Firebase\Factory;

// Cargar .env.staging
$dotenv = Dotenv::createImmutable(__DIR__, '.env.staging');
$dotenv->load();

// Obtener la ruta de credenciales desde la variable de entorno
$firebaseCredentials = $_ENV['FIREBASE_CREDENTIALS_PATH'] ?? null;

// Validaciones
if (empty($firebaseCredentials)) {
    die("❌ Variable FIREBASE_CREDENTIALS_PATH no definida o vacía en .env.staging");
}

if (!file_exists($firebaseCredentials)) {
    die("❌ Archivo de credenciales Firebase no encontrado: $firebaseCredentials");
}

// Conexión con Firebase
try {
    $factory = (new Factory)->withServiceAccount($firebaseCredentials);
    $auth = $factory->createAuth();
    echo "✅ Conexión con Firebase exitosa";
} catch (\Exception $e) {
    echo "❌ Error conectando con Firebase: " . $e->getMessage();
}
