<?php
require_once __DIR__ . '/includes/config.php';

echo "Setting up RBAC Matrix...\n";

// Clear existing permissions (cascade deletes role_permissions)
$pdo->query("SET FOREIGN_KEY_CHECKS = 0;");
$pdo->query("TRUNCATE TABLE role_permissions;");
$pdo->query("TRUNCATE TABLE permissions;");
$pdo->query("SET FOREIGN_KEY_CHECKS = 1;");

$permissions = [
    ['name' => 'dashboard_access', 'module' => 'Dashboard', 'description' => 'Access the main dashboard'],
    ['name' => 'rooms_manage', 'module' => 'Rooms', 'description' => 'Full access to Rooms'],
    ['name' => 'rooms_view', 'module' => 'Rooms', 'description' => 'View Rooms only'],
    ['name' => 'halls_manage', 'module' => 'Halls', 'description' => 'Full access to Halls'],
    ['name' => 'halls_view', 'module' => 'Halls', 'description' => 'View Halls only'],
    ['name' => 'pool_manage', 'module' => 'Pool', 'description' => 'Full access to Pool'],
    ['name' => 'pool_view', 'module' => 'Pool', 'description' => 'View Pool only'],
    ['name' => 'restaurant_manage', 'module' => 'Restaurant', 'description' => 'Full access to Restaurant'],
    ['name' => 'restaurant_limited', 'module' => 'Restaurant', 'description' => 'Limited access to Restaurant'],
    ['name' => 'restaurant_order', 'module' => 'Restaurant', 'description' => 'Place orders in Restaurant'],
    ['name' => 'bookings_manage', 'module' => 'Bookings', 'description' => 'Full access to Bookings'],
    ['name' => 'bookings_own', 'module' => 'Bookings', 'description' => 'View own Bookings'],
    ['name' => 'payments_manage', 'module' => 'Payments', 'description' => 'Full access to Payments'],
    ['name' => 'payments_view', 'module' => 'Payments', 'description' => 'View Payments only'],
    ['name' => 'payments_own', 'module' => 'Payments', 'description' => 'View own Payments'],
    ['name' => 'reports_manage', 'module' => 'Reports', 'description' => 'Access Reports'],
    ['name' => 'users_manage', 'module' => 'System', 'description' => 'Manage Users'],
    ['name' => 'roles_manage', 'module' => 'System', 'description' => 'Manage Roles'],
    ['name' => 'settings_manage', 'module' => 'System', 'description' => 'Manage Settings']
];

$stmt = $pdo->prepare("INSERT INTO permissions (name, module, description) VALUES (?, ?, ?)");
foreach ($permissions as $p) {
    $stmt->execute([$p['name'], $p['module'], $p['description']]);
}

echo "Permissions inserted.\n";

$matrix = [
    'Super Admin' => [
        'dashboard_access', 'rooms_manage', 'halls_manage', 'pool_manage', 
        'restaurant_manage', 'bookings_manage', 'payments_manage', 
        'reports_manage', 'users_manage', 'roles_manage', 'settings_manage'
    ],
    'Hotel Manager' => [
        'dashboard_access', 'rooms_manage', 'halls_manage', 'pool_manage',
        'restaurant_manage', 'bookings_manage', 'payments_view', 'reports_manage'
    ],
    'Receptionist' => [
        'dashboard_access', 'rooms_manage', 'halls_manage', 'pool_manage',
        'restaurant_limited', 'bookings_manage', 'payments_manage'
    ],
    'Guest' => [
        'dashboard_access', 'rooms_view', 'halls_view', 'pool_view',
        'restaurant_order', 'bookings_own', 'payments_own'
    ]
];

$roleQuery = $pdo->prepare("SELECT id FROM roles WHERE name = ?");
$permQuery = $pdo->prepare("SELECT id FROM permissions WHERE name = ?");
$mapQuery = $pdo->prepare("INSERT INTO role_permissions (role_id, permission_id) VALUES (?, ?)");

foreach ($matrix as $roleName => $perms) {
    $roleQuery->execute([$roleName]);
    $role = $roleQuery->fetch();
    if (!$role) {
        echo "Role $roleName not found!\n";
        continue;
    }
    
    foreach ($perms as $permName) {
        $permQuery->execute([$permName]);
        $perm = $permQuery->fetch();
        if ($perm) {
            $mapQuery->execute([$role['id'], $perm['id']]);
        }
    }
}

echo "RBAC matrix mapped successfully.\n";
?>
