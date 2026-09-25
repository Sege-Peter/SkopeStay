<?php
header('Content-Type: application/json');
require_once '../includes/config.php';

$method = $_SERVER['REQUEST_METHOD'];
$upload_dir = '../assets/uploads/menu/';

function respond($success, $message, $extra = []) {
    echo json_encode(array_merge(['success' => $success, 'message' => $message], $extra));
    exit;
}

function handle_image_upload($field, $upload_dir, $old_url = '') {
    if (empty($_FILES[$field]['name'])) return $old_url;

    $file     = $_FILES[$field];
    $ext      = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowed  = ['jpg', 'jpeg', 'png', 'webp', 'gif'];

    if (!in_array($ext, $allowed)) {
        respond(false, 'Invalid image format. Use JPG, PNG, WEBP or GIF.');
    }
    if ($file['size'] > 5 * 1024 * 1024) {
        respond(false, 'Image is too large (max 5MB).');
    }

    $filename = 'menu_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
    $dest     = $upload_dir . $filename;

    if (!move_uploaded_file($file['tmp_name'], $dest)) {
        respond(false, 'Failed to save image. Check upload folder permissions.');
    }

    // Delete old image if it was one we uploaded
    if (!empty($old_url) && strpos($old_url, '/assets/uploads/menu/') !== false) {
        $old_path = '../' . ltrim(parse_url($old_url, PHP_URL_PATH), '/');
        if (file_exists($old_path)) @unlink($old_path);
    }

    return '/skopestay/assets/uploads/menu/' . $filename;
}

// Allow PUT-via-POST (for multipart form data with file upload on edit)
if ($method === 'POST' && ($_GET['method'] ?? '') === 'PUT') {
    $method = 'PUT_WITH_FILE';
}

try {
    switch ($method) {

        // ── GET: list all restaurant_items ──────────────────────────────
        case 'GET':
            $items = $pdo->query("SELECT * FROM restaurant_items ORDER BY category, name ASC")->fetchAll();
            respond(true, 'OK', ['data' => $items]);

        // ── POST: create new item ────────────────────────────────────────
        case 'POST':
            $name        = trim($_POST['name']        ?? '');
            $category    = trim($_POST['category']    ?? '');
            $price       = (float)($_POST['price']    ?? 0);
            $description = trim($_POST['description'] ?? '');
            $emoji       = trim($_POST['emoji']       ?? '🍽️');
            $badge       = trim($_POST['badge']       ?? '');
            $status      = trim($_POST['status']      ?? 'Available');

            if (!$name || !$category || $price <= 0) {
                respond(false, 'Name, category, and a valid price are required.');
            }

            $image_url = handle_image_upload('image', $upload_dir);

            $stmt = $pdo->prepare("INSERT INTO restaurant_items (name, category, price, description, emoji, badge, image_url, status) VALUES (?,?,?,?,?,?,?,?)");
            $stmt->execute([$name, $category, $price, $description, $emoji, $badge, $image_url, $status]);

            respond(true, "\"$name\" added to the menu!", ['id' => $pdo->lastInsertId()]);

        // ── PUT with a new image file (FormData override) ────────────────
        case 'PUT_WITH_FILE':
            $id          = (int)($_GET['id']            ?? 0);
            $name        = trim($_POST['name']          ?? '');
            $category    = trim($_POST['category']      ?? '');
            $price       = (float)($_POST['price']      ?? 0);
            $description = trim($_POST['description']   ?? '');
            $emoji       = trim($_POST['emoji']         ?? '🍽️');
            $badge       = trim($_POST['badge']         ?? '');
            $status      = trim($_POST['status']        ?? 'Available');
            $old_url     = trim($_POST['image_url']     ?? '');

            if (!$id || !$name || !$category || $price <= 0) {
                respond(false, 'ID, name, category and valid price are required.');
            }

            $image_url = handle_image_upload('image', $upload_dir, $old_url);

            $stmt = $pdo->prepare("UPDATE restaurant_items SET name=?, category=?, price=?, description=?, emoji=?, badge=?, image_url=?, status=? WHERE id=?");
            $stmt->execute([$name, $category, $price, $description, $emoji, $badge, $image_url, $status, $id]);

            respond(true, "\"$name\" updated successfully.");

        // ── PUT: update item text fields only (no new file) ──────────────
        case 'PUT':
            // Handle multipart/form-data PUT via POST override
            parse_str(file_get_contents('php://input'), $body);
            $id          = (int)($body['id']          ?? 0);
            $name        = trim($body['name']         ?? '');
            $category    = trim($body['category']     ?? '');
            $price       = (float)($body['price']     ?? 0);
            $description = trim($body['description']  ?? '');
            $emoji       = trim($body['emoji']        ?? '🍽️');
            $badge       = trim($body['badge']        ?? '');
            $status      = trim($body['status']       ?? 'Available');
            $image_url   = trim($body['image_url']    ?? '');

            if (!$id || !$name || !$category || $price <= 0) {
                respond(false, 'ID, name, category and valid price are required.');
            }

            $stmt = $pdo->prepare("UPDATE restaurant_items SET name=?, category=?, price=?, description=?, emoji=?, badge=?, image_url=?, status=? WHERE id=?");
            $stmt->execute([$name, $category, $price, $description, $emoji, $badge, $image_url, $status, $id]);

            respond(true, "\"$name\" updated successfully.");

        // ── DELETE: remove item ──────────────────────────────────────────
        case 'DELETE':
            $id = (int)($_GET['id'] ?? 0);
            if (!$id) respond(false, 'Item ID required.');

            // Get image URL for cleanup
            $row = $pdo->prepare("SELECT image_url FROM restaurant_items WHERE id=?");
            $row->execute([$id]);
            $old = $row->fetchColumn();

            $pdo->prepare("DELETE FROM restaurant_items WHERE id=?")->execute([$id]);

            // Clean up uploaded image
            if (!empty($old) && strpos($old, '/assets/uploads/menu/') !== false) {
                $path = '../' . ltrim(parse_url($old, PHP_URL_PATH), '/');
                if (file_exists($path)) @unlink($path);
            }

            respond(true, 'Menu item deleted.');

        default:
            respond(false, 'Method not allowed.');
    }
} catch (Exception $e) {
    respond(false, $e->getMessage());
}
