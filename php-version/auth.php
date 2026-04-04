<?php
/**
 * LÓGICA DE AUTENTICACIÓN Y SEGURIDAD
 */
require_once __DIR__ . '/includes/config.php';

/**
 * Iniciar sesión segura (Ya está en config, pero asegurar aquí)
 */
function start_secure_session() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start([
            'cookie_lifetime' => 86400,
            'cookie_httponly' => true,
            'cookie_secure' => false, // Cambiar a true en producción con SSL
            'use_strict_mode' => true,
            'cookie_samesite' => 'Lax'
        ]);
    }
}

/**
 * Validar credenciales de administrador
 */
function authenticate($email, $password) {
    if ($email === ADMIN_EMAIL && password_verify($password, ADMIN_PASS_HASH)) {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_email'] = $email;
        $_SESSION['last_activity'] = time();
        return true;
    }
    return false;
}

/**
 * Verificar si el usuario está activo y logueado
 */
function check_auth() {
    start_secure_session();
    
    if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
        return false;
    }

    // Opcional: Timeout de sesión (30 minutos)
    if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > 1800)) {
        session_unset();
        session_destroy();
        return false;
    }
    
    $_SESSION['last_activity'] = time();
    return true;
}

/**
 * Logout
 */
function logout() {
    start_secure_session();
    $_SESSION = array();
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    session_destroy();
}

/**
 * Protección CSRF
 */
function get_csrf_token() {
    return $_SESSION['csrf_token'] ?? '';
}

function verify_csrf() {
    $token = $_POST['csrf_token'] ?? $_GET['token'] ?? '';
    return hash_equals($_SESSION['csrf_token'] ?? '', $token);
}
?>
