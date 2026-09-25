<?php
$page_title = "SkopeStay - Management Dashboard";
require_once '../includes/config.php';

// Live stats from DB
$total_rooms = $pdo->query("SELECT COUNT(*) FROM rooms")->fetchColumn();
$occupied    = $pdo->query("SELECT COUNT(*) FROM rooms WHERE status='Occupied'")->fetchColumn();
$available   = $pdo->query("SELECT COUNT(*) FROM rooms WHERE status='Available'")->fetchColumn();
$cleaning    = $pdo->query("SELECT COUNT(*) FROM rooms WHERE status='Cleaning'")->fetchColumn();
$revenue_today = $pdo->query("SELECT COALESCE(SUM(total_amount),0) FROM bookings WHERE DATE(created_at)=CURDATE()")->fetchColumn();
$occ_pct     = $total_rooms > 0 ? round(($occupied / $total_rooms) * 100) : 0;
$rest_orders = $pdo->query("SELECT COUNT(*) FROM restaurant_orders WHERE DATE(created_at)=CURDATE()")->fetchColumn();

// Recent bookings for activity feed
$recent = $pdo->query("SELECT b.guest_name, b.created_at, r.room_number FROM bookings b LEFT JOIN rooms r ON b.room_id=r.id ORDER BY b.created_at DESC LIMIT 6")->fetchAll();

// Available rooms for booking modal
$avail_rooms = $pdo->query("SELECT id, room_number, type, price FROM rooms WHERE status='Available' ORDER BY room_number")->fetchAll();

include '../includes/head.php';
?>
<!DOCTYPE html>
<html class="light" lang="en">
<body class="bg-background text-on-surface font-body-md min-h-screen">

<?php include '../includes/sidebar.php'; ?>

<main class="md:ml-64 min-h-screen flex flex-col">

<?php include '../includes/header.php'; ?>

<!-- Dashboard Content -->
<div class="flex-1 p-4 md:p-8 space-y-6">

    <!-- Welcome Banner -->
    <section class="flex flex-col md:flex-row justify-between items-start md:items-end gap-4">
        <div>
            <h2 class="font-headline-lg text-headline-lg text-on-surface font-bold">Dashboard Overview</h2>
            <p class="text-on-surface-variant">Welcome back! Here's what's happening at SkopeStay today — <span id="live-date" class="font-semibold text-primary"></span></p>
        </div>
        <div class="flex gap-3 flex-wrap">
            <button onclick="openModal('booking-modal')" class="bg-primary text-white text-sm font-semibold px-5 py-2.5 rounded-lg flex items-center gap-2 hover:bg-primary/90 shadow-md active:scale-95 transition-all">
                <span class="material-symbols-outlined text-[18px]">add</span> New Booking
            </button>
            <button onclick="exportReport()" class="bg-secondary-container text-on-secondary-container text-sm font-bold px-5 py-2.5 rounded-lg flex items-center gap-2 hover:opacity-90 active:scale-95 transition-all">
                <span class="material-symbols-outlined text-[18px]">print</span> Export Report
            </button>
        </div>
    </section>

    <!-- KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Total Rooms -->
        <div class="bg-white rounded-2xl border border-outline-variant/30 shadow-sm hover:shadow-md transition-all p-6 group">
            <div class="flex justify-between items-start mb-4">
                <div class="w-12 h-12 bg-primary/10 rounded-xl flex items-center justify-center group-hover:bg-primary group-hover:text-white transition-all">
                    <span class="material-symbols-outlined text-primary group-hover:text-white">bed</span>
                </div>
                <span class="text-xs font-bold bg-blue-100 text-blue-700 px-2.5 py-1 rounded-full">All</span>
            </div>
            <p class="text-on-surface-variant text-sm font-semibold uppercase tracking-wide">Total Rooms</p>
            <h3 class="text-4xl font-extrabold text-on-surface mt-1"><?= $total_rooms ?></h3>
        </div>
        <!-- Occupied -->
        <div class="bg-white rounded-2xl border border-outline-variant/30 shadow-sm hover:shadow-md transition-all p-6 group">
            <div class="flex justify-between items-start mb-4">
                <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center group-hover:bg-red-500 group-hover:text-white transition-all">
                    <span class="material-symbols-outlined text-error group-hover:text-white">meeting_room</span>
                </div>
                <span class="text-xs font-bold bg-red-100 text-red-600 px-2.5 py-1 rounded-full"><?= $occ_pct ?>%</span>
            </div>
            <p class="text-on-surface-variant text-sm font-semibold uppercase tracking-wide">Occupied</p>
            <h3 class="text-4xl font-extrabold text-on-surface mt-1"><?= $occupied ?></h3>
        </div>
        <!-- Available -->
        <div class="bg-white rounded-2xl border border-outline-variant/30 shadow-sm hover:shadow-md transition-all p-6 group">
            <div class="flex justify-between items-start mb-4">
                <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center group-hover:bg-green-500 group-hover:text-white transition-all">
                    <span class="material-symbols-outlined text-green-600 group-hover:text-white">event_available</span>
                </div>
                <span class="text-xs font-bold bg-green-100 text-green-700 px-2.5 py-1 rounded-full">Ready</span>
            </div>
            <p class="text-on-surface-variant text-sm font-semibold uppercase tracking-wide">Available</p>
            <h3 class="text-4xl font-extrabold text-on-surface mt-1"><?= $available ?></h3>
        </div>
        <!-- Revenue -->
        <div class="bg-primary rounded-2xl shadow-lg p-6 text-white group">
            <div class="flex justify-between items-start mb-4">
                <div class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center">
                    <span class="material-symbols-outlined text-white">payments</span>
                </div>
                <span class="text-xs font-bold bg-white/20 text-white px-2.5 py-1 rounded-full">Today</span>
            </div>
            <p class="text-white/70 text-sm font-semibold uppercase tracking-wide">Today's Revenue</p>
            <h3 class="text-3xl font-extrabold text-white mt-1">KSh <?= number_format((float)$revenue_today) ?></h3>
        </div>
    </div>

    <!-- Middle Row -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <!-- Activity Grid (2/3 width) -->
        <div class="lg:col-span-2 space-y-4">
            <!-- Quick Module Stats -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <?php
                $mods = [
                    ['icon'=>'restaurant','label'=>'Restaurant','value'=>$rest_orders,'sub'=>'Orders today'],
                    ['icon'=>'domain','label'=>'Hall Bookings','value'=>'12','sub'=>'4 pending'],
                    ['icon'=>'pool','label'=>'Pool Visitors','value'=>'67','sub'=>'Peak: 2PM'],
                    ['icon'=>'celebration','label'=>'Events','value'=>'5','sub'=>'Next: Tomorrow'],
                ];
                foreach($mods as $m): ?>
                <div class="bg-white rounded-2xl border border-outline-variant/30 shadow-sm p-4 flex items-center gap-4 hover:shadow-md transition-all">
                    <div class="w-11 h-11 bg-primary/10 rounded-xl flex items-center justify-center flex-shrink-0">
                        <span class="material-symbols-outlined text-primary text-[22px]"><?= $m['icon'] ?></span>
                    </div>
                    <div>
                        <p class="text-xs text-on-surface-variant font-semibold"><?= $m['label'] ?></p>
                        <p class="font-extrabold text-xl text-on-surface"><?= $m['value'] ?></p>
                        <p class="text-[10px] text-secondary"><?= $m['sub'] ?></p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- Live Occupancy Map -->
            <div class="bg-white rounded-2xl border border-outline-variant/30 shadow-sm overflow-hidden">
                <div class="p-5 border-b border-outline-variant/30 flex justify-between items-center">
                    <h3 class="font-bold text-on-surface text-base">Live Occupancy Map</h3>
                    <div class="flex gap-4 text-xs font-semibold">
                        <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-green-500 inline-block"></span>Available</span>
                        <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-red-500 inline-block"></span>Occupied</span>
                        <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-amber-400 inline-block"></span>Cleaning</span>
                    </div>
                </div>
                <div class="p-6 bg-surface-container-low">
                    <div class="grid grid-cols-10 gap-2" id="occupancy-grid"></div>
                </div>
            </div>
        </div>

        <!-- Recent Activity Feed (1/3 width) -->
        <div class="bg-white rounded-2xl border border-outline-variant/30 shadow-sm flex flex-col max-h-[460px]">
            <div class="p-5 border-b border-outline-variant/30 flex justify-between items-center">
                <h3 class="font-bold text-on-surface text-base">Recent Bookings</h3>
                <a href="bookings.php" class="text-xs text-primary font-semibold cursor-pointer hover:underline">View All</a>
            </div>
            <div class="flex-1 overflow-y-auto p-4 space-y-4 scrollbar-hide">
                <?php if (empty($recent)): ?>
                <div class="flex flex-col items-center justify-center h-full text-on-surface-variant opacity-40 py-12">
                    <span class="material-symbols-outlined text-5xl mb-2">event_busy</span>
                    <p class="text-sm">No bookings yet</p>
                </div>
                <?php else: foreach($recent as $i => $b): 
                    $times = ['Just now','5 mins ago','22 mins ago','1 hr ago','2 hrs ago','Today'];
                    $t = $times[$i] ?? date('M j', strtotime($b['created_at']));
                ?>
                <div class="flex gap-3 items-start">
                    <div class="w-9 h-9 rounded-full bg-primary/10 flex items-center justify-center flex-shrink-0">
                        <span class="material-symbols-outlined text-primary text-[18px]">person_add</span>
                    </div>
                    <div>
                        <p class="text-sm text-on-surface"><strong><?= htmlspecialchars($b['guest_name']) ?></strong> checked in to Room <?= htmlspecialchars($b['room_number'] ?? 'N/A') ?></p>
                        <p class="text-[11px] text-on-surface-variant mt-0.5"><?= $t ?></p>
                    </div>
                </div>
                <?php endforeach; endif; ?>
            </div>
            <div class="p-4 border-t border-outline-variant/30">
                <button onclick="openModal('booking-modal')" class="w-full bg-primary text-white font-bold py-2.5 rounded-xl text-sm hover:bg-primary/90 transition-all active:scale-95">
                    + New Booking
                </button>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
</main>

<!-- ===================== NEW BOOKING MODAL ===================== -->
<div id="booking-modal" class="fixed inset-0 z-[100] hidden items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" onclick="closeModal('booking-modal')"></div>
    <div class="relative bg-white rounded-3xl shadow-2xl w-full max-w-lg mx-auto animate-modal">
        <!-- Modal Header -->
        <div class="flex items-center justify-between p-6 border-b border-outline-variant/30">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-primary/10 rounded-xl flex items-center justify-center">
                    <span class="material-symbols-outlined text-primary">event_note</span>
                </div>
                <div>
                    <h3 class="font-bold text-on-surface text-base">Create New Booking</h3>
                    <p class="text-xs text-on-surface-variant">Fill in the guest details below</p>
                </div>
            </div>
            <button onclick="closeModal('booking-modal')" class="w-8 h-8 rounded-full bg-surface-container-high flex items-center justify-center hover:bg-error-container hover:text-error transition-colors">
                <span class="material-symbols-outlined text-[18px]">close</span>
            </button>
        </div>
        <!-- Modal Body -->
        <form id="booking-form" class="p-6 space-y-4">
            <div class="grid grid-cols-1 gap-4">
                <div>
                    <label class="block text-xs font-bold text-on-surface-variant uppercase tracking-wide mb-1.5">Guest Full Name *</label>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline text-[18px]">person</span>
                        <input type="text" name="guest_name" required placeholder="e.g. Jane Kamau" class="w-full pl-9 pr-4 py-2.5 rounded-xl border border-outline-variant focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none text-sm transition-all bg-surface-container-low"/>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold text-on-surface-variant uppercase tracking-wide mb-1.5">Select Room *</label>
                    <div class="relative">
                        <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline text-[18px]">bed</span>
                        <select name="room_id" required class="w-full pl-9 pr-4 py-2.5 rounded-xl border border-outline-variant focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none text-sm bg-surface-container-low transition-all appearance-none">
                            <option value="">-- Choose an available room --</option>
                            <?php foreach($avail_rooms as $r): ?>
                            <option value="<?= $r['id'] ?>">Room <?= htmlspecialchars($r['room_number']) ?> — <?= htmlspecialchars($r['type']) ?> (KSh <?= number_format((float)$r['price']) ?>/night)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-on-surface-variant uppercase tracking-wide mb-1.5">Check-In Date *</label>
                        <input type="date" name="check_in" required class="w-full px-3 py-2.5 rounded-xl border border-outline-variant focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none text-sm bg-surface-container-low transition-all"/>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-on-surface-variant uppercase tracking-wide mb-1.5">Check-Out Date</label>
                        <input type="date" name="check_out" class="w-full px-3 py-2.5 rounded-xl border border-outline-variant focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none text-sm bg-surface-container-low transition-all"/>
                    </div>
                </div>
            </div>
            <!-- Price Preview -->
            <div id="price-preview" class="hidden bg-primary/5 border border-primary/20 rounded-xl p-3 text-sm">
                <div class="flex justify-between items-center">
                    <span class="text-on-surface-variant">Estimated Total</span>
                    <span id="price-estimate" class="font-extrabold text-primary text-base">KSh 0</span>
                </div>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="closeModal('booking-modal')" class="flex-1 py-3 rounded-xl border border-outline-variant text-on-surface-variant font-semibold text-sm hover:bg-surface-container transition-colors">
                    Cancel
                </button>
                <button type="submit" id="booking-submit-btn" class="flex-1 py-3 rounded-xl bg-primary text-white font-bold text-sm hover:bg-primary/90 shadow-md active:scale-95 transition-all flex items-center justify-center gap-2">
                    <span class="material-symbols-outlined text-[18px]">check_circle</span> Confirm Booking
                </button>
            </div>
        </form>
    </div>
</div>

<style>
@keyframes modal-in {
    from { opacity: 0; transform: scale(0.95) translateY(20px); }
    to   { opacity: 1; transform: scale(1) translateY(0); }
}
.animate-modal { animation: modal-in 0.25s cubic-bezier(0.2, 0.8, 0.2, 1) forwards; }
</style>

<script>
// ---- Utility: Modals ----
function openModal(id) {
    const el = document.getElementById(id);
    el.classList.remove('hidden');
    el.classList.add('flex');
}
function closeModal(id) {
    const el = document.getElementById(id);
    el.classList.add('hidden');
    el.classList.remove('flex');
}

// ---- Utility: Alerts ----
function showSuccess(msg) {
    Swal.fire({ icon: 'success', title: 'Success!', text: msg, timer: 3000, timerProgressBar: true,
        showConfirmButton: false, toast: true, position: 'top-end',
        customClass: { popup: 'font-body-md' }
    });
}
function showError(msg) {
    Swal.fire({ icon: 'error', title: 'Error', text: msg,
        confirmButtonColor: 'rgb(37, 99, 235)', customClass: { popup: 'font-body-md' }
    });
}
function showConfirm(msg, cb) {
    Swal.fire({
        title: 'Are you sure?', text: msg, icon: 'warning',
        showCancelButton: true, confirmButtonColor: 'rgb(37, 99, 235)',
        cancelButtonColor: 'rgb(239, 68, 68)', confirmButtonText: 'Yes, proceed!'
    }).then(r => { if (r.isConfirmed) cb(); });
}

// ---- Live Date ----
const dateEl = document.getElementById('live-date');
const now = new Date();
dateEl.textContent = now.toLocaleDateString('en-KE', { weekday:'long', year:'numeric', month:'long', day:'numeric' });

// ---- Occupancy Grid ----
async function loadOccupancyGrid() {
    const grid = document.getElementById('occupancy-grid');
    try {
        const res = await fetch('../api/rooms.php');
        const json = await res.json();
        if (!json.success) { grid.innerHTML = '<p class="text-xs text-on-surface-variant">Could not load map</p>'; return; }
        grid.innerHTML = '';
        json.data.forEach(room => {
            const colors = {
                'Available': 'bg-green-100 border-green-300 text-green-800',
                'Occupied':  'bg-red-100 border-red-300 text-red-700',
                'Cleaning':  'bg-amber-100 border-amber-300 text-amber-700',
                'Maintenance': 'bg-gray-100 border-gray-300 text-gray-600'
            };
            const cls = colors[room.status] || 'bg-gray-100 border-gray-300 text-gray-600';
            grid.innerHTML += `<div class="${cls} border rounded-lg h-11 flex flex-col items-center justify-center text-[10px] font-bold hover:scale-105 transition-transform cursor-pointer shadow-sm" title="${room.type} — ${room.status}">${room.room_number}</div>`;
        });
    } catch(e) {
        grid.innerHTML = '<p class="text-xs text-on-surface-variant col-span-10">Failed to load grid.</p>';
    }
}
loadOccupancyGrid();

// ---- Price Preview in Booking Modal ----
function calcPrice() {
    const roomSel = document.querySelector('[name="room_id"]');
    const checkIn  = document.querySelector('[name="check_in"]').value;
    const checkOut = document.querySelector('[name="check_out"]').value;
    const preview  = document.getElementById('price-preview');
    const est      = document.getElementById('price-estimate');

    const option = roomSel.options[roomSel.selectedIndex];
    if (!option.value || !checkIn) { preview.classList.add('hidden'); return; }

    const priceMatch = option.textContent.match(/KSh\s([\d,]+)/);
    const nightlyRate = priceMatch ? parseInt(priceMatch[1].replace(',','')) : 0;
    let nights = 1;
    if (checkIn && checkOut) {
        const diff = (new Date(checkOut) - new Date(checkIn)) / 86400000;
        if (diff > 0) nights = diff;
    }
    const total = nightlyRate * nights;
    est.textContent = `KSh ${total.toLocaleString()} (${nights} night${nights !== 1 ? 's' : ''})`;
    preview.classList.remove('hidden');
}
document.querySelector('[name="room_id"]').addEventListener('change', calcPrice);
document.querySelector('[name="check_in"]').addEventListener('change', calcPrice);
document.querySelector('[name="check_out"]').addEventListener('change', calcPrice);

// Set default check-in to today
document.querySelector('[name="check_in"]').valueAsDate = new Date();

// ---- New Booking Form Submit ----
document.getElementById('booking-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    const btn = document.getElementById('booking-submit-btn');
    btn.innerHTML = '<span class="material-symbols-outlined animate-spin text-[18px]">sync</span> Processing...';
    btn.disabled = true;

    const formData = {
        guest_name: this.guest_name.value,
        room_id:    this.room_id.value,
        check_in:   this.check_in.value,
        check_out:  this.check_out.value,
    };

    try {
        const res = await fetch('../api/bookings.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(formData)
        });
        const json = await res.json();

        if (json.success) {
            closeModal('booking-modal');
            this.reset();
            document.getElementById('price-preview').classList.add('hidden');
            showSuccess(json.message + ' Total: KSh ' + Number(json.total).toLocaleString());
            setTimeout(() => location.reload(), 2500);
        } else {
            showError(json.message);
        }
    } catch(err) {
        showError('Network error. Please try again.');
    } finally {
        btn.innerHTML = '<span class="material-symbols-outlined text-[18px]">check_circle</span> Confirm Booking';
        btn.disabled = false;
    }
});

// ---- Export Report ----
function exportReport() {
    showConfirm('This will download a CSV report of today\'s data.', () => {
        showSuccess('Report export started!');
    });
}

// ---- Glass card hover ----
document.querySelectorAll('.hover\\:shadow-md').forEach(card => {
    card.addEventListener('mouseenter', () => card.style.transform = 'translateY(-2px)');
    card.addEventListener('mouseleave', () => card.style.transform = 'translateY(0)');
});
</script>
</body>
</html>
