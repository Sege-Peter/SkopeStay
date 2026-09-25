<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(204); exit; }

require_once '../includes/config.php';

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? 'list';

try {
    switch ($method) {
        case 'GET':
            if ($action === 'stats') {
                // Dashboard stats
                $total    = $pdo->query("SELECT COUNT(*) FROM rooms")->fetchColumn();
                $occupied = $pdo->query("SELECT COUNT(*) FROM rooms WHERE status='Occupied'")->fetchColumn();
                $cleaning = $pdo->query("SELECT COUNT(*) FROM rooms WHERE status='Cleaning'")->fetchColumn();
                $available= $pdo->query("SELECT COUNT(*) FROM rooms WHERE status='Available'")->fetchColumn();
                $revenue  = $pdo->query("SELECT COALESCE(SUM(total_amount),0) FROM bookings WHERE DATE(created_at)=CURDATE()")->fetchColumn();
                $rest_orders = $pdo->query("SELECT COUNT(*) FROM restaurant_orders WHERE DATE(created_at)=CURDATE()")->fetchColumn();
                echo json_encode([
                    'success'       => true,
                    'total_rooms'   => (int)$total,
                    'occupied'      => (int)$occupied,
                    'cleaning'      => (int)$cleaning,
                    'available'     => (int)$available,
                    'revenue_today' => number_format((float)$revenue, 2),
                    'rest_orders'   => (int)$rest_orders,
                ]);
            } else {
                // List bookings
                $stmt = $pdo->query("SELECT b.*, r.room_number, r.type FROM bookings b LEFT JOIN rooms r ON b.room_id=r.id ORDER BY b.created_at DESC LIMIT 50");
                echo json_encode(['success' => true, 'data' => $stmt->fetchAll()]);
            }
            break;

        case 'POST':
            $data = json_decode(file_get_contents('php://input'), true);
            if (!$data) $data = $_POST;

            $required = ['room_id', 'guest_name', 'check_in'];
            foreach ($required as $field) {
                if (empty($data[$field])) {
                    echo json_encode(['success' => false, 'message' => "Field '$field' is required."]);
                    exit;
                }
            }

            // Step 1: Check room exists and its general status
            $check = $pdo->prepare("SELECT status, room_number, type FROM rooms WHERE id=?");
            $check->execute([$data['room_id']]);
            $room = $check->fetch();
            if (!$room) {
                echo json_encode(['success' => false, 'message' => 'Room not found.']);
                exit;
            }
            if ($room['status'] === 'Occupied' || $room['status'] === 'Cleaning') {
                echo json_encode(['success' => false, 'message' => "Room {$room['room_number']} ({$room['type']}) is currently {$room['status']} and cannot be booked."]);
                exit;
            }

            // Step 2: Check for overlapping bookings on the requested dates
            $check_in  = $data['check_in'];
            $check_out = $data['check_out'] ?? null;

            if ($check_out) {
                // Full date-range overlap check
                $overlap = $pdo->prepare("
                    SELECT COUNT(*) FROM bookings
                    WHERE room_id = ?
                      AND status IN ('Active')
                      AND check_in  < ?
                      AND (check_out > ? OR check_out IS NULL)
                ");
                $overlap->execute([$data['room_id'], $check_out, $check_in]);
            } else {
                // Check-in only: check if room is occupied on that single day
                $overlap = $pdo->prepare("
                    SELECT COUNT(*) FROM bookings
                    WHERE room_id = ?
                      AND status IN ('Active')
                      AND check_in <= ?
                      AND (check_out > ? OR check_out IS NULL)
                ");
                $overlap->execute([$data['room_id'], $check_in, $check_in]);
            }

            if ((int)$overlap->fetchColumn() > 0) {
                echo json_encode([
                    'success' => false,
                    'message' => "Room {$room['room_number']} is already booked for those dates. Please select different dates or another room."
                ]);
                exit;
            }

            $nights = isset($data['check_out']) ? max(1, (int)((strtotime($data['check_out']) - strtotime($data['check_in'])) / 86400)) : 1;
            $price_stmt = $pdo->prepare("SELECT price FROM rooms WHERE id=?");
            $price_stmt->execute([$data['room_id']]);
            $price_row = $price_stmt->fetch();
            $total = ($price_row['price'] ?? 0) * $nights;

            $stmt = $pdo->prepare("INSERT INTO bookings (room_id, guest_name, check_in, check_out, total_amount, status) VALUES (?, ?, ?, ?, ?, 'Active')");
            $stmt->execute([$data['room_id'], $data['guest_name'], $data['check_in'], $data['check_out'] ?? null, $total]);

            // Update room status to Occupied
            $pdo->prepare("UPDATE rooms SET status='Occupied' WHERE id=?")->execute([$data['room_id']]);

            echo json_encode(['success' => true, 'message' => 'Booking created successfully!', 'id' => $pdo->lastInsertId(), 'total' => $total]);
            break;

        default:
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
