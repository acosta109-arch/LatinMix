<?php
/**
 * DB.php - Controlador de Conexión y Operaciones MySQL (PDO)
 */
require_once __DIR__ . '/config.php';

function get_db_connection() {
    static $pdo = null;
    if ($pdo === null) {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            // Error de conexión silencioso para no romper todo si no hay DB
            error_log("Connection Error: " . $e->getMessage());
            return null;
        }
    }
    return $pdo;
}

/**
 * RE-IMPLEMENTACIÓN DE LOAD_DATA PARA MYSQL
 */
function db_load_data($type) {
    $db = get_db_connection();
    if (!$db) return null; // Fallback (devolver null indica que intente JSON)

    try {
        switch($type) {
            case 'radio_config':
                $stmt = $db->query("SELECT * FROM radio_config LIMIT 1");
                return $stmt->fetch() ?: [];

            case 'radio_news':
                $stmt = $db->query("SELECT * FROM radio_news ORDER BY date DESC");
                return $stmt->fetchAll() ?: [];

            case 'radio_ads':
                $stmt = $db->query("SELECT * FROM radio_ads ORDER BY createdAt DESC");
                return $stmt->fetchAll() ?: [];

            case 'radio_users':
                $stmt = $db->query("SELECT * FROM radio_users ORDER BY name ASC");
                return $stmt->fetchAll() ?: [];
        }
    } catch (PDOException $e) {
        error_log("DB Query Error (" . $type . "): " . $e->getMessage());
        return null;
    }
    return [];
}

/**
 * RE-IMPLEMENTACIÓN DE SAVE_DATA PARA MYSQL (Añadir o Actualizar)
 * En una DB real, save_data es menos genérico, pero para compatibilidad:
 */
function db_save_data($type, $data) {
    if (empty($data)) return false;
    $db = get_db_connection();
    if (!$db) return false;

    try {
        switch($type) {
            case 'radio_config':
                // Datos de Configuración (Siempre actualizamos ID 1)
                $sql = "UPDATE radio_config SET 
                        azuraCastUrl = ?, streamUrl = ?, youtubeLiveId = ?, 
                        facebookUrl = ?, instagramUrl = ?, tiktokUrl = ?, 
                        xUrl = ?, youtubeChannelUrl = ? WHERE id = 1";
                $db->prepare($sql)->execute([
                    $data['azuraCastUrl'] ?? '', $data['streamUrl'] ?? '', $data['youtubeLiveId'] ?? '',
                    $data['facebookUrl'] ?? '', $data['instagramUrl'] ?? '', $data['tiktokUrl'] ?? '',
                    $data['xUrl'] ?? '', $data['youtubeChannelUrl'] ?? ''
                ]);
                return true;

            case 'radio_news':
                $db->exec("TRUNCATE TABLE radio_news");
                $stmt = $db->prepare("INSERT INTO radio_news (id, title, image, summary, date, category) VALUES (?, ?, ?, ?, ?, ?)");
                foreach($data as $n) {
                    $stmt->execute([$n['id'], $n['title'], $n['image'], $n['summary'], $n['date'], $n['category']]);
                }
                return true;

            case 'radio_ads':
                $db->exec("TRUNCATE TABLE radio_ads");
                $stmt = $db->prepare("INSERT INTO radio_ads (id, title, imageUrl, active, startDate, endDate, createdAt) VALUES (?, ?, ?, ?, ?, ?, ?)");
                foreach($data as $a) {
                    $stmt->execute([$a['id'], $a['title'], $a['imageUrl'], (int)($a['active'] ?? 1), $a['startDate'] ?? null, $a['endDate'] ?? null, $a['createdAt'] ?? date('c')]);
                }
                return true;

            case 'radio_users':
                $db->exec("TRUNCATE TABLE radio_users");
                $stmt = $db->prepare("INSERT INTO radio_users (id, name, email, password, role, status, createdAt) VALUES (?, ?, ?, ?, ?, ?, ?)");
                foreach($data as $u) {
                    $stmt->execute([$u['id'], $u['name'], $u['email'], $u['password'], $u['role'] ?? 'Admin', $u['status'] ?? 'Active', $u['createdAt'] ?? date('c')]);
                }
                return true;
        }
    } catch (PDOException $e) {
        error_log("DB Save Error (" . $type . "): " . $e->getMessage());
        return false;
    }
    return false;
}
