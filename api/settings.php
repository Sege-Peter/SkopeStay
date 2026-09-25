<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../includes/config.php';

// Ensure user is authenticated and has administrative permissions
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized: Please log in to modify settings.']);
    exit;
}

$user_id = $_SESSION['user_id'];
$user_role = get_user_primary_role($pdo, $user_id);
if ($user_role !== 'Super Admin' && $user_role !== 'Hotel Manager' && $user_role !== 'Manager') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Forbidden: Administrative privileges required.']);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];

try {
    if ($method === 'GET') {
        $settings = get_all_settings($pdo);
        echo json_encode(['success' => true, 'data' => $settings]);
        exit;
    }

    if ($method === 'POST') {
        $input = json_decode(file_get_contents('php://input'), true);
        if (!$input) {
            $input = $_POST;
        }

        if (empty($input) || !is_array($input)) {
            echo json_encode(['success' => false, 'message' => 'No settings data received.']);
            exit;
        }

        $group = $input['group'] ?? 'general';
        unset($input['group']);

        // Handle Theme Preset Shortcuts
        if (isset($input['theme_palette']) && $input['theme_palette'] !== 'custom') {
            $presets = [
                'navy_gold' => [
                    'primary_color'   => '#0B132B',
                    'secondary_color' => '#D4AF37',
                    'accent_color'    => '#10B981'
                ],
                'emerald_gold' => [
                    'primary_color'   => '#064E3B',
                    'secondary_color' => '#D4AF37',
                    'accent_color'    => '#059669'
                ],
                'burgundy_bronze' => [
                    'primary_color'   => '#4A0E17',
                    'secondary_color' => '#D97706',
                    'accent_color'    => '#B45309'
                ],
                'azure_gold' => [
                    'primary_color'   => '#0F172A',
                    'secondary_color' => '#38BDF8',
                    'accent_color'    => '#F59E0B'
                ],
                'obsidian_platinum' => [
                    'primary_color'   => '#18181B',
                    'secondary_color' => '#A1A1AA',
                    'accent_color'    => '#6366F1'
                ]
            ];

            if (isset($presets[$input['theme_palette']])) {
                $p = $presets[$input['theme_palette']];
                $input['primary_color'] = $p['primary_color'];
                $input['secondary_color'] = $p['secondary_color'];
                $input['accent_color'] = $p['accent_color'];
            }
        }

        $stmt = $pdo->prepare("
            INSERT INTO system_settings (setting_key, setting_value, setting_group) 
            VALUES (?, ?, ?) 
            ON DUPLICATE KEY UPDATE 
                setting_value = VALUES(setting_value),
                updated_at = CURRENT_TIMESTAMP
        ");

        $updated_keys = [];
        foreach ($input as $key => $val) {
            // Sanitize key name
            $clean_key = preg_replace('/[^a-zA-Z0-9_-]/', '', $key);
            if (empty($clean_key)) continue;

            // Normalize boolean checkboxes
            if (is_bool($val)) {
                $val = $val ? '1' : '0';
            } elseif (is_array($val)) {
                $val = json_encode($val);
            }

            $stmt->execute([$clean_key, (string)$val, $group]);
            $updated_keys[] = $clean_key;
        }

        // Audit Trail log
        log_action($pdo, $user_id, 'update_settings', 'Settings', "Updated " . count($updated_keys) . " setting(s) in group [$group]");

        echo json_encode([
            'success' => true, 
            'message' => 'Settings saved and applied successfully across all site pages!',
            'updated' => $updated_keys
        ]);
        exit;
    }

    echo json_encode(['success' => false, 'message' => 'Method not supported']);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error saving settings: ' . $e->getMessage()]);
}
