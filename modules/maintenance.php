<?php
$page_title = "SkopeStay ERP - Facilities, Housekeeping & Work Orders";
require_once __DIR__ . '/../includes/config.php';

// Handle POST actions
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    $action = $_POST['action'] ?? '';

    // 1. Update Housekeeping Status
    if ($action === 'update_hk_status') {
        $task_id = (int)$_POST['task_id'];
        $new_status = $_POST['status'];
        $inspector = $_POST['inspector'] ?? 'Housekeeping Executive';

        try {
            $stmt = $pdo->prepare("UPDATE housekeeping_tasks SET status = ?, inspected_by = IF(?='Inspected & Ready', ?, inspected_by) WHERE id = ?");
            $stmt->execute([$new_status, $new_status, $inspector, $task_id]);

            // If Inspected & Ready, release room to Available in front office
            if ($new_status === 'Inspected & Ready') {
                $room_id = $pdo->query("SELECT room_id FROM housekeeping_tasks WHERE id = $task_id")->fetchColumn();
                if ($room_id) {
                    $pdo->prepare("UPDATE rooms SET status = 'Available' WHERE id = ?")->execute([$room_id]);
                }
            } elseif ($new_status === 'Dirty') {
                $room_id = $pdo->query("SELECT room_id FROM housekeeping_tasks WHERE id = $task_id")->fetchColumn();
                if ($room_id) {
                    $pdo->prepare("UPDATE rooms SET status = 'Cleaning' WHERE id = ?")->execute([$room_id]);
                }
            }
            header("Location: maintenance.php?tab=housekeeping&msg=hk_updated");
            exit;
        } catch (Exception $e) {
            $error_msg = "Error updating housekeeping status: " . $e->getMessage();
        }
    }

    // 2. Create Maintenance Ticket
    if ($action === 'create_work_order') {
        $title = trim($_POST['title'] ?? '');
        $category = $_POST['category'] ?? 'HVAC & AC';
        $priority = $_POST['priority'] ?? 'Medium';
        $room_id = !empty($_POST['room_id']) ? (int)$_POST['room_id'] : null;
        $description = trim($_POST['description'] ?? '');
        $assigned_to = trim($_POST['assigned_to'] ?? 'Chief Facilities Engineer');
        $reported_by = trim($_POST['reported_by'] ?? 'Staff');

        $count = $pdo->query("SELECT COUNT(*) FROM maintenance_orders")->fetchColumn() + 1;
        $ticketNo = 'WO-' . date('Y') . '-' . str_pad($count, 3, '0', STR_PAD_LEFT);

        try {
            $stmt = $pdo->prepare("INSERT INTO maintenance_orders (ticket_number, title, category, priority, room_id, description, assigned_to, status, reported_by) VALUES (?,?,?,?,?,?,?, 'Open', ?)");
            $stmt->execute([$ticketNo, $title, $category, $priority, $room_id, $description, $assigned_to, $reported_by]);
            header("Location: maintenance.php?tab=workorders&msg=ticket_created");
            exit;
        } catch (Exception $e) {
            $error_msg = "Error creating work order: " . $e->getMessage();
        }
    }

    // 3. Resolve Maintenance Ticket
    if ($action === 'resolve_work_order') {
        $ticket_id = (int)$_POST['ticket_id'];
        $notes = trim($_POST['resolution_notes'] ?? 'Repaired and verified.');

        try {
            $stmt = $pdo->prepare("UPDATE maintenance_orders SET status = 'Resolved', resolution_notes = ?, resolved_at = NOW() WHERE id = ?");
            $stmt->execute([$notes, $ticket_id]);
            header("Location: maintenance.php?tab=workorders&msg=ticket_resolved");
            exit;
        } catch (Exception $e) {
            $error_msg = "Error resolving work order: " . $e->getMessage();
        }
    }
}

$active_tab = $_GET['tab'] ?? 'housekeeping';

// Metrics
$dirty_rooms_count = $pdo->query("SELECT COUNT(*) FROM housekeeping_tasks WHERE status = 'Dirty'")->fetchColumn() ?: 0;
$cleaning_rooms_count = $pdo->query("SELECT COUNT(*) FROM housekeeping_tasks WHERE status = 'In Progress'")->fetchColumn() ?: 0;
$inspected_rooms_count = $pdo->query("SELECT COUNT(*) FROM housekeeping_tasks WHERE status = 'Inspected & Ready'")->fetchColumn() ?: 0;
$open_wo_count = $pdo->query("SELECT COUNT(*) FROM maintenance_orders WHERE status IN ('Open','In Progress')")->fetchColumn() ?: 0;

// Fetch Housekeeping Tasks with Room Details
$hk_tasks = $pdo->query("
    SELECT hk.*, r.room_number, r.type as room_type, r.floor 
    FROM housekeeping_tasks hk
    JOIN rooms r ON hk.room_id = r.id
    ORDER BY hk.priority = 'VIP Priority' DESC, hk.id ASC
")->fetchAll(PDO::FETCH_ASSOC);

// Fetch Maintenance Tickets
$work_orders = $pdo->query("
    SELECT wo.*, r.room_number, r.type as room_type
    FROM maintenance_orders wo
    LEFT JOIN rooms r ON wo.room_id = r.id
    ORDER BY wo.priority = 'Critical Emergency' DESC, wo.priority = 'High' DESC, wo.id DESC
")->fetchAll(PDO::FETCH_ASSOC);

// Rooms for selection
$all_rooms = $pdo->query("SELECT id, room_number, type FROM rooms ORDER BY room_number ASC")->fetchAll(PDO::FETCH_ASSOC);

include __DIR__ . '/../includes/head.php';
?>
<!DOCTYPE html>
<html class="light" lang="en">
<body class="bg-background text-on-surface font-body-md min-h-screen">

<?php include __DIR__ . '/../includes/sidebar.php'; ?>

<main class="md:ml-64 min-h-screen flex flex-col">
<?php include __DIR__ . '/../includes/header.php'; ?>

<div class="flex-1 p-4 md:p-8 space-y-6">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="text-xs font-bold uppercase tracking-wider text-primary bg-primary/10 px-2.5 py-1 rounded-full">Hospitality ERP</span>
                <span class="text-xs font-semibold text-gray-500">Facilities & CMMS</span>
            </div>
            <h2 class="text-2xl sm:text-3xl font-bold text-on-surface mt-1">Housekeeping & Asset Maintenance</h2>
            <p class="text-sm text-gray-500">Coordinate room turnover inspections, HVAC servicing, and facilities engineering work orders.</p>
        </div>
        <div class="flex gap-2">
            <button onclick="openModal('wo-modal')" class="bg-primary hover:bg-primary/90 text-white font-semibold text-xs px-4 py-2.5 rounded-xl shadow flex items-center gap-1.5 transition-all">
                <span class="material-symbols-outlined text-[18px]">build</span>
                <span>Log Work Order</span>
            </button>
        </div>
    </div>

    <!-- Alert Notifications -->
    <?php if(!empty($error_msg)): ?>
    <div class="p-4 rounded-xl bg-red-50 text-red-700 border border-red-200 text-sm flex items-center gap-2">
        <span class="material-symbols-outlined">error</span>
        <span><?= htmlspecialchars($error_msg) ?></span>
    </div>
    <?php endif; ?>

    <!-- KPI Metric Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl p-5 border border-outline-variant/30 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-red-50 text-red-600 flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-2xl">cleaning_services</span>
            </div>
            <div>
                <span class="text-xs font-bold text-gray-400 uppercase tracking-wider block">Requires Cleaning</span>
                <span class="text-2xl font-bold text-red-600"><?= $dirty_rooms_count ?></span>
                <span class="text-[11px] text-gray-500 block">Pending Turnover</span>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-outline-variant/30 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-2xl">mop</span>
            </div>
            <div>
                <span class="text-xs font-bold text-gray-400 uppercase tracking-wider block">In Progress</span>
                <span class="text-2xl font-bold text-amber-600"><?= $cleaning_rooms_count ?></span>
                <span class="text-[11px] text-gray-500 block">Housekeepers Active</span>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-outline-variant/30 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-2xl">verified</span>
            </div>
            <div>
                <span class="text-xs font-bold text-gray-400 uppercase tracking-wider block">Inspected & Ready</span>
                <span class="text-2xl font-bold text-emerald-600"><?= $inspected_rooms_count ?></span>
                <span class="text-[11px] text-emerald-600 block">Released for Check-In</span>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-outline-variant/30 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-2xl">engineering</span>
            </div>
            <div>
                <span class="text-xs font-bold text-gray-400 uppercase tracking-wider block">Open Work Orders</span>
                <span class="text-2xl font-bold text-purple-600"><?= $open_wo_count ?></span>
                <span class="text-[11px] text-gray-500 block">Engineering Tickets</span>
            </div>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <div class="flex border-b border-gray-200 overflow-x-auto gap-2">
        <a href="?tab=housekeeping" class="px-5 py-3 font-semibold text-xs sm:text-sm border-b-2 whitespace-nowrap transition-colors <?= $active_tab === 'housekeeping' ? 'border-primary text-primary font-bold' : 'border-transparent text-gray-500 hover:text-navy' ?>">
            Housekeeping Live Queue (<?= count($hk_tasks) ?>)
        </a>
        <a href="?tab=workorders" class="px-5 py-3 font-semibold text-xs sm:text-sm border-b-2 whitespace-nowrap transition-colors <?= $active_tab === 'workorders' ? 'border-primary text-primary font-bold' : 'border-transparent text-gray-500 hover:text-navy' ?>">
            Engineering Work Orders (<?= count($work_orders) ?>)
        </a>
    </div>

    <!-- TAB 1: HOUSEKEEPING -->
    <?php if($active_tab === 'housekeeping'): ?>
    <div class="bg-white rounded-2xl border border-outline-variant/30 shadow-sm overflow-hidden">
        <div class="p-5 border-b border-gray-100 flex justify-between items-center">
            <h3 class="font-bold text-lg text-navy">Live Turnover & Inspection Board</h3>
            <span class="text-xs text-gray-400 font-mono">Auto-synced with Front Desk PMS</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs sm:text-sm">
                <thead>
                    <tr class="bg-gray-50/70 border-b border-gray-100 text-gray-500 font-bold uppercase text-[11px] tracking-wider">
                        <th class="p-4">Suite / Room</th>
                        <th class="p-4">Clean Type</th>
                        <th class="p-4">Priority</th>
                        <th class="p-4">Inspection Notes</th>
                        <th class="p-4">Current Status</th>
                        <th class="p-4 text-right">Housekeeper / Inspector Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php foreach ($hk_tasks as $hk): ?>
                    <tr class="hover:bg-gray-50/80 transition-colors">
                        <td class="p-4">
                            <span class="font-bold text-navy text-sm block">Room <?= htmlspecialchars($hk['room_number']) ?></span>
                            <span class="text-xs text-gray-400"><?= htmlspecialchars($hk['room_type']) ?> (Floor <?= $hk['floor'] ?>)</span>
                        </td>
                        <td class="p-4 font-medium text-gray-700">
                            <?= htmlspecialchars($hk['clean_type']) ?>
                        </td>
                        <td class="p-4">
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold <?= $hk['priority'] === 'VIP Priority' ? 'bg-purple-100 text-purple-700' : ($hk['priority'] === 'High' ? 'bg-red-100 text-red-700' : 'bg-gray-100 text-gray-700') ?>">
                                <?= htmlspecialchars($hk['priority']) ?>
                            </span>
                        </td>
                        <td class="p-4 text-gray-500 text-xs max-w-xs truncate">
                            <?= htmlspecialchars($hk['notes'] ?? 'Standard turnover') ?>
                        </td>
                        <td class="p-4">
                            <?php 
                                $statusStyles = [
                                    'Dirty' => 'bg-red-100 text-red-700',
                                    'In Progress' => 'bg-amber-100 text-amber-700',
                                    'Cleaned' => 'bg-blue-100 text-blue-700',
                                    'Inspected & Ready' => 'bg-emerald-100 text-emerald-700'
                                ];
                                $sClass = $statusStyles[$hk['status']] ?? 'bg-gray-100 text-gray-700';
                            ?>
                            <span class="px-2.5 py-1 rounded-full text-xs font-bold <?= $sClass ?>">
                                <?= htmlspecialchars($hk['status']) ?>
                            </span>
                        </td>
                        <td class="p-4 text-right space-x-1">
                            <?php if($hk['status'] === 'Dirty'): ?>
                            <form method="POST" class="inline">
                                <input type="hidden" name="action" value="update_hk_status">
                                <input type="hidden" name="task_id" value="<?= $hk['id'] ?>">
                                <input type="hidden" name="status" value="In Progress">
                                <button type="submit" class="px-3 py-1 bg-amber-600 text-white rounded-lg text-xs font-bold hover:bg-amber-700">Start Clean</button>
                            </form>
                            <?php elseif($hk['status'] === 'In Progress'): ?>
                            <form method="POST" class="inline">
                                <input type="hidden" name="action" value="update_hk_status">
                                <input type="hidden" name="task_id" value="<?= $hk['id'] ?>">
                                <input type="hidden" name="status" value="Cleaned">
                                <button type="submit" class="px-3 py-1 bg-blue-600 text-white rounded-lg text-xs font-bold hover:bg-blue-700">Mark Cleaned</button>
                            </form>
                            <?php elseif($hk['status'] === 'Cleaned'): ?>
                            <form method="POST" class="inline">
                                <input type="hidden" name="action" value="update_hk_status">
                                <input type="hidden" name="task_id" value="<?= $hk['id'] ?>">
                                <input type="hidden" name="status" value="Inspected & Ready">
                                <button type="submit" class="px-3 py-1 bg-emerald-600 text-white rounded-lg text-xs font-bold hover:bg-emerald-700" title="Sign off and release to Front Office">
                                    Release to Front Desk
                                </button>
                            </form>
                            <?php else: ?>
                            <span class="text-xs text-emerald-700 font-bold flex items-center justify-end gap-1">
                                <span class="material-symbols-outlined text-[15px]">check_circle</span> Ready
                            </span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>

    <!-- TAB 2: WORK ORDERS -->
    <?php if($active_tab === 'workorders'): ?>
    <div class="bg-white rounded-2xl border border-outline-variant/30 shadow-sm overflow-hidden p-6 space-y-5">
        <div class="flex justify-between items-center">
            <div>
                <h3 class="font-bold text-lg text-navy">Facility Engineering Work Orders</h3>
                <p class="text-xs text-gray-500">Track MEP, HVAC, plumbing, and structural maintenance tickets with priority resolution.</p>
            </div>
            <button onclick="openModal('wo-modal')" class="bg-primary text-white text-xs font-semibold px-4 py-2 rounded-xl shadow">
                + New Maintenance Ticket
            </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach ($work_orders as $wo): ?>
            <div class="p-5 rounded-2xl border border-gray-100 bg-gray-50/40 hover:border-primary/40 transition-all flex flex-col justify-between space-y-4">
                <div>
                    <div class="flex justify-between items-start mb-2">
                        <span class="text-[10px] font-mono font-bold px-2 py-0.5 rounded bg-gray-200 text-gray-700"><?= htmlspecialchars($wo['ticket_number']) ?></span>
                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full <?= $wo['priority'] === 'Critical Emergency' ? 'bg-red-100 text-red-700' : ($wo['priority'] === 'High' ? 'bg-orange-100 text-orange-700' : 'bg-blue-100 text-blue-700') ?>">
                            <?= htmlspecialchars($wo['priority']) ?>
                        </span>
                    </div>
                    <h4 class="font-bold text-navy text-base leading-snug"><?= htmlspecialchars($wo['title']) ?></h4>
                    <span class="text-xs text-primary font-semibold block mb-2"><?= htmlspecialchars($wo['category']) ?></span>
                    <p class="text-xs text-gray-600 line-clamp-2 leading-relaxed mb-3">
                        <?= htmlspecialchars($wo['description']) ?>
                    </p>
                    <div class="text-[11px] text-gray-500 space-y-1 bg-white p-2 rounded-lg border border-gray-100">
                        <div><strong>Location:</strong> <?= $wo['room_number'] ? 'Room ' . htmlspecialchars($wo['room_number']) : 'Estate Facility' ?></div>
                        <div><strong>Assigned:</strong> <?= htmlspecialchars($wo['assigned_to'] ?? 'Engineering Team') ?></div>
                    </div>
                </div>

                <div class="pt-3 border-t border-gray-100 flex justify-between items-center text-xs">
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold <?= $wo['status'] === 'Resolved' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' ?>">
                        <?= htmlspecialchars($wo['status']) ?>
                    </span>
                    <?php if($wo['status'] !== 'Resolved'): ?>
                    <form method="POST" class="inline">
                        <input type="hidden" name="action" value="resolve_work_order">
                        <input type="hidden" name="ticket_id" value="<?= $wo['id'] ?>">
                        <button type="submit" class="px-3 py-1 bg-emerald-600 text-white rounded-lg text-xs font-bold hover:bg-emerald-700">Mark Resolved</button>
                    </form>
                    <?php else: ?>
                    <span class="text-xs text-gray-400">Fixed</span>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

</div>
</main>

<!-- MODAL: CREATE WORK ORDER -->
<div id="wo-modal" class="fixed inset-0 bg-black/50 z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl space-y-4">
        <div class="flex justify-between items-center border-b pb-3">
            <h3 class="font-bold text-lg text-navy">Log Facility Work Order</h3>
            <button onclick="closeModal('wo-modal')" class="text-gray-400 hover:text-navy"><span class="material-symbols-outlined">close</span></button>
        </div>
        <form method="POST" class="space-y-3 text-xs sm:text-sm">
            <input type="hidden" name="action" value="create_work_order">
            <div>
                <label class="block font-bold text-gray-700 mb-1">Issue Title *</label>
                <input type="text" name="title" required placeholder="e.g. Chiller leakage in Suite 201" class="w-full border rounded-xl p-2.5">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-gray-700 mb-1">Category</label>
                    <select name="category" class="w-full border rounded-xl p-2.5">
                        <option value="HVAC & AC">HVAC & Air Conditioning</option>
                        <option value="Plumbing">Plumbing & Water</option>
                        <option value="Electrical">Electrical & Lighting</option>
                        <option value="Carpentry & Furniture">Carpentry & Furniture</option>
                        <option value="Kitchen Appliance">Kitchen Appliance</option>
                        <option value="Pool & Spa Equipment">Pool Equipment</option>
                    </select>
                </div>
                <div>
                    <label class="block font-bold text-gray-700 mb-1">Priority</label>
                    <select name="priority" class="w-full border rounded-xl p-2.5">
                        <option value="Low">Low</option>
                        <option value="Medium" selected>Medium</option>
                        <option value="High">High</option>
                        <option value="Critical Emergency">Critical Emergency</option>
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-gray-700 mb-1">Affected Room (Optional)</label>
                    <select name="room_id" class="w-full border rounded-xl p-2.5">
                        <option value="">General Facility</option>
                        <?php foreach($all_rooms as $rm): ?>
                        <option value="<?= $rm['id'] ?>">Room <?= htmlspecialchars($rm['room_number']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block font-bold text-gray-700 mb-1">Assigned Technician</label>
                    <input type="text" name="assigned_to" value="Samson Kariuki" class="w-full border rounded-xl p-2.5">
                </div>
            </div>
            <div>
                <label class="block font-bold text-gray-700 mb-1">Technical Fault Description *</label>
                <textarea name="description" rows="3" required placeholder="Describe the fault symptoms..." class="w-full border rounded-xl p-2.5"></textarea>
            </div>
            <button type="submit" class="w-full bg-primary text-white font-bold py-3 rounded-xl shadow hover:bg-primary/90 mt-2">
                Dispatch Maintenance Ticket
            </button>
        </form>
    </div>
</div>

<script>
function openModal(id) { document.getElementById(id)?.classList.remove('hidden'); }
function closeModal(id) { document.getElementById(id)?.classList.add('hidden'); }
</script>

</body>
</html>
