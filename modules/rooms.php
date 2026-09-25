<?php
$page_title = "Room Management | SkopeStay";
require_once '../includes/config.php';

$rooms = $pdo->query("SELECT * FROM rooms ORDER BY floor ASC, room_number ASC")->fetchAll();
$total    = count($rooms);
$available= count(array_filter($rooms, fn($r) => $r['status'] === 'Available'));
$occupied = count(array_filter($rooms, fn($r) => $r['status'] === 'Occupied'));
$cleaning = count(array_filter($rooms, fn($r) => $r['status'] === 'Cleaning'));

include '../includes/head.php';
?>
<!DOCTYPE html>
<html class="light" lang="en">
<body class="bg-background text-on-surface font-body-md min-h-screen">

<?php include '../includes/sidebar.php'; ?>

<div class="md:ml-64 flex flex-col min-h-screen">
<?php include '../includes/header.php'; ?>

<main class="p-4 md:p-8 flex-grow space-y-6">

    <!-- Page Title + Add Button -->
    <div class="flex justify-between items-end">
        <div>
            <nav class="flex items-center gap-2 text-on-surface-variant text-xs font-semibold mb-2">
                <span>Management</span>
                <span class="material-symbols-outlined text-[14px]">chevron_right</span>
                <span class="text-primary font-bold">Room Management</span>
            </nav>
            <h1 class="font-bold text-2xl md:text-3xl text-on-surface">Room Availability</h1>
        </div>
        <a href="add_room.php" class="flex items-center gap-2 bg-primary text-white px-5 py-2.5 rounded-xl font-bold text-sm shadow-md hover:bg-primary/90 active:scale-95 transition-all">
            <span class="material-symbols-outlined text-[18px]">add</span> Add Room
        </a>
    </div>

    <!-- Stats Strip -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
        <?php
        $strips = [
            ['label'=>'Total Rooms', 'val'=>$total,     'color'=>'bg-primary/10 text-primary'],
            ['label'=>'Available',   'val'=>$available,  'color'=>'bg-green-100 text-green-700'],
            ['label'=>'Occupied',    'val'=>$occupied,   'color'=>'bg-red-100 text-red-600'],
            ['label'=>'Cleaning',    'val'=>$cleaning,   'color'=>'bg-amber-100 text-amber-700'],
        ];
        foreach($strips as $s): ?>
        <div class="bg-white rounded-2xl border border-outline-variant/30 shadow-sm p-4 flex items-center gap-3">
            <span class="text-3xl font-extrabold <?= explode(' ',$s['color'])[1] ?>"><?= $s['val'] ?></span>
            <span class="text-sm font-semibold text-on-surface-variant"><?= $s['label'] ?></span>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white rounded-2xl border border-outline-variant/30 shadow-sm p-4 flex flex-wrap items-center gap-3">
        <div class="flex gap-2 flex-wrap">
            <button onclick="filterRooms('all')" id="filter-all" class="px-4 py-1.5 bg-primary text-white rounded-full text-sm font-semibold filter-btn">All (<?= $total ?>)</button>
            <button onclick="filterRooms('Available')" id="filter-Available" class="px-4 py-1.5 bg-surface-container text-on-surface-variant rounded-full text-sm font-semibold filter-btn hover:bg-primary/10 transition-colors">Available (<?= $available ?>)</button>
            <button onclick="filterRooms('Occupied')" id="filter-Occupied" class="px-4 py-1.5 bg-surface-container text-on-surface-variant rounded-full text-sm font-semibold filter-btn hover:bg-primary/10 transition-colors">Occupied (<?= $occupied ?>)</button>
            <button onclick="filterRooms('Cleaning')" id="filter-Cleaning" class="px-4 py-1.5 bg-surface-container text-on-surface-variant rounded-full text-sm font-semibold filter-btn hover:bg-primary/10 transition-colors">Cleaning (<?= $cleaning ?>)</button>
        </div>
        <div class="ml-auto relative">
            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline text-[18px]">search</span>
            <input id="room-search" oninput="searchRooms(this.value)" type="text" placeholder="Search rooms..." class="pl-9 pr-4 py-2 rounded-xl border border-outline-variant text-sm bg-surface-container-low focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all"/>
        </div>
    </div>

    <!-- Rooms Grid -->
    <div id="rooms-grid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
        <?php foreach ($rooms as $i => $room):
            $status = $room['status'];
            $statusStyles = [
                'Available'   => ['dot'=>'bg-green-500', 'badge'=>'bg-green-100 border-green-200 text-green-700', 'overlay'=>''],
                'Occupied'    => ['dot'=>'bg-red-500',   'badge'=>'bg-red-100 border-red-200 text-red-600',   'overlay'=>'grayscale-[0.15]'],
                'Cleaning'    => ['dot'=>'bg-amber-400 animate-pulse', 'badge'=>'bg-amber-100 border-amber-200 text-amber-700', 'overlay'=>''],
                'Maintenance' => ['dot'=>'bg-gray-400',  'badge'=>'bg-gray-100 border-gray-200 text-gray-600', 'overlay'=>'grayscale-[0.3]'],
            ];
            $s = $statusStyles[$status] ?? $statusStyles['Available'];
            $imgs = [
                'Deluxe King'      => 'https://lh3.googleusercontent.com/aida-public/AB6AXuAkjEupU35BMkONDPPOUDmARx6bb07eiESHjotC7ubPCk--06bSXfanL9G65uuxREikoJBV-dheWORt7SjB2RNi5T45Uix323IS2DANPorRUBVaibpVIHP4V7w37Xm37dKsePhxUPcoU50RmmhA4KmvgMk1YTFHzMxjSyHQ27c4TYgEMuJ2nB272t0fl6zVQ4GHaE6Cf9-aEiueuWwHSXP5jxmmCDbub3WRzb8AY4cIU0XuRZu87h3oOKJJJG4k7Z0vo9Y9q0hqejX1',
                'Executive Suite'  => 'https://lh3.googleusercontent.com/aida-public/AB6AXuA1j2r1_Lqd5bC4haKTLsvICDXdthjp1LFB4dU9wXo1erQmcPAgKmLpqJEkRVcRjpgHFTqGt2T6YcFv8cdfN7g6-bDGWNhXuvdGkYK72OOha7CmyTSIUh5c_HFSBnGpCD9V5JObKcdQbARKfxewPvJvlxRgiGMoahxy-hfwS6d6Q9re2Eekdhp8XFBQZWeT8ENdUvXAdNbncODfk6OUIuIkg7_hnbl4NHnUhLmdIwUXon-UuHGQJyePp3RuZ8iR9yR4Sbt7tT2hOybE',
                'Single Standard'  => 'https://lh3.googleusercontent.com/aida-public/AB6AXuCtsEvae48WE_diedZQOIwJRkeMIbWD3sV2rahKRirBrXasXe3kQQ9O4TBl-1bimKVkzp-dfeBp2lDcJI1dCe3oUXV2OcI7rWtdlibH5njUjBQPRa31Pc9yoINMhThzBjfLFCf4Lzo3hO3TAftvF5_d1KYOUPu4MjuAnOiFZEW7fbsAY4HuvGhefs1kaGm3GSVtVs7mtXsVnDMKgvhczqedKNARxlcmOnq6RrfcjdOsYJnS-rlVSb5wJT80uUMTQWEOtoNGaef_FZgQ',
                'Family Suite'     => 'https://lh3.googleusercontent.com/aida-public/AB6AXuAFCJqywhSVw-rEwCp2pp4m_NKlJYE5Q5XlC_QV04QCinPiYYgxyTsL9uhYvvcMVTY2jsltNZLUCW0AjlYNeANsMBvFF_c3vvg4sr3fu1X536Qkbw7cssYZ3O5V0uQ263yknh11H6bB6QpmemZL64BdTnfyUgoAum5KHer59dHPKGf-whl02UmOXV1q4_Luqy2VNxDfAWuxFpe8vXUpGzdzU6JxS389O8mHhQkNV6KcgL0Y-EyEMrIu_7r8ZIds5V3Ajim_RnY349b3',
                'Honeymoon Suite'  => 'https://lh3.googleusercontent.com/aida-public/AB6AXuB6D8L-_HCBA53hLXKi3UHM_JWbEQBl-vGXP6wkvx-ZHcbBitz2Tu-PhmABjpNA0MiJgpcxRNTREW-DCsCIbmitMB8tt6Yx8o-ENCd3nNsa1onJqn-d5VGjmrnC2HbaHLIGVKK3i1gzHQmUp6jrABeRGBQgLncfUwWanzTxmAaKguNpTF7nw66OXTB5tZlDg_0_Vzjy61GsAsfdu6CZMlv8lzEVPBrparSgZT1ZPHRc_-71BI79dEp7SehRyE3NTL3yNDnlyuU6Q8LT',
                'Presidential Suite'=> 'https://lh3.googleusercontent.com/aida-public/AB6AXuAkjEupU35BMkONDPPOUDmARx6bb07eiESHjotC7ubPCk--06bSXfanL9G65uuxREikoJBV-dheWORt7SjB2RNi5T45Uix323IS2DANPorRUBVaibpVIHP4V7w37Xm37dKsePhxUPcoU50RmmhA4KmvgMk1YTFHzMxjSyHQ27c4TYgEMuJ2nB272t0fl6zVQ4GHaE6Cf9-aEiueuWwHSXP5jxmmCDbub3WRzb8AY4cIU0XuRZu87h3oOKJJJG4k7Z0vo9Y9q0hqejX1',
            ];
            $img = $imgs[$room['type']] ?? $imgs['Deluxe King'];
        ?>
        <div class="bg-white rounded-2xl border border-outline-variant/30 shadow-sm overflow-hidden room-card group hover:-translate-y-1 hover:shadow-lg transition-all duration-300"
             data-status="<?= $status ?>" data-room="<?= strtolower($room['room_number']) ?>" data-type="<?= strtolower($room['type']) ?>"
             style="animation-delay:<?= $i * 60 ?>ms; opacity:0; animation: fadeUp 0.4s ease forwards;">
            <!-- Image -->
            <div class="h-40 relative overflow-hidden">
                <img src="<?= $img ?>" alt="<?= htmlspecialchars($room['type']) ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 <?= $s['overlay'] ?>"/>
                <div class="absolute top-2.5 left-2.5 bg-white/90 backdrop-blur-sm px-2.5 py-1 rounded-full text-[10px] font-bold text-primary shadow-sm">Floor <?= $room['floor'] ?></div>
                <div class="absolute top-2.5 right-2.5 flex items-center gap-1.5 backdrop-blur-sm border px-2.5 py-1 rounded-full <?= $s['badge'] ?>">
                    <span class="w-1.5 h-1.5 rounded-full <?= $s['dot'] ?> inline-block"></span>
                    <span class="text-[10px] font-bold uppercase"><?= $status ?></span>
                </div>
            </div>
            <!-- Body -->
            <div class="p-4">
                <div class="flex justify-between items-start mb-3">
                    <div>
                        <h3 class="font-bold text-on-surface text-base">Room <?= htmlspecialchars($room['room_number']) ?></h3>
                        <p class="text-xs text-on-surface-variant"><?= htmlspecialchars($room['type']) ?></p>
                    </div>
                    <div class="text-right">
                        <p class="font-extrabold text-primary text-base">KSh <?= number_format((float)$room['price']) ?></p>
                        <p class="text-[10px] text-on-surface-variant">/night</p>
                    </div>
                </div>
                <div class="flex gap-2 pt-3 border-t border-outline-variant/20">
                    <?php if ($status === 'Available'): ?>
                    <button onclick="changeStatus(<?= $room['id'] ?>, 'Occupied', this)" class="flex-1 py-2 text-xs font-bold bg-primary text-white rounded-xl hover:bg-primary/90 transition-colors active:scale-95">Book Now</button>
                    <?php elseif ($status === 'Occupied'): ?>
                    <button onclick="changeStatus(<?= $room['id'] ?>, 'Cleaning', this)" class="flex-1 py-2 text-xs font-bold bg-amber-500 text-white rounded-xl hover:bg-amber-600 transition-colors active:scale-95">Check Out</button>
                    <?php elseif ($status === 'Cleaning'): ?>
                    <button onclick="changeStatus(<?= $room['id'] ?>, 'Available', this)" class="flex-1 py-2 text-xs font-bold bg-green-600 text-white rounded-xl hover:bg-green-700 transition-colors active:scale-95">Mark Ready</button>
                    <?php endif; ?>
                    <button onclick="deleteRoom(<?= $room['id'] ?>, 'Room <?= $room['room_number'] ?>')" class="py-2 px-3 text-xs font-bold border border-error/30 text-error rounded-xl hover:bg-error-container transition-colors active:scale-95">
                        <span class="material-symbols-outlined text-[16px]">delete</span>
                    </button>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
        <?php if (empty($rooms)): ?>
        <div class="col-span-4 flex flex-col items-center justify-center py-20 text-on-surface-variant opacity-40">
            <span class="material-symbols-outlined text-6xl mb-3">bed</span>
            <p class="font-semibold">No rooms found. Add your first room!</p>
        </div>
        <?php endif; ?>
    </div>
</main>

<?php include '../includes/footer.php'; ?>
</div>

<style>
@keyframes fadeUp {
    from { opacity:0; transform: translateY(16px); }
    to   { opacity:1; transform: translateY(0); }
}
.room-card { animation: fadeUp 0.4s ease forwards; }
</style>

<script>
function showSuccess(msg) { Swal.fire({icon:'success',title:'Success!',text:msg,timer:3000,timerProgressBar:true,showConfirmButton:false,toast:true,position:'top-end'}); }
function showError(msg) { Swal.fire({icon:'error',title:'Error',text:msg,confirmButtonColor:'rgb(37,99,235)'}); }

// ---- Filter rooms ----
function filterRooms(status) {
    document.querySelectorAll('.filter-btn').forEach(b => {
        b.classList.remove('bg-primary','text-white');
        b.classList.add('bg-surface-container','text-on-surface-variant');
    });
    const activeBtn = document.getElementById('filter-' + status);
    if (activeBtn) { activeBtn.classList.add('bg-primary','text-white'); activeBtn.classList.remove('bg-surface-container','text-on-surface-variant'); }
    document.querySelectorAll('.room-card').forEach(card => {
        const show = status === 'all' || card.dataset.status === status;
        card.style.display = show ? '' : 'none';
    });
}

// ---- Search rooms ----
function searchRooms(q) {
    const query = q.toLowerCase();
    document.querySelectorAll('.room-card').forEach(card => {
        const match = card.dataset.room.includes(query) || card.dataset.type.includes(query);
        card.style.display = match ? '' : 'none';
    });
}

// ---- Change room status ----
async function changeStatus(id, newStatus, btn) {
    const labels = { 'Occupied': 'Book this room?', 'Cleaning': 'Check out guest and start cleaning?', 'Available': 'Mark room as ready/available?' };
    Swal.fire({
        title: labels[newStatus] || 'Update status?',
        icon: 'question', showCancelButton: true,
        confirmButtonColor: 'rgb(37,99,235)', cancelButtonColor: 'rgb(239,68,68)',
        confirmButtonText: 'Yes, proceed!'
    }).then(async r => {
        if (!r.isConfirmed) return;
        try {
            const res = await fetch('../api/rooms.php', {
                method: 'PUT', headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({id, status: newStatus})
            });
            const json = await res.json();
            if (json.success) { showSuccess(json.message); setTimeout(() => location.reload(), 1800); }
            else showError(json.message);
        } catch(e) { showError('Network error.'); }
    });
}

// ---- Delete room ----
function deleteRoom(id, label) {
    Swal.fire({
        title: `Delete ${label}?`, text: 'This action cannot be undone.', icon: 'warning',
        showCancelButton: true, confirmButtonColor: 'rgb(239,68,68)', cancelButtonColor: 'rgb(100,116,139)',
        confirmButtonText: 'Delete'
    }).then(async r => {
        if (!r.isConfirmed) return;
        try {
            const res = await fetch(`../api/rooms.php?id=${id}`, { method: 'DELETE' });
            const json = await res.json();
            if (json.success) { showSuccess(json.message); setTimeout(() => location.reload(), 1800); }
            else showError(json.message);
        } catch(e) { showError('Network error.'); }
    });
}

</script>
</body>
</html>
