<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'skopestay');

try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    http_response_code(500);
    die(json_encode(['success' => false, 'message' => 'Database connection failed: ' . $e->getMessage()]));
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/rbac.php';

// System Settings Helper Functions
function get_all_settings($pdo) {
    static $cached_settings = null;
    if ($cached_settings !== null) {
        return $cached_settings;
    }
    $cached_settings = [];
    try {
        $stmt = $pdo->query("SELECT setting_key, setting_value FROM system_settings");
        while ($row = $stmt->fetch()) {
            $cached_settings[$row['setting_key']] = $row['setting_value'];
        }
    } catch (Exception $e) {
        // Fallback if table doesn't exist yet
    }
    return $cached_settings;
}

function get_setting($pdo, $key, $default = '') {
    $settings = get_all_settings($pdo);
    return $settings[$key] ?? $default;
}

function hex_to_rgb_triplet($hex) {
    $hex = ltrim($hex, '#');
    if (strlen($hex) === 3) {
        $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
    }
    if (strlen($hex) !== 6) return '11 19 43';
    return hexdec(substr($hex, 0, 2)) . ' ' . hexdec(substr($hex, 2, 2)) . ' ' . hexdec(substr($hex, 4, 2));
}

// Global Maintenance Mode Guard
$maintenance_active = get_setting($pdo, 'maintenance_mode', '0') === '1';
if ($maintenance_active) {
    $uri = $_SERVER['REQUEST_URI'] ?? '';
    $is_auth = strpos($uri, '/auth/') !== false;
    $is_api = strpos($uri, '/api/') !== false;
    $is_modules = strpos($uri, '/modules/') !== false;
    $is_admin = isset($_SESSION['user_id']);
    
    // Check for secret bypass parameter
    $bypass_key = get_setting($pdo, 'maintenance_bypass_key', 'skope_vip_bypass_2026');
    if (isset($_GET['bypass']) && $_GET['bypass'] === $bypass_key) {
        $_SESSION['maintenance_bypass'] = true;
    }
    $has_bypass = isset($_SESSION['maintenance_bypass']) && $_SESSION['maintenance_bypass'] === true;

    // If visiting public pages without staff authentication or VIP bypass
    if (!$is_admin && !$is_auth && !$is_api && !$has_bypass) {
        $m_title = get_setting($pdo, 'maintenance_title', 'Enhancing Your 5-Star Experience');
        $m_message = get_setting($pdo, 'maintenance_message', 'We are currently performing scheduled maintenance to upgrade our digital concierge and reservation ecosystem.');
        $m_end = get_setting($pdo, 'maintenance_estimated_end', 'Scheduled duration: approx. 45 minutes');
        $m_phone = get_setting($pdo, 'contact_phone', '+254 742 380 183');
        $m_email = get_setting($pdo, 'contact_email', 'admin@skopestay.com');
        $m_hotel = get_setting($pdo, 'hotel_name', 'SkopeStay Resort & Suites');
        
        require __DIR__ . '/maintenance_guard.php';
        exit;
    }
}
?>
