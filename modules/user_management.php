<?php
$page_title = "User Management | SkopeStay";
require_once '../includes/config.php';

// Only Super Admin and Hotel Manager can access
$uid = $_SESSION['user_id'] ?? null;
if (!$uid || !has_permission($pdo, $uid, 'manage_users')) {
    header("Location: dashboard.php?error=access_denied"); exit;
}

// Load all staff users with their primary role
$users = $pdo->query("
    SELECT u.id, u.username, u.email, u.created_at,
           COALESCE(r.name, 'No Role') AS role,
           COALESCE(r.type, 'Staff') AS role_type
    FROM users u
    LEFT JOIN user_roles ur ON u.id = ur.user_id
    LEFT JOIN roles r ON ur.role_id = r.id
    ORDER BY u.created_at DESC
")->fetchAll();

// Load all roles for the assign dropdown
$all_roles = $pdo->query("SELECT * FROM roles ORDER BY type, name")->fetchAll();

include '../includes/head.php';
?>
<!DOCTYPE html>
<html class="light" lang="en">
<body class="bg-background text-on-surface font-body-md min-h-screen">
<?php include '../includes/sidebar.php'; ?>

<div class="md:ml-64 flex flex-col min-h-screen">
<?php include '../includes/header.php'; ?>

<main class="p-4 md:p-8 flex-grow space-y-6">

    <!-- Page Header -->
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <nav class="flex items-center gap-2 text-xs font-semibold text-on-surface-variant mb-2">
                <span>System</span>
                <span class="material-symbols-outlined text-[14px]">chevron_right</span>
                <span class="text-primary font-bold">User Management</span>
            </nav>
            <h1 class="font-bold text-2xl md:text-3xl text-on-surface flex items-center gap-2">
                <span class="material-symbols-outlined text-primary text-3xl">admin_panel_settings</span>
                User Management
            </h1>
            <p class="text-sm text-on-surface-variant mt-1">Manage staff accounts and role assignments.</p>
        </div>
        <button onclick="openModal('add-user-modal')"
                class="flex items-center gap-2 bg-primary text-white px-5 py-2.5 rounded-xl font-bold text-sm shadow-md hover:bg-primary/90 active:scale-95 transition-all">
            <span class="material-symbols-outlined text-[18px]">person_add</span> Add User
        </button>
    </div>

    <!-- Role Summary Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
        <?php
        $staff_count  = count(array_filter($users, fn($u) => $u['role_type'] === 'Staff'));
        $client_count = count(array_filter($users, fn($u) => $u['role_type'] === 'Client'));
        $no_role      = count(array_filter($users, fn($u) => $u['role'] === 'No Role'));
        $cards = [
            ['Total Users',   count($users),  'group',   'bg-primary/10 text-primary'],
            ['Staff Members', $staff_count,   'badge',   'bg-blue-100 text-blue-700'],
            ['Clients',       $client_count,  'person',  'bg-green-100 text-green-700'],
            ['No Role',       $no_role,       'warning', 'bg-amber-100 text-amber-700'],
        ];
        foreach ($cards as [$label, $val, $icon, $color]): ?>
        <div class="bg-white rounded-2xl border border-outline-variant/30 shadow-sm p-4 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0 <?= explode(' ', $color)[0] ?>">
                <span class="material-symbols-outlined <?= explode(' ', $color)[1] ?>"><?= $icon ?></span>
            </div>
            <div>
                <p class="text-2xl font-extrabold <?= explode(' ', $color)[1] ?>"><?= $val ?></p>
                <p class="text-xs text-on-surface-variant font-semibold"><?= $label ?></p>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Users Table -->
    <div class="bg-white rounded-2xl border border-outline-variant/30 shadow-sm overflow-hidden">
        <div class="p-5 border-b border-outline-variant/20 flex flex-wrap items-center justify-between gap-3">
            <h2 class="font-bold text-on-surface">All Users</h2>
            <div class="relative">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline text-[18px]">search</span>
                <input type="text" id="user-search" placeholder="Search users..." oninput="searchUsers(this.value)"
                       class="pl-9 pr-4 py-2 rounded-xl border border-outline-variant text-sm bg-surface-container-low focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all">
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full" id="users-table">
                <thead>
                    <tr class="bg-surface-container/50 border-b border-outline-variant/20">
                        <th class="text-left px-6 py-3 text-xs font-bold text-on-surface-variant uppercase tracking-wide">User</th>
                        <th class="text-left px-6 py-3 text-xs font-bold text-on-surface-variant uppercase tracking-wide">Role</th>
                        <th class="text-left px-6 py-3 text-xs font-bold text-on-surface-variant uppercase tracking-wide">Type</th>
                        <th class="text-left px-6 py-3 text-xs font-bold text-on-surface-variant uppercase tracking-wide">Joined</th>
                        <th class="text-center px-6 py-3 text-xs font-bold text-on-surface-variant uppercase tracking-wide">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant/10">
                    <?php foreach ($users as $u): 
                        $role_colors = [
                            'Super Admin'    => 'bg-red-100 text-red-700',
                            'Hotel Manager'  => 'bg-orange-100 text-orange-700',
                            'Receptionist'   => 'bg-green-100 text-green-700',
                            'Chef'           => 'bg-yellow-100 text-yellow-700',
                            'Waiter'         => 'bg-blue-100 text-blue-700',
                            'Cashier'        => 'bg-purple-100 text-purple-700',
                            'Accountant'     => 'bg-teal-100 text-teal-700',
                        ];
                        $role_cls = $role_colors[$u['role']] ?? 'bg-gray-100 text-gray-600';
                    ?>
                    <tr class="hover:bg-surface-container-low/50 transition-colors user-row" data-search="<?= strtolower($u['username'].' '.$u['email'].' '.$u['role']) ?>">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-full bg-primary/10 flex items-center justify-center font-bold text-primary text-sm flex-shrink-0">
                                    <?= strtoupper(substr($u['username'],0,1)) ?>
                                </div>
                                <div>
                                    <p class="font-bold text-on-surface text-sm"><?= htmlspecialchars($u['username']) ?></p>
                                    <p class="text-xs text-on-surface-variant"><?= htmlspecialchars($u['email']) ?></p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2.5 py-1 rounded-full text-xs font-bold <?= $role_cls ?>">
                                <?= htmlspecialchars($u['role']) ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-on-surface-variant font-medium"><?= $u['role_type'] ?></td>
                        <td class="px-6 py-4 text-sm text-on-surface-variant"><?= date('d M Y', strtotime($u['created_at'])) ?></td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-center gap-2">
                                <button onclick="openAssignRole(<?= $u['id'] ?>, '<?= htmlspecialchars($u['username']) ?>')"
                                        class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-primary/10 text-primary text-xs font-bold hover:bg-primary/20 transition-colors">
                                    <span class="material-symbols-outlined text-[14px]">manage_accounts</span> Assign Role
                                </button>
                                <?php if ($u['id'] != ($uid ?? 0)): ?>
                                <button onclick="deleteUser(<?= $u['id'] ?>, '<?= htmlspecialchars($u['username']) ?>')"
                                        class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-error/10 text-error text-xs font-bold hover:bg-error/20 transition-colors">
                                    <span class="material-symbols-outlined text-[14px]">delete</span>
                                </button>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($users)): ?>
                    <tr><td colspan="5" class="text-center py-12 text-on-surface-variant/50 text-sm">No users found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</main>
<?php include '../includes/footer.php'; ?>
</div>

<!-- Assign Role Modal -->
<div id="assign-role-modal" class="fixed inset-0 z-[100] hidden items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" onclick="closeModal('assign-role-modal')"></div>
    <div class="relative bg-white rounded-3xl shadow-2xl w-full max-w-sm mx-auto" style="animation: modal-in 0.25s ease forwards;">
        <div class="flex items-center justify-between p-6 border-b border-outline-variant/30">
            <div>
                <h3 class="font-bold text-on-surface text-base">Assign Role</h3>
                <p class="text-xs text-on-surface-variant" id="assign-role-subtitle">User</p>
            </div>
            <button onclick="closeModal('assign-role-modal')" class="w-8 h-8 rounded-full bg-surface-container-high flex items-center justify-center hover:bg-error-container hover:text-error transition-colors">
                <span class="material-symbols-outlined text-[18px]">close</span>
            </button>
        </div>
        <div class="p-6 space-y-4">
            <input type="hidden" id="assign-user-id">
            <div>
                <label class="block text-xs font-bold text-on-surface-variant uppercase tracking-wide mb-1.5">Select Role</label>
                <select id="assign-role-select" class="w-full px-4 py-3 rounded-xl border border-outline-variant focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none bg-surface-container-low transition-all">
                    <?php 
                    $grouped = ['Staff' => [], 'Client' => []];
                    foreach ($all_roles as $r) $grouped[$r['type']][] = $r;
                    foreach ($grouped as $type => $grp_roles):
                        if (empty($grp_roles)) continue;
                    ?>
                    <optgroup label="── <?= $type ?> Roles ──">
                        <?php foreach ($grp_roles as $r): ?>
                        <option value="<?= $r['id'] ?>"><?= htmlspecialchars($r['name']) ?></option>
                        <?php endforeach; ?>
                    </optgroup>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="flex gap-3 pt-2">
                <button onclick="closeModal('assign-role-modal')" class="flex-1 py-3 rounded-xl border border-outline-variant text-on-surface-variant font-semibold text-sm hover:bg-surface-container transition-colors">Cancel</button>
                <button onclick="submitRoleAssignment()" class="flex-1 py-3 rounded-xl bg-primary text-white font-bold text-sm hover:bg-primary/90 shadow-md active:scale-95 transition-all flex items-center justify-center gap-2">
                    <span class="material-symbols-outlined text-[18px]">save</span> Assign
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Add User Modal -->
<div id="add-user-modal" class="fixed inset-0 z-[100] hidden items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" onclick="closeModal('add-user-modal')"></div>
    <div class="relative bg-white rounded-3xl shadow-2xl w-full max-w-md mx-auto" style="animation: modal-in 0.25s ease forwards;">
        <div class="flex items-center justify-between p-6 border-b border-outline-variant/30">
            <div>
                <h3 class="font-bold text-on-surface text-base">Add New User</h3>
                <p class="text-xs text-on-surface-variant">Create a staff or client account</p>
            </div>
            <button onclick="closeModal('add-user-modal')" class="w-8 h-8 rounded-full bg-surface-container-high flex items-center justify-center hover:bg-error-container hover:text-error transition-colors">
                <span class="material-symbols-outlined text-[18px]">close</span>
            </button>
        </div>
        <form id="add-user-form" class="p-6 space-y-4">
            <div class="grid grid-cols-2 gap-4">
                <div class="col-span-2">
                    <label class="block text-xs font-bold text-on-surface-variant uppercase tracking-wide mb-1.5">Username *</label>
                    <input type="text" name="username" required placeholder="e.g. john_doe"
                           class="w-full px-4 py-3 rounded-xl border border-outline-variant focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none bg-surface-container-low transition-all">
                </div>
                <div class="col-span-2">
                    <label class="block text-xs font-bold text-on-surface-variant uppercase tracking-wide mb-1.5">Email *</label>
                    <input type="email" name="email" required placeholder="john@skopestay.com"
                           class="w-full px-4 py-3 rounded-xl border border-outline-variant focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none bg-surface-container-low transition-all">
                </div>
                <div>
                    <label class="block text-xs font-bold text-on-surface-variant uppercase tracking-wide mb-1.5">Password *</label>
                    <input type="password" name="password" required placeholder="••••••••"
                           class="w-full px-4 py-3 rounded-xl border border-outline-variant focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none bg-surface-container-low transition-all">
                </div>
                <div>
                    <label class="block text-xs font-bold text-on-surface-variant uppercase tracking-wide mb-1.5">Role</label>
                    <select name="role_id" class="w-full px-4 py-3 rounded-xl border border-outline-variant focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none bg-surface-container-low transition-all">
                        <?php foreach ($all_roles as $r): ?>
                        <option value="<?= $r['id'] ?>"><?= htmlspecialchars($r['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="closeModal('add-user-modal')" class="flex-1 py-3 rounded-xl border border-outline-variant text-on-surface-variant font-semibold text-sm hover:bg-surface-container transition-colors">Cancel</button>
                <button type="submit" class="flex-1 py-3 rounded-xl bg-primary text-white font-bold text-sm hover:bg-primary/90 shadow-md active:scale-95 transition-all flex items-center justify-center gap-2">
                    <span class="material-symbols-outlined text-[18px]">person_add</span> Create User
                </button>
            </div>
        </form>
    </div>
</div>

<style>
@keyframes modal-in {
    from { opacity:0; transform: scale(0.95) translateY(16px); }
    to   { opacity:1; transform: scale(1) translateY(0); }
}
</style>

<script>
function openModal(id)  { const el=document.getElementById(id); el.classList.remove('hidden'); el.classList.add('flex'); }
function closeModal(id) { const el=document.getElementById(id); el.classList.add('hidden'); el.classList.remove('flex'); }
function showSuccess(msg) { Swal.fire({icon:'success',title:'Done!',text:msg,timer:3000,timerProgressBar:true,showConfirmButton:false,toast:true,position:'top-end'}); }
function showError(msg)   { Swal.fire({icon:'error',title:'Error',text:msg}); }

function searchUsers(q) {
    const query = q.toLowerCase();
    document.querySelectorAll('.user-row').forEach(row => {
        row.style.display = row.dataset.search.includes(query) ? '' : 'none';
    });
}

function openAssignRole(userId, username) {
    document.getElementById('assign-user-id').value = userId;
    document.getElementById('assign-role-subtitle').textContent = 'Assigning role to: ' + username;
    openModal('assign-role-modal');
}

async function submitRoleAssignment() {
    const userId = document.getElementById('assign-user-id').value;
    const roleId = document.getElementById('assign-role-select').value;
    try {
        const res = await fetch('../api/users.php', {
            method: 'PUT',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({ user_id: userId, role_id: roleId })
        });
        const json = await res.json();
        if (json.success) { closeModal('assign-role-modal'); showSuccess(json.message); setTimeout(() => location.reload(), 1500); }
        else showError(json.message);
    } catch(e) { showError('Network error.'); }
}

async function deleteUser(id, username) {
    const r = await Swal.fire({
        title: `Delete ${username}?`, text: 'This cannot be undone.', icon: 'warning',
        showCancelButton: true, confirmButtonColor: 'rgb(239,68,68)', confirmButtonText: 'Delete'
    });
    if (!r.isConfirmed) return;
    try {
        const res = await fetch(`../api/users.php?id=${id}`, { method: 'DELETE' });
        const json = await res.json();
        if (json.success) { showSuccess(json.message); setTimeout(() => location.reload(), 1500); }
        else showError(json.message);
    } catch(e) { showError('Network error.'); }
}

document.getElementById('add-user-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    const data = Object.fromEntries(formData.entries());
    try {
        const res = await fetch('../api/users.php', {
            method: 'POST', headers: {'Content-Type': 'application/json'},
            body: JSON.stringify(data)
        });
        const json = await res.json();
        if (json.success) { closeModal('add-user-modal'); this.reset(); showSuccess(json.message); setTimeout(() => location.reload(), 1500); }
        else showError(json.message);
    } catch(e) { showError('Network error.'); }
});
</script>
</body>
</html>
