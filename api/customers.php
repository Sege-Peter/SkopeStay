<?php
header('Content-Type: application/json');
require_once '../includes/config.php';

$method = $_SERVER['REQUEST_METHOD'];

try {
    switch ($method) {
        case 'GET':
            $id = $_GET['id'] ?? null;
            if ($id) {
                // Get customer profile + stats
                $customer = $pdo->prepare("SELECT * FROM customers WHERE id = ?");
                $customer->execute([$id]);
                $c = $customer->fetch();
                
                if ($c) {
                    $stats = [
                        'room_bookings' => $pdo->query("SELECT COUNT(*) FROM bookings WHERE guest_name = '{$c['full_name']}'")->fetchColumn(),
                        'hall_bookings' => $pdo->query("SELECT COUNT(*) FROM hall_bookings WHERE customer_id = $id")->fetchColumn(),
                        'pool_visits'   => $pdo->query("SELECT COUNT(*) FROM pool_passes WHERE customer_id = $id")->fetchColumn(),
                        'total_spent'   => 0 // Could aggregate later
                    ];
                    echo json_encode(['success' => true, 'data' => $c, 'stats' => $stats]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Customer not found.']);
                }
            } else {
                $customers = $pdo->query("SELECT * FROM customers ORDER BY created_at DESC")->fetchAll();
                echo json_encode(['success' => true, 'data' => $customers]);
            }
            break;

        case 'POST':
            $data = json_decode(file_get_contents('php://input'), true);
            $stmt = $pdo->prepare("INSERT INTO customers (full_name, phone, email, national_id, address, customer_category) VALUES (?,?,?,?,?,?)");
            $stmt->execute([
                $data['full_name'],
                $data['phone'] ?? null,
                $data['email'] ?? null,
                $data['national_id'] ?? null,
                $data['address'] ?? null,
                $data['customer_category'] ?? 'Hotel Guest'
            ]);
            echo json_encode(['success' => true, 'message' => 'Customer registered successfully!']);
            break;

        case 'PUT':
            $data = json_decode(file_get_contents('php://input'), true);
            $stmt = $pdo->prepare("UPDATE customers SET full_name=?, phone=?, email=?, national_id=?, address=?, customer_category=?, loyalty_tier=?, reward_points=? WHERE id=?");
            $stmt->execute([
                $data['full_name'],
                $data['phone'],
                $data['email'],
                $data['national_id'],
                $data['address'],
                $data['customer_category'],
                $data['loyalty_tier'],
                $data['reward_points'],
                $data['id']
            ]);
            echo json_encode(['success' => true, 'message' => 'Customer profile updated.']);
            break;

        case 'DELETE':
            $id = $_GET['id'] ?? null;
            if ($id) {
                $pdo->prepare("DELETE FROM customers WHERE id=?")->execute([$id]);
                echo json_encode(['success' => true, 'message' => 'Customer removed.']);
            }
            break;

        default:
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
