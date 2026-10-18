<?php
$page_title = "My Profile | SkopeStay";
require_once '../includes/config.php';

$uid = $_SESSION['user_id'];
$st = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$st->execute([$uid]);
$me = $st->fetch();
$role = get_user_primary_role($pdo, $uid);

$activity = $pdo->prepare("SELECT action, module, details, ip_address, created_at FROM audit_logs WHERE user_id = ? ORDER BY id DESC LIMIT 8");
$activity->execute([$uid]);
$activity = $activity->fetchAll();

$perms = ($role === 'Super Admin') ? ['All permissions'] : ($_SESSION['permissions'] ?? load_user_permissions($pdo, $uid));

include '../includes/head.php';
?>
<body class="bg-background text-on-surface min-h-screen">

<?php include '../includes/sidebar.php'; ?>

<div class="md:ml-64 flex flex-col min-h-screen">
<?php include '../includes/header.php'; ?>

<main class="p-4 md:p-8 flex-grow max-w-5xl w-full mx-auto space-y-6">

    <!-- Identity card -->
    <div class="rounded-3xl overflow-hidden border border-outline-variant/40 bg-surface">
        <div class="h-24 bg-navy relative" style="background:linear-gradient(120deg,var(--theme-primary),var(--theme-sidebar))">
            <div class="absolute inset-0 opacity-20" style="background:radial-gradient(circle at 80% 20%,var(--theme-secondary),transparent 60%)"></div>
        </div>
        <div class="px-6 pb-6 -mt-10 flex flex-col sm:flex-row sm:items-end gap-4">
            <div class="w-20 h-20 rounded-2xl bg-gold text-navy flex items-center justify-center text-2xl font-extrabold ring-4 ring-surface shadow-lg relative" style="color:var(--theme-on-secondary)">
                <?= e(strtoupper(substr($me['full_name'] ?: $me['username'], 0, 2))) ?>
            </div>
            <div class="flex-1 min-w-0">
                <h1 class="text-2xl font-extrabold truncate"><?= e($me['full_name'] ?: $me['username']) ?></h1>
                <p class="text-sm text-on-surface-variant">@<?= e($me['username']) ?> · <span class="font-bold text-primary"><?= e($role) ?></span></p>
            </div>
            <div class="text-xs text-on-surface-variant sm:text-right">
                <p>Member since <?= date('M Y', strtotime($me['created_at'])) ?></p>
                <p>Last sign-in <?= $me['last_login'] ? date('M j, H:i', strtotime($me['last_login'])) : 'now' ?></p>
            </div>
        </div>
    </div>

    <div class="grid lg:grid-cols-2 gap-6">
        <!-- Details -->
        <form id="profile-form" class="bg-surface rounded-3xl border border-outline-variant/40 p-6 space-y-4">
            <h2 class="text-lg font-extrabold flex items-center gap-2"><span class="material-symbols-outlined text-gold">person</span>Personal details</h2>
            <div>
                <label for="p-name" class="block text-[11px] font-bold uppercase tracking-wider text-on-surface-variant mb-1.5">Full name</label>
                <input id="p-name" name="full_name" value="<?= e($me['full_name']) ?>" class="w-full bg-surface-container-low border border-outline-variant/40 rounded-xl px-3.5 py-2.5 text-sm outline-none focus:border-primary">
            </div>
            <div>
                <label for="p-email" class="block text-[11px] font-bold uppercase tracking-wider text-on-surface-variant mb-1.5">Email</label>
                <input id="p-email" name="email" type="email" required value="<?= e($me['email']) ?>" class="w-full bg-surface-container-low border border-outline-variant/40 rounded-xl px-3.5 py-2.5 text-sm outline-none focus:border-primary">
            </div>
            <div>
                <label for="p-phone" class="block text-[11px] font-bold uppercase tracking-wider text-on-surface-variant mb-1.5">Phone</label>
                <input id="p-phone" name="phone" value="<?= e($me['phone']) ?>" class="w-full bg-surface-container-low border border-outline-variant/40 rounded-xl px-3.5 py-2.5 text-sm outline-none focus:border-primary">
            </div>
            <button type="submit" class="px-5 py-2.5 rounded-xl bg-primary text-white text-sm font-bold">Save details</button>
        </form>

        <!-- Password -->
        <form id="password-form" class="bg-surface rounded-3xl border border-outline-variant/40 p-6 space-y-4 scroll-mt-24" autocomplete="off">
            <h2 id="password" class="text-lg font-extrabold flex items-center gap-2"><span class="material-symbols-outlined text-gold">key</span>Change password</h2>
            <div>
                <label for="pw-cur" class="block text-[11px] font-bold uppercase tracking-wider text-on-surface-variant mb-1.5">Current password</label>
                <input id="pw-cur" name="current_password" type="password" required autocomplete="current-password" class="w-full bg-surface-container-low border border-outline-variant/40 rounded-xl px-3.5 py-2.5 text-sm outline-none focus:border-primary">
            </div>
            <div>
                <label for="pw-new" class="block text-[11px] font-bold uppercase tracking-wider text-on-surface-variant mb-1.5">New password</label>
                <input id="pw-new" name="new_password" type="password" required minlength="8" autocomplete="new-password" class="w-full bg-surface-container-low border border-outline-variant/40 rounded-xl px-3.5 py-2.5 text-sm outline-none focus:border-primary">
                <div class="h-1.5 rounded-full bg-surface-container mt-2 overflow-hidden"><div id="pw-meter" class="h-full w-0 transition-all"></div></div>
                <p id="pw-hint" class="text-[11px] text-on-surface-variant mt-1">At least 8 characters. Mix letters, numbers and symbols.</p>
            </div>
            <div>
                <label for="pw-conf" class="block text-[11px] font-bold uppercase tracking-wider text-on-surface-variant mb-1.5">Confirm new password</label>
                <input id="pw-conf" type="password" required autocomplete="new-password" class="w-full bg-surface-container-low border border-outline-variant/40 rounded-xl px-3.5 py-2.5 text-sm outline-none focus:border-primary">
            </div>
            <button type="submit" class="px-5 py-2.5 rounded-xl bg-navy text-white text-sm font-bold">Update password</button>
        </form>
    </div>

    <div class="grid lg:grid-cols-2 gap-6">
        <div class="bg-surface rounded-3xl border border-outline-variant/40 p-6">
            <h2 class="text-lg font-extrabold flex items-center gap-2 mb-4"><span class="material-symbols-outlined text-gold">verified_user</span>Your access</h2>
            <div class="flex flex-wrap gap-2">
                <?php if (!$perms): ?><p class="text-sm text-on-surface-variant">No permissions assigned yet. Ask an administrator to give your account a role.</p><?php endif; ?>
                <?php foreach ($perms as $p): ?>
                <span class="px-2.5 py-1 rounded-full bg-surface-container text-xs font-bold"><?= e(ucwords(str_replace('_', ' ', $p))) ?></span>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="bg-surface rounded-3xl border border-outline-variant/40 p-6">
            <h2 class="text-lg font-extrabold flex items-center gap-2 mb-4"><span class="material-symbols-outlined text-gold">history</span>Recent activity</h2>
            <div class="space-y-3">
                <?php if (!$activity): ?><p class="text-sm text-on-surface-variant">No activity yet.</p><?php endif; ?>
                <?php foreach ($activity as $a): ?>
                <div class="flex justify-between gap-3 text-xs">
                    <span class="min-w-0"><span class="font-bold"><?= e(ucwords(str_replace('_', ' ', $a['action']))) ?></span> <span class="text-on-surface-variant"><?= e($a['details']) ?></span></span>
                    <span class="text-on-surface-variant shrink-0 text-right"><?= date('M j, H:i', strtotime($a['created_at'])) ?><br><?= e($a['ip_address']) ?></span>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</main>
<?php include '../includes/footer.php'; ?>
</div>

<script>
const CSRF = document.querySelector('meta[name="csrf-token"]').content;
async function post(payload) {
    const res = await fetch('../api/auth.php', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': CSRF }, body: JSON.stringify(payload) });
    return res.json();
}
document.getElementById('profile-form').addEventListener('submit', async e => {
    e.preventDefault();
    const f = e.target;
    const d = await post({ action: 'update_profile', full_name: f.full_name.value, email: f.email.value, phone: f.phone.value });
    Swal.fire({ icon: d.success ? 'success' : 'error', title: d.success ? 'Saved' : 'Not saved', text: d.message, timer: d.success ? 1500 : undefined, showConfirmButton: !d.success })
        .then(() => d.success && location.reload());
});
const pwNew = document.getElementById('pw-new');
pwNew.addEventListener('input', () => {
    const v = pwNew.value;
    const score = [v.length >= 8, v.length >= 12, /[a-z]/.test(v) && /[A-Z]/.test(v), /\d/.test(v), /[^A-Za-z0-9]/.test(v)].filter(Boolean).length;
    const m = document.getElementById('pw-meter');
    m.style.width = (score * 20) + '%';
    m.style.background = ['#ef4444', '#ef4444', '#f59e0b', '#f59e0b', '#10b981', '#10b981'][score];
    document.getElementById('pw-hint').textContent = ['Too short', 'Weak', 'Fair', 'Good', 'Strong', 'Very strong'][score];
});
document.getElementById('password-form').addEventListener('submit', async e => {
    e.preventDefault();
    const f = e.target;
    if (f.new_password.value !== document.getElementById('pw-conf').value) {
        return Swal.fire('Passwords do not match', 'Please type the same new password twice.', 'warning');
    }
    const d = await post({ action: 'change_password', current_password: f.current_password.value, new_password: f.new_password.value });
    Swal.fire({ icon: d.success ? 'success' : 'error', title: d.success ? 'Password updated' : 'Not updated', text: d.message });
    if (d.success) f.reset();
});
if (location.hash === '#password') document.getElementById('pw-cur').focus();
</script>
</body>
</html>
