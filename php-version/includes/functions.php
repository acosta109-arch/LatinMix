<?php
/**
 * LATINMIX RADIO - FUNCIONES DE UTILIDAD
 */

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';

/**
 * Cargar datos según la fuente configurada (Prioriza DB si está conectada)
 */
function load_data($filename) {
    // Intentar desde MySQL
    $db_data = db_load_data($filename);
    if ($db_data !== null) {
        return $db_data;
    }

    // Fallback a JSON si la DB no está disponible
    $path = DATA_PATH . $filename . '.json';
    if (!file_exists($path)) {
        return [];
    }
    $content = file_get_contents($path);
    return json_decode($content, true) ?: [];
}

/**
 * Guardar datos
 */
function save_data($filename, $data) {
    // Intentar en MySQL si es configuración o compatible
    if ($filename === 'radio_config') {
        if (db_save_data($filename, $data)) return true;
    }

    // Guardar también en JSON por seguridad/portabilidad si se desea, 
    // pero para un backend real, aquí desviaríamos a inserciones MySQL directas.
    $path = DATA_PATH . $filename . '.json';
    return file_put_contents($path, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

/**
 * Filtrar anuncios activos por fecha
 */
function get_active_ads($ads) {
    if (empty($ads)) return [];
    
    $today = date('Y-m-d');
    $filtered = array_filter($ads, function($ad) use ($today) {
        $active = isset($ad['active']) ? $ad['active'] : false;
        if (!$active) return false;
        
        $start = isset($ad['startDate']) && !empty($ad['startDate']) ? $ad['startDate'] : null;
        $end = isset($ad['endDate']) && !empty($ad['endDate']) ? $ad['endDate'] : null;
        
        if ($start && $end) {
            return ($today >= $start && $today <= $end);
        }
        
        return true; // Si no hay fecha, pero está activo, se muestra
    });
    
    return array_values($filtered); // Reindexar array
}

/**
 * Formatear fecha para el UI
 */
function format_date($date_str) {
    if (empty($date_str)) return '';
    $timestamp = strtotime($date_str);
    $months = [
        'Jan' => 'Ene', 'Feb' => 'Feb', 'Mar' => 'Mar', 'Apr' => 'Abr', 
        'May' => 'May', 'Jun' => 'Jun', 'Jul' => 'Jul', 'Aug' => 'Ago', 
        'Sep' => 'Sep', 'Oct' => 'Oct', 'Nov' => 'Nov', 'Dec' => 'Dic'
    ];
    $formatted = date('d M Y', $timestamp);
    foreach ($months as $en => $es) {
        $formatted = str_replace($en, $es, $formatted);
    }
    return $formatted;
}

/**
 * Verificar si el usuario está logueado
 */
function is_logged_in() {
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

/**
 * Validar Token CSRF
 */
function validate_csrf($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Redirección Simple
 */
function redirect($url) {
    header("Location: " . $url);
    exit;
}

/**
 * Limpiar Entrada (XSS protection)
 */
/**
 * Procesar Carga de Imagen Segura
 */
function upload_image($file, $folder) {
    if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
        return false;
    }

    $allowed_types = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
    $file_type = $file['type'];
    
    if (!in_array($file_type, $allowed_types)) {
        return false;
    }

    $upload_base = __DIR__ . '/../uploads/' . $folder . '/';
    if (!is_dir($upload_base)) {
        mkdir($upload_base, 0755, true);
    }

    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid() . '.' . $extension;
    $target_path = $upload_base . $filename;

    if (move_uploaded_file($file['tmp_name'], $target_path)) {
        return 'uploads/' . $folder . '/' . $filename;
    }

    return false;
}
function clean_input($input) {
    if (is_array($input)) {
        return array_map('clean_input', $input);
    }
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}
?>
