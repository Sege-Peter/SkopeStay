<?php
$page_title = "Finance | SkopeStay";
require_once '../includes/config.php';

$expenses = $pdo->query("SELECT * FROM expenses ORDER BY expense_date DESC")->fetchAll();
$monthly_total = $pdo->query("SELECT COALESCE(SUM(amount),0) FROM expenses WHERE MONTH(expense_date)=MONTH(CURDATE())")->fetchColumn();
$monthly_count = $pdo->query("SELECT COUNT(*) FROM expenses WHERE MONTH(expense_date)=MONTH(CURDATE())")->fetchColumn();
$pending_count = $pdo->query("SELECT COUNT(*) FROM expenses WHERE status='Pending'")->fetchColumn();
$budget = 200000;
$budget_pct = min(100, round(($monthly_total / $budget) * 100));

include '../includes/head.php';
?>
<!DOCTYPE html>
<html class="light" lang="en">
<body class="bg-background text-on-surface font-body-md min-h-screen">
<?php include '../includes/sidebar.php'; ?>
<div class="md:ml-64 flex flex-col min-h-screen">
<?php include '../includes/header.php'; ?>
<main class="flex-1 p-4 md:p-8 space-y-6">

<div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
    <div>
        <nav class="flex items-center gap-2 text-xs font-semibold text-on-surface-variant mb-2">
            <span>Finance</span><span class="material-symbols-outlined text-[14px]">chevron_right</span><span class="text-primary font-bold">Expenses</span>
        </nav>
        <h1 class="text-2xl md:text-3xl font-extrabold text-on-surface">Record New Expense</h1>
        <p class="text-on-surface-variant text-sm mt-1">Log facility maintenance, inventory restock, or utility payments.</p>
    </div>
    <button onclick="openModal('expense-modal')" class="flex items-center gap-2 bg-primary text-white px-5 py-2.5 rounded-xl font-bold text-sm shadow-md hover:bg-primary/90 active:scale-95 transition-all">
        <span class="material-symbols-outlined text-[18px]">add</span> Add Expense
    </button>
</div>

<!-- Grid Layout -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    <!-- Left: Expense Form + Table -->
    <div class="lg:col-span-2 space-y-6">
        <!-- Quick Entry Card -->
        <div class="bg-white rounded-2xl border border-outline-variant/30 shadow-sm p-6">
            <h3 class="font-bold text-base text-on-surface flex items-center gap-2 mb-5">
                <span class="material-symbols-outlined text-secondary">description</span> Quick Expense Entry
            </h3>
            <form id="expense-quick-form" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-on-surface-variant uppercase tracking-wide mb-1.5">Expense Title *</label>
                    <input type="text" name="title" required placeholder="e.g. Monthly Electricity Bill" class="w-full px-3 py-2.5 rounded-xl border border-outline-variant focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none text-sm bg-surface-container-low transition-all"/>
                </div>
                <div>
                    <label class="block text-xs font-bold text-on-surface-variant uppercase tracking-wide mb-1.5">Category *</label>
                    <select name="category" required class="w-full px-3 py-2.5 rounded-xl border border-outline-variant focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none text-sm bg-surface-container-low appearance-none">
                        <option value="">-- Select --</option>
                        <option>Utilities (Water/Electricity)</option>
                        <option>Facility Maintenance</option>
                        <option>Kitchen &amp; Restaurant Supplies</option>
                        <option>Housekeeping Inventory</option>
                        <option>Staff Payroll</option>
                        <option>Marketing &amp; Commissions</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-on-surface-variant uppercase tracking-wide mb-1.5">Amount (KSh) *</label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-sm font-bold text-secondary">KSh</span>
                        <input type="number" name="amount" required min="0" placeholder="0.00" class="w-full pl-12 pr-4 py-2.5 rounded-xl border border-outline-variant focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none text-sm bg-surface-container-low transition-all"/>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold text-on-surface-variant uppercase tracking-wide mb-1.5">Date *</label>
                    <input type="date" name="expense_date" required class="w-full px-3 py-2.5 rounded-xl border border-outline-variant focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none text-sm bg-surface-container-low"/>
                </div>
                <div>
                    <label class="block text-xs font-bold text-on-surface-variant uppercase tracking-wide mb-1.5">Payment Method</label>
                    <select name="payment_method" class="w-full px-3 py-2.5 rounded-xl border border-outline-variant focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none text-sm bg-surface-container-low appearance-none">
                        <option>Cash</option><option>Bank Transfer</option><option>Card / M-Pesa</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-on-surface-variant uppercase tracking-wide mb-1.5">Status</label>
                    <select name="status" class="w-full px-3 py-2.5 rounded-xl border border-outline-variant focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none text-sm bg-surface-container-low appearance-none">
                        <option value="Paid">Paid</option><option value="Pending">Pending</option>
                    </select>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-on-surface-variant uppercase tracking-wide mb-1.5">Notes</label>
                    <textarea name="notes" rows="2" placeholder="Additional context..." class="w-full px-3 py-2.5 rounded-xl border border-outline-variant focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none text-sm bg-surface-container-low resize-none transition-all"></textarea>
                </div>
                <div class="md:col-span-2 flex gap-3">
                    <button type="reset" class="flex-1 py-3 rounded-xl border border-outline-variant text-on-surface-variant font-semibold text-sm hover:bg-surface-container transition-colors">Discard</button>
                    <button type="submit" id="expense-submit" class="flex-1 py-3 rounded-xl bg-primary text-white font-bold text-sm hover:bg-primary/90 shadow-md active:scale-95 transition-all flex items-center justify-center gap-2">
                        <span class="material-symbols-outlined text-[18px]">save</span> Save Transaction
                    </button>
                </div>
            </form>
        </div>

        <!-- Recent Expenses Table -->
        <div class="bg-white rounded-2xl border border-outline-variant/30 shadow-sm overflow-hidden">
            <div class="p-5 border-b border-outline-variant/30 flex items-center justify-between">
                <h3 class="font-bold text-on-surface text-base">Recent Expenses</h3>
                <span class="text-xs bg-secondary-container text-on-secondary-container px-3 py-1 rounded-full font-bold"><?= $monthly_count ?> this month</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead><tr class="bg-surface-container-low/50 border-b border-outline-variant/30">
                        <th class="px-5 py-3.5 text-xs font-bold uppercase text-on-surface">Description</th>
                        <th class="px-5 py-3.5 text-xs font-bold uppercase text-on-surface hidden md:table-cell">Category</th>
                        <th class="px-5 py-3.5 text-xs font-bold uppercase text-on-surface text-right">Amount</th>
                        <th class="px-5 py-3.5 text-xs font-bold uppercase text-on-surface text-center">Status</th>
                        <th class="px-5 py-3.5 text-xs font-bold uppercase text-on-surface text-right">Action</th>
                    </tr></thead>
                    <tbody class="divide-y divide-outline-variant/20">
                    <?php if (empty($expenses)): ?>
                    <tr><td colspan="5" class="text-center py-12 text-on-surface-variant opacity-40">No expenses logged yet.</td></tr>
                    <?php else: foreach($expenses as $exp):
                        $sb = $exp['status']==='Paid' ? 'bg-green-100 text-green-700 border-green-200' : 'bg-secondary-container text-on-secondary-container border-secondary-container';
                    ?>
                    <tr class="hover:bg-surface-container-low/40 transition-colors group">
                        <td class="px-5 py-4">
                            <p class="font-semibold text-sm"><?= htmlspecialchars($exp['title']) ?></p>
                            <p class="text-[11px] text-on-surface-variant"><?= date('M j, Y', strtotime($exp['expense_date'])) ?> • <?= htmlspecialchars($exp['payment_method']) ?></p>
                        </td>
                        <td class="px-5 py-4 text-sm text-on-surface-variant hidden md:table-cell"><?= htmlspecialchars($exp['category']) ?></td>
                        <td class="px-5 py-4 text-right font-extrabold text-sm">KSh <?= number_format((float)$exp['amount']) ?></td>
                        <td class="px-5 py-4 text-center">
                            <span class="px-2.5 py-1 rounded-full border text-[11px] font-bold <?= $sb ?>"><?= $exp['status'] ?></span>
                        </td>
                        <td class="px-5 py-4 text-right opacity-0 group-hover:opacity-100 transition-opacity">
                            <button onclick="deleteExpense(<?= $exp['id'] ?>, '<?= addslashes($exp['title']) ?>')" class="p-1.5 text-on-surface-variant hover:text-error transition-colors">
                                <span class="material-symbols-outlined text-[18px]">delete</span>
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Right Sidebar Panel -->
    <div class="space-y-4">
        <!-- Budget Card -->
        <div class="bg-primary rounded-2xl p-6 text-white shadow-lg">
            <p class="text-xs font-bold uppercase tracking-widest text-white/60 mb-4">Monthly Budget</p>
            <div class="flex justify-between items-end mb-2">
                <span class="text-sm text-white/70">Budget Utilization</span>
                <span class="font-extrabold text-lg"><?= $budget_pct ?>%</span>
            </div>
            <div class="w-full h-2.5 bg-white/20 rounded-full overflow-hidden mb-4">
                <div class="h-full rounded-full <?= $budget_pct > 85 ? 'bg-red-400' : 'bg-secondary-container' ?> transition-all" style="width:<?= $budget_pct ?>%"></div>
            </div>
            <div class="flex justify-between text-sm">
                <div><p class="text-white/60 text-[11px]">Spent</p><p class="font-extrabold">KSh <?= number_format((float)$monthly_total) ?></p></div>
                <div class="text-right"><p class="text-white/60 text-[11px]">Remaining</p><p class="font-extrabold">KSh <?= number_format($budget - $monthly_total) ?></p></div>
            </div>
        </div>

        <!-- Stats -->
        <div class="bg-white rounded-2xl border border-outline-variant/30 shadow-sm p-5 grid grid-cols-2 gap-4">
            <?php foreach ([
                ['This Month', $monthly_count.' Expenses', 'receipt_long', 'bg-primary/10 text-primary'],
                ['Pending', $pending_count.' Items', 'pending_actions', 'bg-secondary-container text-on-secondary-container'],
            ] as [$lbl,$val,$ico,$cls]): ?>
            <div class="text-center p-3 bg-surface-container-low rounded-xl">
                <div class="w-10 h-10 <?= $cls ?> rounded-xl flex items-center justify-center mx-auto mb-2">
                    <span class="material-symbols-outlined text-[20px]"><?= $ico ?></span>
                </div>
                <p class="text-xs text-on-surface-variant font-semibold"><?= $lbl ?></p>
                <p class="font-extrabold text-lg text-on-surface"><?= $val ?></p>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Tip Card -->
        <div class="bg-secondary-container/20 border border-secondary-container rounded-2xl p-5">
            <div class="flex gap-3">
                <span class="material-symbols-outlined text-secondary flex-shrink-0">info</span>
                <div>
                    <p class="font-bold text-sm text-on-secondary-container">Accounting Tip</p>
                    <p class="text-xs text-on-secondary-container/70 mt-1 leading-relaxed">Tag all maintenance expenses with the correct category for accurate year-end tax reconciliation.</p>
                </div>
            </div>
        </div>
    </div>
</div>
</main>
<?php include '../includes/footer.php'; ?>
</div>

<style>
@keyframes modal-in{from{opacity:0;transform:scale(0.95) translateY(20px)}to{opacity:1;transform:scale(1) translateY(0)}}
.animate-modal{animation:modal-in 0.25s cubic-bezier(0.2,0.8,0.2,1) forwards}
</style>
<script>
function openModal(id){const e=document.getElementById(id);e.classList.remove('hidden');e.classList.add('flex');}
function closeModal(id){const e=document.getElementById(id);e.classList.add('hidden');e.classList.remove('flex');}
function showSuccess(m){Swal.fire({icon:'success',title:'Success!',text:m,timer:3000,timerProgressBar:true,showConfirmButton:false,toast:true,position:'top-end'});}
function showError(m){Swal.fire({icon:'error',title:'Error',text:m,confirmButtonColor:'rgb(37,99,235)'});}

document.getElementById('expense-quick-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    const btn = document.getElementById('expense-submit');
    btn.innerHTML = '<span class="material-symbols-outlined animate-spin text-[18px]">sync</span> Saving...'; btn.disabled = true;
    const data = {
        title: this.title.value, category: this.category.value, amount: this.amount.value,
        expense_date: this.expense_date.value, payment_method: this.payment_method.value,
        status: this.status.value, notes: this.notes.value
    };
    try {
        const res = await fetch('../api/finance.php', {method:'POST', headers:{'Content-Type':'application/json'}, body: JSON.stringify(data)});
        const json = await res.json();
        if (json.success) { this.reset(); showSuccess(json.message); setTimeout(() => location.reload(), 2000); }
        else showError(json.message);
    } catch(err) { showError('Network error.'); }
    finally { btn.innerHTML = '<span class="material-symbols-outlined text-[18px]">save</span> Save Transaction'; btn.disabled = false; }
});

function deleteExpense(id, title) {
    Swal.fire({title: `Delete "${title}"?`, text:'This record will be permanently removed.', icon:'warning',
        showCancelButton:true, confirmButtonColor:'rgb(239,68,68)', confirmButtonText:'Delete'})
    .then(async r => {
        if (!r.isConfirmed) return;
        const res = await fetch(`../api/finance.php?id=${id}`, {method:'DELETE'});
        const json = await res.json();
        if (json.success) { showSuccess(json.message); setTimeout(() => location.reload(), 1800); }
        else showError(json.message);
    });
}

document.querySelector('[name="expense_date"]').valueAsDate = new Date();
</script>
</body></html>
