<?php
$current_page = basename($_SERVER['PHP_SELF']);

// Get current user info from session
$current_user_id = $_SESSION['user_id'] ?? null;
$current_username = $_SESSION['username'] ?? 'Staff';
$current_role = $current_user_id ? get_user_primary_role($pdo, $current_user_id) : 'Guest';
$is_super_admin = ($current_role === 'Super Admin');

$nav = [];

if (has_permission($pdo, $current_user_id, 'dashboard_access')) {
    $nav[] = ['href' => 'dashboard.php', 'icon' => 'dashboard', 'label' => 'Dashboard'];
}
if (has_permission($pdo, $current_user_id, 'bookings_manage') || has_permission($pdo, $current_user_id, 'bookings_own')) {
    $nav[] = ['href' => 'bookings.php', 'icon' => 'event', 'label' => 'Bookings'];
}
if (has_permission($pdo, $current_user_id, 'rooms_manage') || has_permission($pdo, $current_user_id, 'rooms_view')) {
    $nav[] = ['href' => 'rooms.php', 'icon' => 'bed', 'label' => 'Rooms'];
}
if (has_permission($pdo, $current_user_id, 'halls_manage') || has_permission($pdo, $current_user_id, 'halls_view')) {
    $nav[] = ['href' => 'halls.php', 'icon' => 'domain', 'label' => 'Halls & Venues'];
}
if (has_permission($pdo, $current_user_id, 'pool_manage') || has_permission($pdo, $current_user_id, 'pool_view')) {
    $nav[] = ['href' => 'pool.php', 'icon' => 'pool', 'label' => 'Swimming Pool'];
}
if (has_permission($pdo, $current_user_id, 'restaurant_manage') || has_permission($pdo, $current_user_id, 'restaurant_limited') || has_permission($pdo, $current_user_id, 'restaurant_order')) {
    $nav[] = ['href' => 'restaurant.php', 'icon' => 'restaurant', 'label' => 'Restaurant'];
    $nav[] = ['href' => 'kitchen.php', 'icon' => 'kitchen', 'label' => 'Kitchen (KDS)'];
}
if (has_permission($pdo, $current_user_id, 'bookings_manage') || has_permission($pdo, $current_user_id, 'users_manage')) {
    $nav[] = ['href' => 'customers.php', 'icon' => 'group', 'label' => 'Customers'];
}
if (has_permission($pdo, $current_user_id, 'payments_manage') || has_permission($pdo, $current_user_id, 'payments_view') || has_permission($pdo, $current_user_id, 'payments_own')) {
    $nav[] = ['href' => 'finance.php', 'icon' => 'payments', 'label' => 'Finance'];
}
if (has_permission($pdo, $current_user_id, 'reports_manage')) {
    $nav[] = ['href' => 'inventory.php', 'icon' => 'inventory_2', 'label' => 'Inventory'];
    $nav[] = ['href' => 'bookings_calendar.php', 'icon' => 'analytics', 'label' => 'Reports'];
}
if (has_permission($pdo, $current_user_id, 'settings_manage')) {
    $nav[] = ['href' => 'settings.php', 'icon' => 'settings', 'label' => 'Settings'];
}
if (has_permission($pdo, $current_user_id, 'users_manage')) {
    $nav[] = ['href' => 'user_management.php', 'icon' => 'admin_panel_settings', 'label' => 'User Management'];
    $nav[] = ['href' => 'audit_logs.php', 'icon' => 'manage_history', 'label' => 'Audit Logs'];
}
if ($is_super_admin) {
    $nav[] = [
        'href' => 'http://localhost/phpmyadmin/index.php?route=/database/structure&db=skopestay',
        'icon' => 'database',
        'label' => 'Database (phpMyAdmin)',
        'target' => '_blank'
    ];
}

$role_badge_colors = [
    'Super Admin'       => 'background:rgba(239,68,68,0.2); color:rgb(252,165,165);',
    'Hotel Manager'     => 'background:rgba(249,115,22,0.2); color:rgb(253,186,116);',
    'Receptionist'      => 'background:rgba(34,197,94,0.2); color:rgb(134,239,172);',
    'Chef'              => 'background:rgba(234,179,8,0.2); color:rgb(253,224,71);',
    'Waiter'            => 'background:rgba(59,130,246,0.2); color:rgb(147,197,253);',
    'Cashier'           => 'background:rgba(168,85,247,0.2); color:rgb(216,180,254);',
];
$role_style = $role_badge_colors[$current_role] ?? 'background:rgba(255,255,255,0.1); color:rgba(255,255,255,0.5);';
?>

<!-- Mobile Overlay -->
<div id="sidebar-overlay" onclick="closeSidebar()"
     class="fixed inset-0 bg-black/50 z-40 hidden md:hidden"></div>

<!-- Sidebar — explicit dark blue, never affected by theme token bugs -->
<aside id="sidebar"
       style="background: linear-gradient(180deg, #0f1e3d 0%, #162040 60%, #1a2a52 100%);"
       class="flex flex-col h-screen fixed left-0 top-0 w-64 z-50 shadow-2xl
              transition-transform duration-300 -translate-x-full md:translate-x-0">

    <!-- Logo -->
    <div class="flex items-center gap-3 px-5 py-5 border-b border-white/10 flex-shrink-0">
        <img src="../assets/images/SkopeStay logo.png" alt="SkopeStay Logo"
             class="flex-shrink-0"
             style="width:40px; height:40px; border-radius:50px; object-fit:cover; background:rgba(249,115,22,0.15);">
        <div>
            <p class="font-extrabold text-white text-base leading-none" style="font-family:'Montserrat',sans-serif;">SkopeStay</p>
            <p class="text-[10px] uppercase tracking-widest mt-0.5" style="color:rgba(255,255,255,0.45)">Management</p>
        </div>
    </div>

    <!-- Logged-in User Card -->
    <div class="mx-3 mt-3 mb-1 p-3 rounded-xl border border-white/10 flex items-center gap-3 flex-shrink-0" style="background:rgba(255,255,255,0.05)">
        <div class="w-9 h-9 rounded-full flex items-center justify-center flex-shrink-0 font-bold text-sm"
             style="background:rgba(249,115,22,0.3); color:rgb(253,186,116);">
            <?= strtoupper(substr($current_username, 0, 1)) ?>
        </div>
        <div class="min-w-0">
            <p class="text-white text-sm font-bold truncate"><?= htmlspecialchars($current_username) ?></p>
            <span class="text-[10px] font-bold px-2 py-0.5 rounded-full" style="<?= $role_style ?>">
                <?= htmlspecialchars($current_role) ?>
            </span>
        </div>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-0.5 scrollbar-hide">
        <?php foreach ($nav as $item):
            // Avoid marking external links as active
            $active = !str_starts_with($item['href'], 'http') && basename($current_page) === $item['href'];
        ?>
        <a href="<?= htmlspecialchars($item['href']) ?>"
           <?= isset($item['target']) ? 'target="' . htmlspecialchars($item['target']) . '"' : '' ?>
           class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-semibold transition-all duration-150 group"
           style="<?= $active
               ? 'background:rgba(249,115,22,0.15); border-left:3px solid rgb(249,115,22); color:rgb(255,237,213); padding-left:13px;'
               : 'color:rgba(255,255,255,0.6);' ?>">
            <span class="material-symbols-outlined text-[20px] flex-shrink-0 transition-transform group-hover:scale-110"
                  style="<?= $active ? 'color:rgb(249,115,22); font-variation-settings:\'FILL\' 1;' : '' ?>"><?= htmlspecialchars($item['icon']) ?></span>
            <span><?= htmlspecialchars($item['label']) ?></span>
        </a>
        <?php endforeach; ?>
    </nav>

    <!-- Bottom: Back to Website + Logout -->
    <div class="px-3 py-3 border-t border-white/10 flex-shrink-0 space-y-1">
        <a href="../index.php"
           class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-semibold transition-all hover:bg-white/10"
           style="color:rgba(255,255,255,0.55)">
            <span class="material-symbols-outlined text-[20px]">public</span>
            <span>Back to Website</span>
        </a>
        <a href="../api/auth.php?action=logout"
           class="flex items-center gap-3 px-4 py-2.5 rounded-xl text-sm font-semibold transition-all hover:bg-red-500/20"
           style="color:rgba(255,255,255,0.55)">
            <span class="material-symbols-outlined text-[20px]">logout</span>
            <span>Logout</span>
        </a>
    </div>

    <!-- Storage -->
    <div class="mx-3 mb-4 p-3 rounded-xl border border-white/10" style="background:rgba(255,255,255,0.05)">
        <p class="text-[10px] font-bold uppercase tracking-widest mb-2" style="color:rgba(255,255,255,0.35)">Storage Used</p>
        <div class="h-1.5 rounded-full overflow-hidden" style="background:rgba(255,255,255,0.1)">
            <div class="h-full rounded-full" style="width:72%; background:rgb(249,115,22)"></div>
        </div>
        <p class="text-[10px] mt-1.5" style="color:rgba(255,255,255,0.35)">7.2 GB / 10 GB</p>
    </div>
</aside>

<style>
/* Hover state for inactive nav links */
#sidebar nav a:not([style*="rgba(249,115,22"]):hover {
    background: rgba(255,255,255,0.08);
    color: rgba(255,255,255,0.9);
}
.scrollbar-hide::-webkit-scrollbar { display:none; }
.scrollbar-hide { -ms-overflow-style:none; scrollbar-width:none; }
</style>

<script>
function openSidebar() {
    document.getElementById('sidebar').style.transform = 'translateX(0)';
    document.getElementById('sidebar-overlay').classList.remove('hidden');
}
function closeSidebar() {
    document.getElementById('sidebar').style.transform = 'translateX(-100%)';
    document.getElementById('sidebar-overlay').classList.add('hidden');
}
// Hook hamburger if it exists
document.addEventListener('DOMContentLoaded', () => {
    const hamburger = document.getElementById('hamburger-btn');
    if (hamburger) hamburger.addEventListener('click', openSidebar);
});
</script>
