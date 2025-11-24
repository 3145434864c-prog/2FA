<?php
// tests/test_enroll_2fa.php

$_POST['user_id'] = 1; // Cambia según tu BD local

ob_start();
require_once __DIR__ . '/../src/Controllers/enroll_2fa.php';

$response = ob_get_clean();

echo "\n--- Respuesta enroll_2fa.php ---\n";
print_r($response);
echo "\n--- FIN ---\n";
