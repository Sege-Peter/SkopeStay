<?php
$page_title = "Bookings | SkopeStay";
require_once '../includes/config.php';

// Add checkout support via PUT to bookings API
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['_action'])) {
    $id     = intval($_POST['id']);
    $action = $_POST['_action'];
    if ($action === 'checkout') {
        $pdo->prepare("UPDATE bookings SET status='CheckedOut' WHERE id=?")->execute([$id]);
        $pdo->prepare("UPDATE rooms SET status='Cleaning' WHERE id=(SELECT room_id FROM bookings WHERE id=? LIMIT 1)")->execute([$id]);
        echo json_encode(['success'=>true,'message'=>'Guest checked out successfully.']);
        exit;
    } elseif ($action === 'cancel') {
        $pdo->prepare("UPDATE bookings SET status='Cancelled' WHERE id=?")->execute([$id]);
        $pdo->prepare("UPDATE rooms SET status='Available' WHERE id=(SELECT room_id FROM bookings WHERE id=? LIMIT 1)")->execute([$id]);
        echo json_encode(['success'=>true,'message'=>'Booking cancelled.']);
        exit;
    }
}

$bookings = $pdo->query("SELECT b.*, r.room_number, r.type as room_type FROM bookings b LEFT JOIN rooms r ON b.room_id=r.id ORDER BY b.created_at DESC")->fetchAll();
$total_bookings  = count($bookings);
$arriving_today  = $pdo->query("SELECT COUNT(*) FROM bookings WHERE DATE(check_in)=CURDATE() AND status='Active'")->fetchColumn();
$departing_today = $pdo->query("SELECT COUNT(*) FROM bookings WHERE DATE(check_out)=CURDATE() AND status='Active'")->fetchColumn();
$new_requests    = $pdo->query("SELECT COUNT(*) FROM bookings WHERE DATE(created_at)=CURDATE()")->fetchColumn();
$avail_rooms     = $pdo->query("SELECT id,room_number,type,price FROM rooms WHERE status='Available' ORDER BY room_number")->fetchAll();

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
            <span>Main</span><span class="material-symbols-outlined text-[14px]">chevron_right</span><span class="text-primary font-bold">Bookings</span>
        </nav>
        <h1 class="text-2xl md:text-3xl font-extrabold text-on-surface">Manage Bookings</h1>
        <p class="text-on-surface-variant text-sm mt-1">Oversee reservations, track arrivals, and manage room allocations.</p>
    </div>
    <button onclick="openModal('new-booking-modal')" class="flex items-center gap-2 bg-primary text-white px-5 py-2.5 rounded-xl font-bold text-sm shadow-md hover:bg-primary/90 active:scale-95 transition-all">
        <span class="material-symbols-outlined text-[18px]">add</span> New Reservation
    </button>
</div>

<!-- KPIs -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
    <?php foreach ([
        ['Total Bookings','analytics',$total_bookings,'All time','bg-primary/10 text-primary'],
        ['Arriving Today','login',$arriving_today,'Pending check-in','bg-secondary-container text-on-secondary-container'],
        ['Departing Today','logout',$departing_today,'Rooms to inspect','bg-green-100 text-green-600'],
        ['New Today','inbox',$new_requests,'Added today','bg-error-container text-error'],
    ] as [$lbl,$icon,$val,$sub,$cls]): ?>
    <div class="bg-white rounded-2xl border border-outline-variant/30 shadow-sm p-5 flex items-center justify-between hover:shadow-md transition-all group">
        <div><p class="text-xs font-semibold text-on-surface-variant uppercase tracking-wide"><?= $lbl ?></p>
        <h3 class="text-4xl font-extrabold text-on-surface mt-1"><?= $val ?></h3>
        <p class="text-[11px] text-on-surface-variant mt-1"><?= $sub ?></p></div>
        <div class="w-12 h-12 rounded-full <?= $cls ?> flex items-center justify-center group-hover:scale-110 transition-transform">
            <span class="material-symbols-outlined text-[22px]"><?= $icon ?></span>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- Table Card -->
<div class="bg-white rounded-2xl border border-outline-variant/30 shadow-sm overflow-hidden">
    <div class="p-4 border-b border-outline-variant/30 flex flex-wrap items-center gap-3 bg-surface-container-low/30">
        <div class="relative flex-1 min-w-[220px]">
            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline text-[18px]">search</span>
            <input id="bk-search" oninput="filterBk()" type="text" placeholder="Search guest or booking ID..." class="w-full pl-9 pr-4 py-2.5 rounded-xl border border-outline-variant text-sm bg-white focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all"/>
        </div>
        <div class="flex bg-surface-container-low rounded-xl border border-outline-variant p-1 gap-1">
            <?php foreach (['All','Active','CheckedOut','Cancelled'] as $s): ?>
            <button onclick="filterStatus('<?= $s ?>')" data-status="<?= $s ?>" class="status-btn px-3 py-1.5 rounded-lg text-xs font-bold transition-colors <?= $s==='All' ? 'bg-primary text-white shadow' : 'text-on-surface-variant hover:bg-surface-container' ?>">
                <?= $s==='CheckedOut'?'Checked-out':$s ?>
            </button>
            <?php endforeach; ?>
        </div>
        <button onclick="exportCSV()" class="flex items-center gap-2 px-4 py-2.5 bg-surface-container rounded-xl border border-outline-variant text-sm font-semibold hover:bg-surface-container-high transition-colors ml-auto">
            <span class="material-symbols-outlined text-[18px]">download</span> CSV
        </button>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead><tr class="bg-surface-container-low/50 border-b border-outline-variant/30">
                <th class="px-5 py-3.5 text-xs font-bold uppercase text-on-surface">Guest</th>
                <th class="px-5 py-3.5 text-xs font-bold uppercase text-on-surface hidden md:table-cell">Room</th>
                <th class="px-5 py-3.5 text-xs font-bold uppercase text-on-surface hidden lg:table-cell">Check In/Out</th>
                <th class="px-5 py-3.5 text-xs font-bold uppercase text-on-surface text-right">Amount</th>
                <th class="px-5 py-3.5 text-xs font-bold uppercase text-on-surface text-center">Status</th>
                <th class="px-5 py-3.5 text-xs font-bold uppercase text-on-surface text-right">Actions</th>
            </tr></thead>
            <tbody class="divide-y divide-outline-variant/20">
            <?php if(empty($bookings)): ?>
            <tr><td colspan="6" class="text-center py-16 text-on-surface-variant opacity-40">
                <span class="material-symbols-outlined text-5xl block mb-2">event_busy</span>No bookings yet.</td></tr>
            <?php else: foreach($bookings as $b):
                $badge = ['Active'=>'bg-green-100 border-green-200 text-green-700','CheckedOut'=>'bg-gray-100 border-gray-200 text-gray-500','Cancelled'=>'bg-red-100 border-red-200 text-red-600'][$b['status']] ?? 'bg-gray-100 text-gray-500';
                $nights = (!empty($b['check_out'])&&!empty($b['check_in'])) ? max(1,(int)((strtotime($b['check_out'])-strtotime($b['check_in']))/86400)) : 1;
                $ini = strtoupper(substr($b['guest_name'],0,1)).(strpos($b['guest_name'],' ')!==false?strtoupper(substr(strrchr($b['guest_name'],' '),1,1)):'');
            ?>
            <tr class="hover:bg-surface-container-low/40 transition-colors group bk-row" data-name="<?= strtolower(htmlspecialchars($b['guest_name'])) ?>" data-status="<?= $b['status'] ?>">
                <td class="px-5 py-4"><div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-full bg-primary/10 text-primary flex items-center justify-center font-bold text-sm"><?= $ini ?></div>
                    <div><p class="font-bold text-sm"><?= htmlspecialchars($b['guest_name']) ?></p>
                    <p class="text-[11px] text-on-surface-variant">#BK-<?= str_pad($b['id'],5,'0',STR_PAD_LEFT) ?></p></div>
                </div></td>
                <td class="px-5 py-4 hidden md:table-cell">
                    <p class="text-sm font-medium"><?= htmlspecialchars($b['room_type']??'N/A') ?></p>
                    <p class="text-[11px] text-on-surface-variant">Room <?= htmlspecialchars($b['room_number']??'—') ?></p>
                </td>
                <td class="px-5 py-4 hidden lg:table-cell">
                    <p class="text-sm"><?= date('M j',strtotime($b['check_in'])) ?><?= !empty($b['check_out'])?' — '.date('M j',strtotime($b['check_out'])):'' ?></p>
                    <p class="text-[11px] text-on-surface-variant"><?= $nights ?> Night<?= $nights!==1?'s':'' ?></p>
                </td>
                <td class="px-5 py-4 text-right">
                    <p class="font-extrabold text-sm">KSh <?= number_format((float)$b['total_amount']) ?></p>
                    <p class="text-[11px] text-green-600 font-semibold"><?= $b['status']==='Active'?'Paid':($b['status']==='CheckedOut'?'Settled':'Refunded') ?></p>
                </td>
                <td class="px-5 py-4 text-center">
                    <span class="px-2.5 py-1 rounded-full border text-[11px] font-bold uppercase <?= $badge ?>"><?= $b['status']==='CheckedOut'?'Checked-out':$b['status'] ?></span>
                </td>
                </div></td>
                <td class="px-5 py-4 text-right"><div class="flex items-center justify-end gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                    <?php if($b['status']==='Active'): ?>
                    <button onclick="doAction('checkout',<?= $b['id'] ?>,'<?= addslashes($b['guest_name']) ?>')" class="px-3 py-1.5 bg-secondary text-white text-xs font-bold rounded-xl hover:bg-secondary/90 active:scale-95">Check Out</button>
                    <button onclick="doAction('cancel',<?= $b['id'] ?>,'<?= addslashes($b['guest_name']) ?>')" class="p-1.5 text-on-surface-variant hover:text-error transition-colors" title="Cancel">
                        <span class="material-symbols-outlined text-[18px]">cancel</span></button>
                    <?php endif; ?>
                    <?php if(in_array($b['status'],['Active','CheckedOut'])): ?>
                    <a href="print_receipt.php?type=booking&id=<?= $b['id'] ?>" target="_blank" class="p-1.5 text-on-surface-variant hover:text-primary transition-colors" title="Print Receipt">
                        <span class="material-symbols-outlined text-[18px]">print</span></a>
                    <?php endif; ?>
                </div></td>
            </tr>
            <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
    <div class="px-5 py-3.5 border-t border-outline-variant/30 bg-surface-container-low/20 flex items-center justify-between">
        <p class="text-xs text-on-surface-variant">Showing <?= count($bookings) ?> bookings</p>
    </div>
</div>
</main>
<?php include '../includes/footer.php'; ?>
</div>

<!-- New Booking Modal -->
<div id="new-booking-modal" class="fixed inset-0 z-[100] hidden items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" onclick="closeModal('new-booking-modal')"></div>
    <div class="relative bg-white rounded-3xl shadow-2xl w-full max-w-lg mx-auto animate-modal">
        <div class="flex items-center justify-between p-6 border-b border-outline-variant/30">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-primary/10 rounded-xl flex items-center justify-center"><span class="material-symbols-outlined text-primary">event_note</span></div>
                <div><h3 class="font-bold text-base">New Reservation</h3><p class="text-xs text-on-surface-variant">Fill in guest booking details</p></div>
            </div>
            <button onclick="closeModal('new-booking-modal')" class="w-8 h-8 rounded-full bg-surface-container-high flex items-center justify-center hover:bg-error-container hover:text-error transition-colors"><span class="material-symbols-outlined text-[18px]">close</span></button>
        </div>
        <form id="bk-form" class="p-6 space-y-4">
            <div><label class="block text-xs font-bold text-on-surface-variant uppercase tracking-wide mb-1.5">Guest Full Name *</label>
                <div class="relative"><span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline text-[18px]">person</span>
                <input type="text" name="guest_name" required placeholder="e.g. Jane Kamau" class="w-full pl-9 pr-4 py-2.5 rounded-xl border border-outline-variant focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none text-sm bg-surface-container-low transition-all"/></div>
            </div>
            <div><label class="block text-xs font-bold text-on-surface-variant uppercase tracking-wide mb-1.5">Select Room *</label>
                <select name="room_id" id="bk-room" required class="w-full px-3 py-2.5 rounded-xl border border-outline-variant focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none text-sm bg-surface-container-low appearance-none">
                    <option value="">-- Choose an available room --</option>
                    <?php foreach($avail_rooms as $r): ?>
                    <option value="<?= $r['id'] ?>" data-price="<?= $r['price'] ?>">Room <?= htmlspecialchars($r['room_number']) ?> — <?= htmlspecialchars($r['type']) ?> (KSh <?= number_format((float)$r['price']) ?>/night)</option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div><label class="block text-xs font-bold text-on-surface-variant uppercase tracking-wide mb-1.5">Check-In *</label>
                    <input type="date" name="check_in" id="bk-ci" required class="w-full px-3 py-2.5 rounded-xl border border-outline-variant focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none text-sm bg-surface-container-low"/></div>
                <div><label class="block text-xs font-bold text-on-surface-variant uppercase tracking-wide mb-1.5">Check-Out</label>
                    <input type="date" name="check_out" id="bk-co" class="w-full px-3 py-2.5 rounded-xl border border-outline-variant focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none text-sm bg-surface-container-low"/></div>
            </div>
            <div id="bk-preview" class="hidden bg-primary/5 border border-primary/20 rounded-xl p-3 text-sm flex items-center justify-between">
                <span class="text-on-surface-variant">Estimated Total</span>
                <span id="bk-est" class="font-extrabold text-primary text-base">KSh 0</span>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="closeModal('new-booking-modal')" class="flex-1 py-3 rounded-xl border border-outline-variant text-on-surface-variant font-semibold text-sm hover:bg-surface-container transition-colors">Cancel</button>
                <button type="submit" id="bk-btn" class="flex-1 py-3 rounded-xl bg-primary text-white font-bold text-sm hover:bg-primary/90 shadow-md active:scale-95 transition-all flex items-center justify-center gap-2">
                    <span class="material-symbols-outlined text-[18px]">check_circle</span> Confirm Booking
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
function openModal(id){const e=document.getElementById(id);e.classList.remove('hidden');e.classList.add('flex');}
function closeModal(id){const e=document.getElementById(id);e.classList.add('hidden');e.classList.remove('flex');}
function showSuccess(m){Swal.fire({icon:'success',title:'Success!',text:m,timer:3000,timerProgressBar:true,showConfirmButton:false,toast:true,position:'top-end'});}
function showError(m){Swal.fire({icon:'error',title:'Error',text:m,confirmButtonColor:'rgb(37,99,235)'});}

function filterBk(){
    const q=document.getElementById('bk-search').value.toLowerCase();
    document.querySelectorAll('.bk-row').forEach(r=>r.style.display=r.dataset.name.includes(q)?'':'none');
}
function filterStatus(s){
    document.querySelectorAll('.status-btn').forEach(b=>{
        const a=b.dataset.status===s;
        b.classList.toggle('bg-primary',a);b.classList.toggle('text-white',a);b.classList.toggle('shadow',a);
        b.classList.toggle('text-on-surface-variant',!a);
    });
    document.querySelectorAll('.bk-row').forEach(r=>r.style.display=(s==='All'||r.dataset.status===s)?'':'none');
}

function calcPrice(){
    const sel=document.getElementById('bk-room');
    const price=parseFloat(sel.options[sel.selectedIndex]?.dataset?.price||0);
    const ci=document.getElementById('bk-ci').value, co=document.getElementById('bk-co').value;
    const prev=document.getElementById('bk-preview'), est=document.getElementById('bk-est');
    if(!price||!ci){prev.classList.add('hidden');return;}
    let nights=1;
    if(ci&&co){const d=(new Date(co)-new Date(ci))/86400000;if(d>0)nights=d;}
    est.textContent=`KSh ${(price*nights).toLocaleString()} (${nights} night${nights!==1?'s':''})`;
    prev.classList.remove('hidden');
}
document.getElementById('bk-room').addEventListener('change',calcPrice);
document.getElementById('bk-ci').addEventListener('change',calcPrice);
document.getElementById('bk-co').addEventListener('change',calcPrice);
document.getElementById('bk-ci').valueAsDate=new Date();

document.getElementById('bk-form').addEventListener('submit',async function(e){
    e.preventDefault();
    const btn=document.getElementById('bk-btn');
    btn.innerHTML='<span class="material-symbols-outlined animate-spin text-[18px]">sync</span> Processing...';btn.disabled=true;
    const data={guest_name:this.guest_name.value,room_id:this.room_id.value,check_in:this.check_in.value,check_out:this.check_out.value};
    try{
        const res=await fetch('../api/bookings.php',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify(data)});
        const json=await res.json();
        if(json.success){closeModal('new-booking-modal');this.reset();document.getElementById('bk-preview').classList.add('hidden');showSuccess(json.message);setTimeout(()=>location.reload(),2000);}
        else showError(json.message);
    }catch(e){showError('Network error.');}
    finally{btn.innerHTML='<span class="material-symbols-outlined text-[18px]">check_circle</span> Confirm Booking';btn.disabled=false;}
});

async function doAction(action,id,name){
    const cfg={
        checkout:{title:`Check out ${name}?`,text:'Room will be queued for cleaning.',color:'rgb(249,115,22)',label:'Check Out'},
        cancel:{title:`Cancel booking for ${name}?`,text:'Room will be made available.',color:'rgb(239,68,68)',label:'Cancel Booking'}
    }[action];
    Swal.fire({title:cfg.title,text:cfg.text,icon:'question',showCancelButton:true,confirmButtonColor:cfg.color,confirmButtonText:cfg.label})
    .then(async r=>{
        if(!r.isConfirmed)return;
        const form=new FormData();form.append('id',id);form.append('_action',action);
        const res=await fetch('bookings.php',{method:'POST',body:form});
        const json=await res.json();
        if(json.success){showSuccess(json.message);setTimeout(()=>location.reload(),1800);}
        else showError(json.message);
    });
}

function exportCSV(){
    const rows=[['ID','Guest','Status']];
    document.querySelectorAll('.bk-row').forEach(r=>rows.push([r.querySelector('p:last-child')?.textContent||'',r.dataset.name,r.dataset.status]));
    const a=document.createElement('a');a.href=URL.createObjectURL(new Blob([rows.map(r=>r.join(',')).join('\n')],{type:'text/csv'}));
    a.download=`bookings_${new Date().toISOString().slice(0,10)}.csv`;a.click();
    showSuccess('CSV exported!');
}
</script>
</body></html>
