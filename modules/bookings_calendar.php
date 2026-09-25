<?php
$page_title = "Booking Calendar | SkopeStay";
require_once '../includes/config.php';

$year  = (int)($_GET['year']  ?? date('Y'));
$month = (int)($_GET['month'] ?? date('n'));
if ($month < 1) { $month = 12; $year--; }
if ($month > 12){ $month = 1;  $year++; }

$first_day  = mktime(0,0,0,$month,1,$year);
$days_in    = date('t', $first_day);
$start_dow  = (int)date('w', $first_day); // 0=Sun

// Fetch bookings for this month
$bookings_month = $pdo->prepare("
    SELECT b.*, r.room_number
    FROM bookings b
    LEFT JOIN rooms r ON b.room_id = r.id
    WHERE (YEAR(b.check_in)=? AND MONTH(b.check_in)=?)
       OR (YEAR(b.check_out)=? AND MONTH(b.check_out)=?)
");
$bookings_month->execute([$year,$month,$year,$month]);
$bk_data = $bookings_month->fetchAll();

// Group bookings by day
$bk_by_day = [];
foreach ($bk_data as $bk) {
    $ci_day = (int)date('j', strtotime($bk['check_in']));
    $bk_by_day[$ci_day][] = $bk;
}

// Today's check-ins for sidebar
$today_checkins = $pdo->query("SELECT b.*, r.room_number FROM bookings b LEFT JOIN rooms r ON b.room_id=r.id WHERE DATE(b.check_in)=CURDATE() ORDER BY b.created_at DESC LIMIT 10")->fetchAll();

$prev_month = $month - 1; $prev_year = $year;
if ($prev_month < 1) { $prev_month = 12; $prev_year--; }
$next_month = $month + 1; $next_year = $year;
if ($next_month > 12) { $next_month = 1; $next_year++; }

$avail_rooms = $pdo->query("SELECT id,room_number,type,price FROM rooms WHERE status='Available' ORDER BY room_number")->fetchAll();

include '../includes/head.php';
?>
<!DOCTYPE html>
<html class="light" lang="en">
<body class="bg-background text-on-surface font-body-md min-h-screen">
<?php include '../includes/sidebar.php'; ?>
<div class="md:ml-64 flex flex-col min-h-screen">
<?php include '../includes/header.php'; ?>

<main class="flex-1 p-4 md:p-6 flex flex-col lg:flex-row gap-5 overflow-hidden" style="max-height:calc(100vh - 128px)">

    <!-- Calendar Section -->
    <div class="flex-1 bg-white rounded-2xl border border-outline-variant/30 shadow-sm overflow-hidden flex flex-col min-h-0">

        <!-- Calendar Header -->
        <div class="px-5 py-4 border-b border-outline-variant/30 flex items-center justify-between flex-wrap gap-3">
            <div class="flex items-center gap-4">
                <h2 class="font-extrabold text-xl text-on-surface"><?= date('F Y', mktime(0,0,0,$month,1,$year)) ?></h2>
                <div class="flex items-center bg-surface-container-low rounded-xl border border-outline-variant overflow-hidden">
                    <a href="?month=<?= $prev_month ?>&year=<?= $prev_year ?>" class="px-3 py-2 hover:bg-surface-container-high transition-colors">
                        <span class="material-symbols-outlined text-[20px]">chevron_left</span>
                    </a>
                    <a href="?month=<?= date('n') ?>&year=<?= date('Y') ?>" class="px-3 py-1.5 text-xs font-bold hover:bg-surface-container-high transition-colors border-x border-outline-variant">Today</a>
                    <a href="?month=<?= $next_month ?>&year=<?= $next_year ?>" class="px-3 py-2 hover:bg-surface-container-high transition-colors">
                        <span class="material-symbols-outlined text-[20px]">chevron_right</span>
                    </a>
                </div>
            </div>
            <button onclick="openModal('cal-booking-modal')" class="flex items-center gap-2 bg-primary text-white px-4 py-2.5 rounded-xl font-bold text-sm shadow hover:bg-primary/90 active:scale-95 transition-all">
                <span class="material-symbols-outlined text-[18px]">add</span> New Booking
            </button>
        </div>

        <!-- Day Headers -->
        <div class="grid grid-cols-7 bg-surface-container-low/40 border-b border-outline-variant/30">
            <?php foreach (['SUN','MON','TUE','WED','THU','FRI','SAT'] as $d): ?>
            <div class="py-2.5 text-center text-[11px] font-bold text-on-surface-variant tracking-widest"><?= $d ?></div>
            <?php endforeach; ?>
        </div>

        <!-- Calendar Grid -->
        <div class="flex-1 overflow-y-auto">
            <div class="grid grid-cols-7 border-l border-t border-outline-variant/20 h-full">
                <?php
                // Lead empty cells
                for ($i = 0; $i < $start_dow; $i++):
                    $prev_days = date('t', mktime(0,0,0,$month-1,1,$year));
                    $prev_day  = $prev_days - $start_dow + $i + 1;
                ?>
                <div class="border-r border-b border-outline-variant/20 min-h-[100px] p-2 bg-surface/40 opacity-50">
                    <span class="text-xs text-on-surface-variant"><?= $prev_day ?></span>
                </div>
                <?php endfor; ?>

                <?php for ($day = 1; $day <= $days_in; $day++):
                    $isToday = ($day == date('j') && $month == date('n') && $year == date('Y'));
                    $dayBk   = $bk_by_day[$day] ?? [];
                ?>
                <div class="border-r border-b border-outline-variant/20 min-h-[100px] p-2 transition-colors hover:bg-surface-container-low/40 <?= $isToday ? 'bg-primary/5 border-primary/30 border-2' : '' ?>">
                    <div class="flex items-center justify-between mb-1.5">
                        <span class="text-xs font-bold <?= $isToday ? 'text-primary bg-primary text-white w-6 h-6 flex items-center justify-center rounded-full text-[11px]' : 'text-on-surface' ?>"><?= $day ?></span>
                        <?php if ($isToday): ?><span class="text-[9px] font-bold text-primary uppercase tracking-wider">Today</span><?php endif; ?>
                    </div>
                    <div class="space-y-0.5">
                        <?php foreach (array_slice($dayBk, 0, 3) as $bk):
                            $tag_colors = [
                                'Active'    => 'bg-green-100 text-green-700 border-green-200',
                                'CheckedOut'=> 'bg-gray-100 text-gray-500 border-gray-200',
                                'Cancelled' => 'bg-red-100 text-red-500 border-red-200',
                            ];
                            $tcls = $tag_colors[$bk['status']] ?? 'bg-blue-100 text-blue-700 border-blue-200';
                        ?>
                        <div class="text-[9px] font-bold px-1.5 py-0.5 rounded border <?= $tcls ?> flex items-center gap-1 truncate cursor-pointer"
                             onclick="showBookingDetail(<?= htmlspecialchars(json_encode(['guest'=>$bk['guest_name'],'room'=>$bk['room_number'],'ci'=>$bk['check_in'],'co'=>$bk['check_out'],'status'=>$bk['status'],'total'=>$bk['total_amount']])) ?>)">
                            <span class="w-1 h-1 rounded-full bg-current inline-block flex-shrink-0"></span>
                            <span class="truncate"><?= htmlspecialchars($bk['guest_name']) ?></span>
                        </div>
                        <?php endforeach; ?>
                        <?php if (count($dayBk) > 3): ?>
                        <div class="text-[9px] text-on-surface-variant font-semibold px-1">+<?= count($dayBk)-3 ?> more</div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endfor; ?>

                <?php
                // Trailing empty cells
                $total_cells = $start_dow + $days_in;
                $trailing = (7 - ($total_cells % 7)) % 7;
                for ($i = 1; $i <= $trailing; $i++): ?>
                <div class="border-r border-b border-outline-variant/20 min-h-[100px] p-2 bg-surface/40 opacity-50">
                    <span class="text-xs text-on-surface-variant"><?= $i ?></span>
                </div>
                <?php endfor; ?>
            </div>
        </div>
    </div>

    <!-- Right Side Panel -->
    <div class="w-full lg:w-72 flex flex-col gap-4 overflow-y-auto">

        <!-- Filters Card -->
        <div class="bg-white rounded-2xl border border-outline-variant/30 shadow-sm p-5 flex-shrink-0">
            <h3 class="font-bold text-sm text-on-surface flex items-center gap-2 mb-4">
                <span class="material-symbols-outlined text-[18px]">tune</span> Filters
            </h3>
            <p class="text-[10px] font-bold text-on-surface-variant uppercase tracking-widest mb-2">Status Guide</p>
            <div class="grid grid-cols-2 gap-2 mb-4">
                <?php foreach ([['Available','bg-green-500'],['Active','bg-blue-500'],['Checking-out','bg-amber-400'],['Cancelled','bg-red-400']] as [$lbl,$col]): ?>
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full <?= $col ?> flex-shrink-0"></span>
                    <span class="text-[11px] font-semibold text-on-surface-variant"><?= $lbl ?></span>
                </div>
                <?php endforeach; ?>
            </div>
            <p class="text-[10px] font-bold text-on-surface-variant uppercase tracking-widest mb-2">Month Stats</p>
            <div class="space-y-2">
                <div class="flex justify-between text-xs"><span class="text-on-surface-variant">Bookings this month</span><span class="font-bold"><?= count($bk_data) ?></span></div>
                <div class="flex justify-between text-xs"><span class="text-on-surface-variant">Today's check-ins</span><span class="font-bold"><?= count($today_checkins) ?></span></div>
            </div>
        </div>

        <!-- Today's Check-ins -->
        <div class="bg-white rounded-2xl border border-outline-variant/30 shadow-sm flex flex-col flex-1 min-h-0">
            <div class="p-5 border-b border-outline-variant/30 flex items-center justify-between flex-shrink-0">
                <h3 class="font-bold text-sm text-on-surface">Today's Check-ins</h3>
                <span class="bg-secondary-container text-on-secondary-container text-[11px] font-bold px-2.5 py-1 rounded-full"><?= count($today_checkins) ?> total</span>
            </div>
            <div class="flex-1 overflow-y-auto p-4 space-y-3">
                <?php if (empty($today_checkins)): ?>
                <div class="flex flex-col items-center justify-center h-32 text-on-surface-variant opacity-40">
                    <span class="material-symbols-outlined text-4xl mb-2">event_busy</span>
                    <p class="text-xs">No check-ins today</p>
                </div>
                <?php else: foreach ($today_checkins as $ci):
                    $ini = strtoupper(substr($ci['guest_name'],0,1)).(strpos($ci['guest_name'],' ')!==false?strtoupper(substr(strrchr($ci['guest_name'],' '),1,1)):'');
                ?>
                <div class="p-3 bg-surface rounded-xl border border-outline-variant/20 hover:border-primary/30 transition-all cursor-pointer group">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="w-8 h-8 rounded-full bg-primary text-white flex items-center justify-center font-bold text-xs flex-shrink-0"><?= $ini ?></div>
                        <div class="flex-1 min-w-0">
                            <p class="font-bold text-sm truncate"><?= htmlspecialchars($ci['guest_name']) ?></p>
                            <p class="text-[10px] text-on-surface-variant">Room <?= htmlspecialchars($ci['room_number']??'—') ?></p>
                        </div>
                        <span class="material-symbols-outlined text-secondary text-[18px]">verified</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-[10px] font-bold bg-green-100 text-green-700 px-2 py-0.5 rounded-full">Active</span>
                        <span class="text-[10px] font-bold text-primary">KSh <?= number_format((float)$ci['total_amount']) ?></span>
                    </div>
                </div>
                <?php endforeach; endif; ?>
            </div>
            <div class="p-4 border-t border-outline-variant/30 flex-shrink-0">
                <button onclick="openModal('cal-booking-modal')" class="w-full py-2.5 bg-primary text-white font-bold text-sm rounded-xl hover:bg-primary/90 active:scale-95 transition-all">
                    + New Booking
                </button>
            </div>
        </div>
    </div>
</main>

<?php include '../includes/footer.php'; ?>
</div>

<!-- New Booking Modal -->
<div id="cal-booking-modal" class="fixed inset-0 z-[100] hidden items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" onclick="closeModal('cal-booking-modal')"></div>
    <div class="relative bg-white rounded-3xl shadow-2xl w-full max-w-lg mx-auto animate-modal">
        <div class="flex items-center justify-between p-6 border-b border-outline-variant/30">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-primary/10 rounded-xl flex items-center justify-center"><span class="material-symbols-outlined text-primary">event_note</span></div>
                <div><h3 class="font-bold text-base">New Booking</h3><p class="text-xs text-on-surface-variant">Fill in guest details</p></div>
            </div>
            <button onclick="closeModal('cal-booking-modal')" class="w-8 h-8 rounded-full bg-surface-container-high flex items-center justify-center hover:bg-error-container hover:text-error transition-colors"><span class="material-symbols-outlined text-[18px]">close</span></button>
        </div>
        <form id="cal-form" class="p-6 space-y-4">
            <div><label class="block text-xs font-bold text-on-surface-variant uppercase tracking-wide mb-1.5">Guest Name *</label>
            <input type="text" name="guest_name" required placeholder="e.g. Jane Kamau" class="w-full px-3 py-2.5 rounded-xl border border-outline-variant focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none text-sm bg-surface-container-low"/></div>
            <div><label class="block text-xs font-bold text-on-surface-variant uppercase tracking-wide mb-1.5">Select Room *</label>
            <select name="room_id" id="cal-room" required class="w-full px-3 py-2.5 rounded-xl border border-outline-variant focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none text-sm bg-surface-container-low appearance-none">
                <option value="">-- Choose room --</option>
                <?php foreach($avail_rooms as $r): ?>
                <option value="<?= $r['id'] ?>" data-price="<?= $r['price'] ?>">Room <?= htmlspecialchars($r['room_number']) ?> — <?= htmlspecialchars($r['type']) ?> (KSh <?= number_format((float)$r['price']) ?>/night)</option>
                <?php endforeach; ?>
            </select></div>
            <div class="grid grid-cols-2 gap-3">
                <div><label class="block text-xs font-bold text-on-surface-variant uppercase tracking-wide mb-1.5">Check-In *</label>
                <input type="date" name="check_in" id="cal-ci" required class="w-full px-3 py-2.5 rounded-xl border border-outline-variant focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none text-sm bg-surface-container-low"/></div>
                <div><label class="block text-xs font-bold text-on-surface-variant uppercase tracking-wide mb-1.5">Check-Out</label>
                <input type="date" name="check_out" id="cal-co" class="w-full px-3 py-2.5 rounded-xl border border-outline-variant focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none text-sm bg-surface-container-low"/></div>
            </div>
            <div id="cal-preview" class="hidden bg-primary/5 border border-primary/20 rounded-xl p-3 text-sm flex items-center justify-between">
                <span class="text-on-surface-variant">Estimated Total</span>
                <span id="cal-est" class="font-extrabold text-primary">KSh 0</span>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="closeModal('cal-booking-modal')" class="flex-1 py-3 rounded-xl border border-outline-variant text-on-surface-variant font-semibold text-sm hover:bg-surface-container transition-colors">Cancel</button>
                <button type="submit" id="cal-btn" class="flex-1 py-3 rounded-xl bg-primary text-white font-bold text-sm hover:bg-primary/90 shadow-md active:scale-95 transition-all flex items-center justify-center gap-2">
                    <span class="material-symbols-outlined text-[18px]">check_circle</span> Confirm Booking
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Booking Detail Modal -->
<div id="detail-modal" class="fixed inset-0 z-[100] hidden items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" onclick="closeModal('detail-modal')"></div>
    <div class="relative bg-white rounded-3xl shadow-2xl w-full max-w-sm mx-auto animate-modal p-6">
        <button onclick="closeModal('detail-modal')" class="absolute top-4 right-4 w-8 h-8 rounded-full bg-surface-container-high flex items-center justify-center hover:bg-error-container hover:text-error transition-colors">
            <span class="material-symbols-outlined text-[18px]">close</span>
        </button>
        <div class="text-center mb-4">
            <div class="w-14 h-14 bg-primary/10 rounded-2xl flex items-center justify-center mx-auto mb-3">
                <span class="material-symbols-outlined text-primary text-3xl">bed</span>
            </div>
            <h3 id="detail-guest" class="font-extrabold text-lg text-on-surface"></h3>
            <p id="detail-room" class="text-sm text-on-surface-variant"></p>
        </div>
        <div class="space-y-3 text-sm">
            <div class="flex justify-between py-2.5 border-b border-outline-variant/20">
                <span class="text-on-surface-variant font-semibold">Check-In</span><span id="detail-ci" class="font-bold"></span>
            </div>
            <div class="flex justify-between py-2.5 border-b border-outline-variant/20">
                <span class="text-on-surface-variant font-semibold">Check-Out</span><span id="detail-co" class="font-bold"></span>
            </div>
            <div class="flex justify-between py-2.5 border-b border-outline-variant/20">
                <span class="text-on-surface-variant font-semibold">Total</span><span id="detail-total" class="font-extrabold text-primary text-base"></span>
            </div>
            <div class="flex justify-between py-2.5">
                <span class="text-on-surface-variant font-semibold">Status</span><span id="detail-status" class="font-bold"></span>
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
function showSuccess(m){Swal.fire({icon:'success',title:'Success!',text:m,timer:3000,timerProgressBar:true,showConfirmButton:false,toast:true,position:'top-end'});}
function showError(m){Swal.fire({icon:'error',title:'Error',text:m,confirmButtonColor:'rgb(37,99,235)'});}

function showBookingDetail(data) {
    document.getElementById('detail-guest').textContent = data.guest;
    document.getElementById('detail-room').textContent = 'Room ' + (data.room || 'N/A');
    document.getElementById('detail-ci').textContent = data.ci ? new Date(data.ci).toLocaleDateString('en-KE',{month:'short',day:'numeric',year:'numeric'}) : '—';
    document.getElementById('detail-co').textContent = data.co ? new Date(data.co).toLocaleDateString('en-KE',{month:'short',day:'numeric',year:'numeric'}) : '—';
    document.getElementById('detail-total').textContent = 'KSh ' + Number(data.total).toLocaleString();
    document.getElementById('detail-status').textContent = data.status;
    openModal('detail-modal');
}

function calcPrice() {
    const sel = document.getElementById('cal-room');
    const price = parseFloat(sel.options[sel.selectedIndex]?.dataset?.price || 0);
    const ci = document.getElementById('cal-ci').value, co = document.getElementById('cal-co').value;
    const prev = document.getElementById('cal-preview'), est = document.getElementById('cal-est');
    if (!price || !ci) { prev.classList.add('hidden'); return; }
    let nights = 1;
    if (ci && co) { const d = (new Date(co) - new Date(ci)) / 86400000; if (d > 0) nights = d; }
    est.textContent = `KSh ${(price * nights).toLocaleString()} (${nights} night${nights !== 1 ? 's' : ''})`;
    prev.classList.remove('hidden');
}
document.getElementById('cal-room').addEventListener('change', calcPrice);
document.getElementById('cal-ci').addEventListener('change', calcPrice);
document.getElementById('cal-co').addEventListener('change', calcPrice);
document.getElementById('cal-ci').valueAsDate = new Date();

document.getElementById('cal-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    const btn = document.getElementById('cal-btn');
    btn.innerHTML = '<span class="material-symbols-outlined animate-spin text-[18px]">sync</span> Processing...'; btn.disabled = true;
    const data = { guest_name: this.guest_name.value, room_id: this.room_id.value, check_in: this.check_in.value, check_out: this.check_out.value };
    try {
        const res = await fetch('../api/bookings.php', { method:'POST', headers:{'Content-Type':'application/json'}, body: JSON.stringify(data) });
        const json = await res.json();
        if (json.success) { closeModal('cal-booking-modal'); this.reset(); document.getElementById('cal-preview').classList.add('hidden'); showSuccess(json.message); setTimeout(() => location.reload(), 2000); }
        else showError(json.message);
    } catch(e) { showError('Network error.'); }
    finally { btn.innerHTML = '<span class="material-symbols-outlined text-[18px]">check_circle</span> Confirm Booking'; btn.disabled = false; }
});
</script>
</body></html>
