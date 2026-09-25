<?php
header('Content-Type: application/json');
require_once '../includes/config.php';

$method = $_SERVER['REQUEST_METHOD'];

try {
    switch ($method) {
        case 'GET':
            // Fetch active orders for KDS
            $stmt = $pdo->query("
                SELECT o.*, 
                       (SELECT room_number FROM rooms WHERE id = o.room_id) as room_number
                FROM restaurant_orders o
                WHERE o.kitchen_status IN ('New Order', 'Accepted', 'Preparing', 'Ready')
                  AND o.status NOT IN ('Completed', 'Paid', 'Cancelled')
                ORDER BY o.created_at ASC
            ");
            $orders = $stmt->fetchAll();

            // Fetch items for each order
            foreach ($orders as &$order) {
                $itemStmt = $pdo->prepare("
                    SELECT roi.*, ri.name 
                    FROM restaurant_order_items roi
                    LEFT JOIN restaurant_items ri ON roi.item_id = ri.id
                    WHERE roi.order_id = ?
                ");
                $itemStmt->execute([$order['id']]);
                $order['items'] = $itemStmt->fetchAll();
            }

            echo json_encode(['success' => true, 'data' => $orders]);
            break;

        case 'PUT':
            // Update kitchen status
            $body = json_decode(file_get_contents('php://input'), true);
            $id = (int)($body['id'] ?? 0);
            $kitchen_status = $body['kitchen_status'] ?? '';
            
            if (!$id || !$kitchen_status) {
                echo json_encode(['success' => false, 'message' => 'Missing ID or status']);
                exit;
            }

            // Sync main order status if necessary
            $main_status_update = "";
            $params = [$kitchen_status];
            if ($kitchen_status === 'Preparing') {
                $main_status_update = ", status = 'Preparing'";
            } else if ($kitchen_status === 'Ready') {
                $main_status_update = ", status = 'Ready'";
            }

            $params[] = $id;
            
            $stmt = $pdo->prepare("UPDATE restaurant_orders SET kitchen_status = ? $main_status_update WHERE id = ?");
            $stmt->execute($params);

            echo json_encode(['success' => true, 'message' => 'Status updated']);
            break;

        default:
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            break;
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
