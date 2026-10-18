<?php
$current_username = $_SESSION['username'] ?? 'Guest User';
$user_id = $_SESSION['user_id'] ?? null;
$current_role = 'Viewer';

if ($user_id && isset($pdo)) {
    $current_username = ucfirst($current_username);
    $current_role = get_user_primary_role($pdo, $user_id);
}

// Extract initials safely
$initials = strtoupper(substr($current_username, 0, 2));
if (strpos($current_username, ' ') !== false) {
    $parts = explode(' ', $current_username);
    if(isset($parts[0]) && isset($parts[1])) {
        $initials = strtoupper(substr($parts[0], 0, 1) . substr($parts[1], 0, 1));
    }
}
$can_settings = $user_id && has_permission($pdo, $user_id, 'settings_manage');
?>
<!-- TopNavBar -->
<header class="flex justify-between items-center w-full px-4 md:px-6 h-16 sticky top-0 z-40 bg-surface-container-lowest/90 backdrop-blur border-b border-outline-variant/40 transition-colors duration-300">
    <div class="flex items-center gap-3 min-w-0 flex-1">
        <!-- Hamburger Menu (Mobile Only) -->
        <button id="mobile-menu-btn" aria-label="Open menu" class="md:hidden text-on-surface hover:bg-surface-container-low p-2 rounded-full transition-colors duration-200">
            <span class="material-symbols-outlined">menu</span>
        </button>

        <!-- Global search with live results -->
        <div class="relative w-full max-w-md min-w-[110px]" id="global-search-wrap">
            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline text-[20px]">search</span>
            <input id="global-search" autocomplete="off" class="w-full bg-surface-container-low border border-outline-variant/40 rounded-xl pl-10 pr-3 lg:pr-14 py-2 text-sm text-on-surface focus:ring-2 focus:ring-primary/40 focus:border-primary transition-all outline-none" placeholder="Search..." type="search"/>
            <kbd class="hidden lg:flex absolute right-3 top-1/2 -translate-y-1/2 text-[10px] font-bold text-outline border border-outline-variant rounded px-1.5 py-0.5">Ctrl K</kbd>
            <div id="search-results" class="hidden absolute left-0 right-0 top-full mt-2 bg-surface rounded-2xl shadow-2xl border border-outline-variant/50 max-h-[70vh] overflow-y-auto z-50"></div>
        </div>
    </div>

    <div class="flex items-center gap-1 md:gap-2 shrink-0">
        <!-- Theme Toggle -->
        <button id="theme-toggle" class="text-on-surface hover:bg-surface-container-low p-2 rounded-full transition-colors duration-200" title="Toggle dark mode" aria-label="Toggle dark mode">
            <span class="material-symbols-outlined dark:hidden">dark_mode</span>
            <span class="material-symbols-outlined hidden dark:block">light_mode</span>
        </button>

        <!-- Notifications -->
        <div class="relative">
            <button id="notif-btn" class="relative text-on-surface hover:bg-surface-container-low p-2 rounded-full transition-colors duration-200" title="Notifications" aria-label="Notifications">
                <span class="material-symbols-outlined">notifications</span>
                <span id="notif-dot" class="hidden absolute top-1 right-1 min-w-[16px] h-4 px-1 bg-error text-white text-[9px] font-bold rounded-full items-center justify-center"></span>
            </button>
            <div id="notif-panel" class="hidden absolute right-0 top-full mt-2 w-[min(92vw,360px)] bg-surface rounded-2xl shadow-2xl border border-outline-variant/50 overflow-hidden z-50">
                <div class="px-4 py-3 border-b border-outline-variant/50 flex items-center justify-between">
                    <p class="font-bold text-sm text-on-surface">Notifications</p>
                    <span class="text-[10px] uppercase tracking-wider text-on-surface-variant font-bold">Live</span>
                </div>
                <div id="notif-list" class="max-h-[60vh] overflow-y-auto divide-y divide-outline-variant/30">
                    <p class="p-6 text-center text-xs text-on-surface-variant">Loading...</p>
                </div>
            </div>
        </div>

        <!-- Guest messages (inquiries) -->
        <div class="relative hidden sm:block">
            <button id="msg-btn" class="relative text-on-surface hover:bg-surface-container-low p-2 rounded-full transition-colors duration-200" title="Guest messages" aria-label="Guest messages">
                <span class="material-symbols-outlined">mail</span>
                <span id="msg-dot" class="hidden absolute top-1 right-1 min-w-[16px] h-4 px-1 bg-primary text-white text-[9px] font-bold rounded-full items-center justify-center"></span>
            </button>
            <div id="msg-panel" class="hidden absolute right-0 top-full mt-2 w-[min(92vw,360px)] bg-surface rounded-2xl shadow-2xl border border-outline-variant/50 overflow-hidden z-50">
                <div class="px-4 py-3 border-b border-outline-variant/50 flex items-center justify-between">
                    <p class="font-bold text-sm text-on-surface">Guest Messages</p>
                    <a href="inquiries.php" class="text-xs font-bold text-primary hover:underline">Open inbox</a>
                </div>
                <div id="msg-list" class="max-h-[60vh] overflow-y-auto divide-y divide-outline-variant/30">
                    <p class="p-6 text-center text-xs text-on-surface-variant">Loading...</p>
                </div>
            </div>
        </div>

        <!-- Profile menu -->
        <div class="relative pl-2 md:pl-3 ml-1 border-l border-outline-variant">
            <button id="profile-btn" class="flex items-center gap-3 hover:opacity-80 transition-opacity" aria-label="Account menu">
                <div class="text-right hidden sm:block">
                    <p class="text-sm font-bold text-on-surface leading-tight"><?= e($current_username) ?></p>
                    <p class="text-[10px] text-on-surface-variant uppercase tracking-wider"><?= e($current_role) ?></p>
                </div>
                <div class="w-10 h-10 rounded-full bg-navy text-white flex items-center justify-center font-bold shadow-sm ring-2 ring-gold/40">
                    <?= e($initials) ?>
                </div>
            </button>
            <div id="profile-panel" class="hidden absolute right-0 top-full mt-2 w-60 bg-surface rounded-2xl shadow-2xl border border-outline-variant/50 overflow-hidden z-50 py-2">
                <div class="px-4 py-2 border-b border-outline-variant/40 mb-1 sm:hidden">
                    <p class="text-sm font-bold text-on-surface"><?= e($current_username) ?></p>
                    <p class="text-[10px] text-on-surface-variant uppercase tracking-wider"><?= e($current_role) ?></p>
                </div>
                <a href="profile.php" class="flex items-center gap-3 px-4 py-2.5 text-sm text-on-surface hover:bg-surface-container-low"><span class="material-symbols-outlined text-[20px]">account_circle</span>My profile</a>
                <a href="profile.php#password" class="flex items-center gap-3 px-4 py-2.5 text-sm text-on-surface hover:bg-surface-container-low"><span class="material-symbols-outlined text-[20px]">key</span>Change password</a>
                <?php if ($can_settings): ?>
                <a href="settings.php" class="flex items-center gap-3 px-4 py-2.5 text-sm text-on-surface hover:bg-surface-container-low"><span class="material-symbols-outlined text-[20px]">settings</span>System settings</a>
                <a href="settings.php#branding" class="flex items-center gap-3 px-4 py-2.5 text-sm text-on-surface hover:bg-surface-container-low"><span class="material-symbols-outlined text-[20px]">palette</span>Branding & colours</a>
                <?php endif; ?>
                <a href="../index.php" target="_blank" class="flex items-center gap-3 px-4 py-2.5 text-sm text-on-surface hover:bg-surface-container-low"><span class="material-symbols-outlined text-[20px]">open_in_new</span>View website</a>
                <div class="border-t border-outline-variant/40 mt-1 pt-1">
                    <a href="../api/auth.php?action=logout" class="flex items-center gap-3 px-4 py-2.5 text-sm font-bold text-error hover:bg-error/10"><span class="material-symbols-outlined text-[20px]">logout</span>Sign out</a>
                </div>
            </div>
        </div>
    </div>
</header>

<script>
(function () {
    const htmlElement = document.documentElement;
    const esc = s => String(s ?? '').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));

    // Theme toggle
    if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
        htmlElement.classList.add('dark'); htmlElement.classList.remove('light');
    } else {
        htmlElement.classList.remove('dark'); htmlElement.classList.add('light');
    }
    document.getElementById('theme-toggle').addEventListener('click', () => {
        const dark = !htmlElement.classList.contains('dark');
        htmlElement.classList.toggle('dark', dark);
        htmlElement.classList.toggle('light', !dark);
        localStorage.theme = dark ? 'dark' : 'light';
    });

    // Mobile sidebar
    const mobileMenuBtn = document.getElementById('mobile-menu-btn');
    const sidebar = document.getElementById('sidebar');
    const sidebarOverlay = document.getElementById('sidebar-overlay');
    if (mobileMenuBtn && sidebar && sidebarOverlay) {
        mobileMenuBtn.addEventListener('click', () => {
            sidebarOverlay.classList.contains('hidden') ? openSidebar() : closeSidebar();
        });
    }

    // Dropdown panels (one open at a time)
    const panels = { 'notif-btn': 'notif-panel', 'msg-btn': 'msg-panel', 'profile-btn': 'profile-panel' };
    const closeAll = except => Object.values(panels).forEach(id => { if (id !== except) document.getElementById(id)?.classList.add('hidden'); });
    Object.entries(panels).forEach(([btn, panel]) => {
        document.getElementById(btn)?.addEventListener('click', e => {
            e.stopPropagation();
            closeAll(panel);
            document.getElementById(panel).classList.toggle('hidden');
        });
    });
    document.addEventListener('click', e => {
        if (!e.target.closest('#notif-panel, #msg-panel, #profile-panel')) closeAll();
        if (!e.target.closest('#global-search-wrap')) document.getElementById('search-results').classList.add('hidden');
    });

    const tones = { blue: 'bg-blue-500/10 text-blue-600', green: 'bg-emerald-500/10 text-emerald-600', red: 'bg-red-500/10 text-red-600', amber: 'bg-amber-500/10 text-amber-600' };
    const setDot = (el, n) => {
        if (!el) return;
        el.textContent = n > 9 ? '9+' : n;
        el.classList.toggle('hidden', !n);
        el.classList.toggle('flex', !!n);
    };

    async function loadFeed() {
        try {
            const res = await fetch('../api/notifications.php?action=feed');
            if (!res.ok) return;
            const d = await res.json();
            const nl = document.getElementById('notif-list');
            nl.innerHTML = d.alerts.length ? d.alerts.map(a => `
                <a href="${esc(a.href)}" class="flex gap-3 px-4 py-3 hover:bg-surface-container-low transition-colors">
                    <span class="w-9 h-9 rounded-xl ${tones[a.tone] || tones.blue} flex items-center justify-center shrink-0"><span class="material-symbols-outlined text-[18px]">${esc(a.icon)}</span></span>
                    <span class="min-w-0"><span class="block text-sm font-bold text-on-surface truncate">${esc(a.title)}</span><span class="block text-xs text-on-surface-variant truncate">${esc(a.text)}</span></span>
                </a>`).join('') : '<div class="p-8 text-center"><span class="material-symbols-outlined text-3xl text-success">task_alt</span><p class="text-xs text-on-surface-variant mt-2">All clear. Nothing needs attention.</p></div>';
            setDot(document.getElementById('notif-dot'), d.alerts.length);

            const ml = document.getElementById('msg-list');
            ml.innerHTML = d.messages.length ? d.messages.map(m => `
                <a href="inquiries.php?open=${m.id}" class="flex gap-3 px-4 py-3 hover:bg-surface-container-low transition-colors">
                    <span class="w-9 h-9 rounded-full bg-navy text-white flex items-center justify-center font-bold text-xs shrink-0">${esc((m.full_name || '?').slice(0, 2).toUpperCase())}</span>
                    <span class="min-w-0"><span class="block text-sm font-bold text-on-surface truncate">${esc(m.full_name)} <span class="font-normal text-[10px] text-on-surface-variant">· ${esc(m.inquiry_type)}</span></span><span class="block text-xs text-on-surface-variant truncate">${esc(m.preview)}</span></span>
                </a>`).join('') : '<p class="p-8 text-center text-xs text-on-surface-variant">No unread guest messages.</p>';
            setDot(document.getElementById('msg-dot'), d.unread);
        } catch (e) { /* offline: keep previous state */ }
    }
    loadFeed();
    setInterval(loadFeed, 60000);

    // Global search
    const searchInput = document.getElementById('global-search');
    if (window.innerWidth >= 640) searchInput.placeholder = 'Search guests, bookings, rooms, pages...';
    const box = document.getElementById('search-results');
    let timer;
    searchInput.addEventListener('input', () => {
        clearTimeout(timer);
        const q = searchInput.value.trim();
        if (q.length < 2) { box.classList.add('hidden'); return; }
        timer = setTimeout(async () => {
            try {
                const res = await fetch('../api/notifications.php?action=search&q=' + encodeURIComponent(q));
                const d = await res.json();
                if (!d.results.length) {
                    box.innerHTML = `<p class="p-5 text-center text-xs text-on-surface-variant">No matches for "${esc(q)}".</p>`;
                } else {
                    let html = '', group = '';
                    d.results.forEach(r => {
                        if (r.group !== group) { group = r.group; html += `<p class="px-4 pt-3 pb-1 text-[10px] font-extrabold uppercase tracking-wider text-on-surface-variant">${esc(group)}</p>`; }
                        html += `<a href="${esc(r.href)}" class="search-hit flex items-center gap-3 px-4 py-2 hover:bg-surface-container-low"><span class="material-symbols-outlined text-[18px] text-primary">${esc(r.icon)}</span><span class="min-w-0"><span class="block text-sm font-semibold text-on-surface truncate">${esc(r.title)}</span><span class="block text-[11px] text-on-surface-variant truncate">${esc(r.meta)}</span></span></a>`;
                    });
                    box.innerHTML = html + '<div class="h-2"></div>';
                }
                box.classList.remove('hidden');
            } catch (e) {}
        }, 220);
    });
    searchInput.addEventListener('keydown', e => {
        if (e.key === 'Enter') { const first = box.querySelector('.search-hit'); if (first) window.location.href = first.href; }
        if (e.key === 'Escape') { box.classList.add('hidden'); searchInput.blur(); }
    });
    document.addEventListener('keydown', e => {
        if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'k') { e.preventDefault(); searchInput.focus(); searchInput.select(); }
    });
})();

// Wide data tables scroll sideways inside their card instead of being clipped on phones
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('main table, div[class*="ml-64"] table').forEach(t => {
        const p = t.parentElement;
        if (!t.style.minWidth && t.querySelectorAll('thead th').length > 3) t.style.minWidth = '640px';
        if (!p || p.classList.contains('table-scroll') || ['auto', 'scroll'].includes(getComputedStyle(p).overflowX)) return;
        const wrap = document.createElement('div');
        wrap.className = 'table-scroll';
        wrap.style.cssText = 'overflow-x:auto;-webkit-overflow-scrolling:touch;max-width:100%';
        p.insertBefore(wrap, t);
        wrap.appendChild(t);
        if (!t.style.minWidth && t.querySelectorAll('thead th').length > 3) t.style.minWidth = '640px';
    });
});

// Kept for backwards compatibility with pages that call these directly
function showNotifications() { document.getElementById('notif-btn')?.click(); }
function showMessages() { document.getElementById('msg-btn')?.click(); }
function showProfile() { document.getElementById('profile-btn')?.click(); }
</script>
