<?php
header('Content-Type: application/json');
require_once '../includes/config.php';

$method = $_SERVER['REQUEST_METHOD'];

try {
    switch ($method) {

        case 'GET':
            $orders = $pdo->query("
                SELECT o.*, COUNT(oi.id) as item_count
                FROM restaurant_orders o
                LEFT JOIN restaurant_order_items oi ON o.id = oi.order_id
                GROUP BY o.id
                ORDER BY o.created_at DESC
                LIMIT 50
            ")->fetchAll();
            echo json_encode(['success' => true, 'data' => $orders]);
            break;

        case 'POST':
            $data = json_decode(file_get_contents('php://input'), true);
            if (empty($data['items']) || !is_array($data['items'])) {
                echo json_encode(['success' => false, 'message' => 'No items in order.']);
                exit;
            }

            $table  = $data['table_number'] ?? 'Walk-in';
            $guest  = $data['guest_name']   ?? 'Walk-in Guest';
            $type   = $data['order_type']   ?? 'Dine-In';
            $roomId = !empty($data['room_id']) ? (int)$data['room_id'] : null;
            $total  = (float)($data['total'] ?? 0);
            $status = 'Pending';
            $k_status = 'New Order';

            // Insert order
            $stmt = $pdo->prepare("INSERT INTO restaurant_orders (table_number, guest_name, order_type, room_id, total_amount, status, kitchen_status) VALUES (?,?,?,?,?,?,?)");
            $stmt->execute([$table, $guest, $type, $roomId, $total, $status, $k_status]);
            $order_id = $pdo->lastInsertId();

            // Insert order items
            $item_stmt = $pdo->prepare("INSERT INTO restaurant_order_items (order_id, item_id, price_at_time, quantity) VALUES (?,?,?,?)");
            $find_item = $pdo->prepare("SELECT id FROM restaurant_items WHERE name = ? LIMIT 1");
            foreach ($data['items'] as $item) {
                $find_item->execute([$item['name']]);
                $item_id = $find_item->fetchColumn();
                if ($item_id) {
                    $item_stmt->execute([$order_id, $item_id, $item['price'], $item['qty']]);
                }
            }

            echo json_encode([
                'success'  => true,
                'message'  => 'Order #' . $order_id . ' placed successfully!',
                'order_id' => $order_id
            ]);
            break;

        case 'PUT':
            $body = json_decode(file_get_contents('php://input'), true);
            $id     = (int)($body['id']     ?? 0);
            $status = $body['status'] ?? '';
            $payMethod = $body['pay_method'] ?? 'Cash';

            if (!$id || !$status) {
                echo json_encode(['success' => false, 'message' => 'ID and status required.']);
                exit;
            }
            
            // If checking out to room
            if ($status === 'ChargeToRoom') {
                $pdo->prepare("UPDATE restaurant_orders SET status='Completed' WHERE id=?")->execute([$id]);
                // In a real system, insert into room_charges folio here
                echo json_encode(['success' => true, 'message' => 'Charged to room.']);
                exit;
            }
            
            // If paying
            if ($status === 'Paid') {
                $pdo->prepare("UPDATE restaurant_orders SET status='Paid' WHERE id=?")->execute([$id]);
                echo json_encode(['success' => true, 'message' => 'Payment received.']);
                exit;
            }

            $pdo->prepare("UPDATE restaurant_orders SET status=? WHERE id=?")->execute([$status, $id]);
            echo json_encode(['success' => true, 'message' => 'Order status updated.']);
            break;

        case 'DELETE':
            $id = (int)($_GET['id'] ?? 0);
            if (!$id) { echo json_encode(['success' => false, 'message' => 'ID required.']); exit; }
            $pdo->prepare("DELETE FROM restaurant_order_items WHERE order_id=?")->execute([$id]);
            $pdo->prepare("DELETE FROM restaurant_orders WHERE id=?")->execute([$id]);
            echo json_encode(['success' => true, 'message' => 'Order deleted.']);
            break;

        default:
            echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
    }

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
