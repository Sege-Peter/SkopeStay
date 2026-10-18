<?php
/**
 * Live header data for the admin shell:
 *   GET ?action=feed          -> operational alerts + unread inquiry previews
 *   GET ?action=search&q=...  -> cross-module quick search
 */
header('Content-Type: application/json');
require_once __DIR__ . '/../includes/config.php';

$uid = $_SESSION['user_id'];
$action = $_GET['action'] ?? 'feed';

function safe_rows($pdo, $sql, $params = []) {
    try {
        $st = $pdo->prepare($sql);
        $st->execute($params);
        return $st->fetchAll();
    } catch (Exception $e) {
        return [];
    }
}

if ($action === 'feed') {
    $alerts = [];

    if (has_permission($pdo, $uid, 'bookings_manage')) {
        foreach (safe_rows($pdo, "SELECT id, guest_name, booking_type, check_in FROM bookings WHERE status = 'Pending' ORDER BY created_at DESC LIMIT 5") as $b) {
            $alerts[] = ['type' => 'booking', 'icon' => 'event_available', 'tone' => 'blue',
                'title' => 'Pending ' . ($b['booking_type'] ?: 'Room') . ' booking',
                'text' => $b['guest_name'] . ' · ' . date('M j', strtotime($b['check_in'])),
                'href' => 'bookings.php'];
        }
        foreach (safe_rows($pdo, "SELECT COUNT(*) n FROM bookings WHERE DATE(check_in) = CURDATE() AND status IN ('Active','Pending')") as $r) {
            if ($r['n'] > 0) $alerts[] = ['type' => 'arrivals', 'icon' => 'flight_land', 'tone' => 'green',
                'title' => $r['n'] . ' arrival(s) today', 'text' => 'Guests due to check in today', 'href' => 'bookings.php'];
        }
    }

    if (has_permission($pdo, $uid, 'reports_manage')) {
        foreach (safe_rows($pdo, "SELECT name, current_stock, min_level FROM inventory_items WHERE current_stock <= min_level ORDER BY current_stock ASC LIMIT 5") as $i) {
            $alerts[] = ['type' => 'stock', 'icon' => 'inventory_2', 'tone' => 'red',
                'title' => 'Low stock: ' . $i['name'],
                'text' => $i['current_stock'] . ' left (minimum ' . $i['min_level'] . ')',
                'href' => 'inventory.php'];
        }
    }

    if (has_permission($pdo, $uid, 'rooms_manage')) {
        foreach (safe_rows($pdo, "SELECT ticket_number, title, priority FROM maintenance_orders WHERE status = 'Open' AND priority IN ('High','Critical') ORDER BY created_at DESC LIMIT 5") as $m) {
            $alerts[] = ['type' => 'maintenance', 'icon' => 'build', 'tone' => 'amber',
                'title' => $m['priority'] . ' work order ' . $m['ticket_number'],
                'text' => $m['title'], 'href' => 'maintenance.php'];
        }
    }

    $messages = [];
    $unread = 0;
    if (has_permission($pdo, $uid, 'inquiries_manage')) {
        $messages = safe_rows($pdo, "SELECT id, full_name, inquiry_type, LEFT(message, 90) AS preview, created_at FROM inquiries WHERE status = 'Unread' ORDER BY created_at DESC LIMIT 5");
        $unread = (int)(safe_rows($pdo, "SELECT COUNT(*) n FROM inquiries WHERE status = 'Unread'")[0]['n'] ?? 0);
    }

    echo json_encode(['success' => true, 'alerts' => $alerts, 'messages' => $messages, 'unread' => $unread]);
    exit;
}

if ($action === 'search') {
    $q = trim($_GET['q'] ?? '');
    if (mb_strlen($q) < 2) {
        echo json_encode(['success' => true, 'results' => []]);
        exit;
    }
    $like = '%' . $q . '%';
    $results = [];

    if (has_permission($pdo, $uid, 'bookings_manage')) {
        foreach (safe_rows($pdo, "SELECT id, guest_name, booking_type, status FROM bookings WHERE guest_name LIKE ? OR guest_email LIKE ? OR guest_phone LIKE ? OR CONCAT('BK-', LPAD(id, 5, '0')) LIKE ? ORDER BY id DESC LIMIT 6", [$like, $like, $like, $like]) as $r) {
            $results[] = ['group' => 'Bookings', 'icon' => 'event', 'title' => $r['guest_name'], 'meta' => '#BK-' . str_pad($r['id'], 5, '0', STR_PAD_LEFT) . ' · ' . $r['booking_type'] . ' · ' . $r['status'], 'href' => 'bookings.php'];
        }
        foreach (safe_rows($pdo, "SELECT id, full_name, phone, email FROM customers WHERE full_name LIKE ? OR phone LIKE ? OR email LIKE ? LIMIT 6", [$like, $like, $like]) as $r) {
            $results[] = ['group' => 'Guests', 'icon' => 'person', 'title' => $r['full_name'], 'meta' => trim($r['phone'] . ' ' . $r['email']), 'href' => 'customers.php'];
        }
    }
    if (has_permission($pdo, $uid, 'rooms_manage')) {
        foreach (safe_rows($pdo, "SELECT id, room_number, type, status FROM rooms WHERE room_number LIKE ? OR type LIKE ? OR room_name LIKE ? LIMIT 6", [$like, $like, $like]) as $r) {
            $results[] = ['group' => 'Rooms', 'icon' => 'bed', 'title' => 'Room ' . $r['room_number'], 'meta' => $r['type'] . ' · ' . $r['status'], 'href' => 'rooms.php'];
        }
    }
    if (has_permission($pdo, $uid, 'reports_manage')) {
        foreach (safe_rows($pdo, "SELECT name, sku, current_stock FROM inventory_items WHERE name LIKE ? OR sku LIKE ? LIMIT 5", [$like, $like]) as $r) {
            $results[] = ['group' => 'Inventory', 'icon' => 'inventory_2', 'title' => $r['name'], 'meta' => ($r['sku'] ? $r['sku'] . ' · ' : '') . $r['current_stock'] . ' in stock', 'href' => 'inventory.php'];
        }
    }
    if (has_permission($pdo, $uid, 'users_manage')) {
        foreach (safe_rows($pdo, "SELECT first_name, last_name, designation, employee_code FROM employees WHERE CONCAT(first_name, ' ', last_name) LIKE ? OR employee_code LIKE ? LIMIT 5", [$like, $like]) as $r) {
            $results[] = ['group' => 'Staff', 'icon' => 'badge', 'title' => $r['first_name'] . ' ' . $r['last_name'], 'meta' => $r['employee_code'] . ' · ' . $r['designation'], 'href' => 'hrms.php'];
        }
    }
    if (has_permission($pdo, $uid, 'inquiries_manage')) {
        foreach (safe_rows($pdo, "SELECT id, full_name, inquiry_type FROM inquiries WHERE full_name LIKE ? OR email LIKE ? OR message LIKE ? ORDER BY id DESC LIMIT 5", [$like, $like, $like]) as $r) {
            $results[] = ['group' => 'Inquiries', 'icon' => 'forum', 'title' => $r['full_name'], 'meta' => $r['inquiry_type'], 'href' => 'inquiries.php?open=' . $r['id']];
        }
    }

    // Jump straight to admin pages by name
    $pages = ['Dashboard' => 'dashboard.php', 'Reservations' => 'bookings.php', 'Rooms' => 'rooms.php', 'Settings' => 'settings.php',
              'Branding & Logo' => 'settings.php#branding', 'Theme Colours' => 'settings.php#theme', 'Restaurant POS' => 'restaurant.php',
              'Inventory' => 'inventory.php', 'Accounting' => 'accounting.php', 'HRMS' => 'hrms.php', 'Inquiries' => 'inquiries.php',
              'Maintenance' => 'maintenance.php', 'Audit Trail' => 'audit_logs.php', 'My Profile' => 'profile.php'];
    foreach ($pages as $label => $href) {
        if (stripos($label, $q) !== false) {
            $results[] = ['group' => 'Pages', 'icon' => 'arrow_outward', 'title' => $label, 'meta' => 'Open page', 'href' => $href];
        }
    }

    echo json_encode(['success' => true, 'results' => $results]);
    exit;
}

echo json_encode(['success' => false, 'message' => 'Unknown action.']);
