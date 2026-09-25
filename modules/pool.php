<?php
$page_title = "Swimming Pool | SkopeStay";
require_once '../includes/config.php';

// Fetch pool passes
$passes = $pdo->query("
    SELECT p.*, c.full_name as customer_name 
    FROM pool_passes p 
    LEFT JOIN customers c ON p.customer_id = c.id 
    ORDER BY p.created_at DESC
")->fetchAll();

$today_visitors = $pdo->query("SELECT COALESCE(SUM(number_of_guests),0) FROM pool_passes WHERE visit_date = CURDATE()")->fetchColumn();
$active_members = $pdo->query("SELECT COUNT(*) FROM pool_passes WHERE pass_type IN ('Monthly','Annual') AND status = 'Active'")->fetchColumn();
$today_revenue  = $pdo->query("SELECT COALESCE(SUM(amount),0) FROM pool_passes WHERE visit_date = CURDATE()")->fetchColumn();

// Fetch customers for the dropdown
$customers = $pdo->query("SELECT id, full_name, phone FROM customers ORDER BY full_name")->fetchAll();

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
                <span>Facilities</span><span class="material-symbols-outlined text-[14px]">chevron_right</span>
                <span class="text-primary font-bold">Swimming Pool</span>
            </nav>
            <h1 class="text-2xl md:text-3xl font-extrabold text-on-surface">Pool Management</h1>
            <p class="text-on-surface-variant text-sm mt-1">Manage day passes, memberships, and capacity.</p>
        </div>
        <button onclick="openModal('issue-pass-modal')" class="flex items-center gap-2 bg-primary text-white px-5 py-2.5 rounded-xl font-bold text-sm shadow-md hover:bg-primary/90 active:scale-95 transition-all">
            <span class="material-symbols-outlined text-[18px]">add_circle</span> Issue New Pass
        </button>
    </div>

    <!-- KPIs -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <?php foreach([
            ["Today's Visitors", $today_visitors, 'pool', 'bg-blue-100 text-blue-700'],
            ['Active Members', $active_members, 'card_membership', 'bg-purple-100 text-purple-700'],
            ['Today Revenue', 'KSh '.number_format((float)$today_revenue), 'payments', 'bg-green-100 text-green-700']
        ] as [$lbl, $val, $ico, $cls]): ?>
        <div class="bg-white rounded-2xl border border-outline-variant/30 shadow-sm p-5 flex items-center justify-between hover:shadow-md transition-all">
            <div><p class="text-xs font-semibold text-on-surface-variant uppercase tracking-wide"><?= $lbl ?></p>
            <h3 class="text-3xl font-extrabold text-on-surface mt-1"><?= $val ?></h3></div>
            <div class="w-12 h-12 rounded-full <?= $cls ?> flex items-center justify-center">
                <span class="material-symbols-outlined text-[22px]"><?= $ico ?></span>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Pool Passes Table -->
    <div class="bg-white rounded-2xl border border-outline-variant/30 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-outline-variant/30 flex items-center justify-between bg-surface-container-low/30">
            <h2 class="font-bold text-base text-on-surface">Recent Passes &amp; Memberships</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead><tr class="bg-surface-container-low/50 border-b border-outline-variant/30">
                    <th class="px-5 py-3.5 text-xs font-bold uppercase text-on-surface">Guest</th>
                    <th class="px-5 py-3.5 text-xs font-bold uppercase text-on-surface">Pass Type</th>
                    <th class="px-5 py-3.5 text-xs font-bold uppercase text-on-surface">Date &amp; Time</th>
                    <th class="px-5 py-3.5 text-xs font-bold uppercase text-on-surface text-right">Amount</th>
                    <th class="px-5 py-3.5 text-xs font-bold uppercase text-on-surface text-center">Status</th>
                    <th class="px-5 py-3.5 text-xs font-bold uppercase text-on-surface text-right">Action</th>
                </tr></thead>
                <tbody class="divide-y divide-outline-variant/20">
                    <?php if(empty($passes)): ?>
                    <tr><td colspan="6" class="text-center py-12 text-on-surface-variant opacity-50">No pool passes issued yet.</td></tr>
                    <?php else: foreach($passes as $p): 
                        $name = $p['customer_name'] ?? $p['guest_name'];
                    ?>
                    <tr class="hover:bg-surface-container-low/40 transition-colors group">
                        <td class="px-5 py-4"><p class="font-bold text-sm text-on-surface"><?= htmlspecialchars($name) ?></p>
                            <p class="text-[11px] text-on-surface-variant"><?= $p['number_of_guests'] ?> Guest(s)</p></td>
                        <td class="px-5 py-4"><span class="px-2.5 py-1 bg-surface-container text-on-surface-variant rounded-md text-[11px] font-bold uppercase"><?= htmlspecialchars($p['pass_type']) ?></span></td>
                        <td class="px-5 py-4">
                            <p class="text-sm"><?= date('M j, Y', strtotime($p['visit_date'])) ?></p>
                            <p class="text-[11px] text-on-surface-variant"><?= $p['entry_time'] ? date('h:i A', strtotime($p['entry_time'])) : '—' ?></p>
                        </td>
                        <td class="px-5 py-4 text-right font-extrabold text-sm">KSh <?= number_format($p['amount']) ?></td>
                        <td class="px-5 py-4 text-center">
                            <?php $sb = match($p['status']) {
                                'Active' => 'bg-green-100 text-green-700 border-green-200',
                                'Expired' => 'bg-gray-100 text-gray-500 border-gray-200',
                                'Cancelled' => 'bg-red-100 text-red-600 border-red-200',
                                default => 'bg-gray-100'
                            }; ?>
                            <span class="px-2.5 py-1 rounded-full border text-[11px] font-bold <?= $sb ?>"><?= $p['status'] ?></span>
                        </td>
                        <td class="px-5 py-4 text-right">
                            <button onclick="deletePass(<?= $p['id'] ?>)" class="p-1.5 text-on-surface-variant hover:text-error transition-colors opacity-0 group-hover:opacity-100"><span class="material-symbols-outlined text-[18px]">delete</span></button>
                        </td>
                    </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>
<?php include '../includes/footer.php'; ?>
</div>

<!-- Issue Pass Modal -->
<div id="issue-pass-modal" class="fixed inset-0 z-[100] hidden items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" onclick="closeModal('issue-pass-modal')"></div>
    <div class="relative bg-white rounded-3xl shadow-2xl w-full max-w-lg mx-auto animate-modal">
        <div class="flex items-center justify-between p-6 border-b border-outline-variant/30">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-primary/10 rounded-xl flex items-center justify-center"><span class="material-symbols-outlined text-primary">pool</span></div>
                <div><h3 class="font-bold text-base">Issue Pool Pass</h3><p class="text-xs text-on-surface-variant">Create a day pass or membership</p></div>
            </div>
            <button onclick="closeModal('issue-pass-modal')" class="w-8 h-8 rounded-full bg-surface-container-high flex items-center justify-center hover:bg-error-container hover:text-error transition-colors"><span class="material-symbols-outlined text-[18px]">close</span></button>
        </div>
        <form id="pass-form" class="p-6 space-y-4">
            <!-- Customer Type Toggle -->
            <div class="flex bg-surface-container-low rounded-xl p-1 mb-4 border border-outline-variant/30">
                <button type="button" onclick="setGuestType('walkin')" id="btn-walkin" class="flex-1 py-1.5 text-xs font-bold rounded-lg bg-primary text-white shadow">Walk-in Guest</button>
                <button type="button" onclick="setGuestType('registered')" id="btn-reg" class="flex-1 py-1.5 text-xs font-bold rounded-lg text-on-surface-variant hover:bg-surface-container">Registered Customer</button>
            </div>

            <div id="walkin-fields">
                <label class="block text-xs font-bold text-on-surface-variant uppercase tracking-wide mb-1.5">Guest Name *</label>
                <input type="text" id="guest_name" class="w-full px-3 py-2.5 rounded-xl border border-outline-variant focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none text-sm bg-surface-container-low"/>
            </div>
            <div id="reg-fields" class="hidden">
                <label class="block text-xs font-bold text-on-surface-variant uppercase tracking-wide mb-1.5">Select Customer *</label>
                <select id="customer_id" class="w-full px-3 py-2.5 rounded-xl border border-outline-variant focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none text-sm bg-surface-container-low appearance-none">
                    <option value="">-- Select --</option>
                    <?php foreach($customers as $c): ?>
                    <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['full_name']) ?> (<?= htmlspecialchars($c['phone']) ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div><label class="block text-xs font-bold text-on-surface-variant uppercase tracking-wide mb-1.5">Pass Type *</label>
                <select id="pass_type" required onchange="calcAmount()" class="w-full px-3 py-2.5 rounded-xl border border-outline-variant focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none text-sm bg-surface-container-low appearance-none">
                    <option value="Daily Adult" data-price="1000">Daily Adult - KSh 1,000</option>
                    <option value="Daily Child" data-price="500">Daily Child - KSh 500</option>
                    <option value="Monthly" data-price="15000">Monthly Membership - KSh 15,000</option>
                    <option value="Annual" data-price="120000">Annual Membership - KSh 120,000</option>
                </select></div>

                <div><label class="block text-xs font-bold text-on-surface-variant uppercase tracking-wide mb-1.5">No. of Guests</label>
                <input type="number" id="number_of_guests" value="1" min="1" required oninput="calcAmount()" class="w-full px-3 py-2.5 rounded-xl border border-outline-variant focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none text-sm bg-surface-container-low"/></div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div><label class="block text-xs font-bold text-on-surface-variant uppercase tracking-wide mb-1.5">Visit Date</label>
                <input type="date" id="visit_date" required class="w-full px-3 py-2.5 rounded-xl border border-outline-variant focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none text-sm bg-surface-container-low"/></div>
                <div><label class="block text-xs font-bold text-on-surface-variant uppercase tracking-wide mb-1.5">Total Amount (KSh)</label>
                <input type="number" id="amount" required readonly class="w-full px-3 py-2.5 rounded-xl border border-outline-variant outline-none text-sm bg-surface-container font-extrabold text-primary"/></div>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="button" onclick="closeModal('issue-pass-modal')" class="flex-1 py-3 rounded-xl border border-outline-variant text-on-surface-variant font-semibold text-sm hover:bg-surface-container transition-colors">Cancel</button>
                <button type="submit" id="pass-btn" class="flex-1 py-3 rounded-xl bg-primary text-white font-bold text-sm hover:bg-primary/90 shadow-md active:scale-95 transition-all flex items-center justify-center gap-2">
                    <span class="material-symbols-outlined text-[18px]">qr_code</span> Issue Pass
                </button>
            </div>
        </form>
    </div>
</div>

<style>
@keyframes modal-in{from{opacity:0;transform:scale(0.95) translateY(20px)}to{opacity:1;transform:scale(1) translateY(0)}}
.animate-modal{animation:modal-in 0.25s cubic-bezier(0.2,0.8,0.2,1) forwards}
</style>
<script>
function openModal(id){document.getElementById(id).classList.remove('hidden');document.getElementById(id).classList.add('flex');}
function closeModal(id){document.getElementById(id).classList.add('hidden');document.getElementById(id).classList.remove('flex');}
function showSuccess(m){Swal.fire({icon:'success',title:'Success',text:m,timer:2000,toast:true,position:'top-end',showConfirmButton:false});}
function showError(m){Swal.fire({icon:'error',title:'Error',text:m});}

let isWalkin = true;
function setGuestType(t) {
    isWalkin = (t === 'walkin');
    const w = document.getElementById('btn-walkin'), r = document.getElementById('btn-reg');
    const wf = document.getElementById('walkin-fields'), rf = document.getElementById('reg-fields');
    if (isWalkin) {
        w.className = 'flex-1 py-1.5 text-xs font-bold rounded-lg bg-primary text-white shadow';
        r.className = 'flex-1 py-1.5 text-xs font-bold rounded-lg text-on-surface-variant hover:bg-surface-container';
        wf.classList.remove('hidden'); rf.classList.add('hidden');
    } else {
        r.className = 'flex-1 py-1.5 text-xs font-bold rounded-lg bg-primary text-white shadow';
        w.className = 'flex-1 py-1.5 text-xs font-bold rounded-lg text-on-surface-variant hover:bg-surface-container';
        rf.classList.remove('hidden'); wf.classList.add('hidden');
    }
}

function calcAmount() {
    const sel = document.getElementById('pass_type');
    const price = sel.options[sel.selectedIndex].dataset.price;
    const qty = document.getElementById('number_of_guests').value || 1;
    document.getElementById('amount').value = price * qty;
}

document.getElementById('visit_date').valueAsDate = new Date();
calcAmount();

document.getElementById('pass-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    const btn = document.getElementById('pass-btn'); btn.disabled=true; btn.innerHTML='<span class="material-symbols-outlined animate-spin text-[18px]">sync</span> Processing...';
    
    const payload = {
        pass_type: document.getElementById('pass_type').value,
        visit_date: document.getElementById('visit_date').value,
        number_of_guests: document.getElementById('number_of_guests').value,
        amount: document.getElementById('amount').value
    };
    if (isWalkin) { payload.guest_name = document.getElementById('guest_name').value; }
    else { payload.customer_id = document.getElementById('customer_id').value; }

    try {
        const res = await fetch('../api/pool.php', {method:'POST', headers:{'Content-Type':'application/json'}, body:JSON.stringify(payload)});
        const json = await res.json();
        if(json.success) {
            closeModal('issue-pass-modal');
            Swal.fire({
                icon:'success',
                title:'Pass Issued! ✅',
                text:'Pool pass generated successfully.',
                showCancelButton: true,
                confirmButtonText: '<span class="material-symbols-outlined align-middle mr-1 text-[18px]">print</span> Print Pass',
                cancelButtonText: 'Close',
                confirmButtonColor: '#2563eb'
            }).then((result) => {
                if (result.isConfirmed && json.pass_id) {
                    window.open('print_receipt.php?type=pool&id=' + json.pass_id, '_blank');
                } else {
                    location.reload();
                }
            });
        }
        else showError(json.message);
    } catch(e) { showError('Network error'); }
});

function deletePass(id) {
    Swal.fire({title:`Delete Pass?`, text:"Remove this pass from the system.", icon:"warning", showCancelButton:true, confirmButtonColor:"#ef4444", confirmButtonText:"Delete"})
    .then(async (res) => {
        if(res.isConfirmed) {
            const r = await fetch(`../api/pool.php?id=${id}`, {method:'DELETE'});
            const j = await r.json();
            if(j.success) { showSuccess(j.message); setTimeout(()=>location.reload(), 1500); }
            else showError(j.message);
        }
    });
}
</script>
</body></html>
