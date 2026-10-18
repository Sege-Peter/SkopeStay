<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../includes/config.php';

// Ensure user is authenticated and has administrative permissions
$user_id = $_SESSION['user_id'];
$user_role = get_user_primary_role($pdo, $user_id);
if (!is_admin_role($user_role) && !has_permission($pdo, $user_id, 'settings_manage')) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Forbidden: Administrative privileges required.']);
    exit;
}

// Every editable key with its validation rule. Unknown keys are rejected so the
// endpoint cannot be used to plant arbitrary settings (e.g. logo paths).
const SETTING_RULES = [
    // Property & contacts
    'hotel_name' => ['text', 120, true], 'hotel_tagline' => ['text', 160], 'hotel_star_rating' => ['int', 1, 7],
    'contact_email' => ['email'], 'support_email' => ['email'], 'contact_phone' => ['text', 40], 'contact_phone_alt' => ['text', 40],
    'contact_address' => ['text', 255], 'contact_city' => ['text', 80], 'contact_country' => ['text', 80], 'tax_pin' => ['text', 40],
    'currency_code' => ['text', 3, true], 'currency_symbol' => ['text', 8, true], 'tax_rate' => ['num', 0, 100], 'service_charge' => ['num', 0, 100],
    'timezone' => ['tz'],
    // Branding
    'brand_word_1' => ['text', 40, true], 'brand_word_2' => ['text', 40], 'brand_subtitle' => ['text', 60], 'erp_subtitle' => ['text', 60],
    'logo_display' => ['enum', ['logo', 'icon']], 'brand_icon' => ['slug'],
    // Theme
    'theme_palette' => ['slug'], 'primary_color' => ['hex'], 'secondary_color' => ['hex'], 'accent_color' => ['hex'], 'sidebar_color' => ['hex', true],
    // Maintenance
    'maintenance_mode' => ['bool'], 'maintenance_title' => ['text', 120, true], 'maintenance_message' => ['text', 1000, true],
    'maintenance_estimated_end' => ['text', 120], 'maintenance_bypass_key' => ['slug'],
    // Operations
    'checkin_time' => ['time'], 'checkout_time' => ['time'], 'enable_online_booking' => ['bool'], 'enable_restaurant_pos' => ['bool'],
    'enable_pool_passes' => ['bool'], 'enable_hall_booking' => ['bool'],
    // Notifications
    'notify_new_booking' => ['bool'], 'notify_maintenance' => ['bool'], 'notify_low_stock' => ['bool'], 'notify_email_recipient' => ['email'],
    // Social & website content
    'whatsapp_number' => ['digits'], 'social_facebook' => ['url'], 'social_instagram' => ['url'], 'social_x' => ['url'],
    'social_tiktok' => ['url'], 'social_linkedin' => ['url'], 'social_youtube' => ['url'],
    'footer_about' => ['text', 500], 'meta_description' => ['text', 300], 'google_maps_embed' => ['url'],
    // Billing
    'receipt_footer' => ['text', 300], 'booking_prefix' => ['slug'],
    // Security
    'allow_public_signup' => ['bool'], 'session_timeout' => ['int', 5, 1440], 'show_demo_login' => ['bool'],
];

const THEME_PRESETS = [
    'navy_gold'         => ['#0B132B', '#D4AF37', '#10B981'],
    'emerald_gold'      => ['#064E3B', '#D4AF37', '#059669'],
    'burgundy_bronze'   => ['#4A0E17', '#D97706', '#B45309'],
    'azure_gold'        => ['#0F172A', '#38BDF8', '#F59E0B'],
    'obsidian_platinum' => ['#18181B', '#A1A1AA', '#6366F1'],
    'safari_sand'       => ['#3F2E1E', '#E0B872', '#7C9A3D'],
    'lagoon_coral'      => ['#0E3B43', '#FF7F50', '#2DD4BF'],
    'royal_plum'        => ['#2E1065', '#F0ABFC', '#A855F7'],
];

/** Returns [ok, normalisedValue|errorMessage]. */
function validate_setting($key, $val) {
    $rule = SETTING_RULES[$key];
    $type = $rule[0];
    if (is_bool($val)) $val = $val ? '1' : '0';
    $val = is_scalar($val) ? trim((string)$val) : '';

    switch ($type) {
        case 'text':
            if (!empty($rule[2]) && $val === '') return [false, 'is required'];
            return [true, mb_substr(strip_tags($val), 0, $rule[1])];
        case 'int':
        case 'num':
            if (!is_numeric($val) || $val < $rule[1] || $val > $rule[2]) return [false, "must be between {$rule[1]} and {$rule[2]}"];
            return [true, $type === 'int' ? (string)(int)$val : (string)(float)$val];
        case 'email':
            if ($val !== '' && !filter_var($val, FILTER_VALIDATE_EMAIL)) return [false, 'must be a valid email'];
            return [true, $val];
        case 'url':
            if ($val !== '' && (!filter_var($val, FILTER_VALIDATE_URL) || !preg_match('#^https?://#i', $val))) return [false, 'must be a full https:// link'];
            return [true, $val];
        case 'hex':
            if ($val === '' && !empty($rule[1])) return [true, ''];
            if (!valid_hex($val)) return [false, 'must be a hex colour like #0B132B'];
            return [true, strtoupper($val)];
        case 'bool':
            return [true, in_array($val, ['1', 'true', 'on'], true) ? '1' : '0'];
        case 'enum':
            return in_array($val, $rule[1], true) ? [true, $val] : [false, 'has an invalid choice'];
        case 'slug':
            if (!preg_match('/^[A-Za-z0-9_\-]{1,60}$/', $val)) return [false, 'may only contain letters, numbers, - and _'];
            return [true, $val];
        case 'digits':
            return [true, preg_replace('/\D/', '', $val)];
        case 'time':
            return preg_match('/^\d{2}:\d{2}$/', $val) ? [true, $val] : [false, 'must be a time like 14:00'];
        case 'tz':
            return in_array($val, timezone_identifiers_list(), true) ? [true, $val] : [false, 'is not a known timezone'];
    }
    return [false, 'is not editable'];
}

$method = $_SERVER['REQUEST_METHOD'];

try {
    if ($method === 'GET') {
        echo json_encode(['success' => true, 'data' => get_all_settings($pdo), 'presets' => THEME_PRESETS]);
        exit;
    }

    if ($method === 'POST') {
        verify_csrf();
        $input = json_decode(file_get_contents('php://input'), true);
        if (!$input) {
            $input = $_POST;
        }
        unset($input['csrf_token']);

        if (empty($input) || !is_array($input)) {
            echo json_encode(['success' => false, 'message' => 'No settings data received.']);
            exit;
        }

        $group = preg_replace('/[^a-z_]/', '', $input['group'] ?? 'general') ?: 'general';
        unset($input['group']);

        // Theme presets fill in the three core colours
        if (isset($input['theme_palette'], THEME_PRESETS[$input['theme_palette']])) {
            [$input['primary_color'], $input['secondary_color'], $input['accent_color']] = THEME_PRESETS[$input['theme_palette']];
        }

        $clean = [];
        $errors = [];
        foreach ($input as $key => $val) {
            if (!isset(SETTING_RULES[$key])) continue;
            [$ok, $res] = validate_setting($key, $val);
            if ($ok) $clean[$key] = $res;
            else $errors[] = ucfirst(str_replace('_', ' ', $key)) . ' ' . $res;
        }
        if ($errors) {
            http_response_code(422);
            echo json_encode(['success' => false, 'message' => implode("\n", $errors), 'errors' => $errors]);
            exit;
        }

        $stmt = $pdo->prepare("
            INSERT INTO system_settings (setting_key, setting_value, setting_group)
            VALUES (?, ?, ?)
            ON DUPLICATE KEY UPDATE
                setting_value = VALUES(setting_value),
                updated_at = CURRENT_TIMESTAMP
        ");
        $old = get_all_settings($pdo);
        $changed = [];
        foreach ($clean as $key => $val) {
            $stmt->execute([$key, $val, $group]);
            if (($old[$key] ?? null) !== $val) $changed[] = $key;
        }

        // Audit Trail log
        log_action($pdo, $user_id, 'update_settings', 'Settings',
            "Updated [$group]: " . ($changed ? implode(', ', $changed) : 'no changes'));

        echo json_encode([
            'success' => true,
            'message' => $changed ? 'Settings saved and applied across the site.' : 'No changes to save.',
            'updated' => $changed
        ]);
        exit;
    }

    echo json_encode(['success' => false, 'message' => 'Method not supported']);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error saving settings: ' . $e->getMessage()]);
}
