<?php
$page_title = "Customers | SkopeStay CRM";
require_once '../includes/config.php';

// Fetch customers & basic stats
$customers = $pdo->query("SELECT * FROM customers ORDER BY created_at DESC")->fetchAll();
$total_customers = count($customers);
$new_this_month  = $pdo->query("SELECT COUNT(*) FROM customers WHERE MONTH(created_at)=MONTH(CURDATE()) AND YEAR(created_at)=YEAR(CURDATE())")->fetchColumn();
$loyalty_members = count(array_filter($customers, fn($c) => $c['loyalty_tier'] !== 'Standard'));

// Helper to get initials
function getInitials($name) {
    $parts = explode(' ', trim($name));
    if (count($parts) > 1) return strtoupper(substr($parts[0],0,1).substr(end($parts),0,1));
    return strtoupper(substr($name,0,2));
}

include '../includes/head.php';
?>
<!DOCTYPE html>
<html class="light" lang="en">
<body class="bg-background text-on-surface font-body-md min-h-screen">
<?php include '../includes/sidebar.php'; ?>

<div class="md:ml-64 flex flex-col min-h-screen">
<?php include '../includes/header.php'; ?>

<main class="flex-1 p-4 md:p-8 space-y-6">

    <!-- Header & Actions -->
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
            <nav class="flex items-center gap-2 text-xs font-semibold text-on-surface-variant mb-2">
                <span>CRM</span><span class="material-symbols-outlined text-[14px]">chevron_right</span>
                <span class="text-primary font-bold">Customers</span>
            </nav>
            <h1 class="text-2xl md:text-3xl font-extrabold text-on-surface">Customer Directory</h1>
            <p class="text-on-surface-variant text-sm mt-1">Manage guest profiles, view history, and handle loyalty programs.</p>
        </div>
        <button onclick="openModal('add-customer-modal')" class="flex items-center gap-2 bg-primary text-white px-5 py-2.5 rounded-xl font-bold text-sm shadow-md hover:bg-primary/90 active:scale-95 transition-all">
            <span class="material-symbols-outlined text-[18px]">person_add</span> New Customer
        </button>
    </div>

    <!-- KPIs -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <?php foreach([
            ['Total Customers', $total_customers, 'group', 'bg-primary/10 text-primary'],
            ['New This Month', $new_this_month, 'person_add', 'bg-green-100 text-green-700'],
            ['Loyalty Members', $loyalty_members, 'stars', 'bg-amber-100 text-amber-700']
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

    <!-- Data Table -->
    <div class="bg-white rounded-2xl border border-outline-variant/30 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-outline-variant/30 flex flex-wrap items-center gap-3 bg-surface-container-low/30">
            <div class="relative flex-1 min-w-[220px]">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline text-[18px]">search</span>
                <input type="text" id="cust-search" oninput="filterCustomers()" placeholder="Search by name, email, or phone..." class="w-full pl-9 pr-4 py-2.5 rounded-xl border border-outline-variant text-sm bg-white focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all"/>
            </div>
            <button class="px-4 py-2.5 bg-surface-container rounded-xl border border-outline-variant text-sm font-semibold hover:bg-surface-container-high transition-colors flex items-center gap-2">
                <span class="material-symbols-outlined text-[18px]">filter_list</span> Filter
            </button>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead><tr class="bg-surface-container-low/50 border-b border-outline-variant/30">
                    <th class="px-5 py-3.5 text-xs font-bold uppercase text-on-surface">Customer</th>
                    <th class="px-5 py-3.5 text-xs font-bold uppercase text-on-surface hidden sm:table-cell">Contact</th>
                    <th class="px-5 py-3.5 text-xs font-bold uppercase text-on-surface">Category</th>
                    <th class="px-5 py-3.5 text-xs font-bold uppercase text-on-surface text-center">Loyalty</th>
                    <th class="px-5 py-3.5 text-xs font-bold uppercase text-on-surface text-right">Actions</th>
                </tr></thead>
                <tbody class="divide-y divide-outline-variant/20" id="cust-table">
                    <?php if(empty($customers)): ?>
                    <tr><td colspan="5" class="text-center py-12 text-on-surface-variant opacity-50">No customers found.</td></tr>
                    <?php else: foreach($customers as $c): ?>
                    <tr class="hover:bg-surface-container-low/40 transition-colors group cust-row" data-name="<?= strtolower(htmlspecialchars($c['full_name'].' '.$c['email'].' '.$c['phone'])) ?>">
                        <td class="px-5 py-4"><div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-primary/10 text-primary flex items-center justify-center font-bold text-sm shadow-sm"><?= getInitials($c['full_name']) ?></div>
                            <div><p class="font-bold text-sm"><?= htmlspecialchars($c['full_name']) ?></p>
                            <p class="text-[11px] text-on-surface-variant">ID: <?= htmlspecialchars($c['national_id']??'N/A') ?></p></div>
                        </div></td>
                        <td class="px-5 py-4 hidden sm:table-cell">
                            <p class="text-sm"><?= htmlspecialchars($c['phone']??'—') ?></p>
                            <p class="text-[11px] text-on-surface-variant"><?= htmlspecialchars($c['email']??'—') ?></p>
                        </td>
                        <td class="px-5 py-4"><span class="px-2.5 py-1 bg-surface-container text-on-surface-variant rounded-md text-[11px] font-bold uppercase"><?= htmlspecialchars($c['customer_category']) ?></span></td>
                        <td class="px-5 py-4 text-center">
                            <?php 
                            $badge = match($c['loyalty_tier']) {
                                'Gold' => 'bg-amber-100 text-amber-700 border-amber-200',
                                'Silver' => 'bg-slate-100 text-slate-700 border-slate-200',
                                'Platinum' => 'bg-purple-100 text-purple-700 border-purple-200',
                                default => 'bg-gray-50 text-gray-500 border-gray-200'
                            };
                            ?>
                            <span class="px-2.5 py-1 rounded-full border text-[11px] font-bold <?= $badge ?> flex items-center justify-center gap-1 w-max mx-auto">
                                <?php if($c['loyalty_tier']!=='Standard') echo '<span class="material-symbols-outlined text-[12px]">stars</span>'; ?>
                                <?= htmlspecialchars($c['loyalty_tier']) ?>
                            </span>
                        </td>
                        <td class="px-5 py-4 text-right">
                            <div class="flex items-center justify-end gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                <button onclick="viewCustomer(<?= $c['id'] ?>)" class="p-1.5 text-on-surface-variant hover:text-primary transition-colors" title="View Profile"><span class="material-symbols-outlined text-[18px]">visibility</span></button>
                                <button onclick="deleteCustomer(<?= $c['id'] ?>,'<?= addslashes($c['full_name']) ?>')" class="p-1.5 text-on-surface-variant hover:text-error transition-colors" title="Delete"><span class="material-symbols-outlined text-[18px]">delete</span></button>
                            </div>
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

<!-- Add Customer Modal -->
<div id="add-customer-modal" class="fixed inset-0 z-[100] hidden items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" onclick="closeModal('add-customer-modal')"></div>
    <div class="relative bg-white rounded-3xl shadow-2xl w-full max-w-lg mx-auto animate-modal">
        <div class="flex items-center justify-between p-6 border-b border-outline-variant/30">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-primary/10 rounded-xl flex items-center justify-center"><span class="material-symbols-outlined text-primary">person_add</span></div>
                <div><h3 class="font-bold text-base">New Customer Profile</h3><p class="text-xs text-on-surface-variant">Register a new guest or client</p></div>
            </div>
            <button onclick="closeModal('add-customer-modal')" class="w-8 h-8 rounded-full bg-surface-container-high flex items-center justify-center hover:bg-error-container hover:text-error transition-colors"><span class="material-symbols-outlined text-[18px]">close</span></button>
        </div>
        <form id="cust-form" class="p-6 space-y-4">
            <div class="grid grid-cols-2 gap-4">
                <div class="col-span-2"><label class="block text-xs font-bold text-on-surface-variant uppercase tracking-wide mb-1.5">Full Name *</label>
                <input type="text" name="full_name" required class="w-full px-3 py-2.5 rounded-xl border border-outline-variant focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none text-sm bg-surface-container-low"/></div>
                
                <div><label class="block text-xs font-bold text-on-surface-variant uppercase tracking-wide mb-1.5">Phone Number</label>
                <input type="text" name="phone" class="w-full px-3 py-2.5 rounded-xl border border-outline-variant focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none text-sm bg-surface-container-low"/></div>
                
                <div><label class="block text-xs font-bold text-on-surface-variant uppercase tracking-wide mb-1.5">Email Address</label>
                <input type="email" name="email" class="w-full px-3 py-2.5 rounded-xl border border-outline-variant focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none text-sm bg-surface-container-low"/></div>

                <div><label class="block text-xs font-bold text-on-surface-variant uppercase tracking-wide mb-1.5">National ID / Passport</label>
                <input type="text" name="national_id" class="w-full px-3 py-2.5 rounded-xl border border-outline-variant focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none text-sm bg-surface-container-low"/></div>

                <div><label class="block text-xs font-bold text-on-surface-variant uppercase tracking-wide mb-1.5">Category</label>
                <select name="customer_category" class="w-full px-3 py-2.5 rounded-xl border border-outline-variant focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none text-sm bg-surface-container-low appearance-none">
                    <option>Hotel Guest</option><option>Pool Visitor</option><option>Restaurant Customer</option><option>Event Organizer</option><option>Corporate Client</option><option>VIP Customer</option>
                </select></div>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="closeModal('add-customer-modal')" class="flex-1 py-3 rounded-xl border border-outline-variant text-on-surface-variant font-semibold text-sm hover:bg-surface-container transition-colors">Cancel</button>
                <button type="submit" id="cust-btn" class="flex-1 py-3 rounded-xl bg-primary text-white font-bold text-sm hover:bg-primary/90 shadow-md active:scale-95 transition-all flex items-center justify-center gap-2">
                    <span class="material-symbols-outlined text-[18px]">save</span> Save Profile
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Customer Profile Modal -->
<div id="profile-modal" class="fixed inset-0 z-[100] hidden items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" onclick="closeModal('profile-modal')"></div>
    <div class="relative bg-white rounded-3xl shadow-2xl w-full max-w-2xl mx-auto animate-modal flex flex-col max-h-[90vh]">
        <div class="p-6 border-b border-outline-variant/30 flex justify-between items-start bg-surface-container-lowest rounded-t-3xl flex-shrink-0">
            <div class="flex items-center gap-4">
                <div id="prof-ini" class="w-14 h-14 rounded-full bg-primary text-white flex items-center justify-center font-extrabold text-xl shadow-md"></div>
                <div><h3 id="prof-name" class="font-extrabold text-xl text-on-surface"></h3>
                <p id="prof-email" class="text-sm text-on-surface-variant"></p></div>
            </div>
            <button onclick="closeModal('profile-modal')" class="w-8 h-8 rounded-full bg-surface-container-high flex items-center justify-center hover:bg-error-container hover:text-error transition-colors"><span class="material-symbols-outlined text-[18px]">close</span></button>
        </div>
        <div class="p-6 overflow-y-auto flex-1 bg-surface-container-lowest rounded-b-3xl space-y-6">
            <!-- Stats -->
            <div class="grid grid-cols-3 gap-4">
                <div class="bg-surface-container-low p-4 rounded-2xl border border-outline-variant/30 text-center"><span class="material-symbols-outlined text-primary mb-1">bed</span><p class="text-[10px] uppercase font-bold text-on-surface-variant tracking-wider">Room Bookings</p><p id="stat-rooms" class="text-2xl font-extrabold">0</p></div>
                <div class="bg-surface-container-low p-4 rounded-2xl border border-outline-variant/30 text-center"><span class="material-symbols-outlined text-secondary mb-1">domain</span><p class="text-[10px] uppercase font-bold text-on-surface-variant tracking-wider">Event Halls</p><p id="stat-halls" class="text-2xl font-extrabold">0</p></div>
                <div class="bg-surface-container-low p-4 rounded-2xl border border-outline-variant/30 text-center"><span class="material-symbols-outlined text-blue-500 mb-1">pool</span><p class="text-[10px] uppercase font-bold text-on-surface-variant tracking-wider">Pool Visits</p><p id="stat-pool" class="text-2xl font-extrabold">0</p></div>
            </div>
            <!-- Details -->
            <div>
                <h4 class="font-bold text-sm mb-3 text-on-surface flex items-center gap-2"><span class="material-symbols-outlined text-[18px]">badge</span> Personal Info</h4>
                <div class="bg-white border border-outline-variant/30 rounded-xl divide-y divide-outline-variant/20">
                    <div class="flex justify-between p-3 text-sm"><span class="text-on-surface-variant">Phone</span><span id="prof-phone" class="font-semibold"></span></div>
                    <div class="flex justify-between p-3 text-sm"><span class="text-on-surface-variant">National ID</span><span id="prof-nid" class="font-semibold"></span></div>
                    <div class="flex justify-between p-3 text-sm"><span class="text-on-surface-variant">Loyalty Tier</span><span id="prof-tier" class="font-bold text-primary"></span></div>
                    <div class="flex justify-between p-3 text-sm"><span class="text-on-surface-variant">Category</span><span id="prof-cat" class="font-semibold"></span></div>
                </div>
            </div>
            <!-- Actions -->
            <div class="grid grid-cols-2 gap-3 pt-2 border-t border-outline-variant/30">
                <button class="py-3 bg-surface-container-low rounded-xl text-sm font-bold text-on-surface-variant hover:bg-surface-container border border-outline-variant flex justify-center items-center gap-2"><span class="material-symbols-outlined text-[18px]">receipt_long</span> Statement</button>
                <button class="py-3 bg-primary/10 text-primary rounded-xl text-sm font-bold hover:bg-primary/20 flex justify-center items-center gap-2"><span class="material-symbols-outlined text-[18px]">mail</span> Send Message</button>
            </div>
        </div>
    </div>
</div>

<style>
@keyframes modal-in{from{opacity:0;transform:scale(0.95) translateY(20px)}to{opacity:1;transform:scale(1) translateY(0)}}
.animate-modal{animation:modal-in 0.25s cubic-bezier(0.2,0.8,0.2,1) forwards}
</style>
<script>
function openModal(id){document.getElementById(id).classList.remove('hidden');document.getElementById(id).classList.add('flex');}
function closeModal(id){document.getElementById(id).classList.add('hidden');document.getElementById(id).classList.remove('flex');}
function showSuccess(m){Swal.fire({icon:'success',title:'Success',text:m,timer:2500,toast:true,position:'top-end',showConfirmButton:false});}
function showError(m){Swal.fire({icon:'error',title:'Error',text:m});}

function filterCustomers() {
    const q = document.getElementById('cust-search').value.toLowerCase();
    document.querySelectorAll('.cust-row').forEach(r => r.style.display = r.dataset.name.includes(q) ? '' : 'none');
}

document.getElementById('cust-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    const btn=document.getElementById('cust-btn'); btn.disabled=true; btn.innerHTML='<span class="material-symbols-outlined animate-spin text-[18px]">sync</span> Saving...';
    try {
        const data = Object.fromEntries(new FormData(this));
        const res = await fetch('../api/customers.php', {method:'POST', headers:{'Content-Type':'application/json'}, body:JSON.stringify(data)});
        const json = await res.json();
        if (json.success) { closeModal('add-customer-modal'); showSuccess(json.message); setTimeout(()=>location.reload(), 1500); }
        else showError(json.message);
    } catch(err) { showError('Network error'); }
    btn.disabled=false; btn.innerHTML='<span class="material-symbols-outlined text-[18px]">save</span> Save Profile';
});

async function viewCustomer(id) {
    try {
        const res = await fetch('../api/customers.php?id=' + id);
        const json = await res.json();
        if(json.success) {
            const d = json.data, s = json.stats;
            document.getElementById('prof-name').textContent = d.full_name;
            document.getElementById('prof-ini').textContent = d.full_name.substring(0,2).toUpperCase();
            document.getElementById('prof-email').textContent = d.email || 'No email provided';
            document.getElementById('prof-phone').textContent = d.phone || '—';
            document.getElementById('prof-nid').textContent = d.national_id || '—';
            document.getElementById('prof-tier').textContent = d.loyalty_tier;
            document.getElementById('prof-cat').textContent = d.customer_category;
            
            document.getElementById('stat-rooms').textContent = s.room_bookings;
            document.getElementById('stat-halls').textContent = s.hall_bookings;
            document.getElementById('stat-pool').textContent = s.pool_visits;
            openModal('profile-modal');
        } else showError(json.message);
    } catch(e) { showError('Failed to load profile'); }
}

function deleteCustomer(id, name) {
    Swal.fire({title:`Delete ${name}?`, text:"This will permanently remove this customer profile.", icon:"warning", showCancelButton:true, confirmButtonColor:"#ef4444", confirmButtonText:"Delete"})
    .then(async (res) => {
        if(res.isConfirmed) {
            const r = await fetch(`../api/customers.php?id=${id}`, {method:'DELETE'});
            const j = await r.json();
            if(j.success) { showSuccess(j.message); setTimeout(()=>location.reload(), 1500); }
            else showError(j.message);
        }
    });
}
</script>
</body></html>
