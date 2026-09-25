<?php
$page_title = "Audit Logs | SkopeStay";
require_once '../includes/config.php';

$uid = $_SESSION['user_id'] ?? null;
if (!$uid || !has_permission($pdo, $uid, 'manage_roles')) {
    header("Location: dashboard.php?error=access_denied"); exit;
}

$logs = $pdo->query("
    SELECT al.*, u.username 
    FROM audit_logs al
    LEFT JOIN users u ON al.user_id = u.id
    ORDER BY al.created_at DESC
    LIMIT 200
")->fetchAll();

include '../includes/head.php';
?>
<!DOCTYPE html>
<html class="light" lang="en">
<body class="bg-background text-on-surface font-body-md min-h-screen">
<?php include '../includes/sidebar.php'; ?>
<div class="md:ml-64 flex flex-col min-h-screen">
<?php include '../includes/header.php'; ?>
<main class="p-4 md:p-8 flex-grow space-y-6">

    <div>
        <nav class="flex items-center gap-2 text-xs font-semibold text-on-surface-variant mb-2">
            <span>System</span>
            <span class="material-symbols-outlined text-[14px]">chevron_right</span>
            <span class="text-primary font-bold">Audit Logs</span>
        </nav>
        <h1 class="font-bold text-2xl md:text-3xl text-on-surface flex items-center gap-2">
            <span class="material-symbols-outlined text-primary text-3xl">manage_history</span>
            Audit Logs
        </h1>
        <p class="text-sm text-on-surface-variant mt-1">Track all administrative actions across the system (last 200 records).</p>
    </div>

    <div class="bg-white rounded-2xl border border-outline-variant/30 shadow-sm overflow-hidden">
        <div class="p-5 border-b border-outline-variant/20 flex flex-wrap items-center justify-between gap-3">
            <h2 class="font-bold text-on-surface">Activity Log</h2>
            <div class="relative">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline text-[18px]">search</span>
                <input type="text" id="log-search" placeholder="Filter logs..." oninput="filterLogs(this.value)"
                       class="pl-9 pr-4 py-2 rounded-xl border border-outline-variant text-sm bg-surface-container-low focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all">
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="bg-surface-container/50 border-b border-outline-variant/20">
                        <th class="text-left px-5 py-3 text-xs font-bold text-on-surface-variant uppercase tracking-wide">Time</th>
                        <th class="text-left px-5 py-3 text-xs font-bold text-on-surface-variant uppercase tracking-wide">User</th>
                        <th class="text-left px-5 py-3 text-xs font-bold text-on-surface-variant uppercase tracking-wide">Action</th>
                        <th class="text-left px-5 py-3 text-xs font-bold text-on-surface-variant uppercase tracking-wide">Module</th>
                        <th class="text-left px-5 py-3 text-xs font-bold text-on-surface-variant uppercase tracking-wide">Details</th>
                        <th class="text-left px-5 py-3 text-xs font-bold text-on-surface-variant uppercase tracking-wide">IP</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant/10">
                    <?php foreach ($logs as $log):
                        $action_colors = [
                            'login'         => 'bg-green-100 text-green-700',
                            'logout'        => 'bg-gray-100 text-gray-600',
                            'create_user'   => 'bg-blue-100 text-blue-700',
                            'delete_user'   => 'bg-red-100 text-red-700',
                            'assign_role'   => 'bg-purple-100 text-purple-700',
                        ];
                        $action_cls = $action_colors[$log['action']] ?? 'bg-amber-100 text-amber-700';
                    ?>
                    <tr class="hover:bg-surface-container-low/50 transition-colors log-row" data-search="<?= strtolower(($log['username'] ?? '').' '.$log['action'].' '.$log['module'].' '.($log['details'] ?? '')) ?>">
                        <td class="px-5 py-3 text-xs text-on-surface-variant whitespace-nowrap"><?= date('d M y H:i', strtotime($log['created_at'])) ?></td>
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-2">
                                <div class="w-6 h-6 rounded-full bg-primary/10 flex items-center justify-center text-[10px] font-bold text-primary">
                                    <?= strtoupper(substr($log['username'] ?? 'S', 0, 1)) ?>
                                </div>
                                <span class="text-sm font-semibold text-on-surface"><?= htmlspecialchars($log['username'] ?? 'System') ?></span>
                            </div>
                        </td>
                        <td class="px-5 py-3">
                            <span class="px-2.5 py-1 rounded-full text-[11px] font-bold <?= $action_cls ?>">
                                <?= htmlspecialchars($log['action']) ?>
                            </span>
                        </td>
                        <td class="px-5 py-3 text-sm text-on-surface-variant"><?= htmlspecialchars($log['module']) ?></td>
                        <td class="px-5 py-3 text-xs text-on-surface-variant max-w-xs truncate"><?= htmlspecialchars($log['details'] ?? '—') ?></td>
                        <td class="px-5 py-3 text-xs text-on-surface-variant font-mono"><?= htmlspecialchars($log['ip_address'] ?? '—') ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($logs)): ?>
                    <tr><td colspan="6" class="text-center py-12 text-on-surface-variant/50 text-sm">No audit logs yet.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>
<?php include '../includes/footer.php'; ?>
</div>
<script>
function filterLogs(q) {
    const query = q.toLowerCase();
    document.querySelectorAll('.log-row').forEach(row => {
        row.style.display = row.dataset.search.includes(query) ? '' : 'none';
    });
}
</script>
</body>
</html>
