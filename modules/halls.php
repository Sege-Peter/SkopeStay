<?php
$page_title = "Halls & Venues | SkopeStay";
require_once '../includes/config.php';

// Fetch venues
$halls = $pdo->query("SELECT * FROM halls ORDER BY name")->fetchAll();
$hall_ids = array_column($halls, 'id');

// Fetch upcoming bookings
$bookings = $pdo->query("
    SELECT hb.*, h.name as hall_name, c.full_name as customer_name, c.phone
    FROM hall_bookings hb
    JOIN halls h ON hb.hall_id = h.id
    JOIN customers c ON hb.customer_id = c.id
    WHERE hb.event_date >= CURDATE()
    ORDER BY hb.event_date ASC, hb.start_time ASC
")->fetchAll();

// Fetch customers for the booking dropdown
$customers = $pdo->query("SELECT id, full_name, phone FROM customers ORDER BY full_name")->fetchAll();

// KPIs
$total_venues = count($halls);
$upcoming_events = count($bookings);
$monthly_revenue = $pdo->query("SELECT COALESCE(SUM(total_amount),0) FROM hall_bookings WHERE MONTH(event_date) = MONTH(CURDATE())")->fetchColumn();

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
                <span class="text-primary font-bold">Halls & Venues</span>
            </nav>
            <h1 class="text-2xl md:text-3xl font-extrabold text-on-surface">Venue Management</h1>
            <p class="text-on-surface-variant text-sm mt-1">Manage event spaces, conferences, and venue bookings.</p>
        </div>
        <div class="flex items-center gap-3">
            <button onclick="openModal('add-venue-modal')" class="px-5 py-2.5 bg-surface-container text-on-surface-variant rounded-xl font-bold text-sm hover:bg-surface-container-high transition-colors flex items-center gap-2">
                <span class="material-symbols-outlined text-[18px]">add_location</span> Add Venue
            </button>
            <button onclick="openModal('book-venue-modal')" class="px-5 py-2.5 bg-primary text-white rounded-xl font-bold text-sm shadow-md hover:bg-primary/90 active:scale-95 transition-all flex items-center gap-2">
                <span class="material-symbols-outlined text-[18px]">event</span> Book Venue
            </button>
        </div>
    </div>

    <!-- KPIs -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <?php foreach([
            ['Total Venues', $total_venues, 'domain', 'bg-blue-100 text-blue-700'],
            ['Upcoming Events', $upcoming_events, 'celebration', 'bg-purple-100 text-purple-700'],
            ['Revenue (Month)', 'KSh '.number_format((float)$monthly_revenue), 'payments', 'bg-green-100 text-green-700']
        ] as [$lbl, $val, $ico, $cls]): ?>
        <div class="bg-white rounded-2xl border border-outline-variant/30 shadow-sm p-5 flex items-center justify-between hover:shadow-md transition-all">
            <div><p class="text-xs font-semibold text-on-surface-variant uppercase tracking-wide"><?= $lbl ?></p>
            <h3 class="text-2xl font-extrabold text-on-surface mt-1"><?= $val ?></h3></div>
            <div class="w-12 h-12 rounded-full <?= $cls ?> flex items-center justify-center">
                <span class="material-symbols-outlined text-[22px]"><?= $ico ?></span>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Left Col: Venues List -->
        <div class="lg:col-span-1 space-y-4">
            <h3 class="font-bold text-lg text-on-surface">Available Venues</h3>
            <div class="space-y-4">
                <?php foreach($halls as $h): ?>
                <div class="bg-white rounded-2xl border border-outline-variant/30 shadow-sm overflow-hidden group">
                    <div class="p-5">
                        <div class="flex justify-between items-start mb-2">
                            <div>
                                <h4 class="font-extrabold text-on-surface text-base"><?= htmlspecialchars($h['name']) ?></h4>
                                <span class="text-[10px] font-bold text-primary uppercase tracking-wider"><?= htmlspecialchars($h['type']) ?></span>
                            </div>
                            <span class="bg-surface-container px-2 py-1 rounded text-xs font-bold text-on-surface-variant flex items-center gap-1">
                                <span class="material-symbols-outlined text-[14px]">groups</span> <?= $h['capacity'] ?>
                            </span>
                        </div>
                        <p class="text-xs text-on-surface-variant mt-2 mb-4 leading-relaxed line-clamp-2"><?= htmlspecialchars($h['description']) ?></p>
                        <div class="flex items-center justify-between pt-3 border-t border-outline-variant/30">
                            <div>
                                <p class="text-[10px] text-on-surface-variant font-semibold">Daily Rate</p>
                                <p class="font-extrabold text-sm">KSh <?= number_format($h['daily_rate']) ?></p>
                            </div>
                            <button onclick="deleteVenue(<?= $h['id'] ?>)" class="p-1.5 text-on-surface-variant hover:text-error transition-colors opacity-0 group-hover:opacity-100"><span class="material-symbols-outlined text-[18px]">delete</span></button>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Right Col: Bookings Calendar/Table -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl border border-outline-variant/30 shadow-sm overflow-hidden h-full">
                <div class="p-5 border-b border-outline-variant/30 flex items-center justify-between">
                    <h3 class="font-bold text-lg text-on-surface">Upcoming Events</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead><tr class="bg-surface-container-low/50 border-b border-outline-variant/30">
                            <th class="px-5 py-3.5 text-xs font-bold uppercase text-on-surface">Date & Time</th>
                            <th class="px-5 py-3.5 text-xs font-bold uppercase text-on-surface">Venue & Client</th>
                            <th class="px-5 py-3.5 text-xs font-bold uppercase text-on-surface text-center">Status</th>
                            <th class="px-5 py-3.5 text-xs font-bold uppercase text-on-surface text-right">Action</th>
                        </tr></thead>
                        <tbody class="divide-y divide-outline-variant/20">
                            <?php if(empty($bookings)): ?>
                            <tr><td colspan="4" class="text-center py-16 text-on-surface-variant opacity-50">No upcoming events.</td></tr>
                            <?php else: foreach($bookings as $b): 
                                $date_str = date('M j, Y', strtotime($b['event_date']));
                                $time_str = date('h:i A', strtotime($b['start_time'])) . ' - ' . date('h:i A', strtotime($b['end_time']));
                            ?>
                            <tr class="hover:bg-surface-container-low/40 transition-colors group">
                                <td class="px-5 py-4">
                                    <p class="font-bold text-sm text-on-surface"><?= $date_str ?></p>
                                    <p class="text-[11px] text-on-surface-variant"><?= $time_str ?></p>
                                </td>
                                <td class="px-5 py-4">
                                    <p class="font-bold text-sm text-primary"><?= htmlspecialchars($b['hall_name']) ?></p>
                                    <p class="text-[11px] text-on-surface-variant flex items-center gap-1">
                                        <span class="material-symbols-outlined text-[12px]">person</span> <?= htmlspecialchars($b['customer_name']) ?>
                                    </p>
                                </td>
                                <td class="px-5 py-4 text-center">
                                    <?php $sb = match($b['status']) {
                                        'Confirmed' => 'bg-green-100 text-green-700 border-green-200',
                                        'Pending' => 'bg-amber-100 text-amber-700 border-amber-200',
                                        'Cancelled' => 'bg-red-100 text-red-600 border-red-200',
                                        default => 'bg-gray-100'
                                    }; ?>
                                    <span class="px-2.5 py-1 rounded-full border text-[11px] font-bold <?= $sb ?>"><?= $b['status'] ?></span>
                                </td>
                                <td class="px-5 py-4 text-right">
                                    <div class="flex justify-end gap-1 opacity-0 group-hover:opacity-100">
                                        <?php if($b['status'] === 'Pending'): ?>
                                        <button onclick="confirmBooking(<?= $b['id'] ?>)" class="w-7 h-7 rounded-lg bg-green-50 text-green-600 flex items-center justify-center hover:bg-green-100" title="Confirm"><span class="material-symbols-outlined text-[16px]">check</span></button>
                                        <?php endif; ?>
                                        <button onclick="deleteBooking(<?= $b['id'] ?>)" class="w-7 h-7 rounded-lg bg-red-50 text-red-600 flex items-center justify-center hover:bg-red-100" title="Cancel/Delete"><span class="material-symbols-outlined text-[16px]">close</span></button>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</main>
<?php include '../includes/footer.php'; ?>
</div>

<!-- Add Venue Modal -->
<div id="add-venue-modal" class="fixed inset-0 z-[100] hidden items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" onclick="closeModal('add-venue-modal')"></div>
    <div class="relative bg-white rounded-3xl shadow-2xl w-full max-w-lg mx-auto animate-modal">
        <div class="p-6 border-b border-outline-variant/30 flex justify-between">
            <h3 class="font-bold text-lg">Add New Venue</h3>
            <button onclick="closeModal('add-venue-modal')" class="text-on-surface-variant hover:text-error"><span class="material-symbols-outlined">close</span></button>
        </div>
        <form id="venue-form" class="p-6 space-y-4">
            <input type="hidden" name="action" value="hall">
            <div><label class="block text-xs font-bold text-on-surface-variant uppercase mb-1.5">Venue Name *</label>
            <input type="text" name="name" required class="w-full px-3 py-2.5 rounded-xl border border-outline-variant focus:border-primary text-sm bg-surface-container-low"/></div>
            
            <div class="grid grid-cols-2 gap-4">
                <div><label class="block text-xs font-bold text-on-surface-variant uppercase mb-1.5">Type *</label>
                <select name="type" class="w-full px-3 py-2.5 rounded-xl border border-outline-variant focus:border-primary text-sm bg-surface-container-low">
                    <option>Conference Hall</option><option>Banquet Hall</option><option>Meeting Room</option><option>Outdoor Event Ground</option>
                </select></div>
                <div><label class="block text-xs font-bold text-on-surface-variant uppercase mb-1.5">Capacity *</label>
                <input type="number" name="capacity" required class="w-full px-3 py-2.5 rounded-xl border border-outline-variant focus:border-primary text-sm bg-surface-container-low"/></div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div><label class="block text-xs font-bold text-on-surface-variant uppercase mb-1.5">Daily Rate (KSh)</label>
                <input type="number" name="daily_rate" value="0" class="w-full px-3 py-2.5 rounded-xl border border-outline-variant focus:border-primary text-sm bg-surface-container-low"/></div>
                <div><label class="block text-xs font-bold text-on-surface-variant uppercase mb-1.5">Hourly Rate (KSh)</label>
                <input type="number" name="hourly_rate" value="0" class="w-full px-3 py-2.5 rounded-xl border border-outline-variant focus:border-primary text-sm bg-surface-container-low"/></div>
            </div>

            <div><label class="block text-xs font-bold text-on-surface-variant uppercase mb-1.5">Description</label>
            <textarea name="description" rows="2" class="w-full px-3 py-2.5 rounded-xl border border-outline-variant focus:border-primary text-sm bg-surface-container-low resize-none"></textarea></div>

            <button type="submit" class="w-full py-3 rounded-xl bg-primary text-white font-bold text-sm shadow-md">Save Venue</button>
        </form>
    </div>
</div>

<!-- Book Venue Modal -->
<div id="book-venue-modal" class="fixed inset-0 z-[100] hidden items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" onclick="closeModal('book-venue-modal')"></div>
    <div class="relative bg-white rounded-3xl shadow-2xl w-full max-w-lg mx-auto animate-modal">
        <div class="p-6 border-b border-outline-variant/30 flex justify-between">
            <h3 class="font-bold text-lg">Book Venue</h3>
            <button onclick="closeModal('book-venue-modal')" class="text-on-surface-variant hover:text-error"><span class="material-symbols-outlined">close</span></button>
        </div>
        <form id="book-form" class="p-6 space-y-4">
            <input type="hidden" name="action" value="booking">
            
            <div class="grid grid-cols-2 gap-4">
                <div><label class="block text-xs font-bold text-on-surface-variant uppercase mb-1.5">Venue *</label>
                <select name="hall_id" required onchange="calcVenuePrice()" id="v-hall" class="w-full px-3 py-2.5 rounded-xl border border-outline-variant focus:border-primary text-sm bg-surface-container-low">
                    <option value="">-- Select --</option>
                    <?php foreach($halls as $h): ?>
                    <option value="<?= $h['id'] ?>" data-price="<?= $h['daily_rate'] ?>"><?= htmlspecialchars($h['name']) ?></option>
                    <?php endforeach; ?>
                </select></div>
                <div><label class="block text-xs font-bold text-on-surface-variant uppercase mb-1.5">Client *</label>
                <select name="customer_id" required class="w-full px-3 py-2.5 rounded-xl border border-outline-variant focus:border-primary text-sm bg-surface-container-low">
                    <option value="">-- Select --</option>
                    <?php foreach($customers as $c): ?>
                    <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['full_name']) ?></option>
                    <?php endforeach; ?>
                </select></div>
            </div>

            <div><label class="block text-xs font-bold text-on-surface-variant uppercase mb-1.5">Event Date *</label>
            <input type="date" name="event_date" id="v-date" required class="w-full px-3 py-2.5 rounded-xl border border-outline-variant focus:border-primary text-sm bg-surface-container-low"/></div>

            <div class="grid grid-cols-2 gap-4">
                <div><label class="block text-xs font-bold text-on-surface-variant uppercase mb-1.5">Start Time *</label>
                <input type="time" name="start_time" required class="w-full px-3 py-2.5 rounded-xl border border-outline-variant focus:border-primary text-sm bg-surface-container-low"/></div>
                <div><label class="block text-xs font-bold text-on-surface-variant uppercase mb-1.5">End Time *</label>
                <input type="time" name="end_time" required class="w-full px-3 py-2.5 rounded-xl border border-outline-variant focus:border-primary text-sm bg-surface-container-low"/></div>
            </div>

            <div><label class="block text-xs font-bold text-on-surface-variant uppercase mb-1.5">Total Amount (KSh)</label>
            <input type="number" name="total_amount" id="v-total" value="0" class="w-full px-3 py-2.5 rounded-xl border border-outline-variant text-sm bg-surface-container font-bold text-primary"/></div>

            <button type="submit" class="w-full py-3 rounded-xl bg-primary text-white font-bold text-sm shadow-md flex justify-center items-center gap-2"><span class="material-symbols-outlined text-[18px]">event_available</span> Confirm Booking</button>
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

function calcVenuePrice() {
    const sel = document.getElementById('v-hall');
    if(sel.selectedIndex > 0) {
        document.getElementById('v-total').value = sel.options[sel.selectedIndex].dataset.price;
    }
}
document.getElementById('v-date').valueAsDate = new Date();

// Submit Handlers
['venue-form', 'book-form'].forEach(fid => {
    document.getElementById(fid).addEventListener('submit', async function(e) {
        e.preventDefault();
        const btn = this.querySelector('button[type="submit"]'); btn.disabled=true;
        try {
            const data = Object.fromEntries(new FormData(this));
            const res = await fetch('../api/halls.php', {method:'POST', headers:{'Content-Type':'application/json'}, body:JSON.stringify(data)});
            const json = await res.json();
            if(json.success) { closeModal(fid.replace('-form','-modal')); showSuccess(json.message); setTimeout(()=>location.reload(), 1500); }
            else showError(json.message);
        } catch(err) { showError('Network error'); }
        btn.disabled=false;
    });
});

async function confirmBooking(id) {
    const r = await fetch('../api/halls.php', {method:'PUT', headers:{'Content-Type':'application/json'}, body:JSON.stringify({action:'booking_status', id:id, status:'Confirmed'})});
    const j = await r.json();
    if(j.success) { showSuccess('Booking confirmed!'); setTimeout(()=>location.reload(),1000); }
}

function deleteBooking(id) {
    Swal.fire({title:'Cancel Booking?', icon:'warning', showCancelButton:true, confirmButtonColor:'#ef4444', confirmButtonText:'Cancel'})
    .then(async (res) => {
        if(res.isConfirmed) {
            const r = await fetch(`../api/halls.php?id=${id}&type=booking`, {method:'DELETE'});
            const j = await r.json();
            if(j.success) { showSuccess('Cancelled.'); setTimeout(()=>location.reload(), 1000); }
        }
    });
}
function deleteVenue(id) {
    Swal.fire({title:'Delete Venue?', text:'This removes the venue and its history.', icon:'error', showCancelButton:true, confirmButtonColor:'#ef4444', confirmButtonText:'Delete'})
    .then(async (res) => {
        if(res.isConfirmed) {
            const r = await fetch(`../api/halls.php?id=${id}&type=hall`, {method:'DELETE'});
            const j = await r.json();
            if(j.success) { showSuccess('Deleted.'); setTimeout(()=>location.reload(), 1000); }
            else showError(j.message);
        }
    });
}
</script>
</body></html>
