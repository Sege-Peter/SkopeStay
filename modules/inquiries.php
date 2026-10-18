<?php
$page_title = "Guest Inquiries | SkopeStay";
require_once '../includes/config.php';

// ---- JSON actions ---------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    verify_csrf();
    $data = json_decode(file_get_contents('php://input'), true) ?: [];
    $id = (int)($data['id'] ?? 0);
    $act = $data['action'] ?? '';
    $uid = $_SESSION['user_id'];

    if ($act === 'status' && in_array($data['status'] ?? '', ['Unread', 'Read', 'Responded'], true)) {
        $pdo->prepare("UPDATE inquiries SET status = ? WHERE id = ?")->execute([$data['status'], $id]);
        log_action($pdo, $uid, 'inquiry_status', 'Inquiries', "Marked inquiry #$id as {$data['status']}", $id);
        echo json_encode(['success' => true]);
    } elseif ($act === 'notes') {
        $pdo->prepare("UPDATE inquiries SET staff_notes = ? WHERE id = ?")->execute([mb_substr(trim($data['notes'] ?? ''), 0, 5000), $id]);
        echo json_encode(['success' => true, 'message' => 'Notes saved.']);
    } elseif ($act === 'delete') {
        $pdo->prepare("DELETE FROM inquiries WHERE id = ?")->execute([$id]);
        log_action($pdo, $uid, 'inquiry_delete', 'Inquiries', "Deleted inquiry #$id", $id);
        echo json_encode(['success' => true, 'message' => 'Inquiry deleted.']);
    } elseif ($act === 'mark_all_read') {
        $n = $pdo->exec("UPDATE inquiries SET status = 'Read' WHERE status = 'Unread'");
        echo json_encode(['success' => true, 'message' => "$n inquiry(ies) marked as read."]);
    } elseif ($act === 'unsubscribe') {
        $pdo->prepare("DELETE FROM newsletter_subscribers WHERE id = ?")->execute([$id]);
        echo json_encode(['success' => true, 'message' => 'Subscriber removed.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Unknown action.']);
    }
    exit;
}

// ---- CSV exports ----------------------------------------------------------
if (isset($_GET['export'])) {
    $which = $_GET['export'] === 'subscribers' ? 'subscribers' : 'inquiries';
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $which . '_' . date('Y-m-d') . '.csv"');
    $out = fopen('php://output', 'w');
    fwrite($out, "\xEF\xBB\xBF"); // Excel-friendly UTF-8
    if ($which === 'subscribers') {
        fputcsv($out, ['Email', 'Source', 'Subscribed']);
        foreach ($pdo->query("SELECT email, source, created_at FROM newsletter_subscribers ORDER BY created_at DESC") as $r) fputcsv($out, $r);
    } else {
        fputcsv($out, ['ID', 'Name', 'Email', 'Phone', 'Type', 'Status', 'Message', 'Staff notes', 'Received']);
        foreach ($pdo->query("SELECT id, full_name, email, phone, inquiry_type, status, message, staff_notes, created_at FROM inquiries ORDER BY created_at DESC") as $r) fputcsv($out, $r);
    }
    exit;
}

$inquiries = $pdo->query("SELECT * FROM inquiries ORDER BY (status = 'Unread') DESC, created_at DESC LIMIT 500")->fetchAll();
$subscribers = $pdo->query("SELECT * FROM newsletter_subscribers ORDER BY created_at DESC LIMIT 500")->fetchAll();
$counts = ['All' => count($inquiries), 'Unread' => 0, 'Read' => 0, 'Responded' => 0];
foreach ($inquiries as $q) $counts[$q['status']] = ($counts[$q['status']] ?? 0) + 1;
$open_id = (int)($_GET['open'] ?? 0);
$hotel = brand('hotel_name');

include '../includes/head.php';
?>
<body class="bg-background text-on-surface min-h-screen">

<?php include '../includes/sidebar.php'; ?>

<div class="md:ml-64 flex flex-col min-h-screen">
<?php include '../includes/header.php'; ?>

<main class="p-4 md:p-8 flex-grow space-y-6 max-w-[1600px] w-full mx-auto">

    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-end gap-4">
        <div>
            <nav class="flex items-center gap-2 text-on-surface-variant text-xs font-semibold mb-2">
                <span>Operations</span><span class="material-symbols-outlined text-[14px]">chevron_right</span>
                <span class="text-primary font-bold">Guest Inquiries</span>
            </nav>
            <h1 class="text-2xl md:text-3xl font-extrabold text-on-surface">Guest Inquiries</h1>
            <p class="text-sm text-on-surface-variant mt-1">Messages from the website contact form and booking requests, plus newsletter sign-ups.</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <button onclick="act({action:'mark_all_read'}, true)" class="px-4 py-2 rounded-xl border border-outline-variant bg-surface text-xs font-bold hover:border-primary flex items-center gap-1.5"><span class="material-symbols-outlined text-[18px]">done_all</span>Mark all read</button>
            <a href="?export=inquiries" class="px-4 py-2 rounded-xl bg-navy text-white text-xs font-bold flex items-center gap-1.5"><span class="material-symbols-outlined text-[18px] text-gold">download</span>Export CSV</a>
        </div>
    </div>

    <!-- KPI strip -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
        <?php foreach ([['Total', $counts['All'], 'forum', 'text-primary'], ['Unread', $counts['Unread'], 'mark_email_unread', 'text-error'], ['Responded', $counts['Responded'], 'mark_email_read', 'text-success'], ['Subscribers', count($subscribers), 'campaign', 'text-gold']] as [$l, $v, $ic, $c]): ?>
        <div class="bg-surface rounded-2xl border border-outline-variant/40 p-4 flex items-center gap-3">
            <span class="w-11 h-11 rounded-xl bg-surface-container flex items-center justify-center <?= $c ?>"><span class="material-symbols-outlined"><?= $ic ?></span></span>
            <div><p class="text-2xl font-extrabold leading-none"><?= (int)$v ?></p><p class="text-[11px] uppercase tracking-wider font-bold text-on-surface-variant mt-1"><?= $l ?></p></div>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- View switch -->
    <div class="flex gap-2 border-b border-outline-variant/40">
        <button class="view-tab px-4 py-2.5 text-sm font-bold border-b-2 border-primary text-primary" data-view="inbox">Inbox</button>
        <button class="view-tab px-4 py-2.5 text-sm font-bold border-b-2 border-transparent text-on-surface-variant" data-view="subs">Newsletter (<?= count($subscribers) ?>)</button>
    </div>

    <!-- Inbox -->
    <section id="view-inbox" class="grid grid-cols-1 xl:grid-cols-[minmax(0,420px)_1fr] gap-5">
        <div class="bg-surface rounded-2xl border border-outline-variant/40 overflow-hidden flex flex-col max-h-[75vh]">
            <div class="p-3 border-b border-outline-variant/40 space-y-2">
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline text-[18px]">search</span>
                    <input id="inq-search" type="search" placeholder="Search name, email, message..." class="w-full bg-surface-container-low border border-outline-variant/40 rounded-xl pl-9 pr-3 py-2 text-sm outline-none focus:border-primary">
                </div>
                <div class="flex gap-1.5 overflow-x-auto scrollbar-hide">
                    <?php foreach ($counts as $st => $n): ?>
                    <button class="filter-btn shrink-0 px-3 py-1.5 rounded-lg text-xs font-bold <?= $st === 'All' ? 'bg-primary text-white' : 'text-on-surface-variant hover:bg-surface-container' ?>" data-status="<?= $st ?>"><?= $st ?> <span class="opacity-70"><?= $n ?></span></button>
                    <?php endforeach; ?>
                </div>
            </div>
            <div id="inq-list" class="overflow-y-auto divide-y divide-outline-variant/30 flex-1">
                <?php if (!$inquiries): ?>
                <div class="p-10 text-center text-on-surface-variant"><span class="material-symbols-outlined text-5xl opacity-30">inbox</span><p class="text-sm mt-2">No inquiries yet. Messages from the website contact form will appear here.</p></div>
                <?php endif; ?>
                <?php foreach ($inquiries as $q): ?>
                <button type="button" class="inq-row w-full text-left p-4 hover:bg-surface-container-low transition-colors flex gap-3" data-id="<?= (int)$q['id'] ?>" data-status="<?= e($q['status']) ?>"
                        data-search="<?= e(strtolower($q['full_name'] . ' ' . $q['email'] . ' ' . $q['phone'] . ' ' . $q['message'] . ' ' . $q['inquiry_type'])) ?>">
                    <span class="w-10 h-10 rounded-full <?= $q['status'] === 'Unread' ? 'bg-primary text-white' : 'bg-surface-container text-on-surface-variant' ?> flex items-center justify-center font-bold text-xs shrink-0"><?= e(strtoupper(mb_substr($q['full_name'], 0, 2))) ?></span>
                    <span class="min-w-0 flex-1">
                        <span class="flex items-center justify-between gap-2">
                            <span class="text-sm truncate <?= $q['status'] === 'Unread' ? 'font-extrabold' : 'font-semibold' ?>"><?= e($q['full_name']) ?></span>
                            <span class="text-[10px] text-on-surface-variant shrink-0"><?= date('M j', strtotime($q['created_at'])) ?></span>
                        </span>
                        <span class="block text-[11px] font-bold text-primary"><?= e($q['inquiry_type']) ?></span>
                        <span class="block text-xs text-on-surface-variant truncate"><?= e($q['message']) ?></span>
                    </span>
                    <?php if ($q['status'] === 'Unread'): ?><span class="w-2 h-2 rounded-full bg-error mt-2 shrink-0" aria-label="Unread"></span><?php endif; ?>
                </button>
                <?php endforeach; ?>
                <p id="inq-empty" class="hidden p-8 text-center text-sm text-on-surface-variant">No inquiries match this filter.</p>
            </div>
        </div>

        <!-- Detail -->
        <div id="inq-detail" class="bg-surface rounded-2xl border border-outline-variant/40 p-6 min-h-[320px]">
            <div class="h-full flex flex-col items-center justify-center text-center text-on-surface-variant py-16">
                <span class="material-symbols-outlined text-6xl opacity-30">mark_email_unread</span>
                <p class="text-sm mt-3">Select an inquiry to read and respond.</p>
            </div>
        </div>
    </section>

    <!-- Subscribers -->
    <section id="view-subs" class="hidden bg-surface rounded-2xl border border-outline-variant/40 overflow-hidden">
        <div class="p-4 border-b border-outline-variant/40 flex items-center justify-between gap-3">
            <p class="text-sm font-bold">Newsletter subscribers</p>
            <a href="?export=subscribers" class="px-3 py-1.5 rounded-lg bg-navy text-white text-xs font-bold flex items-center gap-1"><span class="material-symbols-outlined text-[16px] text-gold">download</span>CSV</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm min-w-[480px]">
                <thead class="bg-surface-container-low text-[11px] uppercase tracking-wider text-on-surface-variant"><tr><th class="p-3 text-left">Email</th><th class="p-3 text-left">Source</th><th class="p-3 text-left">Subscribed</th><th class="p-3"></th></tr></thead>
                <tbody class="divide-y divide-outline-variant/30">
                    <?php if (!$subscribers): ?><tr><td colspan="4" class="p-8 text-center text-on-surface-variant">No subscribers yet.</td></tr><?php endif; ?>
                    <?php foreach ($subscribers as $sub): ?>
                    <tr>
                        <td class="p-3 font-semibold"><a href="mailto:<?= e($sub['email']) ?>" class="hover:text-primary"><?= e($sub['email']) ?></a></td>
                        <td class="p-3 text-on-surface-variant"><?= e($sub['source']) ?></td>
                        <td class="p-3 text-on-surface-variant"><?= date('M j, Y', strtotime($sub['created_at'])) ?></td>
                        <td class="p-3 text-right"><button onclick="removeSub(<?= (int)$sub['id'] ?>)" class="p-1.5 text-on-surface-variant hover:text-error" title="Remove" aria-label="Remove subscriber"><span class="material-symbols-outlined text-[18px]">delete</span></button></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </section>
</main>
<?php include '../includes/footer.php'; ?>
</div>

<script>
const CSRF = document.querySelector('meta[name="csrf-token"]').content;
const INQ = <?= json_encode(array_column($inquiries, null, 'id'), JSON_HEX_TAG | JSON_HEX_AMP) ?>;
const HOTEL = <?= json_encode($hotel) ?>;
const esc = s => String(s ?? '').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
let currentId = null, currentFilter = 'All';

async function act(payload, reload = false) {
    const res = await fetch('inquiries.php', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': CSRF }, body: JSON.stringify(payload) });
    const d = await res.json();
    if (!d.success) { Swal.fire('Error', d.message || 'Action failed', 'error'); return d; }
    if (d.message) Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: d.message, showConfirmButton: false, timer: 1800 });
    if (reload) setTimeout(() => location.reload(), 800);
    return d;
}

function applyFilter() {
    const q = document.getElementById('inq-search').value.toLowerCase().trim();
    let shown = 0;
    document.querySelectorAll('.inq-row').forEach(r => {
        const ok = (currentFilter === 'All' || r.dataset.status === currentFilter) && (!q || r.dataset.search.includes(q));
        r.style.display = ok ? '' : 'none';
        if (ok) shown++;
    });
    document.getElementById('inq-empty').classList.toggle('hidden', shown > 0 || !Object.keys(INQ).length);
}
document.getElementById('inq-search').addEventListener('input', applyFilter);
document.querySelectorAll('.filter-btn').forEach(b => b.addEventListener('click', () => {
    currentFilter = b.dataset.status;
    document.querySelectorAll('.filter-btn').forEach(x => { const a = x === b; x.classList.toggle('bg-primary', a); x.classList.toggle('text-white', a); x.classList.toggle('text-on-surface-variant', !a); });
    applyFilter();
}));

function openInquiry(id) {
    const q = INQ[id];
    if (!q) return;
    currentId = id;
    document.querySelectorAll('.inq-row').forEach(r => r.classList.toggle('bg-surface-container-low', +r.dataset.id === +id));
    const phone = (q.phone || '').replace(/[^0-9+]/g, '');
    const wa = phone.replace(/^\+/, '').replace(/^0/, '254');
    const subject = encodeURIComponent(`Re: your ${q.inquiry_type} inquiry - ${HOTEL}`);
    const body = encodeURIComponent(`Dear ${q.full_name},\n\nThank you for contacting ${HOTEL}.\n\n\n\nKind regards,\n${HOTEL}`);
    const badge = { Unread: 'bg-error/10 text-error', Read: 'bg-surface-container text-on-surface-variant', Responded: 'bg-success/10 text-success' }[q.status];
    document.getElementById('inq-detail').innerHTML = `
        <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4 pb-5 border-b border-outline-variant/40">
            <div class="flex items-center gap-3 min-w-0">
                <span class="w-12 h-12 rounded-full bg-navy text-white flex items-center justify-center font-bold">${esc(q.full_name.slice(0, 2).toUpperCase())}</span>
                <div class="min-w-0">
                    <h2 class="text-lg font-extrabold truncate">${esc(q.full_name)}</h2>
                    <p class="text-xs text-on-surface-variant">${esc(q.inquiry_type)} · ${new Date(q.created_at.replace(' ', 'T')).toLocaleString()}</p>
                </div>
            </div>
            <span class="self-start px-2.5 py-1 rounded-full text-[11px] font-bold uppercase ${badge}">${esc(q.status)}</span>
        </div>
        <div class="grid sm:grid-cols-2 gap-3 py-4 text-sm">
            ${q.email ? `<a href="mailto:${esc(q.email)}" class="flex items-center gap-2 p-3 rounded-xl bg-surface-container-low hover:bg-surface-container break-all"><span class="material-symbols-outlined text-primary text-[20px]">mail</span>${esc(q.email)}</a>` : ''}
            ${q.phone ? `<a href="tel:${esc(phone)}" class="flex items-center gap-2 p-3 rounded-xl bg-surface-container-low hover:bg-surface-container"><span class="material-symbols-outlined text-primary text-[20px]">call</span>${esc(q.phone)}</a>` : ''}
        </div>
        <div class="p-4 rounded-2xl bg-surface-container-low text-sm leading-relaxed whitespace-pre-wrap break-words">${esc(q.message)}</div>
        <div class="flex flex-wrap gap-2 mt-5">
            ${q.email ? `<a href="mailto:${esc(q.email)}?subject=${subject}&body=${body}" onclick="setStatus('Responded')" class="px-4 py-2 rounded-xl bg-navy text-white text-xs font-bold flex items-center gap-1.5"><span class="material-symbols-outlined text-[18px] text-gold">reply</span>Reply by email</a>` : ''}
            ${phone ? `<a href="https://wa.me/${esc(wa)}?text=${body}" target="_blank" rel="noopener" onclick="setStatus('Responded')" class="px-4 py-2 rounded-xl bg-[#25D366] text-white text-xs font-bold flex items-center gap-1.5"><span class="material-symbols-outlined text-[18px]">chat</span>WhatsApp</a>` : ''}
            <button onclick="setStatus('Responded')" class="px-4 py-2 rounded-xl border border-outline-variant text-xs font-bold hover:border-success flex items-center gap-1.5"><span class="material-symbols-outlined text-[18px] text-success">check_circle</span>Mark responded</button>
            <button onclick="setStatus('Unread')" class="px-4 py-2 rounded-xl border border-outline-variant text-xs font-bold hover:border-primary flex items-center gap-1.5"><span class="material-symbols-outlined text-[18px]">mark_email_unread</span>Mark unread</button>
            <button onclick="deleteInquiry()" class="px-4 py-2 rounded-xl border border-outline-variant text-xs font-bold hover:border-error hover:text-error flex items-center gap-1.5 sm:ml-auto"><span class="material-symbols-outlined text-[18px]">delete</span>Delete</button>
        </div>
        <div class="mt-6">
            <label for="inq-notes" class="block text-[11px] font-bold uppercase tracking-wider text-on-surface-variant mb-1.5">Internal notes (staff only)</label>
            <textarea id="inq-notes" rows="3" class="w-full bg-surface-container-low border border-outline-variant/40 rounded-xl px-3 py-2 text-sm outline-none focus:border-primary" placeholder="e.g. Called back, quoted for 3 nights...">${esc(q.staff_notes || '')}</textarea>
            <button onclick="saveNotes()" class="mt-2 px-4 py-2 rounded-xl bg-primary text-white text-xs font-bold">Save notes</button>
        </div>`;
    if (q.status === 'Unread') setStatus('Read', true);
    if (window.innerWidth < 1280) document.getElementById('inq-detail').scrollIntoView({ behavior: 'smooth' });
}

async function setStatus(status, silent = false) {
    if (!currentId) return;
    const res = await fetch('inquiries.php', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': CSRF }, body: JSON.stringify({ action: 'status', id: currentId, status }) });
    if (!(await res.json()).success) return;
    INQ[currentId].status = status;
    const row = document.querySelector(`.inq-row[data-id="${currentId}"]`);
    row.dataset.status = status;
    row.querySelector('.bg-error')?.remove();
    row.querySelector('.text-sm').classList.replace('font-extrabold', 'font-semibold');
    if (!silent) { openInquiry(currentId); Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: 'Marked ' + status.toLowerCase(), showConfirmButton: false, timer: 1500 }); }
}
async function saveNotes() {
    const notes = document.getElementById('inq-notes').value;
    const d = await act({ action: 'notes', id: currentId, notes });
    if (d.success) INQ[currentId].staff_notes = notes;
}
async function deleteInquiry() {
    const ok = await Swal.fire({ title: 'Delete this inquiry?', text: 'This cannot be undone.', icon: 'warning', showCancelButton: true, confirmButtonText: 'Delete', confirmButtonColor: '#dc2626' });
    if (ok.isConfirmed) act({ action: 'delete', id: currentId }, true);
}
async function removeSub(id) {
    const ok = await Swal.fire({ title: 'Remove subscriber?', icon: 'question', showCancelButton: true, confirmButtonText: 'Remove' });
    if (ok.isConfirmed) act({ action: 'unsubscribe', id }, true);
}

document.querySelectorAll('.inq-row').forEach(r => r.addEventListener('click', () => openInquiry(r.dataset.id)));
document.querySelectorAll('.view-tab').forEach(t => t.addEventListener('click', () => {
    document.querySelectorAll('.view-tab').forEach(x => { const a = x === t; x.classList.toggle('border-primary', a); x.classList.toggle('text-primary', a); x.classList.toggle('border-transparent', !a); x.classList.toggle('text-on-surface-variant', !a); });
    document.getElementById('view-inbox').classList.toggle('hidden', t.dataset.view !== 'inbox');
    document.getElementById('view-subs').classList.toggle('hidden', t.dataset.view !== 'subs');
}));
<?php if ($open_id): ?>openInquiry(<?= $open_id ?>);<?php endif; ?>
</script>
</body>
</html>
