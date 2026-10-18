<?php
/**
 * Central access guard, loaded by config.php for every request.
 * - modules/*  : must be signed in and hold the page's permission
 * - api/*      : must be signed in (except the public and auth endpoints)
 * - Enforces deactivated accounts and the configured idle timeout.
 */

$__dir    = basename(dirname($_SERVER['SCRIPT_FILENAME'] ?? ''));
$__script = basename($_SERVER['SCRIPT_FILENAME'] ?? '');
$__is_cli = PHP_SAPI === 'cli';

// Page => permission required (Super Admin bypasses all checks)
const MODULE_PERMISSIONS = [
    'dashboard.php'         => 'dashboard_access',
    'bookings.php'          => 'bookings_manage',
    'bookings_calendar.php' => 'reports_manage',
    'rooms.php'             => 'rooms_manage',
    'add_room.php'          => 'rooms_manage',
    'halls.php'             => 'halls_manage',
    'restaurant.php'        => ['restaurant_manage', 'restaurant_limited', 'restaurant_order'],
    'kitchen.php'           => 'restaurant_manage',
    'pool.php'              => 'pool_manage',
    'customers.php'         => 'bookings_manage',
    'inquiries.php'         => 'inquiries_manage',
    'hrms.php'              => 'users_manage',
    'inventory.php'         => 'reports_manage',
    'procurement.php'       => 'reports_manage',
    'maintenance.php'       => 'rooms_manage',
    'accounting.php'        => 'payments_manage',
    'finance.php'           => ['payments_manage', 'payments_view'],
    'print_receipt.php'     => ['payments_manage', 'payments_view', 'bookings_manage', 'restaurant_manage', 'restaurant_limited'],
    'user_management.php'   => 'users_manage',
    'audit_logs.php'        => 'users_manage',
    'settings.php'          => 'settings_manage',
    'profile.php'           => null, // any signed-in user
];

const API_PERMISSIONS = [
    'users.php'    => 'users_manage',
    'finance.php'  => ['payments_manage', 'payments_view'],
];

const PUBLIC_APIS = ['auth.php', 'public.php'];

// Restaurant terminals can unlock with a staff PIN instead of a full login
const POS_PIN_PAGES = ['restaurant.php'];
const POS_PIN_APIS  = ['restaurant.php', 'menu_items.php', 'kds.php'];

function user_can($pdo, $user_id, $perm) {
    if ($perm === null) return true;
    foreach ((array)$perm as $p) {
        if (has_permission($pdo, $user_id, $p)) return true;
    }
    return false;
}

function guard_deny_json($code, $msg) {
    http_response_code($code);
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => $msg]);
    exit;
}

function guard_access_denied_page() {
    http_response_code(403);
    $name = e(brand('hotel_name'));
    echo '<!DOCTYPE html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">'
       . '<title>Access restricted | ' . $name . '</title>' . brand_favicon_tags()
       . '<style>' . brand_css_vars() . 'body{margin:0;min-height:100vh;display:grid;place-items:center;font-family:system-ui,sans-serif;background:var(--theme-primary);color:#fff;padding:16px}'
       . '.c{max-width:420px;text-align:center;background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.12);border-radius:24px;padding:40px 28px}'
       . 'h1{font-size:22px;margin:16px 0 8px}p{color:rgba(255,255,255,.7);font-size:14px;line-height:1.6}'
       . 'a{display:inline-block;margin:6px;padding:10px 18px;border-radius:12px;text-decoration:none;font-weight:700;font-size:13px}'
       . '.p{background:var(--theme-secondary);color:var(--theme-on-secondary)}.s{border:1px solid rgba(255,255,255,.25);color:#fff}'
       . 'img{width:64px;height:64px;object-fit:contain;background:#fff;border-radius:16px;padding:6px}</style></head><body><div class="c">'
       . '<img src="' . e(brand_logo_url()) . '" alt="">'
       . '<h1>Access restricted</h1><p>Your account does not have permission to open this page. Ask a system administrator to update your role if you need access.</p>'
       . '<a class="p" href="dashboard.php">Go to dashboard</a><a class="s" href="profile.php">My profile</a><a class="s" href="../api/auth.php?action=logout">Sign out</a>'
       . '</div></body></html>';
    exit;
}

if (!$__is_cli && in_array($__dir, ['modules', 'api'], true)) {
    $is_public_api = $__dir === 'api' && in_array($__script, PUBLIC_APIS, true);

    // Idle timeout & deactivated accounts
    if (!empty($_SESSION['user_id'])) {
        $timeout = max(5, (int)get_setting($pdo, 'session_timeout', '120')) * 60;
        $last = $_SESSION['last_activity'] ?? time();
        $active = true;
        try {
            $st = $pdo->prepare("SELECT is_active FROM users WHERE id = ?");
            $st->execute([$_SESSION['user_id']]);
            $flag = $st->fetchColumn();
            $active = ($flag === false) ? false : (int)$flag === 1;
        } catch (Exception $e) { /* column missing before migration */ }

        if (time() - $last > $timeout || !$active) {
            $reason = $active ? 'timeout' : 'disabled';
            $_SESSION = [];
            session_destroy();
            session_start();
            if ($__dir === 'api' && !$is_public_api) {
                guard_deny_json(401, $reason === 'timeout' ? 'Your session expired. Please sign in again.' : 'This account has been deactivated.');
            }
            if ($__dir === 'modules') {
                header('Location: ../auth/login.php?reason=' . $reason);
                exit;
            }
        } else {
            $_SESSION['last_activity'] = time();
        }
    }

    $uid = $_SESSION['user_id'] ?? null;
    $pos_unlocked = !empty($_SESSION['pos_logged_in']);

    if ($__dir === 'modules') {
        if (!$uid && in_array($__script, POS_PIN_PAGES, true)) {
            // restaurant.php shows its own PIN screen when locked
        } elseif (!$uid) {
            $next = urlencode($_SERVER['REQUEST_URI'] ?? '');
            header("Location: ../auth/login.php?next=$next");
            exit;
        }
        elseif (array_key_exists($__script, MODULE_PERMISSIONS) && !user_can($pdo, $uid, MODULE_PERMISSIONS[$__script])) {
            guard_access_denied_page();
        }
    } elseif (!$is_public_api) {
        if (!$uid && $pos_unlocked && in_array($__script, POS_PIN_APIS, true)) {
            // PIN-unlocked POS terminal
        } elseif (!$uid) guard_deny_json(401, 'Unauthorized: please sign in.');
        elseif (isset(API_PERMISSIONS[$__script]) && !user_can($pdo, $uid, API_PERMISSIONS[$__script])) {
            guard_deny_json(403, 'Forbidden: your role does not allow this action.');
        }
    }
}
