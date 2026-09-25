<?php
$page_title = "SkopeStay ERP - Human Capital & HRMS";
require_once __DIR__ . '/../includes/config.php';

// Handle Actions (POST)
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    $action = $_POST['action'] ?? '';

    // 1. Add Employee
    if ($action === 'add_employee') {
        $first_name = trim($_POST['first_name'] ?? '');
        $last_name = trim($_POST['last_name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $dept_id = (int)($_POST['department_id'] ?? 1);
        $designation = trim($_POST['designation'] ?? 'Staff');
        $salary = (float)($_POST['salary'] ?? 0);
        $hire_date = $_POST['hire_date'] ?? date('Y-m-d');
        $national_id = trim($_POST['national_id'] ?? '');
        $bank_name = trim($_POST['bank_name'] ?? 'KCB Bank');
        $bank_account = trim($_POST['bank_account'] ?? '');

        $empCount = $pdo->query("SELECT COUNT(*) FROM employees")->fetchColumn() + 1;
        $empCode = 'EMP-' . str_pad($empCount, 3, '0', STR_PAD_LEFT);

        try {
            $stmt = $pdo->prepare("INSERT INTO employees 
                (employee_code, first_name, last_name, email, phone, department_id, designation, salary, hire_date, national_id, bank_name, bank_account, status)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Active')");
            $stmt->execute([$empCode, $first_name, $last_name, $email, $phone, $dept_id, $designation, $salary, $hire_date, $national_id, $bank_name, $bank_account]);
            header("Location: hrms.php?tab=directory&msg=employee_added");
            exit;
        } catch (Exception $e) {
            $error_msg = "Error adding employee: " . $e->getMessage();
        }
    }

    // 2. Add Shift
    if ($action === 'add_shift') {
        $emp_id = (int)$_POST['employee_id'];
        $shift_name = $_POST['shift_name'] ?? 'Morning Shift';
        $shift_date = $_POST['shift_date'] ?? date('Y-m-d');
        $start_time = $_POST['start_time'] ?? '08:00:00';
        $end_time = $_POST['end_time'] ?? '16:30:00';
        $notes = trim($_POST['notes'] ?? '');

        try {
            $stmt = $pdo->prepare("INSERT INTO employee_shifts (employee_id, shift_name, shift_date, start_time, end_time, notes, status) VALUES (?,?,?,?,?,?, 'Scheduled')");
            $stmt->execute([$emp_id, $shift_name, $shift_date, $start_time, $end_time, $notes]);
            header("Location: hrms.php?tab=shifts&msg=shift_scheduled");
            exit;
        } catch (Exception $e) {
            $error_msg = "Error scheduling shift: " . $e->getMessage();
        }
    }

    // 3. Leave Status Update
    if ($action === 'update_leave_status') {
        $leave_id = (int)$_POST['leave_id'];
        $status = $_POST['status'] === 'Approved' ? 'Approved' : 'Rejected';
        try {
            $stmt = $pdo->prepare("UPDATE leave_requests SET status = ? WHERE id = ?");
            $stmt->execute([$status, $leave_id]);
            header("Location: hrms.php?tab=leaves&msg=leave_updated");
            exit;
        } catch (Exception $e) {
            $error_msg = "Error updating leave: " . $e->getMessage();
        }
    }

    // 4. Add Leave Request
    if ($action === 'add_leave') {
        $emp_id = (int)$_POST['employee_id'];
        $leave_type = $_POST['leave_type'] ?? 'Annual';
        $start_date = $_POST['start_date'] ?? date('Y-m-d');
        $end_date = $_POST['end_date'] ?? date('Y-m-d');
        $days = max(1, (int)((strtotime($end_date) - strtotime($start_date)) / 86400) + 1);
        $reason = trim($_POST['reason'] ?? '');

        try {
            $stmt = $pdo->prepare("INSERT INTO leave_requests (employee_id, leave_type, start_date, end_date, days_count, reason, status) VALUES (?,?,?,?,?,?, 'Pending')");
            $stmt->execute([$emp_id, $leave_type, $start_date, $end_date, $days, $reason]);
            header("Location: hrms.php?tab=leaves&msg=leave_submitted");
            exit;
        } catch (Exception $e) {
            $error_msg = "Error submitting leave: " . $e->getMessage();
        }
    }
}

// Fetch HRMS Live Data
$active_tab = $_GET['tab'] ?? 'directory';

$total_employees = $pdo->query("SELECT COUNT(*) FROM employees WHERE status != 'Terminated'")->fetchColumn() ?: 0;
$today_shifts_count = $pdo->query("SELECT COUNT(*) FROM employee_shifts WHERE shift_date = CURDATE()")->fetchColumn() ?: 0;
$pending_leaves_count = $pdo->query("SELECT COUNT(*) FROM leave_requests WHERE status = 'Pending'")->fetchColumn() ?: 0;
$monthly_payroll_total = $pdo->query("SELECT COALESCE(SUM(salary), 0) FROM employees WHERE status = 'Active'")->fetchColumn() ?: 0;

$departments = $pdo->query("SELECT * FROM departments ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);

// Employee list with department name
$employees = $pdo->query("
    SELECT e.*, d.name as department_name 
    FROM employees e 
    LEFT JOIN departments d ON e.department_id = d.id 
    ORDER BY e.id DESC
")->fetchAll(PDO::FETCH_ASSOC);

// Shifts for current week
$shifts = $pdo->query("
    SELECT s.*, e.first_name, e.last_name, e.employee_code, e.designation, d.name as department_name
    FROM employee_shifts s
    JOIN employees e ON s.employee_id = e.id
    LEFT JOIN departments d ON e.department_id = d.id
    WHERE s.shift_date >= CURDATE() - INTERVAL 1 DAY
    ORDER BY s.shift_date ASC, s.start_time ASC
")->fetchAll(PDO::FETCH_ASSOC);

// Leave Requests
$leaves = $pdo->query("
    SELECT l.*, e.first_name, e.last_name, e.employee_code, e.designation, d.name as department_name
    FROM leave_requests l
    JOIN employees e ON l.employee_id = e.id
    LEFT JOIN departments d ON e.department_id = d.id
    ORDER BY l.id DESC
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
                <span class="text-xs font-semibold text-gray-500">Human Capital Management</span>
            </div>
            <h2 class="text-2xl sm:text-3xl font-bold text-on-surface mt-1">HRMS & Workforce Planning</h2>
            <p class="text-sm text-gray-500">Manage employee records, shift scheduling, leave approvals, and payroll computation.</p>
        </div>
        <div class="flex gap-2">
            <button onclick="openModal('employee-modal')" class="bg-primary hover:bg-primary/90 text-white font-semibold text-xs px-4 py-2.5 rounded-xl shadow flex items-center gap-1.5 transition-all">
                <span class="material-symbols-outlined text-[18px]">person_add</span>
                <span>Onboard Employee</span>
            </button>
            <button onclick="openModal('shift-modal')" class="bg-navy hover:bg-navy/90 text-white font-semibold text-xs px-4 py-2.5 rounded-xl shadow flex items-center gap-1.5 transition-all">
                <span class="material-symbols-outlined text-[18px]">calendar_add_on</span>
                <span>Schedule Shift</span>
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
            <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-2xl">badge</span>
            </div>
            <div>
                <span class="text-xs font-bold text-gray-400 uppercase tracking-wider block">Total Staff</span>
                <span class="text-2xl font-bold text-navy"><?= $total_employees ?></span>
                <span class="text-[11px] text-gray-500 block">Across 9 Departments</span>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-outline-variant/30 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-2xl">assignment_ind</span>
            </div>
            <div>
                <span class="text-xs font-bold text-gray-400 uppercase tracking-wider block">On Duty Today</span>
                <span class="text-2xl font-bold text-emerald-600"><?= $today_shifts_count ?></span>
                <span class="text-[11px] text-gray-500 block">Active Shifts</span>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-outline-variant/30 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-2xl">event_busy</span>
            </div>
            <div>
                <span class="text-xs font-bold text-gray-400 uppercase tracking-wider block">Leave Requests</span>
                <span class="text-2xl font-bold text-amber-600"><?= $pending_leaves_count ?></span>
                <span class="text-[11px] text-gray-500 block">Pending Approval</span>
            </div>
        </div>

        <div class="bg-white rounded-2xl p-5 border border-outline-variant/30 shadow-sm flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center shrink-0">
                <span class="material-symbols-outlined text-2xl">payments</span>
            </div>
            <div>
                <span class="text-xs font-bold text-gray-400 uppercase tracking-wider block">Monthly Payroll</span>
                <span class="text-xl sm:text-2xl font-bold text-navy">KSh <?= number_format($monthly_payroll_total, 0) ?></span>
                <span class="text-[11px] text-purple-600 block">Gross Base Budget</span>
            </div>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <div class="flex border-b border-gray-200 overflow-x-auto gap-2">
        <a href="?tab=directory" class="px-5 py-3 font-semibold text-xs sm:text-sm border-b-2 whitespace-nowrap transition-colors <?= $active_tab === 'directory' ? 'border-primary text-primary font-bold' : 'border-transparent text-gray-500 hover:text-navy' ?>">
            Staff Directory (<?= count($employees) ?>)
        </a>
        <a href="?tab=shifts" class="px-5 py-3 font-semibold text-xs sm:text-sm border-b-2 whitespace-nowrap transition-colors <?= $active_tab === 'shifts' ? 'border-primary text-primary font-bold' : 'border-transparent text-gray-500 hover:text-navy' ?>">
            Shift Rostering & Attendance
        </a>
        <a href="?tab=leaves" class="px-5 py-3 font-semibold text-xs sm:text-sm border-b-2 whitespace-nowrap transition-colors <?= $active_tab === 'leaves' ? 'border-primary text-primary font-bold' : 'border-transparent text-gray-500 hover:text-navy' ?>">
            Leave Approvals <?= $pending_leaves_count > 0 ? "<span class='ml-1 px-1.5 py-0.5 rounded-full bg-amber-500 text-white text-[10px]'>$pending_leaves_count</span>" : "" ?>
        </a>
        <a href="?tab=payroll" class="px-5 py-3 font-semibold text-xs sm:text-sm border-b-2 whitespace-nowrap transition-colors <?= $active_tab === 'payroll' ? 'border-primary text-primary font-bold' : 'border-transparent text-gray-500 hover:text-navy' ?>">
            Automated Payroll Engine
        </a>
    </div>

    <!-- TAB 1: STAFF DIRECTORY -->
    <?php if($active_tab === 'directory'): ?>
    <div class="bg-white rounded-2xl border border-outline-variant/30 shadow-sm overflow-hidden">
        <div class="p-5 border-b border-gray-100 flex flex-col sm:flex-row justify-between gap-3 items-center">
            <h3 class="font-bold text-lg text-navy">Master Employee Directory</h3>
            <div class="w-full sm:w-72">
                <input type="text" id="emp-search" onkeyup="filterEmployees()" placeholder="Search staff name, code, role..." class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3.5 py-2 text-xs focus:outline-none focus:border-primary">
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs sm:text-sm" id="emp-table">
                <thead>
                    <tr class="bg-gray-50/70 border-b border-gray-100 text-gray-500 font-bold uppercase text-[11px] tracking-wider">
                        <th class="p-4">Employee</th>
                        <th class="p-4">Department</th>
                        <th class="p-4">Designation</th>
                        <th class="p-4">Contact</th>
                        <th class="p-4">Base Salary</th>
                        <th class="p-4">Hired Date</th>
                        <th class="p-4">Status</th>
                        <th class="p-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php foreach ($employees as $emp): ?>
                    <tr class="hover:bg-gray-50/80 transition-colors">
                        <td class="p-4 flex items-center gap-3">
                            <div class="w-9 h-9 rounded-full bg-navy/10 text-navy font-bold flex items-center justify-center shrink-0">
                                <?= strtoupper(substr($emp['first_name'],0,1) . substr($emp['last_name'],0,1)) ?>
                            </div>
                            <div>
                                <span class="font-bold text-navy block"><?= htmlspecialchars($emp['first_name'] . ' ' . $emp['last_name']) ?></span>
                                <span class="text-[11px] text-gray-400 font-mono"><?= htmlspecialchars($emp['employee_code']) ?></span>
                            </div>
                        </td>
                        <td class="p-4 font-medium text-gray-700">
                            <?= htmlspecialchars($emp['department_name'] ?? 'General') ?>
                        </td>
                        <td class="p-4 font-semibold text-primary">
                            <?= htmlspecialchars($emp['designation']) ?>
                        </td>
                        <td class="p-4 text-gray-500">
                            <span class="block"><?= htmlspecialchars($emp['phone']) ?></span>
                            <span class="text-[11px] text-gray-400"><?= htmlspecialchars($emp['email']) ?></span>
                        </td>
                        <td class="p-4 font-bold text-navy">
                            KSh <?= number_format($emp['salary'], 2) ?>
                        </td>
                        <td class="p-4 text-gray-500">
                            <?= date('M d, Y', strtotime($emp['hire_date'])) ?>
                        </td>
                        <td class="p-4">
                            <span class="px-2.5 py-1 rounded-full text-[11px] font-bold <?= $emp['status'] === 'Active' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' ?>">
                                <?= htmlspecialchars($emp['status']) ?>
                            </span>
                        </td>
                        <td class="p-4 text-right">
                            <button onclick="previewPayslip(<?= htmlspecialchars(json_encode($emp)) ?>)" class="text-xs bg-navy/5 hover:bg-navy hover:text-white px-3 py-1.5 rounded-lg transition-colors font-semibold" title="Generate Payslip">
                                Payslip
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>

    <!-- TAB 2: SHIFT ROSTERING -->
    <?php if($active_tab === 'shifts'): ?>
    <div class="bg-white rounded-2xl border border-outline-variant/30 shadow-sm overflow-hidden p-6 space-y-5">
        <div class="flex justify-between items-center">
            <div>
                <h3 class="font-bold text-lg text-navy">Weekly Staff Shift Rosters</h3>
                <p class="text-xs text-gray-500">Live operational deployment across front desk, culinary, housekeeping, and engineering.</p>
            </div>
            <button onclick="openModal('shift-modal')" class="bg-primary text-white text-xs font-semibold px-4 py-2 rounded-xl shadow">
                + Add Shift Schedule
            </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <?php foreach ($shifts as $sh): ?>
            <div class="p-4 rounded-xl border border-gray-100 bg-gray-50/50 hover:border-primary/50 transition-colors space-y-2">
                <div class="flex justify-between items-start">
                    <span class="text-xs font-bold px-2 py-0.5 rounded-md bg-blue-100 text-blue-700"><?= htmlspecialchars($sh['shift_name']) ?></span>
                    <span class="text-xs font-mono text-gray-500"><?= date('D, M d', strtotime($sh['shift_date'])) ?></span>
                </div>
                <div>
                    <h4 class="font-bold text-navy text-sm"><?= htmlspecialchars($sh['first_name'] . ' ' . $sh['last_name']) ?></h4>
                    <span class="text-xs text-gray-500"><?= htmlspecialchars($sh['designation']) ?> • <?= htmlspecialchars($sh['department_name'] ?? '') ?></span>
                </div>
                <div class="flex items-center gap-2 text-xs font-semibold text-gray-600 bg-white p-2 rounded-lg border border-gray-100">
                    <span class="material-symbols-outlined text-[16px] text-primary">schedule</span>
                    <span><?= substr($sh['start_time'], 0, 5) ?> — <?= substr($sh['end_time'], 0, 5) ?></span>
                </div>
                <?php if(!empty($sh['notes'])): ?>
                <p class="text-[11px] text-gray-400 italic">"<?= htmlspecialchars($sh['notes']) ?>"</p>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- TAB 3: LEAVE APPROVALS -->
    <?php if($active_tab === 'leaves'): ?>
    <div class="bg-white rounded-2xl border border-outline-variant/30 shadow-sm overflow-hidden">
        <div class="p-5 border-b border-gray-100 flex justify-between items-center">
            <h3 class="font-bold text-lg text-navy">Leave Requests & Absence Tracking</h3>
            <button onclick="openModal('leave-modal')" class="bg-primary text-white text-xs font-semibold px-4 py-2 rounded-xl shadow">
                + Submit Leave Request
            </button>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs sm:text-sm">
                <thead>
                    <tr class="bg-gray-50/70 border-b border-gray-100 text-gray-500 font-bold uppercase text-[11px] tracking-wider">
                        <th class="p-4">Staff Member</th>
                        <th class="p-4">Leave Type</th>
                        <th class="p-4">Dates</th>
                        <th class="p-4">Duration</th>
                        <th class="p-4">Reason</th>
                        <th class="p-4">Status</th>
                        <th class="p-4 text-right">Approval Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php foreach ($leaves as $lv): ?>
                    <tr class="hover:bg-gray-50/80 transition-colors">
                        <td class="p-4">
                            <span class="font-bold text-navy block"><?= htmlspecialchars($lv['first_name'] . ' ' . $lv['last_name']) ?></span>
                            <span class="text-xs text-gray-400"><?= htmlspecialchars($lv['designation']) ?></span>
                        </td>
                        <td class="p-4">
                            <span class="px-2.5 py-1 rounded-md text-xs font-bold bg-purple-100 text-purple-700"><?= htmlspecialchars($lv['leave_type']) ?></span>
                        </td>
                        <td class="p-4 text-gray-600">
                            <?= date('M d', strtotime($lv['start_date'])) ?> — <?= date('M d, Y', strtotime($lv['end_date'])) ?>
                        </td>
                        <td class="p-4 font-bold text-navy">
                            <?= $lv['days_count'] ?> Days
                        </td>
                        <td class="p-4 text-gray-500 text-xs max-w-xs truncate">
                            <?= htmlspecialchars($lv['reason'] ?? 'Standard scheduled leave') ?>
                        </td>
                        <td class="p-4">
                            <span class="px-2.5 py-1 rounded-full text-xs font-bold <?= $lv['status'] === 'Approved' ? 'bg-emerald-100 text-emerald-700' : ($lv['status'] === 'Pending' ? 'bg-amber-100 text-amber-700' : 'bg-red-100 text-red-700') ?>">
                                <?= htmlspecialchars($lv['status']) ?>
                            </span>
                        </td>
                        <td class="p-4 text-right space-x-1">
                            <?php if($lv['status'] === 'Pending'): ?>
                            <form method="POST" class="inline">
                                <input type="hidden" name="action" value="update_leave_status">
                                <input type="hidden" name="leave_id" value="<?= $lv['id'] ?>">
                                <input type="hidden" name="status" value="Approved">
                                <button type="submit" class="px-3 py-1 bg-emerald-600 text-white rounded-lg text-xs font-bold hover:bg-emerald-700">Approve</button>
                            </form>
                            <form method="POST" class="inline">
                                <input type="hidden" name="action" value="update_leave_status">
                                <input type="hidden" name="leave_id" value="<?= $lv['id'] ?>">
                                <input type="hidden" name="status" value="Rejected">
                                <button type="submit" class="px-3 py-1 bg-red-600 text-white rounded-lg text-xs font-bold hover:bg-red-700">Reject</button>
                            </form>
                            <?php else: ?>
                            <span class="text-xs text-gray-400 font-semibold">Processed</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>

    <!-- TAB 4: AUTOMATED PAYROLL ENGINE -->
    <?php if($active_tab === 'payroll'): ?>
    <div class="bg-white rounded-2xl border border-outline-variant/30 shadow-sm overflow-hidden p-6 space-y-6">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h3 class="font-bold text-lg text-navy">Monthly Executive Payroll Engine</h3>
                <p class="text-xs text-gray-500">Automated statutory calculation: NSSF (Tier II), SHIF/NHIF, PAYE withholding & Net Pay.</p>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-xs font-bold text-gray-500">Pay Period:</span>
                <span class="text-xs font-bold bg-primary/10 text-primary px-3 py-1.5 rounded-lg"><?= date('F Y') ?></span>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs sm:text-sm">
                <thead>
                    <tr class="bg-gray-50/70 border-b border-gray-100 text-gray-500 font-bold uppercase text-[11px] tracking-wider">
                        <th class="p-3">Staff Code & Name</th>
                        <th class="p-3">Basic Pay</th>
                        <th class="p-3">NSSF (Tier II)</th>
                        <th class="p-3">SHIF / NHIF</th>
                        <th class="p-3">Est. PAYE</th>
                        <th class="p-3">Net Payable</th>
                        <th class="p-3">Bank Details</th>
                        <th class="p-3 text-right">Document</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php foreach ($employees as $emp): 
                        $basic = (float)$emp['salary'];
                        $nssf = 1080.00; // Standard 2026 Tier II
                        $nhif = min(1700, max(500, round($basic * 0.0275))); // SHIF 2.75%
                        $taxable = max(0, $basic - $nssf);
                        $paye = round($taxable > 24000 ? ($taxable - 24000) * 0.25 : 0);
                        $net = max(0, $basic - ($nssf + $nhif + $paye));
                    ?>
                    <tr class="hover:bg-gray-50/80 transition-colors">
                        <td class="p-3">
                            <span class="font-bold text-navy block"><?= htmlspecialchars($emp['first_name'] . ' ' . $emp['last_name']) ?></span>
                            <span class="text-[11px] text-gray-400 font-mono"><?= htmlspecialchars($emp['employee_code']) ?></span>
                        </td>
                        <td class="p-3 font-semibold text-gray-800">
                            KSh <?= number_format($basic, 2) ?>
                        </td>
                        <td class="p-3 text-red-600 font-mono text-xs">
                            -KSh <?= number_format($nssf, 2) ?>
                        </td>
                        <td class="p-3 text-red-600 font-mono text-xs">
                            -KSh <?= number_format($nhif, 2) ?>
                        </td>
                        <td class="p-3 text-red-600 font-mono text-xs">
                            -KSh <?= number_format($paye, 2) ?>
                        </td>
                        <td class="p-3 font-bold text-emerald-700 text-sm">
                            KSh <?= number_format($net, 2) ?>
                        </td>
                        <td class="p-3 text-xs text-gray-500">
                            <?= htmlspecialchars($emp['bank_name']) ?> (<?= htmlspecialchars($emp['bank_account']) ?>)
                        </td>
                        <td class="p-3 text-right">
                            <button onclick="previewPayslip(<?= htmlspecialchars(json_encode($emp)) ?>)" class="bg-navy hover:bg-gold text-white hover:text-navy px-3 py-1.5 rounded-lg text-xs font-bold transition-all">
                                Payslip
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>

</div>
</main>

<!-- MODAL 1: ONBOARD EMPLOYEE -->
<div id="employee-modal" class="fixed inset-0 bg-black/50 z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl space-y-4 max-h-[90vh] overflow-y-auto">
        <div class="flex justify-between items-center border-b pb-3">
            <h3 class="font-bold text-lg text-navy">Onboard New Staff Member</h3>
            <button onclick="closeModal('employee-modal')" class="text-gray-400 hover:text-navy"><span class="material-symbols-outlined">close</span></button>
        </div>
        <form method="POST" class="space-y-3 text-xs sm:text-sm">
            <input type="hidden" name="action" value="add_employee">
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-gray-700 mb-1">First Name *</label>
                    <input type="text" name="first_name" required class="w-full border rounded-xl p-2.5">
                </div>
                <div>
                    <label class="block font-bold text-gray-700 mb-1">Last Name *</label>
                    <input type="text" name="last_name" required class="w-full border rounded-xl p-2.5">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-gray-700 mb-1">Email *</label>
                    <input type="email" name="email" required class="w-full border rounded-xl p-2.5">
                </div>
                <div>
                    <label class="block font-bold text-gray-700 mb-1">Phone Number *</label>
                    <input type="tel" name="phone" required placeholder="+254 7..." class="w-full border rounded-xl p-2.5">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-gray-700 mb-1">Department</label>
                    <select name="department_id" class="w-full border rounded-xl p-2.5">
                        <?php foreach($departments as $d): ?>
                        <option value="<?= $d['id'] ?>"><?= htmlspecialchars($d['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block font-bold text-gray-700 mb-1">Designation *</label>
                    <input type="text" name="designation" required placeholder="e.g. Senior Barista" class="w-full border rounded-xl p-2.5">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-gray-700 mb-1">Monthly Salary (KSh) *</label>
                    <input type="number" step="0.01" name="salary" required class="w-full border rounded-xl p-2.5">
                </div>
                <div>
                    <label class="block font-bold text-gray-700 mb-1">Hire Date</label>
                    <input type="date" name="hire_date" value="<?= date('Y-m-d') ?>" class="w-full border rounded-xl p-2.5">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-gray-700 mb-1">National ID / Passport</label>
                    <input type="text" name="national_id" class="w-full border rounded-xl p-2.5">
                </div>
                <div>
                    <label class="block font-bold text-gray-700 mb-1">Bank Name</label>
                    <input type="text" name="bank_name" value="KCB Bank" class="w-full border rounded-xl p-2.5">
                </div>
            </div>
            <div>
                <label class="block font-bold text-gray-700 mb-1">Bank Account Number</label>
                <input type="text" name="bank_account" placeholder="e.g. 1102938475" class="w-full border rounded-xl p-2.5">
            </div>
            <button type="submit" class="w-full bg-primary text-white font-bold py-3 rounded-xl shadow hover:bg-primary/90 mt-2">
                Commit Employee Record to ERP
            </button>
        </form>
    </div>
</div>

<!-- MODAL 2: SCHEDULE SHIFT -->
<div id="shift-modal" class="fixed inset-0 bg-black/50 z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl space-y-4">
        <div class="flex justify-between items-center border-b pb-3">
            <h3 class="font-bold text-lg text-navy">Schedule Staff Shift</h3>
            <button onclick="closeModal('shift-modal')" class="text-gray-400 hover:text-navy"><span class="material-symbols-outlined">close</span></button>
        </div>
        <form method="POST" class="space-y-3 text-xs sm:text-sm">
            <input type="hidden" name="action" value="add_shift">
            <div>
                <label class="block font-bold text-gray-700 mb-1">Staff Member *</label>
                <select name="employee_id" required class="w-full border rounded-xl p-2.5">
                    <?php foreach($employees as $e): ?>
                    <option value="<?= $e['id'] ?>"><?= htmlspecialchars($e['first_name'] . ' ' . $e['last_name']) ?> (<?= htmlspecialchars($e['designation']) ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-gray-700 mb-1">Shift Type</label>
                    <select name="shift_name" class="w-full border rounded-xl p-2.5">
                        <option value="Morning Shift">Morning (07:00 - 15:30)</option>
                        <option value="Evening Shift">Evening (15:00 - 23:30)</option>
                        <option value="Night Shift">Night (23:00 - 07:30)</option>
                        <option value="Split Shift">Split (10:00 - 21:30)</option>
                    </select>
                </div>
                <div>
                    <label class="block font-bold text-gray-700 mb-1">Date</label>
                    <input type="date" name="shift_date" value="<?= date('Y-m-d') ?>" class="w-full border rounded-xl p-2.5">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-gray-700 mb-1">Start Time</label>
                    <input type="time" name="start_time" value="07:00" class="w-full border rounded-xl p-2.5">
                </div>
                <div>
                    <label class="block font-bold text-gray-700 mb-1">End Time</label>
                    <input type="time" name="end_time" value="15:30" class="w-full border rounded-xl p-2.5">
                </div>
            </div>
            <div>
                <label class="block font-bold text-gray-700 mb-1">Operational Notes</label>
                <input type="text" name="notes" placeholder="e.g. Station assignment" class="w-full border rounded-xl p-2.5">
            </div>
            <button type="submit" class="w-full bg-navy text-white font-bold py-3 rounded-xl shadow hover:bg-navy/90 mt-2">
                Save Shift to Roster
            </button>
        </form>
    </div>
</div>

<!-- MODAL 3: SUBMIT LEAVE -->
<div id="leave-modal" class="fixed inset-0 bg-black/50 z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl space-y-4">
        <div class="flex justify-between items-center border-b pb-3">
            <h3 class="font-bold text-lg text-navy">Submit Leave Request</h3>
            <button onclick="closeModal('leave-modal')" class="text-gray-400 hover:text-navy"><span class="material-symbols-outlined">close</span></button>
        </div>
        <form method="POST" class="space-y-3 text-xs sm:text-sm">
            <input type="hidden" name="action" value="add_leave">
            <div>
                <label class="block font-bold text-gray-700 mb-1">Staff Member *</label>
                <select name="employee_id" required class="w-full border rounded-xl p-2.5">
                    <?php foreach($employees as $e): ?>
                    <option value="<?= $e['id'] ?>"><?= htmlspecialchars($e['first_name'] . ' ' . $e['last_name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="block font-bold text-gray-700 mb-1">Leave Type</label>
                <select name="leave_type" class="w-full border rounded-xl p-2.5">
                    <option value="Annual">Annual Paid Leave</option>
                    <option value="Sick">Sick Leave</option>
                    <option value="Maternity">Maternity Leave</option>
                    <option value="Paternity">Paternity Leave</option>
                    <option value="Compassionate">Compassionate Leave</option>
                    <option value="Unpaid">Unpaid Leave</option>
                </select>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-gray-700 mb-1">Start Date</label>
                    <input type="date" name="start_date" value="<?= date('Y-m-d') ?>" class="w-full border rounded-xl p-2.5">
                </div>
                <div>
                    <label class="block font-bold text-gray-700 mb-1">End Date</label>
                    <input type="date" name="end_date" value="<?= date('Y-m-d', strtotime('+3 days')) ?>" class="w-full border rounded-xl p-2.5">
                </div>
            </div>
            <div>
                <label class="block font-bold text-gray-700 mb-1">Reason</label>
                <textarea name="reason" rows="2" class="w-full border rounded-xl p-2.5" placeholder="Reason for request..."></textarea>
            </div>
            <button type="submit" class="w-full bg-primary text-white font-bold py-3 rounded-xl shadow hover:bg-primary/90 mt-2">
                Submit for Approval
            </button>
        </form>
    </div>
</div>

<script>
function openModal(id) {
    document.getElementById(id)?.classList.remove('hidden');
}
function closeModal(id) {
    document.getElementById(id)?.classList.add('hidden');
}

function filterEmployees() {
    const input = document.getElementById('emp-search').value.toLowerCase();
    const rows = document.querySelectorAll('#emp-table tbody tr');
    rows.forEach(r => {
        const text = r.innerText.toLowerCase();
        r.style.display = text.includes(input) ? '' : 'none';
    });
}

function previewPayslip(emp) {
    const basic = parseFloat(emp.salary);
    const nssf = 1080.00;
    const nhif = Math.min(1700, Math.max(500, Math.round(basic * 0.0275)));
    const taxable = Math.max(0, basic - nssf);
    const paye = Math.round(taxable > 24000 ? (taxable - 24000) * 0.25 : 0);
    const net = Math.max(0, basic - (nssf + nhif + paye));

    Swal.fire({
        title: 'Official Payslip',
        html: `
        <div style="text-align:left;font-family:sans-serif;font-size:13px;background:#f8fafc;padding:16px;border-radius:12px;border:1px solid #e2e8f0">
            <div style="border-bottom:1px solid #cbd5e1;padding-bottom:8px;margin-bottom:12px">
                <h3 style="margin:0;font-size:16px;color:#0F172A">SkopeStay International Resort</h3>
                <span style="font-size:11px;color:#64748b">Monthly Pay Advice — ${new Date().toLocaleString('default', { month: 'long', year: 'numeric' })}</span>
            </div>
            <p><strong>Employee:</strong> ${emp.first_name} ${emp.last_name} (${emp.employee_code})</p>
            <p><strong>Designation:</strong> ${emp.designation}</p>
            <p><strong>Bank:</strong> ${emp.bank_name} - ${emp.bank_account}</p>
            <hr style="border:none;border-top:1px dashed #cbd5e1;margin:10px 0"/>
            <table style="width:100%;font-size:13px">
                <tr><td>Basic Gross Salary:</td><td style="text-align:right"><strong>KSh ${basic.toLocaleString(undefined, {minimumFractionDigits:2})}</strong></td></tr>
                <tr><td style="color:#dc2626">Less: NSSF Tier II:</td><td style="text-align:right;color:#dc2626">-KSh ${nssf.toFixed(2)}</td></tr>
                <tr><td style="color:#dc2626">Less: SHIF (2.75%):</td><td style="text-align:right;color:#dc2626">-KSh ${nhif.toFixed(2)}</td></tr>
                <tr><td style="color:#dc2626">Less: P.A.Y.E Tax:</td><td style="text-align:right;color:#dc2626">-KSh ${paye.toFixed(2)}</td></tr>
                <tr style="border-top:2px solid #0F172A;font-size:15px"><td style="padding-top:6px"><strong>Net Payable:</strong></td><td style="text-align:right;padding-top:6px;color:#15803d"><strong>KSh ${net.toLocaleString(undefined, {minimumFractionDigits:2})}</strong></td></tr>
            </table>
        </div>`,
        showCancelButton: true,
        confirmButtonText: 'Print Payslip',
        cancelButtonText: 'Close',
        confirmButtonColor: '#0F172A'
    }).then(res => {
        if(res.isConfirmed) {
            window.print();
        }
    });
}
</script>

</body>
</html>
