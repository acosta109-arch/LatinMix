<?php
/**
 * ARCHIVO DE CONFIGURACIÓN GLOBAL
 * LatinMix Radio - Versión Profesional PHP 8+
 */

// 1. Configuración de Base de Datos (Opcional - Fallback a JSON)
define('DB_HOST', 'localhost');
define('DB_NAME', 'latinmix_radio');
define('DB_USER', 'root');
define('DB_PASS', '');

// 2. Configuración de Administrador
define('ADMIN_EMAIL', 'Admin@gmail.com');
// El hash de 'Admin123@@'
define('ADMIN_PASS_HASH', password_hash('Admin123@@', PASSWORD_DEFAULT));

// 3. Rutas del Sistema
define('BASE_URL', '/php-version/');
define('DATA_PATH', __DIR__ . '/../data/');

// 4. Manejo de Errores (Ajustar para producción)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// 5. Iniciar Sesión de forma segura
if (session_status() === PHP_SESSION_NONE) {
    session_start([
        'cookie_lifetime' => 86400,
        'cookie_httponly' => true,
        'cookie_secure' => false, // Cambiar a true si usas HTTPS
        'use_strict_mode' => true
    ]);
}

// 6. Generar CSRF Token si no existe
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>
