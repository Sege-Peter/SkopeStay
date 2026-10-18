<?php
/**
 * Upload / reset brand images (logo, favicon, login background).
 * POST multipart: kind=logo|favicon|login_bg, file=<image>   -> stores and saves setting
 * POST JSON:      {"action":"reset","kind":"logo"}           -> restores default
 */
header('Content-Type: application/json');
require_once __DIR__ . '/../includes/config.php';

$uid = $_SESSION['user_id'];
if (!has_permission($pdo, $uid, 'settings_manage') && !is_admin_role(get_user_primary_role($pdo, $uid))) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Forbidden: administrative privileges required.']);
    exit;
}
verify_csrf();

$kinds = [
    'logo'     => ['setting' => 'logo_url',          'max' => 2 * 1024 * 1024, 'default' => 'assets/images/SkopeStay logo.png'],
    'favicon'  => ['setting' => 'favicon_url',       'max' => 512 * 1024,      'default' => ''],
    'login_bg' => ['setting' => 'login_background',  'max' => 4 * 1024 * 1024, 'default' => ''],
];

$json = json_decode(file_get_contents('php://input'), true);
$kind = $_POST['kind'] ?? ($json['kind'] ?? '');
if (!isset($kinds[$kind])) {
    echo json_encode(['success' => false, 'message' => 'Unknown image type.']);
    exit;
}
$cfg = $kinds[$kind];

$save = $pdo->prepare("INSERT INTO system_settings (setting_key, setting_value, setting_group) VALUES (?, ?, 'branding')
                       ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value), updated_at = CURRENT_TIMESTAMP");

if (($json['action'] ?? '') === 'reset') {
    $save->execute([$cfg['setting'], $cfg['default']]);
    log_action($pdo, $uid, 'reset_branding_image', 'Settings', "Reset $kind to default");
    echo json_encode(['success' => true, 'message' => 'Restored the default image.', 'url' => $cfg['default'] ? '../' . $cfg['default'] : '']);
    exit;
}

$file = $_FILES['file'] ?? null;
if (!$file || $file['error'] !== UPLOAD_ERR_OK) {
    $msg = ($file && in_array($file['error'], [UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE], true))
        ? 'The file is larger than the server allows.' : 'No file was received.';
    echo json_encode(['success' => false, 'message' => $msg]);
    exit;
}
if ($file['size'] > $cfg['max']) {
    echo json_encode(['success' => false, 'message' => 'File too large. Maximum is ' . round($cfg['max'] / 1048576, 1) . ' MB.']);
    exit;
}

// Trust the bytes, not the client-supplied name or MIME type. SVG is refused
// because it can carry script.
$allowed = ['image/png' => 'png', 'image/jpeg' => 'jpg', 'image/webp' => 'webp', 'image/gif' => 'gif', 'image/x-icon' => 'ico', 'image/vnd.microsoft.icon' => 'ico'];
$mime = (new finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']);
if (!isset($allowed[$mime])) {
    echo json_encode(['success' => false, 'message' => 'Please upload a PNG, JPG, WEBP, GIF or ICO image.']);
    exit;
}
if ($allowed[$mime] !== 'ico' && @getimagesize($file['tmp_name']) === false) {
    echo json_encode(['success' => false, 'message' => 'That file does not look like a valid image.']);
    exit;
}

$dir = __DIR__ . '/../assets/uploads/branding';
if (!is_dir($dir)) mkdir($dir, 0755, true);
$name = $kind . '-' . date('Ymd-His') . '-' . bin2hex(random_bytes(4)) . '.' . $allowed[$mime];
if (!move_uploaded_file($file['tmp_name'], "$dir/$name")) {
    echo json_encode(['success' => false, 'message' => 'Could not store the file. Check folder permissions.']);
    exit;
}

$rel = 'assets/uploads/branding/' . $name;
$save->execute([$cfg['setting'], $rel]);
if ($kind === 'logo') {
    // Uploading a logo implies the admin wants to see it
    $save->execute(['logo_display', 'logo']);
}
log_action($pdo, $uid, 'upload_branding_image', 'Settings', "Uploaded new $kind ($name)");

echo json_encode(['success' => true, 'message' => 'Image uploaded and applied across the site.', 'url' => '../' . $rel]);
