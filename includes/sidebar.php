<?php
$current_page = basename($_SERVER['PHP_SELF']);

// Get current user info from session
$current_user_id = $_SESSION['user_id'] ?? null;
$current_username = $_SESSION['username'] ?? 'Admin';
$current_role = $current_user_id ? get_user_primary_role($pdo, $current_user_id) : 'Super Admin';
$is_super_admin = ($current_role === 'Super Admin' || $current_role === 'Manager');

// Define Organized ERP Navigation Sections
$erp_sections = [
    'Operations & PMS' => [
        ['href' => 'dashboard.php', 'icon' => 'dashboard', 'label' => 'Executive Dashboard', 'perm' => 'dashboard_access'],
        ['href' => 'bookings.php', 'icon' => 'event', 'label' => 'Reservations', 'perm' => 'bookings_manage'],
        ['href' => 'rooms.php', 'icon' => 'bed', 'label' => 'Suites & Rooms', 'perm' => 'rooms_manage'],
        ['href' => 'halls.php', 'icon' => 'domain', 'label' => 'Banquets & Venues', 'perm' => 'halls_manage'],
        ['href' => 'restaurant.php', 'icon' => 'restaurant', 'label' => 'Restaurant POS', 'perm' => 'restaurant_manage'],
        ['href' => 'kitchen.php', 'icon' => 'kitchen', 'label' => 'Kitchen KDS', 'perm' => 'restaurant_manage'],
        ['href' => 'pool.php', 'icon' => 'pool', 'label' => 'Pool & Leisure', 'perm' => 'pool_manage'],
        ['href' => 'customers.php', 'icon' => 'group', 'label' => 'Guest CRM', 'perm' => 'bookings_manage'],
    ],
    'Human Capital (HRMS)' => [
        ['href' => 'hrms.php', 'icon' => 'badge', 'label' => 'HRMS & Staff Master', 'perm' => 'users_manage'],
    ],
    'Supply Chain (SCM)' => [
        ['href' => 'inventory.php', 'icon' => 'inventory_2', 'label' => 'Stock & Inventory', 'perm' => 'reports_manage'],
        ['href' => 'procurement.php', 'icon' => 'local_shipping', 'label' => 'Procurement & POs', 'perm' => 'reports_manage'],
    ],
    'Facilities & CMMS' => [
        ['href' => 'maintenance.php', 'icon' => 'build', 'label' => 'Housekeeping & Maint.', 'perm' => 'rooms_manage'],
    ],
    'Finance & Multi-Ledger' => [
        ['href' => 'accounting.php', 'icon' => 'account_balance', 'label' => 'General Ledger & COA', 'perm' => 'payments_manage'],
        ['href' => 'finance.php', 'icon' => 'payments', 'label' => 'Cashflow & Folios', 'perm' => 'payments_manage'],
        ['href' => 'bookings_calendar.php', 'icon' => 'analytics', 'label' => 'BI & Analytics', 'perm' => 'reports_manage'],
    ],
    'System & Security' => [
        ['href' => 'user_management.php', 'icon' => 'admin_panel_settings', 'label' => 'Role RBAC Matrix', 'perm' => 'users_manage'],
        ['href' => 'audit_logs.php', 'icon' => 'manage_history', 'label' => 'Enterprise Audit Trail', 'perm' => 'users_manage'],
        ['href' => 'settings.php', 'icon' => 'settings', 'label' => 'System Settings', 'perm' => 'settings_manage'],
    ]
];

$role_badge_colors = [
    'Super Admin'       => 'background:rgba(239,68,68,0.2); color:rgb(252,165,165);',
    'Hotel Manager'     => 'background:rgba(249,115,22,0.2); color:rgb(253,186,116);',
    'Manager'           => 'background:rgba(249,115,22,0.2); color:rgb(253,186,116);',
    'Financial Controller' => 'background:rgba(168,85,247,0.2); color:rgb(216,180,254);',
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

<!-- Sidebar — SkopeStay Hospitality ERP -->
<aside id="sidebar"
       style="background: linear-gradient(180deg, #091224 0%, #0e1a38 60%, #14234b 100%);"
       class="flex flex-col h-screen fixed left-0 top-0 w-64 z-50 shadow-2xl
              transition-transform duration-300 -translate-x-full md:translate-x-0">

    <!-- Logo & Brand Header -->
    <div class="flex items-center gap-3 px-5 py-4 border-b border-white/10 flex-shrink-0">
        <div class="w-10 h-10 rounded-xl bg-gold/15 text-gold flex items-center justify-center border border-gold/30 shrink-0">
            <span class="material-symbols-outlined text-2xl">apartment</span>
        </div>
        <div>
            <p class="font-extrabold text-white text-base leading-none tracking-tight">Skope<span style="color:#D4AF37">Stay</span></p>
            <p class="text-[9px] uppercase tracking-[0.2em] mt-1 font-bold" style="color:rgba(255,255,255,0.45)">Hospitality ERP</p>
        </div>
    </div>

    <!-- Active User & Session Role -->
    <div class="mx-3 mt-3 mb-2 p-2.5 rounded-xl border border-white/10 flex items-center gap-2.5 flex-shrink-0" style="background:rgba(255,255,255,0.04)">
        <div class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0 font-bold text-xs"
             style="background:rgba(212,175,55,0.25); color:#F5E6AB;">
            <?= strtoupper(substr($current_username, 0, 1)) ?>
        </div>
        <div class="min-w-0 flex-1">
            <p class="text-white text-xs font-bold truncate leading-tight"><?= htmlspecialchars($current_username) ?></p>
            <span class="text-[9px] font-bold px-1.5 py-0.2 rounded-full inline-block mt-0.5" style="<?= $role_style ?>">
                <?= htmlspecialchars($current_role) ?>
            </span>
        </div>
    </div>

    <!-- Scrollable ERP Navigation Menu with Section Headers -->
    <nav class="flex-1 overflow-y-auto px-3 py-2 space-y-4 scrollbar-hide">
        <?php foreach ($erp_sections as $sec_title => $items): 
            $visible_items = [];
            foreach ($items as $it) {
                if ($is_super_admin || has_permission($pdo, $current_user_id, $it['perm'])) {
                    $visible_items[] = $it;
                }
            }
            if (empty($visible_items)) continue;
        ?>
        <div>
            <p class="text-[10px] font-bold uppercase tracking-[0.15em] px-3 mb-1" style="color:rgba(212,175,55,0.7)"><?= htmlspecialchars($sec_title) ?></p>
            <div class="space-y-0.5">
                <?php foreach ($visible_items as $item):
                    $active = ($current_page === $item['href']);
                ?>
                <a href="<?= htmlspecialchars($item['href']) ?>"
                   class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-semibold transition-all duration-150 group"
                   style="<?= $active
                       ? 'background:rgba(212,175,55,0.18); border-left:3px solid #D4AF37; color:#F5E6AB; padding-left:11px;'
                       : 'color:rgba(255,255,255,0.65);' ?>">
                    <span class="material-symbols-outlined text-[18px] flex-shrink-0 transition-transform group-hover:scale-110"
                          style="<?= $active ? 'color:#D4AF37;' : '' ?>"><?= htmlspecialchars($item['icon']) ?></span>
                    <span class="truncate"><?= htmlspecialchars($item['label']) ?></span>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endforeach; ?>

        <?php if ($is_super_admin): ?>
        <div class="pt-2 border-t border-white/10">
            <a href="http://localhost/phpmyadmin/index.php?route=/database/structure&db=skopestay" target="_blank"
               class="flex items-center gap-2.5 px-3 py-1.5 rounded-xl text-[11px] font-medium text-gray-400 hover:text-white transition-colors">
                <span class="material-symbols-outlined text-[16px]">database</span>
                <span>Database Administration</span>
            </a>
        </div>
        <?php endif; ?>
    </nav>

    <!-- Bottom Actions: Back to Website & Logout -->
    <div class="px-3 py-2.5 border-t border-white/10 flex-shrink-0 space-y-1 bg-black/20">
        <a href="../index.php"
           class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-semibold transition-all hover:bg-white/10"
           style="color:rgba(255,255,255,0.7)">
            <span class="material-symbols-outlined text-[18px]">public</span>
            <span>Guest Facing Resort Site</span>
        </a>
        <a href="../api/auth.php?action=logout"
           class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-semibold transition-all hover:bg-red-500/20 text-red-300">
            <span class="material-symbols-outlined text-[18px]">logout</span>
            <span>Logout Session</span>
        </a>
    </div>
</aside>

<style>
#sidebar nav a:not([style*="#D4AF37"]):hover {
    background: rgba(255,255,255,0.08);
    color: rgba(255,255,255,0.95);
}
.scrollbar-hide::-webkit-scrollbar { display:none; }
.scrollbar-hide { -ms-overflow-style:none; scrollbar-width:none; }
</style>

<script>
function openSidebar() {
    const s = document.getElementById('sidebar');
    const o = document.getElementById('sidebar-overlay');
    if (s) s.style.transform = 'translateX(0)';
    if (o) o.classList.remove('hidden');
}
function closeSidebar() {
    const s = document.getElementById('sidebar');
    const o = document.getElementById('sidebar-overlay');
    if (s) s.style.transform = 'translateX(-100%)';
    if (o) o.classList.add('hidden');
}
document.addEventListener('DOMContentLoaded', () => {
    const hamburger = document.getElementById('hamburger-btn');
    if (hamburger) hamburger.addEventListener('click', openSidebar);
});
</script>
