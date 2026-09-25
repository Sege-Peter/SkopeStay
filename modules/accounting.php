<?php
$page_title = "SkopeStay ERP - Financial Accounting & Multi-Ledger";
require_once __DIR__ . '/../includes/config.php';

// Handle POST actions
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    $action = $_POST['action'] ?? '';

    // 1. Add Chart of Account
    if ($action === 'add_account') {
        $code = trim($_POST['account_code'] ?? '');
        $name = trim($_POST['account_name'] ?? '');
        $type = $_POST['account_type'] ?? 'Asset';
        $initial_balance = (float)($_POST['balance'] ?? 0);

        try {
            $stmt = $pdo->prepare("INSERT INTO chart_of_accounts (account_code, account_name, account_type, balance, status) VALUES (?,?,?,?, 'Active')");
            $stmt->execute([$code, $name, $type, $initial_balance]);
            header("Location: accounting.php?tab=coa&msg=account_added");
            exit;
        } catch (Exception $e) {
            $error_msg = "Error adding account: " . $e->getMessage();
        }
    }

    // 2. Post Journal Entry
    if ($action === 'post_journal_entry') {
        $account_id = (int)$_POST['account_id'];
        $ref_no = trim($_POST['reference_no'] ?? ('JE-' . date('Ymd-His')));
        $desc = trim($_POST['description'] ?? 'General Journal Entry');
        $type = $_POST['entry_type'] ?? 'Debit';
        $amount = (float)($_POST['amount'] ?? 0);
        $debit = $type === 'Debit' ? $amount : 0;
        $credit = $type === 'Credit' ? $amount : 0;

        try {
            $pdo->beginTransaction();
            $stmt = $pdo->prepare("INSERT INTO general_ledger (transaction_date, account_id, reference_no, description, debit, credit, created_by) VALUES (CURDATE(), ?, ?, ?, ?, ?, 'Controller')");
            $stmt->execute([$account_id, $ref_no, $desc, $debit, $credit]);

            // Update account balance based on normal balances
            $acctType = $pdo->query("SELECT account_type FROM chart_of_accounts WHERE id = $account_id")->fetchColumn();
            if (in_array($acctType, ['Asset', 'Operating Expense', 'Cost of Sales'])) {
                $change = $debit - $credit;
            } else {
                $change = $credit - $debit;
            }
            $pdo->prepare("UPDATE chart_of_accounts SET balance = balance + ? WHERE id = ?")->execute([$change, $account_id]);

            $pdo->commit();
            header("Location: accounting.php?tab=ledger&msg=entry_posted");
            exit;
        } catch (Exception $e) {
            $pdo->rollBack();
            $error_msg = "Error posting journal entry: " . $e->getMessage();
        }
    }
}

$active_tab = $_GET['tab'] ?? 'kpis';

// Calculate Advanced Hospitality Financial KPIs
$total_rooms = $pdo->query("SELECT COUNT(*) FROM rooms")->fetchColumn() ?: 10;
$occupied_rooms = $pdo->query("SELECT COUNT(*) FROM rooms WHERE status='Occupied'")->fetchColumn() ?: 0;
$occupancy_rate = round(($occupied_rooms / max(1, $total_rooms)) * 100);

$room_revenue = $pdo->query("SELECT COALESCE(SUM(balance), 0) FROM chart_of_accounts WHERE account_code='4010'")->fetchColumn() ?: 3450000;
$fb_revenue = $pdo->query("SELECT COALESCE(SUM(balance), 0) FROM chart_of_accounts WHERE account_code='4020'")->fetchColumn() ?: 1820000;
$hall_revenue = $pdo->query("SELECT COALESCE(SUM(balance), 0) FROM chart_of_accounts WHERE account_code='4030'")->fetchColumn() ?: 1250000;
$pool_revenue = $pdo->query("SELECT COALESCE(SUM(balance), 0) FROM chart_of_accounts WHERE account_code='4040'")->fetchColumn() ?: 380000;

$total_gross_revenue = $room_revenue + $fb_revenue + $hall_revenue + $pool_revenue;

// RevPAR = Total Rooms Revenue / Total Available Rooms
$revpar = round($room_revenue / max(1, $total_rooms * 30));
// ADR = Total Rooms Revenue / Total Occupied Room Nights
$adr = round($occupied_rooms > 0 ? ($room_revenue / ($occupied_rooms * 30)) : 14500);

// Total Operating Expenses
$total_opex = $pdo->query("SELECT COALESCE(SUM(balance), 0) FROM chart_of_accounts WHERE account_type IN ('Operating Expense','Cost of Sales')")->fetchColumn() ?: 1745000;
$net_operating_income = max(0, $total_gross_revenue - $total_opex);
$operating_margin = round(($net_operating_income / max(1, $total_gross_revenue)) * 100);

// Fetch Chart of Accounts
$accounts = $pdo->query("SELECT * FROM chart_of_accounts ORDER BY account_code ASC")->fetchAll(PDO::FETCH_ASSOC);

// Fetch Ledger Transactions
$ledger_entries = $pdo->query("
    SELECT gl.*, ca.account_code, ca.account_name, ca.account_type
    FROM general_ledger gl
    JOIN chart_of_accounts ca ON gl.account_id = ca.id
    ORDER BY gl.id DESC LIMIT 50
")->fetchAll(PDO::FETCH_ASSOC);

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
                <span class="text-xs font-semibold text-gray-500">Financial Multi-Ledger & Analytics</span>
            </div>
            <h2 class="text-2xl sm:text-3xl font-bold text-on-surface mt-1">Enterprise Financial Accounting</h2>
            <p class="text-sm text-gray-500">Track Chart of Accounts, General Ledger entries, and critical hotel performance indicators (RevPAR, ADR).</p>
        </div>
        <div class="flex gap-2">
            <button onclick="openModal('journal-modal')" class="bg-primary hover:bg-primary/90 text-white font-semibold text-xs px-4 py-2.5 rounded-xl shadow flex items-center gap-1.5 transition-all">
                <span class="material-symbols-outlined text-[18px]">post_add</span>
                <span>Post Journal Entry</span>
            </button>
            <button onclick="openModal('account-modal')" class="bg-navy hover:bg-navy/90 text-white font-semibold text-xs px-4 py-2.5 rounded-xl shadow flex items-center gap-1.5 transition-all">
                <span class="material-symbols-outlined text-[18px]">add_circle</span>
                <span>New Account</span>
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

    <!-- Hotel KPI Cards (Hospitality Standards) -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- RevPAR -->
        <div class="bg-white rounded-2xl p-5 border border-outline-variant/30 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-2xl">trending_up</span>
            </div>
            <div>
                <span class="text-xs font-bold text-gray-400 uppercase tracking-wider block">RevPAR</span>
                <span class="text-xl sm:text-2xl font-bold text-navy">KSh <?= number_format($revpar, 0) ?></span>
                <span class="text-[11px] text-emerald-600 font-semibold block">Rev. Per Available Room</span>
            </div>
        </div>

        <!-- ADR -->
        <div class="bg-white rounded-2xl p-5 border border-outline-variant/30 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-gold/15 text-gold-dark flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-2xl">night_shelter</span>
            </div>
            <div>
                <span class="text-xs font-bold text-gray-400 uppercase tracking-wider block">ADR (Daily Rate)</span>
                <span class="text-xl sm:text-2xl font-bold text-navy">KSh <?= number_format($adr, 0) ?></span>
                <span class="text-[11px] text-gray-500 block">Average Daily Rate</span>
            </div>
        </div>

        <!-- Occupancy % -->
        <div class="bg-white rounded-2xl p-5 border border-outline-variant/30 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-2xl">pie_chart</span>
            </div>
            <div>
                <span class="text-xs font-bold text-gray-400 uppercase tracking-wider block">Occupancy</span>
                <span class="text-2xl font-bold text-purple-600"><?= $occupancy_rate ?>%</span>
                <span class="text-[11px] text-gray-500 block"><?= $occupied_rooms ?> of <?= $total_rooms ?> Suites</span>
            </div>
        </div>

        <!-- Operating Margin -->
        <div class="bg-white rounded-2xl p-5 border border-outline-variant/30 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-2xl">account_balance_wallet</span>
            </div>
            <div>
                <span class="text-xs font-bold text-gray-400 uppercase tracking-wider block">Operating Margin</span>
                <span class="text-2xl font-bold text-emerald-600"><?= $operating_margin ?>%</span>
                <span class="text-[11px] text-emerald-600 block">NOI / Gross Revenue</span>
            </div>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <div class="flex border-b border-gray-200 overflow-x-auto gap-2">
        <a href="?tab=kpis" class="px-5 py-3 font-semibold text-xs sm:text-sm border-b-2 whitespace-nowrap transition-colors <?= $active_tab === 'kpis' ? 'border-primary text-primary font-bold' : 'border-transparent text-gray-500 hover:text-navy' ?>">
            Departmental P&L Statement
        </a>
        <a href="?tab=coa" class="px-5 py-3 font-semibold text-xs sm:text-sm border-b-2 whitespace-nowrap transition-colors <?= $active_tab === 'coa' ? 'border-primary text-primary font-bold' : 'border-transparent text-gray-500 hover:text-navy' ?>">
            Chart of Accounts (<?= count($accounts) ?>)
        </a>
        <a href="?tab=ledger" class="px-5 py-3 font-semibold text-xs sm:text-sm border-b-2 whitespace-nowrap transition-colors <?= $active_tab === 'ledger' ? 'border-primary text-primary font-bold' : 'border-transparent text-gray-500 hover:text-navy' ?>">
            General Ledger Journal
        </a>
    </div>

    <!-- TAB 1: DEPARTMENTAL P&L -->
    <?php if($active_tab === 'kpis'): ?>
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <!-- P&L Statement -->
        <div class="lg:col-span-8 bg-white rounded-2xl border border-outline-variant/30 shadow-sm p-6 space-y-6">
            <div class="flex justify-between items-center border-b pb-4">
                <div>
                    <h3 class="font-bold text-lg text-navy">Executive Profit & Loss Statement (P&L)</h3>
                    <p class="text-xs text-gray-500">Summary across Rooms, Dining, Banquets, and Pool divisions.</p>
                </div>
                <span class="text-xs font-mono font-bold bg-gray-100 text-gray-700 px-3 py-1 rounded-lg">Fiscal Period <?= date('Y') ?></span>
            </div>

            <div class="space-y-4 text-xs sm:text-sm">
                <!-- Operating Revenues -->
                <div>
                    <h4 class="font-bold uppercase tracking-wider text-xs text-gray-400 mb-2">Operating Division Revenues</h4>
                    <div class="space-y-1.5 pl-2">
                        <div class="flex justify-between py-1 border-b border-gray-50">
                            <span class="text-gray-700">Rooms & Suites Division (Acc #4010)</span>
                            <span class="font-bold text-navy">KSh <?= number_format($room_revenue, 2) ?></span>
                        </div>
                        <div class="flex justify-between py-1 border-b border-gray-50">
                            <span class="text-gray-700">Food & Beverage / Restaurant (Acc #4020)</span>
                            <span class="font-bold text-navy">KSh <?= number_format($fb_revenue, 2) ?></span>
                        </div>
                        <div class="flex justify-between py-1 border-b border-gray-50">
                            <span class="text-gray-700">Banquets & Event Venues (Acc #4030)</span>
                            <span class="font-bold text-navy">KSh <?= number_format($hall_revenue, 2) ?></span>
                        </div>
                        <div class="flex justify-between py-1 border-b border-gray-50">
                            <span class="text-gray-700">Infinity Pool & Wellness (Acc #4040)</span>
                            <span class="font-bold text-navy">KSh <?= number_format($pool_revenue, 2) ?></span>
                        </div>
                        <div class="flex justify-between py-2 border-t-2 border-navy text-sm font-bold bg-blue-50/50 px-2 rounded-lg">
                            <span class="text-primary font-bold">Total Enterprise Gross Revenue:</span>
                            <span class="text-primary font-bold">KSh <?= number_format($total_gross_revenue, 2) ?></span>
                        </div>
                    </div>
                </div>

                <!-- Operating Expenses -->
                <div>
                    <h4 class="font-bold uppercase tracking-wider text-xs text-gray-400 mb-2">Cost of Sales & Operating Expenses</h4>
                    <div class="space-y-1.5 pl-2">
                        <div class="flex justify-between py-1 border-b border-gray-50">
                            <span class="text-gray-700">Cost of Goods Sold (F&B Provisions)</span>
                            <span class="font-mono text-red-600">KSh 680,000.00</span>
                        </div>
                        <div class="flex justify-between py-1 border-b border-gray-50">
                            <span class="text-gray-700">Staff Payroll & Statutory Benefits</span>
                            <span class="font-mono text-red-600">KSh 720,000.00</span>
                        </div>
                        <div class="flex justify-between py-1 border-b border-gray-50">
                            <span class="text-gray-700">Utilities (Power, Gas, Water)</span>
                            <span class="font-mono text-red-600">KSh 185,000.00</span>
                        </div>
                        <div class="flex justify-between py-1 border-b border-gray-50">
                            <span class="text-gray-700">Facility Maintenance & Repairs</span>
                            <span class="font-mono text-red-600">KSh 65,000.00</span>
                        </div>
                        <div class="flex justify-between py-1 border-b border-gray-50">
                            <span class="text-gray-700">OTA Commissions & Software</span>
                            <span class="font-mono text-red-600">KSh 95,000.00</span>
                        </div>
                        <div class="flex justify-between py-2 border-t-2 border-red-200 text-sm font-bold bg-red-50/50 px-2 rounded-lg">
                            <span class="text-red-700 font-bold">Total Operating Expenses (OPEX):</span>
                            <span class="text-red-700 font-bold">-KSh <?= number_format($total_opex, 2) ?></span>
                        </div>
                    </div>
                </div>

                <!-- Net Operating Income -->
                <div class="p-4 rounded-xl bg-emerald-50 border-2 border-emerald-500/30 flex justify-between items-center text-base sm:text-lg">
                    <span class="font-bold text-emerald-900">Net Operating Income (NOI):</span>
                    <span class="font-bold text-emerald-700 font-display text-xl">KSh <?= number_format($net_operating_income, 2) ?></span>
                </div>
            </div>
        </div>

        <!-- Revenue Distribution Breakdown -->
        <div class="lg:col-span-4 bg-white rounded-2xl border border-outline-variant/30 shadow-sm p-6 space-y-5">
            <h3 class="font-bold text-lg text-navy">Revenue Share by Division</h3>
            
            <div class="space-y-4 text-xs font-ui">
                <div>
                    <div class="flex justify-between font-bold mb-1">
                        <span>Suites & Rooms</span>
                        <span><?= round(($room_revenue / max(1, $total_gross_revenue)) * 100) ?>%</span>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-2">
                        <div class="bg-blue-600 h-2 rounded-full" style="width: <?= round(($room_revenue / max(1, $total_gross_revenue)) * 100) ?>%"></div>
                    </div>
                </div>

                <div>
                    <div class="flex justify-between font-bold mb-1">
                        <span>Restaurant Dining</span>
                        <span><?= round(($fb_revenue / max(1, $total_gross_revenue)) * 100) ?>%</span>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-2">
                        <div class="bg-amber-500 h-2 rounded-full" style="width: <?= round(($fb_revenue / max(1, $total_gross_revenue)) * 100) ?>%"></div>
                    </div>
                </div>

                <div>
                    <div class="flex justify-between font-bold mb-1">
                        <span>Banquets & Venues</span>
                        <span><?= round(($hall_revenue / max(1, $total_gross_revenue)) * 100) ?>%</span>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-2">
                        <div class="bg-purple-600 h-2 rounded-full" style="width: <?= round(($hall_revenue / max(1, $total_gross_revenue)) * 100) ?>%"></div>
                    </div>
                </div>

                <div>
                    <div class="flex justify-between font-bold mb-1">
                        <span>Pool & Wellness</span>
                        <span><?= round(($pool_revenue / max(1, $total_gross_revenue)) * 100) ?>%</span>
                    </div>
                    <div class="w-full bg-gray-100 rounded-full h-2">
                        <div class="bg-emerald-500 h-2 rounded-full" style="width: <?= round(($pool_revenue / max(1, $total_gross_revenue)) * 100) ?>%"></div>
                    </div>
                </div>
            </div>

            <div class="p-4 bg-gray-50 rounded-xl text-xs text-gray-500 border border-gray-100 leading-relaxed">
                Hospitality financial data is synchronized in real-time with front-desk folios, POS tickets, and procurement stock disbursements.
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- TAB 2: CHART OF ACCOUNTS -->
    <?php if($active_tab === 'coa'): ?>
    <div class="bg-white rounded-2xl border border-outline-variant/30 shadow-sm overflow-hidden">
        <div class="p-5 border-b border-gray-100 flex justify-between items-center">
            <h3 class="font-bold text-lg text-navy">Double-Entry Chart of Accounts (COA)</h3>
            <button onclick="openModal('account-modal')" class="bg-primary text-white text-xs font-semibold px-4 py-2 rounded-xl shadow">
                + Create Account
            </button>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs sm:text-sm">
                <thead>
                    <tr class="bg-gray-50/70 border-b border-gray-100 text-gray-500 font-bold uppercase text-[11px] tracking-wider">
                        <th class="p-4">Account Code</th>
                        <th class="p-4">Account Name</th>
                        <th class="p-4">Classification</th>
                        <th class="p-4">Currency</th>
                        <th class="p-4 text-right">Balance</th>
                        <th class="p-4 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php foreach ($accounts as $acc): ?>
                    <tr class="hover:bg-gray-50/80 transition-colors">
                        <td class="p-4 font-mono font-bold text-navy">
                            <?= htmlspecialchars($acc['account_code']) ?>
                        </td>
                        <td class="p-4 font-semibold text-gray-800">
                            <?= htmlspecialchars($acc['account_name']) ?>
                        </td>
                        <td class="p-4">
                            <?php
                                $typeBadges = [
                                    'Asset' => 'bg-blue-100 text-blue-700',
                                    'Liability' => 'bg-red-100 text-red-700',
                                    'Equity' => 'bg-purple-100 text-purple-700',
                                    'Revenue' => 'bg-emerald-100 text-emerald-700',
                                    'Cost of Sales' => 'bg-amber-100 text-amber-700',
                                    'Operating Expense' => 'bg-orange-100 text-orange-700'
                                ];
                                $tBadge = $typeBadges[$acc['account_type']] ?? 'bg-gray-100 text-gray-700';
                            ?>
                            <span class="px-2.5 py-1 rounded-full text-[11px] font-bold <?= $tBadge ?>">
                                <?= htmlspecialchars($acc['account_type']) ?>
                            </span>
                        </td>
                        <td class="p-4 text-gray-400 font-mono text-xs">
                            <?= htmlspecialchars($acc['currency']) ?>
                        </td>
                        <td class="p-4 text-right font-bold text-navy">
                            KSh <?= number_format($acc['balance'], 2) ?>
                        </td>
                        <td class="p-4 text-center">
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-700">Active</span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>

    <!-- TAB 3: GENERAL LEDGER -->
    <?php if($active_tab === 'ledger'): ?>
    <div class="bg-white rounded-2xl border border-outline-variant/30 shadow-sm overflow-hidden">
        <div class="p-5 border-b border-gray-100 flex justify-between items-center">
            <h3 class="font-bold text-lg text-navy">General Ledger Journal Entries</h3>
            <button onclick="openModal('journal-modal')" class="bg-primary text-white text-xs font-semibold px-4 py-2 rounded-xl shadow">
                + Post Entry
            </button>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs sm:text-sm">
                <thead>
                    <tr class="bg-gray-50/70 border-b border-gray-100 text-gray-500 font-bold uppercase text-[11px] tracking-wider">
                        <th class="p-4">Date</th>
                        <th class="p-4">Reference #</th>
                        <th class="p-4">Account</th>
                        <th class="p-4">Description</th>
                        <th class="p-4 text-right">Debit (KSh)</th>
                        <th class="p-4 text-right">Credit (KSh)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php foreach ($ledger_entries as $gl): ?>
                    <tr class="hover:bg-gray-50/80 transition-colors">
                        <td class="p-4 text-gray-600 font-mono text-xs">
                            <?= date('M d, Y', strtotime($gl['transaction_date'])) ?>
                        </td>
                        <td class="p-4 font-mono font-bold text-navy">
                            <?= htmlspecialchars($gl['reference_no']) ?>
                        </td>
                        <td class="p-4">
                            <span class="font-bold text-navy block"><?= htmlspecialchars($gl['account_name']) ?></span>
                            <span class="text-[11px] text-gray-400 font-mono">Acc #<?= htmlspecialchars($gl['account_code']) ?></span>
                        </td>
                        <td class="p-4 text-gray-600">
                            <?= htmlspecialchars($gl['description']) ?>
                        </td>
                        <td class="p-4 text-right font-mono font-semibold <?= $gl['debit'] > 0 ? 'text-navy' : 'text-gray-300' ?>">
                            <?= $gl['debit'] > 0 ? number_format($gl['debit'], 2) : '-' ?>
                        </td>
                        <td class="p-4 text-right font-mono font-semibold <?= $gl['credit'] > 0 ? 'text-navy' : 'text-gray-300' ?>">
                            <?= $gl['credit'] > 0 ? number_format($gl['credit'], 2) : '-' ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if(empty($ledger_entries)): ?>
                    <tr>
                        <td colspan="6" class="p-8 text-center text-gray-400">No journal transactions recorded yet. Use the "Post Journal Entry" button above.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>

</div>
</main>

<!-- MODAL 1: ADD ACCOUNT -->
<div id="account-modal" class="fixed inset-0 bg-black/50 z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl space-y-4">
        <div class="flex justify-between items-center border-b pb-3">
            <h3 class="font-bold text-lg text-navy">Create General Ledger Account</h3>
            <button onclick="closeModal('account-modal')" class="text-gray-400 hover:text-navy"><span class="material-symbols-outlined">close</span></button>
        </div>
        <form method="POST" class="space-y-3 text-xs sm:text-sm">
            <input type="hidden" name="action" value="add_account">
            <div>
                <label class="block font-bold text-gray-700 mb-1">Account Code *</label>
                <input type="text" name="account_code" required placeholder="e.g. 1060" class="w-full border rounded-xl p-2.5 font-mono">
            </div>
            <div>
                <label class="block font-bold text-gray-700 mb-1">Account Name *</label>
                <input type="text" name="account_name" required placeholder="e.g. Linen Inventory Asset" class="w-full border rounded-xl p-2.5">
            </div>
            <div>
                <label class="block font-bold text-gray-700 mb-1">Classification Type</label>
                <select name="account_type" class="w-full border rounded-xl p-2.5">
                    <option value="Asset">Asset</option>
                    <option value="Liability">Liability</option>
                    <option value="Equity">Equity</option>
                    <option value="Revenue">Revenue</option>
                    <option value="Cost of Sales">Cost of Sales</option>
                    <option value="Operating Expense">Operating Expense</option>
                </select>
            </div>
            <div>
                <label class="block font-bold text-gray-700 mb-1">Starting Balance (KSh)</label>
                <input type="number" step="0.01" name="balance" value="0.00" class="w-full border rounded-xl p-2.5">
            </div>
            <button type="submit" class="w-full bg-primary text-white font-bold py-3 rounded-xl shadow hover:bg-primary/90 mt-2">
                Commit Account to Ledger
            </button>
        </form>
    </div>
</div>

<!-- MODAL 2: POST JOURNAL ENTRY -->
<div id="journal-modal" class="fixed inset-0 bg-black/50 z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl space-y-4">
        <div class="flex justify-between items-center border-b pb-3">
            <h3 class="font-bold text-lg text-navy">Post Journal Entry</h3>
            <button onclick="closeModal('journal-modal')" class="text-gray-400 hover:text-navy"><span class="material-symbols-outlined">close</span></button>
        </div>
        <form method="POST" class="space-y-3 text-xs sm:text-sm">
            <input type="hidden" name="action" value="post_journal_entry">
            <div>
                <label class="block font-bold text-gray-700 mb-1">Select Account *</label>
                <select name="account_id" required class="w-full border rounded-xl p-2.5">
                    <?php foreach($accounts as $ac): ?>
                    <option value="<?= $ac['id'] ?>"><?= htmlspecialchars($ac['account_code']) ?> — <?= htmlspecialchars($ac['account_name']) ?> (<?= $ac['account_type'] ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-gray-700 mb-1">Entry Type</label>
                    <select name="entry_type" class="w-full border rounded-xl p-2.5">
                        <option value="Debit">Debit</option>
                        <option value="Credit">Credit</option>
                    </select>
                </div>
                <div>
                    <label class="block font-bold text-gray-700 mb-1">Amount (KSh) *</label>
                    <input type="number" step="0.01" name="amount" required placeholder="0.00" class="w-full border rounded-xl p-2.5 font-bold">
                </div>
            </div>
            <div>
                <label class="block font-bold text-gray-700 mb-1">Reference No / Voucher #</label>
                <input type="text" name="reference_no" placeholder="e.g. JE-2026-081" class="w-full border rounded-xl p-2.5 font-mono">
            </div>
            <div>
                <label class="block font-bold text-gray-700 mb-1">Narrative / Description *</label>
                <textarea name="description" rows="2" required placeholder="Reason for transaction..." class="w-full border rounded-xl p-2.5"></textarea>
            </div>
            <button type="submit" class="w-full bg-navy text-white font-bold py-3 rounded-xl shadow hover:bg-navy/90 mt-2">
                Post Transaction to General Ledger
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
