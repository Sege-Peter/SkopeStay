<?php
header('Content-Type: application/json');
require_once '../includes/config.php'; // Also includes rbac.php and starts session

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    if (!$data) $data = $_POST;

    $action = $data['action'] ?? 'login';

    if ($action === 'signup') {
        $username = $data['username'] ?? '';
        $email = $data['email'] ?? '';
        $password = $data['password'] ?? '';
        $fullname = $data['full_name'] ?? $username;

        if (empty($username) || empty($email) || empty($password)) {
            echo json_encode(['success' => false, 'message' => 'Please fill all required fields.']);
            exit;
        }

        // Check if exists
        $check = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $check->execute([$username, $email]);
        if ($check->fetch()) {
            echo json_encode(['success' => false, 'message' => 'Username or email already exists.']);
            exit;
        }

        // Insert user
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (username, full_name, password, email) VALUES (?, ?, ?, ?)");
        if ($stmt->execute([$username, $fullname, $hash, $email])) {
            $user_id = $pdo->lastInsertId();
            
            // Assign Guest Role
            $role = $pdo->query("SELECT id FROM roles WHERE name = 'Guest' LIMIT 1")->fetch();
            if ($role) {
                $pdo->prepare("INSERT INTO user_roles (user_id, role_id) VALUES (?, ?)")->execute([$user_id, $role['id']]);
            }

            // Log action
            log_action($pdo, $user_id, 'signup', 'Auth', 'New user registered');

            echo json_encode(['success' => true, 'message' => 'Account created successfully! You can now log in.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to create account.']);
        }
    } else {
        // Login Action
        $username = $data['username'] ?? '';
        $password = $data['password'] ?? '';

        if (empty($username) || empty($password)) {
            echo json_encode(['success' => false, 'message' => 'Username and password required.']);
            exit;
        }

        // Authenticate against users table
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            // Success
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            
            $role = get_user_primary_role($pdo, $user['id']);
            $_SESSION['role'] = $role;
            
            // Cache permissions for O(1) checks
            load_user_permissions($pdo, $user['id']);
            
            // Log action
            log_action($pdo, $user['id'], 'login', 'Auth', 'User logged in');

            echo json_encode([
                'success' => true, 
                'message' => 'Login successful', 
                'role' => $role
            ]);
        } else {
            // Failed
            echo json_encode(['success' => false, 'message' => 'Invalid username or password.']);
        }
    }
} else if ($method === 'GET' && isset($_GET['action']) && $_GET['action'] === 'logout') {
    $user_id = $_SESSION['user_id'] ?? null;
    if ($user_id) {
        log_action($pdo, $user_id, 'logout', 'Auth', 'User logged out');
    }
    session_destroy();
    header("Location: ../auth/login.php");
    exit;
} else {
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
}
?>
