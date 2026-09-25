<?php
header('Content-Type: application/json');
require_once '../includes/config.php';

$method = $_SERVER['REQUEST_METHOD'];

try {
    switch ($method) {
        case 'GET':
            $stmt = $pdo->query("SELECT u.id, u.username, u.email, u.created_at, COALESCE(r.name,'No Role') AS role
                FROM users u LEFT JOIN user_roles ur ON u.id = ur.user_id LEFT JOIN roles r ON ur.role_id = r.id
                ORDER BY u.created_at DESC");
            echo json_encode(['success' => true, 'data' => $stmt->fetchAll()]);
            break;

        case 'POST':
            $data = json_decode(file_get_contents('php://input'), true);
            if (empty($data['username']) || empty($data['email']) || empty($data['password'])) {
                echo json_encode(['success' => false, 'message' => 'Username, email, and password are required.']); exit;
            }

            // Check for duplicate
            $dup = $pdo->prepare("SELECT id FROM users WHERE username=? OR email=?");
            $dup->execute([$data['username'], $data['email']]);
            if ($dup->fetch()) {
                echo json_encode(['success' => false, 'message' => 'Username or email already exists.']); exit;
            }

            $hashed = password_hash($data['password'], PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, 'Staff')");
            $stmt->execute([$data['username'], $data['email'], $hashed]);
            $new_id = $pdo->lastInsertId();

            // Assign role if provided
            if (!empty($data['role_id'])) {
                $pdo->prepare("INSERT IGNORE INTO user_roles (user_id, role_id) VALUES (?, ?)")
                    ->execute([$new_id, $data['role_id']]);
            }

            $actor = $_SESSION['user_id'] ?? null;
            log_action($pdo, $actor, 'create_user', 'Users', "Created user: {$data['username']}", $new_id);

            echo json_encode(['success' => true, 'message' => "User '{$data['username']}' created successfully!", 'id' => $new_id]);
            break;

        case 'PUT':
            $data = json_decode(file_get_contents('php://input'), true);
            $user_id = $data['user_id'] ?? null;
            $role_id = $data['role_id'] ?? null;

            if (!$user_id || !$role_id) {
                echo json_encode(['success' => false, 'message' => 'User ID and Role ID required.']); exit;
            }

            // Replace existing role(s) for simplicity
            $pdo->prepare("DELETE FROM user_roles WHERE user_id=?")->execute([$user_id]);
            $pdo->prepare("INSERT INTO user_roles (user_id, role_id) VALUES (?, ?)")->execute([$user_id, $role_id]);

            $actor = $_SESSION['user_id'] ?? null;
            log_action($pdo, $actor, 'assign_role', 'Users', "Assigned role_id=$role_id to user_id=$user_id", $user_id);

            echo json_encode(['success' => true, 'message' => 'Role assigned successfully!']);
            break;

        case 'DELETE':
            $id = $_GET['id'] ?? null;
            if (!$id) { echo json_encode(['success' => false, 'message' => 'User ID required.']); exit; }

            $actor = $_SESSION['user_id'] ?? null;
            if ($id == $actor) {
                echo json_encode(['success' => false, 'message' => 'You cannot delete your own account.']); exit;
            }

            $pdo->prepare("DELETE FROM users WHERE id=?")->execute([$id]);
            log_action($pdo, $actor, 'delete_user', 'Users', "Deleted user_id=$id", $id);

            echo json_encode(['success' => true, 'message' => 'User deleted.']);
            break;

        default:
            echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
    }
} catch (Exception $e) {
    error_log($e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Server error occurred.']);
}
?>
