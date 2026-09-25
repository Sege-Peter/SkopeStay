<?php
header('Content-Type: application/json');
require_once '../includes/config.php';

$method = $_SERVER['REQUEST_METHOD'];

try {
    switch ($method) {
        case 'GET':
            $search   = $_GET['search']   ?? '';
            $category = $_GET['category'] ?? '';
            $status   = $_GET['status']   ?? '';

            $sql    = "SELECT * FROM inventory_items WHERE 1=1";
            $params = [];
            if ($search)   { $sql .= " AND name LIKE ?";     $params[] = "%$search%"; }
            if ($category) { $sql .= " AND category=?";      $params[] = $category; }
            if ($status)   { $sql .= " AND status=?";        $params[] = $status; }
            $sql .= " ORDER BY name ASC";

            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            echo json_encode(['success' => true, 'data' => $stmt->fetchAll()]);
            break;

        case 'POST':
            $data = json_decode(file_get_contents('php://input'), true);
            if (!$data) $data = $_POST;

            $required = ['name', 'sku', 'category', 'current_stock', 'min_level'];
            foreach ($required as $field) {
                if (!isset($data[$field]) || $data[$field] === '') {
                    echo json_encode(['success' => false, 'message' => "Field '$field' is required."]);
                    exit;
                }
            }

            // Duplicate SKU check
            $dup = $pdo->prepare("SELECT id FROM inventory_items WHERE sku=?");
            $dup->execute([$data['sku']]);
            if ($dup->fetch()) {
                echo json_encode(['success' => false, 'message' => "SKU '{$data['sku']}' already exists."]);
                exit;
            }

            $stock = (int)$data['current_stock'];
            $min   = (int)$data['min_level'];
            $status = $stock === 0 ? 'Out of Stock' : ($stock <= $min ? 'Low Stock' : 'In Stock');

            $stmt = $pdo->prepare("INSERT INTO inventory_items (name, sku, category, current_stock, min_level, status, supplier) VALUES (?,?,?,?,?,?,?)");
            $stmt->execute([$data['name'], $data['sku'], $data['category'], $stock, $min, $status, $data['supplier'] ?? null]);

            echo json_encode(['success' => true, 'message' => 'Item added successfully!', 'id' => $pdo->lastInsertId()]);
            break;

        case 'PUT':
            $data = json_decode(file_get_contents('php://input'), true);
            $id   = $data['id'] ?? null;
            if (!$id) { echo json_encode(['success' => false, 'message' => 'Item ID required.']); exit; }

            $stock  = (int)$data['current_stock'];
            $min    = (int)$data['min_level'];
            $status = $stock === 0 ? 'Out of Stock' : ($stock <= $min ? 'Low Stock' : 'In Stock');

            $pdo->prepare("UPDATE inventory_items SET name=?, category=?, current_stock=?, min_level=?, status=?, supplier=? WHERE id=?")
                ->execute([$data['name'], $data['category'], $stock, $min, $status, $data['supplier'] ?? null, $id]);

            echo json_encode(['success' => true, 'message' => 'Item updated successfully!']);
            break;

        case 'DELETE':
            $id = $_GET['id'] ?? null;
            if (!$id) { echo json_encode(['success' => false, 'message' => 'ID required.']); exit; }
            $pdo->prepare("DELETE FROM inventory_items WHERE id=?")->execute([$id]);
            echo json_encode(['success' => true, 'message' => 'Item deleted.']);
            break;

        default:
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
