<?php
header('Content-Type: application/json');
require_once '../includes/config.php';

$method = $_SERVER['REQUEST_METHOD'];

try {
    switch ($method) {
        case 'GET':
            $passes = $pdo->query("
                SELECT p.*, c.full_name as customer_name 
                FROM pool_passes p 
                LEFT JOIN customers c ON p.customer_id = c.id 
                ORDER BY p.visit_date DESC, p.created_at DESC
            ")->fetchAll();
            echo json_encode(['success' => true, 'data' => $passes]);
            break;

        case 'POST':
            $data = json_decode(file_get_contents('php://input'), true);
            $stmt = $pdo->prepare("INSERT INTO pool_passes (customer_id, guest_name, phone, pass_type, ticket_type, visit_date, entry_time, number_of_guests, amount, status) VALUES (?,?,?,?,?,?,?,?,?,?)");
            $stmt->execute([
                $data['customer_id'] ?? null,
                $data['guest_name'] ?? null,
                $data['phone'] ?? null,
                $data['pass_type'],
                $data['ticket_type'] ?? 'Walk-in',
                $data['visit_date'],
                $data['entry_time'] ?? date('H:i:s'),
                $data['number_of_guests'] ?? 1,
                $data['amount'] ?? 0,
                $data['status'] ?? 'Active'
            ]);
            $pass_id = $pdo->lastInsertId();
            echo json_encode(['success' => true, 'message' => 'Pool pass issued successfully!', 'pass_id' => $pass_id]);
            break;

        case 'PUT':
            $data = json_decode(file_get_contents('php://input'), true);
            $pdo->prepare("UPDATE pool_passes SET status=? WHERE id=?")->execute([$data['status'], $data['id']]);
            echo json_encode(['success' => true, 'message' => 'Pass status updated.']);
            break;

        case 'DELETE':
            $id = $_GET['id'] ?? null;
            if ($id) {
                $pdo->prepare("DELETE FROM pool_passes WHERE id=?")->execute([$id]);
                echo json_encode(['success' => true, 'message' => 'Pass deleted.']);
            }
            break;

        default:
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
