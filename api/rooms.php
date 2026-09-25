<?php
header('Content-Type: application/json');
require_once '../includes/config.php';

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? 'list';

try {
    switch ($method) {
        case 'GET':
            if ($action === 'available') {
                $stmt = $pdo->query("SELECT id, room_number, type, price FROM rooms WHERE status='Available' ORDER BY room_number ASC");
                echo json_encode(['success' => true, 'data' => $stmt->fetchAll()]);
            } else {
                $stmt = $pdo->query("SELECT * FROM rooms ORDER BY floor ASC, room_number ASC");
                echo json_encode(['success' => true, 'data' => $stmt->fetchAll()]);
            }
            break;

        case 'POST':
            $data = $_POST;
            if (empty($data) && strpos($_SERVER['CONTENT_TYPE'] ?? '', 'application/json') !== false) {
                $data = json_decode(file_get_contents('php://input'), true) ?? [];
            }

            $required = ['room_number', 'type', 'price'];
            foreach ($required as $field) {
                if (empty($data[$field])) {
                    echo json_encode(['success' => false, 'message' => "Field '$field' is required."]);
                    exit;
                }
            }

            // Check duplicate room number
            $dup = $pdo->prepare("SELECT id FROM rooms WHERE room_number=?");
            $dup->execute([$data['room_number']]);
            if ($dup->fetch()) {
                echo json_encode(['success' => false, 'message' => "Room number '{$data['room_number']}' already exists."]);
                exit;
            }

            // Prepare JSON arrays for features and services
            $features = isset($data['features']) ? json_encode($data['features']) : '[]';
            $services = isset($data['additional_services']) ? json_encode($data['additional_services']) : '[]';

            // Checkboxes might not be in $_POST if unchecked
            $is_featured = isset($data['is_featured']) ? 1 : 0;
            $display_on_website = isset($data['display_on_website']) ? 1 : 0;
            $available_online = isset($data['available_online']) ? 1 : 0;

            $sql = "INSERT INTO rooms (
                room_number, room_name, type, price, floor, status, 
                max_occupancy, adults_allowed, children_allowed, bed_type, num_beds, 
                weekend_rate, holiday_rate, discount_percentage, 
                features, description, additional_services, 
                is_featured, display_on_website, available_online, 
                seo_slug, meta_title, meta_description
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                $data['room_number'],
                $data['room_name'] ?? null,
                $data['type'],
                $data['price'],
                $data['floor'] ?? 1,
                $data['status'] ?? 'Available',
                $data['max_occupancy'] ?? 2,
                $data['adults_allowed'] ?? 2,
                $data['children_allowed'] ?? 0,
                $data['bed_type'] ?? 'Queen Bed',
                $data['num_beds'] ?? 1,
                $data['weekend_rate'] ?: null,
                $data['holiday_rate'] ?: null,
                $data['discount_percentage'] ?: 0,
                $features,
                $data['description'] ?? null,
                $services,
                $is_featured,
                $display_on_website,
                $available_online,
                $data['seo_slug'] ?? null,
                $data['meta_title'] ?? null,
                $data['meta_description'] ?? null
            ]);

            $room_id = $pdo->lastInsertId();

            // Handle Image Uploads
            $upload_dir = '../assets/uploads/rooms/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            $primary_index = isset($data['primary_image_index']) ? (int)$data['primary_image_index'] : 0;

            if (isset($_FILES['room_images']) && !empty($_FILES['room_images']['name'][0])) {
                $file_count = count($_FILES['room_images']['name']);
                for ($i = 0; $i < $file_count; $i++) {
                    if ($_FILES['room_images']['error'][$i] === UPLOAD_ERR_OK) {
                        $tmp_name = $_FILES['room_images']['tmp_name'][$i];
                        $name = basename($_FILES['room_images']['name'][$i]);
                        $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
                        
                        // Security check
                        if (!in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) continue;
                        
                        $new_name = uniqid('room_'.$room_id.'_') . '.' . $ext;
                        $dest = $upload_dir . $new_name;
                        
                        if (move_uploaded_file($tmp_name, $dest)) {
                            $db_path = 'assets/uploads/rooms/' . $new_name;
                            $is_primary = ($i === $primary_index) ? 1 : 0;
                            
                            $img_stmt = $pdo->prepare("INSERT INTO room_images (room_id, image_path, is_primary) VALUES (?, ?, ?)");
                            $img_stmt->execute([$room_id, $db_path, $is_primary]);
                        }
                    }
                }
            }

            echo json_encode(['success' => true, 'message' => 'Room and details added successfully!', 'id' => $room_id]);
            break;

        case 'PUT':
            $data = json_decode(file_get_contents('php://input'), true);
            $id = $data['id'] ?? null;
            $status = $data['status'] ?? null;
            if (!$id || !$status) {
                echo json_encode(['success' => false, 'message' => 'Room ID and status are required.']);
                exit;
            }
            $pdo->prepare("UPDATE rooms SET status=? WHERE id=?")->execute([$status, $id]);
            echo json_encode(['success' => true, 'message' => "Room status updated to '$status'."]);
            break;

        case 'DELETE':
            $id = $_GET['id'] ?? null;
            if (!$id) { echo json_encode(['success' => false, 'message' => 'Room ID required.']); exit; }
            $pdo->prepare("DELETE FROM rooms WHERE id=?")->execute([$id]);
            echo json_encode(['success' => true, 'message' => 'Room deleted.']);
            break;

        default:
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    }
} catch (Exception $e) {
    error_log($e->getMessage());
    echo json_encode(['success' => false, 'message' => 'A server error occurred. Check logs.']);
}
?>
