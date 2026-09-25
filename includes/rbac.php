<?php
/**
 * SkopeStay RBAC (Role-Based Access Control) Helper
 */

function load_user_permissions($pdo, $user_id) {
    if (!$user_id) return [];

    $stmt = $pdo->prepare("
        SELECT p.name FROM user_roles ur
        JOIN role_permissions rp ON ur.role_id = rp.role_id
        JOIN permissions p ON rp.permission_id = p.id
        WHERE ur.user_id = ?
    ");
    $stmt->execute([$user_id]);
    $permissions = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    $_SESSION['permissions'] = $permissions;
    return $permissions;
}

// Helper to check if a user has a specific permission
function has_permission($pdo, $user_id, $permission_name) {
    if (!$user_id) return false;

    // Fast O(1) check if permissions are cached
    if (isset($_SESSION['permissions'])) {
        $role = $_SESSION['role'] ?? get_user_primary_role($pdo, $user_id);
        if ($role === 'Super Admin') return true;
        return in_array($permission_name, $_SESSION['permissions']);
    }

    // Check if user is Super Admin (bypass all checks)
    $role = get_user_primary_role($pdo, $user_id);
    if ($role === 'Super Admin') return true;

    // Load and cache if not present
    $permissions = load_user_permissions($pdo, $user_id);
    return in_array($permission_name, $permissions);
}

// Helper to get user's primary role name
function get_user_primary_role($pdo, $user_id) {
    if (!$user_id) return 'Guest';
    $stmt = $pdo->prepare("
        SELECT r.name FROM user_roles ur
        JOIN roles r ON ur.role_id = r.id
        WHERE ur.user_id = ?
        LIMIT 1
    ");
    $stmt->execute([$user_id]);
    $role = $stmt->fetchColumn();
    return $role ?: 'Guest';
}

// Helper to log an audit action
function log_action($pdo, $user_id, $action, $module, $details = null, $record_id = null) {
    $ip = $_SERVER['REMOTE_ADDR'] ?? null;
    $stmt = $pdo->prepare("
        INSERT INTO audit_logs (user_id, action, module, record_id, details, ip_address)
        VALUES (?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([$user_id, $action, $module, $record_id, $details, $ip]);
}

// Function to enforce access on a page
function require_permission($pdo, $permission_name) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    $user_id = $_SESSION['user_id'] ?? null;
    
    if (!$user_id) {
        header("Location: ../auth/login.php");
        exit;
    }

    if (!has_permission($pdo, $user_id, $permission_name)) {
        // Redirect to a 403 or dashboard
        header("Location: ../modules/dashboard.php?error=access_denied");
        exit;
    }
}
?>
