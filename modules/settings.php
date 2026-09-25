<?php
$page_title = "Settings | SkopeStay";
require_once '../includes/config.php';

// Handle save actions
$success_msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $success_msg = 'Settings saved successfully!';
}

// Fetch staff users
$staff = $pdo->query("SELECT * FROM users ORDER BY created_at ASC")->fetchAll();

include '../includes/head.php';
?>
<!DOCTYPE html>
<html class="light" lang="en">
<body class="bg-background text-on-surface font-body-md min-h-screen">
<?php include '../includes/sidebar.php'; ?>
<div class="md:ml-64 flex flex-col min-h-screen">
<?php include '../includes/header.php'; ?>
<main class="flex-1 p-4 md:p-8 space-y-6">

    <div class="mb-2">
        <h1 class="text-2xl md:text-3xl font-extrabold text-on-surface">System Settings</h1>
        <p class="text-on-surface-variant text-sm mt-1">Configure your SkopeStay environment, staff permissions, and financial rules.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

        <!-- Tab Navigation -->
        <div class="lg:col-span-3 space-y-2">
            <?php
            $tabs = [
                ['id'=>'general',       'icon'=>'tune',                 'label'=>'General'],
                ['id'=>'users',         'icon'=>'badge',                'label'=>'User Management'],
                ['id'=>'rooms',         'icon'=>'sell',                 'label'=>'Rooms & Rates'],
                ['id'=>'notifications', 'icon'=>'notifications_active', 'label'=>'Notifications'],
                ['id'=>'security',      'icon'=>'verified_user',        'label'=>'Security'],
            ];
            foreach ($tabs as $i => $tab): ?>
            <button onclick="switchTab('<?= $tab['id'] ?>')" id="tab-<?= $tab['id'] ?>"
                class="w-full flex items-center justify-between px-5 py-4 bg-white border rounded-2xl shadow-sm transition-all hover:border-primary active:scale-[0.98] settings-tab <?= $i===0 ? 'border-primary' : 'border-outline-variant/30' ?>">
                <div class="flex items-center gap-3">
                    <span class="material-symbols-outlined <?= $i===0 ? 'text-primary' : 'text-on-surface-variant' ?> tab-icon"><?= $tab['icon'] ?></span>
                    <span class="font-semibold text-sm text-on-surface"><?= $tab['label'] ?></span>
                </div>
                <span class="material-symbols-outlined text-on-surface-variant text-[18px]">chevron_right</span>
            </button>
            <?php endforeach; ?>
        </div>

        <!-- Settings Panels -->
        <div class="lg:col-span-9 bg-white rounded-2xl border border-outline-variant/30 shadow-sm overflow-hidden">

            <!-- General -->
            <div id="content-general" class="settings-panel p-6 md:p-8">
                <div class="flex justify-between items-start mb-8">
                    <div>
                        <h3 class="font-bold text-xl text-on-surface">General Settings</h3>
                        <p class="text-on-surface-variant text-sm mt-1">Basic property identity and regional localization.</p>
                    </div>
                    <button onclick="saveSettings('general')" class="px-5 py-2.5 bg-primary text-white rounded-xl font-bold text-sm shadow hover:bg-primary/90 active:scale-95 transition-all">Save Changes</button>
                </div>
                <form class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-on-surface-variant uppercase tracking-wide mb-1.5">Property Name</label>
                        <input type="text" value="SkopeStay Premium Resorts &amp; Spa" class="w-full px-4 py-2.5 rounded-xl border border-outline-variant bg-surface-container-low focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none text-sm transition-all"/>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-on-surface-variant uppercase tracking-wide mb-1.5">Contact Email</label>
                        <input type="email" value="admin@skopestay.com" class="w-full px-4 py-2.5 rounded-xl border border-outline-variant bg-surface-container-low focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none text-sm transition-all"/>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-on-surface-variant uppercase tracking-wide mb-1.5">Phone Number</label>
                        <input type="text" value="+254 700 000000" class="w-full px-4 py-2.5 rounded-xl border border-outline-variant bg-surface-container-low focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none text-sm transition-all"/>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-on-surface-variant uppercase tracking-wide mb-1.5">Timezone</label>
                        <select class="w-full px-4 py-2.5 rounded-xl border border-outline-variant bg-surface-container-low focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none text-sm appearance-none transition-all">
                            <option>(GMT+03:00) East Africa Time</option>
                            <option>(GMT+00:00) UTC</option>
                            <option>(GMT+01:00) London</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-on-surface-variant uppercase tracking-wide mb-1.5">Currency</label>
                        <select class="w-full px-4 py-2.5 rounded-xl border border-outline-variant bg-surface-container-low focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none text-sm appearance-none transition-all">
                            <option>KES (KSh)</option>
                            <option>USD ($)</option>
                            <option>EUR (€)</option>
                        </select>
                    </div>
                </form>
            </div>

            <!-- User Management -->
            <div id="content-users" class="settings-panel hidden p-6 md:p-8">
                <div class="flex justify-between items-start mb-8">
                    <div>
                        <h3 class="font-bold text-xl text-on-surface">User Management</h3>
                        <p class="text-on-surface-variant text-sm mt-1">Manage staff access and permission levels.</p>
                    </div>
                    <button onclick="openModal('add-user-modal')" class="flex items-center gap-2 px-5 py-2.5 bg-secondary-container text-on-secondary-container rounded-xl font-bold text-sm hover:bg-secondary hover:text-white active:scale-95 transition-all">
                        <span class="material-symbols-outlined text-[18px]">person_add</span> Add User
                    </button>
                </div>
                <div class="rounded-2xl border border-outline-variant/30 overflow-hidden">
                    <table class="w-full text-left">
                        <thead><tr class="bg-surface-container-low/50 border-b border-outline-variant/30">
                            <th class="px-5 py-3.5 text-xs font-bold uppercase text-on-surface">Staff Member</th>
                            <th class="px-5 py-3.5 text-xs font-bold uppercase text-on-surface">Role</th>
                            <th class="px-5 py-3.5 text-xs font-bold uppercase text-on-surface">Status</th>
                            <th class="px-5 py-3.5 text-xs font-bold uppercase text-on-surface text-right">Actions</th>
                        </tr></thead>
                        <tbody class="divide-y divide-outline-variant/20">
                        <?php foreach ($staff as $u):
                            $ini = strtoupper(substr($u['username'],0,2));
                        ?>
                        <tr class="hover:bg-surface-container-low/40 transition-colors">
                            <td class="px-5 py-4"><div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-full bg-primary text-white flex items-center justify-center font-bold text-xs"><?= $ini ?></div>
                                <div><p class="font-semibold text-sm"><?= htmlspecialchars(ucfirst($u['username'])) ?></p>
                                <p class="text-[11px] text-on-surface-variant"><?= htmlspecialchars($u['email']) ?></p></div>
                            </div></td>
                            <td class="px-5 py-4"><span class="px-2.5 py-1 bg-surface-container-high text-on-surface-variant rounded-lg text-[11px] font-bold uppercase"><?= htmlspecialchars($u['role']) ?></span></td>
                            <td class="px-5 py-4"><span class="px-2.5 py-1 bg-green-100 text-green-700 border border-green-200 rounded-full text-[11px] font-bold">ACTIVE</span></td>
                            <td class="px-5 py-4 text-right">
                                <button class="p-1.5 text-on-surface-variant hover:text-primary transition-colors"><span class="material-symbols-outlined text-[18px]">edit</span></button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Rooms & Rates -->
            <div id="content-rooms" class="settings-panel hidden p-6 md:p-8">
                <div class="flex justify-between items-start mb-8">
                    <div><h3 class="font-bold text-xl text-on-surface">Rooms &amp; Rates</h3>
                    <p class="text-on-surface-variant text-sm mt-1">Control pricing strategies and taxation rules.</p></div>
                    <button onclick="saveSettings('rates')" class="px-5 py-2.5 bg-primary text-white rounded-xl font-bold text-sm shadow hover:bg-primary/90 active:scale-95 transition-all">Save Changes</button>
                </div>
                <div class="space-y-5">
                    <div class="p-5 bg-surface-container-low rounded-2xl border border-outline-variant/30">
                        <h4 class="font-bold text-sm text-on-surface mb-4">Base Configuration</h4>
                        <div class="grid grid-cols-2 gap-4">
                            <div><label class="block text-xs font-bold text-on-surface-variant uppercase tracking-wide mb-1.5">Global Tax Rate (%)</label>
                            <input type="number" value="16" class="w-full px-4 py-2.5 rounded-xl border border-outline-variant bg-white focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none text-sm"/></div>
                            <div><label class="block text-xs font-bold text-on-surface-variant uppercase tracking-wide mb-1.5">Service Charge (%)</label>
                            <input type="number" value="5" class="w-full px-4 py-2.5 rounded-xl border border-outline-variant bg-white focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none text-sm"/></div>
                        </div>
                    </div>
                    <div class="p-5 bg-surface-container-low rounded-2xl border border-outline-variant/30">
                        <div class="flex justify-between items-center mb-4">
                            <h4 class="font-bold text-sm text-on-surface">Seasonal Pricing Rules</h4>
                            <button class="text-primary text-xs font-bold hover:underline">+ New Rule</button>
                        </div>
                        <div class="space-y-3">
                            <?php foreach ([['Summer Peak','Jun 01 – Aug 31','+25%'],['Holiday Exclusive','Dec 20 – Jan 05','+40%']] as [$name,$range,$pct]): ?>
                            <div class="flex items-center justify-between p-4 bg-white rounded-xl border border-outline-variant/30">
                                <div><p class="font-semibold text-sm"><?= $name ?></p><p class="text-[11px] text-on-surface-variant uppercase"><?= $range ?></p></div>
                                <div class="flex items-center gap-3"><span class="font-bold text-green-600 text-sm"><?= $pct ?> Markup</span>
                                <span class="material-symbols-outlined text-on-surface-variant text-[20px] cursor-grab">drag_indicator</span></div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Notifications -->
            <div id="content-notifications" class="settings-panel hidden p-6 md:p-8">
                <div class="flex justify-between items-start mb-8">
                    <div><h3 class="font-bold text-xl text-on-surface">Notification Preferences</h3>
                    <p class="text-on-surface-variant text-sm mt-1">Choose how and when you want to be notified.</p></div>
                    <button onclick="saveSettings('notifications')" class="px-5 py-2.5 bg-primary text-white rounded-xl font-bold text-sm shadow hover:bg-primary/90 active:scale-95 transition-all">Save Changes</button>
                </div>
                <div class="rounded-2xl border border-outline-variant/30 divide-y divide-outline-variant/20 overflow-hidden">
                    <?php foreach ([
                        ['New Bookings','Get alerted when a new guest makes a reservation.','book_online'],
                        ['Financial Milestones','Weekly revenue reports and invoice alerts.','payments'],
                        ['Low Stock Warning','Alerts when inventory items are running low.','warning'],
                        ['Maintenance Alerts','Notify when a room needs maintenance.','build'],
                    ] as [$title,$desc,$icon]): ?>
                    <div class="p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div class="flex items-center gap-4">
                            <div class="w-11 h-11 rounded-xl bg-surface-container-high flex items-center justify-center flex-shrink-0">
                                <span class="material-symbols-outlined text-secondary text-[20px]"><?= $icon ?></span>
                            </div>
                            <div><p class="font-semibold text-sm"><?= $title ?></p><p class="text-xs text-on-surface-variant mt-0.5"><?= $desc ?></p></div>
                        </div>
                        <div class="flex gap-5 flex-shrink-0">
                            <?php foreach (['Email','SMS','Push'] as $ch): ?>
                            <label class="flex flex-col items-center gap-1 cursor-pointer">
                                <input type="checkbox" checked class="w-4 h-4 accent-primary rounded border-outline-variant"/>
                                <span class="text-[10px] font-bold text-on-surface-variant uppercase"><?= $ch ?></span>
                            </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Security -->
            <div id="content-security" class="settings-panel hidden p-6 md:p-8">
                <div class="flex justify-between items-start mb-8">
                    <div><h3 class="font-bold text-xl text-on-surface">Security &amp; Privacy</h3>
                    <p class="text-on-surface-variant text-sm mt-1">Protect your account and review active sessions.</p></div>
                    <button onclick="saveSettings('security')" class="px-5 py-2.5 bg-primary text-white rounded-xl font-bold text-sm shadow hover:bg-primary/90 active:scale-95 transition-all">Save Changes</button>
                </div>
                <div class="space-y-5">
                    <div class="p-5 bg-surface-container-low rounded-2xl border border-outline-variant/30">
                        <h4 class="font-bold text-sm text-on-surface mb-1">Two-Factor Authentication</h4>
                        <p class="text-xs text-on-surface-variant mb-4">Add an extra layer of security to your account.</p>
                        <div class="flex items-center gap-3">
                            <button onclick="showSuccess('2FA setup initiated! Check your email.')" class="px-5 py-2 bg-primary text-white rounded-xl font-bold text-sm shadow hover:bg-primary/90 active:scale-95 transition-all">Enable 2FA</button>
                            <span class="text-xs font-bold text-error uppercase bg-error-container px-3 py-1 rounded-full">Currently Disabled</span>
                        </div>
                    </div>
                    <div>
                        <h4 class="font-bold text-sm text-on-surface mb-3">Active Login Sessions</h4>
                        <div class="rounded-2xl border border-outline-variant/30 overflow-hidden divide-y divide-outline-variant/20">
                            <?php foreach ([
                                ['Chrome on Windows','Current Session','desktop_windows','New York, USA','ONLINE','bg-green-100 text-green-700'],
                                ['SkopeStay Mobile App','2 hours ago','smartphone','Nairobi, KE','IDLE','bg-amber-100 text-amber-700'],
                            ] as [$device,$time,$icon,$loc,$badge,$bcls]): ?>
                            <div class="p-4 flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <span class="material-symbols-outlined text-secondary"><?= $icon ?></span>
                                    <div><p class="font-semibold text-sm"><?= $device ?></p>
                                    <p class="text-[11px] text-on-surface-variant"><?= $loc ?> • <?= $time ?></p></div>
                                </div>
                                <span class="px-2.5 py-1 rounded-full text-[11px] font-bold <?= $bcls ?>"><?= $badge ?></span>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        <button onclick="showConfirmLogout()" class="mt-4 text-error font-bold text-sm hover:underline flex items-center gap-1.5">
                            <span class="material-symbols-outlined text-[18px]">logout</span> Log out of all other sessions
                        </button>
                    </div>
                </div>
            </div>

        </div><!-- end panels container -->
    </div>
</main>
<?php include '../includes/footer.php'; ?>
</div>

<!-- Add User Modal -->
<div id="add-user-modal" class="fixed inset-0 z-[100] hidden items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" onclick="closeModal('add-user-modal')"></div>
    <div class="relative bg-white rounded-3xl shadow-2xl w-full max-w-md mx-auto animate-modal">
        <div class="flex items-center justify-between p-6 border-b border-outline-variant/30">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-primary/10 rounded-xl flex items-center justify-center"><span class="material-symbols-outlined text-primary">person_add</span></div>
                <div><h3 class="font-bold text-base">Add Staff User</h3><p class="text-xs text-on-surface-variant">Create a new user account</p></div>
            </div>
            <button onclick="closeModal('add-user-modal')" class="w-8 h-8 rounded-full bg-surface-container-high flex items-center justify-center hover:bg-error-container hover:text-error transition-colors">
                <span class="material-symbols-outlined text-[18px]">close</span>
            </button>
        </div>
        <form id="user-form" class="p-6 space-y-4">
            <div><label class="block text-xs font-bold text-on-surface-variant uppercase tracking-wide mb-1.5">Username *</label>
            <input type="text" name="username" required placeholder="e.g. john_doe" class="w-full px-3 py-2.5 rounded-xl border border-outline-variant focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none text-sm bg-surface-container-low"/></div>
            <div><label class="block text-xs font-bold text-on-surface-variant uppercase tracking-wide mb-1.5">Email *</label>
            <input type="email" name="email" required placeholder="e.g. john@skopestay.com" class="w-full px-3 py-2.5 rounded-xl border border-outline-variant focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none text-sm bg-surface-container-low"/></div>
            <div><label class="block text-xs font-bold text-on-surface-variant uppercase tracking-wide mb-1.5">Password *</label>
            <input type="password" name="password" required placeholder="Minimum 8 characters" class="w-full px-3 py-2.5 rounded-xl border border-outline-variant focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none text-sm bg-surface-container-low"/></div>
            <div><label class="block text-xs font-bold text-on-surface-variant uppercase tracking-wide mb-1.5">Role</label>
            <select name="role" class="w-full px-3 py-2.5 rounded-xl border border-outline-variant focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none text-sm bg-surface-container-low appearance-none">
                <option>Manager</option><option>Receptionist</option><option>Finance</option><option>Housekeeping</option>
            </select></div>
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="closeModal('add-user-modal')" class="flex-1 py-3 rounded-xl border border-outline-variant text-on-surface-variant font-semibold text-sm hover:bg-surface-container transition-colors">Cancel</button>
                <button type="submit" id="user-btn" class="flex-1 py-3 rounded-xl bg-primary text-white font-bold text-sm hover:bg-primary/90 shadow-md active:scale-95 transition-all flex items-center justify-center gap-2">
                    <span class="material-symbols-outlined text-[18px]">add_circle</span> Create User
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
function showSuccess(m){Swal.fire({icon:'success',title:'Success!',text:m,timer:3000,timerProgressBar:true,showConfirmButton:false,toast:true,position:'top-end'});}
function showError(m){Swal.fire({icon:'error',title:'Error',text:m,confirmButtonColor:'rgb(37,99,235)'});}

function switchTab(name) {
    document.querySelectorAll('.settings-panel').forEach(p => p.classList.add('hidden'));
    document.querySelectorAll('.settings-tab').forEach((t,i) => {
        const isActive = t.id === 'tab-' + name;
        t.classList.toggle('border-primary', isActive);
        t.classList.toggle('border-outline-variant/30', !isActive);
        t.querySelector('.tab-icon').classList.toggle('text-primary', isActive);
        t.querySelector('.tab-icon').classList.toggle('text-on-surface-variant', !isActive);
    });
    const panel = document.getElementById('content-' + name);
    if (panel) panel.classList.remove('hidden');
}

function saveSettings(section) {
    showSuccess(`${section.charAt(0).toUpperCase()+section.slice(1)} settings saved successfully!`);
}

function showConfirmLogout() {
    Swal.fire({title:'Log out everywhere?', text:'All other active sessions will be terminated.', icon:'warning',
        showCancelButton:true, confirmButtonColor:'rgb(239,68,68)', confirmButtonText:'Yes, log out all'})
    .then(r => { if(r.isConfirmed) showSuccess('All other sessions terminated.'); });
}

// Add User form
document.getElementById('user-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    const btn = document.getElementById('user-btn');
    btn.innerHTML = '<span class="material-symbols-outlined animate-spin text-[18px]">sync</span> Creating...'; btn.disabled = true;
    // For now simulate — in production POST to api/users.php
    await new Promise(r => setTimeout(r, 1000));
    closeModal('add-user-modal'); this.reset();
    showSuccess('User account created successfully!');
    btn.innerHTML = '<span class="material-symbols-outlined text-[18px]">add_circle</span> Create User'; btn.disabled = false;
});
</script>
</body></html>
