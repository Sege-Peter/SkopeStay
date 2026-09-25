<?php
header('Content-Type: application/json');
require_once '../includes/config.php';

$method = $_SERVER['REQUEST_METHOD'];

try {
    switch ($method) {
        case 'GET':
            $type = $_GET['type'] ?? 'halls';
            if ($type === 'halls') {
                $halls = $pdo->query("SELECT * FROM halls ORDER BY name")->fetchAll();
                echo json_encode(['success' => true, 'data' => $halls]);
            } else if ($type === 'bookings') {
                $bookings = $pdo->query("
                    SELECT hb.*, h.name as hall_name, c.full_name as customer_name
                    FROM hall_bookings hb
                    JOIN halls h ON hb.hall_id = h.id
                    JOIN customers c ON hb.customer_id = c.id
                    ORDER BY hb.event_date DESC
                ")->fetchAll();
                echo json_encode(['success' => true, 'data' => $bookings]);
            }
            break;

        case 'POST':
            $data = json_decode(file_get_contents('php://input'), true);
            $action = $data['action'] ?? 'booking';

            if ($action === 'booking') {
                $stmt = $pdo->prepare("INSERT INTO hall_bookings (hall_id, customer_id, event_date, start_time, end_time, total_amount, deposit_paid, services, status) VALUES (?,?,?,?,?,?,?,?,?)");
                $stmt->execute([
                    $data['hall_id'],
                    $data['customer_id'],
                    $data['event_date'],
                    $data['start_time'],
                    $data['end_time'],
                    $data['total_amount'] ?? 0,
                    $data['deposit_paid'] ?? 0,
                    $data['services'] ?? null,
                    $data['status'] ?? 'Pending'
                ]);
                echo json_encode(['success' => true, 'message' => 'Venue booked successfully!']);
            } else if ($action === 'hall') {
                $stmt = $pdo->prepare("INSERT INTO halls (name, type, capacity, hourly_rate, daily_rate, description, amenities, status) VALUES (?,?,?,?,?,?,?,?)");
                $stmt->execute([
                    $data['name'],
                    $data['type'],
                    $data['capacity'],
                    $data['hourly_rate'] ?? 0,
                    $data['daily_rate'] ?? 0,
                    $data['description'] ?? null,
                    $data['amenities'] ?? null,
                    $data['status'] ?? 'Available'
                ]);
                echo json_encode(['success' => true, 'message' => 'Venue added successfully!']);
            }
            break;

        case 'PUT':
            $data = json_decode(file_get_contents('php://input'), true);
            $action = $data['action'] ?? 'booking_status';

            if ($action === 'booking_status') {
                $pdo->prepare("UPDATE hall_bookings SET status=? WHERE id=?")->execute([$data['status'], $data['id']]);
                echo json_encode(['success' => true, 'message' => 'Booking status updated.']);
            }
            break;

        case 'DELETE':
            $id = $_GET['id'] ?? null;
            $type = $_GET['type'] ?? 'booking';
            if ($id) {
                if ($type === 'booking') {
                    $pdo->prepare("DELETE FROM hall_bookings WHERE id=?")->execute([$id]);
                    echo json_encode(['success' => true, 'message' => 'Booking deleted.']);
                } else if ($type === 'hall') {
                    $pdo->prepare("DELETE FROM halls WHERE id=?")->execute([$id]);
                    echo json_encode(['success' => true, 'message' => 'Venue deleted.']);
                }
            }
            break;

        default:
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
