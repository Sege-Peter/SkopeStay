<?php
header('Content-Type: application/json');
require_once '../includes/config.php'; // Also includes rbac.php and starts session

$method = $_SERVER['REQUEST_METHOD'];

// Simple per-session brute-force throttle: 5 failures => 5 minute lock
function login_locked_for() {
    $until = $_SESSION['login_lock_until'] ?? 0;
    return max(0, $until - time());
}
function register_login_failure() {
    $_SESSION['login_failures'] = ($_SESSION['login_failures'] ?? 0) + 1;
    if ($_SESSION['login_failures'] >= 5) {
        $_SESSION['login_lock_until'] = time() + 300;
        $_SESSION['login_failures'] = 0;
    }
}

if ($method === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    if (!$data) $data = $_POST;

    $action = $data['action'] ?? 'login';

    if ($action === 'signup') {
        if (get_setting($pdo, 'allow_public_signup', '0') !== '1') {
            echo json_encode(['success' => false, 'message' => 'Self-registration is disabled. Please contact the property administrator.']);
            exit;
        }

        $username = trim($data['username'] ?? '');
        $email = trim($data['email'] ?? '');
        $password = $data['password'] ?? '';
        $fullname = trim($data['full_name'] ?? '') ?: $username;

        if ($username === '' || $email === '' || $password === '') {
            echo json_encode(['success' => false, 'message' => 'Please fill all required fields.']);
            exit;
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo json_encode(['success' => false, 'message' => 'Please enter a valid email address.']);
            exit;
        }
        if (strlen($password) < 8) {
            echo json_encode(['success' => false, 'message' => 'Password must be at least 8 characters.']);
            exit;
        }

        // Check if exists
        $check = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $check->execute([$username, $email]);
        if ($check->fetch()) {
            echo json_encode(['success' => false, 'message' => 'Username or email already exists.']);
            exit;
        }

        // New accounts start without staff privileges until an admin assigns a role
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (username, full_name, password, email, role) VALUES (?, ?, ?, ?, 'Guest')");
        if ($stmt->execute([$username, $fullname, $hash, $email])) {
            $user_id = $pdo->lastInsertId();
            log_action($pdo, $user_id, 'signup', 'Auth', 'New user registered');
            echo json_encode(['success' => true, 'message' => 'Account created successfully! You can now log in.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to create account.']);
        }

    } elseif ($action === 'update_profile' || $action === 'change_password') {
        $uid = $_SESSION['user_id'] ?? null;
        if (!$uid) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Please sign in first.']);
            exit;
        }
        verify_csrf();

        $user = $pdo->prepare("SELECT * FROM users WHERE id = ?");
        $user->execute([$uid]);
        $user = $user->fetch();

        if ($action === 'update_profile') {
            $full_name = trim($data['full_name'] ?? '');
            $email = trim($data['email'] ?? '');
            $phone = trim($data['phone'] ?? '');
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                echo json_encode(['success' => false, 'message' => 'Please enter a valid email address.']);
                exit;
            }
            $pdo->prepare("UPDATE users SET full_name = ?, email = ?, phone = ? WHERE id = ?")
                ->execute([$full_name ?: null, $email, $phone ?: null, $uid]);
            log_action($pdo, $uid, 'update_profile', 'Auth', 'Updated own profile details');
            echo json_encode(['success' => true, 'message' => 'Profile updated.']);
        } else {
            $current = $data['current_password'] ?? '';
            $new = $data['new_password'] ?? '';
            if (!password_verify($current, $user['password'])) {
                echo json_encode(['success' => false, 'message' => 'Your current password is incorrect.']);
                exit;
            }
            if (strlen($new) < 8) {
                echo json_encode(['success' => false, 'message' => 'New password must be at least 8 characters.']);
                exit;
            }
            $pdo->prepare("UPDATE users SET password = ? WHERE id = ?")->execute([password_hash($new, PASSWORD_DEFAULT), $uid]);
            log_action($pdo, $uid, 'change_password', 'Auth', 'Changed own password');
            echo json_encode(['success' => true, 'message' => 'Password changed successfully.']);
        }

    } else {
        // Login Action
        $username = trim($data['username'] ?? '');
        $password = $data['password'] ?? '';

        if ($wait = login_locked_for()) {
            echo json_encode(['success' => false, 'message' => 'Too many failed attempts. Try again in ' . ceil($wait / 60) . ' minute(s).']);
            exit;
        }

        if ($username === '' || $password === '') {
            echo json_encode(['success' => false, 'message' => 'Username and password required.']);
            exit;
        }

        // Several legacy rows can share a username, so verify each candidate
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? OR email = ? ORDER BY id ASC");
        $stmt->execute([$username, $username]);
        $matched = null;
        foreach ($stmt->fetchAll() as $candidate) {
            if (password_verify($password, $candidate['password'])) { $matched = $candidate; break; }
        }

        if ($matched && isset($matched['is_active']) && (int)$matched['is_active'] !== 1) {
            echo json_encode(['success' => false, 'message' => 'This account has been deactivated. Contact your administrator.']);
            exit;
        }

        if ($matched) {
            session_regenerate_id(true);
            unset($_SESSION['login_failures'], $_SESSION['login_lock_until'], $_SESSION['permissions']);
            $_SESSION['user_id'] = $matched['id'];
            $_SESSION['username'] = $matched['username'];
            $_SESSION['last_activity'] = time();

            $role = get_user_primary_role($pdo, $matched['id']);
            $_SESSION['role'] = $role;

            // Cache permissions for O(1) checks
            load_user_permissions($pdo, $matched['id']);

            try {
                $pdo->prepare("UPDATE users SET last_login = NOW() WHERE id = ?")->execute([$matched['id']]);
            } catch (Exception $e) { /* pre-migration */ }

            log_action($pdo, $matched['id'], 'login', 'Auth', 'User logged in');

            echo json_encode([
                'success' => true,
                'message' => 'Login successful',
                'role' => $role
            ]);
        } else {
            register_login_failure();
            echo json_encode(['success' => false, 'message' => 'Invalid username or password.']);
        }
    }
} else if ($method === 'GET' && isset($_GET['action']) && $_GET['action'] === 'logout') {
    $user_id = $_SESSION['user_id'] ?? null;
    if ($user_id) {
        log_action($pdo, $user_id, 'logout', 'Auth', 'User logged out');
    }
    $_SESSION = [];
    session_destroy();
    header("Location: ../auth/login.php");
    exit;
} else {
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
}
