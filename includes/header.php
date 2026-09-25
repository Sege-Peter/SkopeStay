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
?>
<!-- TopNavBar -->
<header class="flex justify-between items-center w-full px-4 md:px-margin-desktop h-16 sticky top-0 z-40 bg-surface-container-lowest shadow-sm transition-colors duration-300">
    <div class="flex items-center gap-4">
        <!-- Hamburger Menu (Mobile Only) -->
        <button id="mobile-menu-btn" class="md:hidden text-on-surface hover:bg-surface-container-low p-2 rounded-full transition-colors duration-200">
            <span class="material-symbols-outlined">menu</span>
        </button>
        
        <div class="relative hidden sm:block w-72 lg:w-96 max-w-full">
            <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-outline">search</span>
            <input id="global-search" class="w-full bg-surface-container-low border border-outline-variant/30 rounded-lg pl-10 pr-4 py-2 text-on-surface font-body-md focus:ring-2 focus:ring-primary focus:border-primary transition-all outline-none" placeholder="Search across SkopeStay..." type="text"/>
        </div>
    </div>
    
    <div class="flex items-center gap-2 md:gap-4">
        <!-- Theme Toggle -->
        <button id="theme-toggle" class="text-on-surface hover:bg-surface-container-low p-2 rounded-full transition-colors duration-200" title="Toggle Dark Mode">
            <span class="material-symbols-outlined dark:hidden">dark_mode</span>
            <span class="material-symbols-outlined hidden dark:block">light_mode</span>
        </button>
        
        <div class="hidden md:flex items-center gap-2 relative">
            <button onclick="showNotifications()" class="relative material-symbols-outlined text-on-surface hover:bg-surface-container-low p-2 rounded-full transition-colors duration-200" title="Notifications">
                notifications
                <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-error rounded-full animate-pulse"></span>
            </button>
            <button onclick="showMessages()" class="relative material-symbols-outlined text-on-surface hover:bg-surface-container-low p-2 rounded-full transition-colors duration-200" title="Messages">
                mail
                <span class="absolute top-1.5 right-1.5 w-4 h-4 bg-primary text-white text-[9px] font-bold rounded-full flex items-center justify-center">3</span>
            </button>
        </div>
        
        <div class="flex items-center gap-3 pl-2 md:pl-4 border-l border-outline-variant cursor-pointer hover:opacity-80 transition-opacity" onclick="showProfile()">
            <div class="text-right hidden sm:block">
                <p class="font-label-md text-label-md font-bold text-on-surface"><?= htmlspecialchars($current_username) ?></p>
                <p class="text-[10px] text-on-surface-variant uppercase tracking-wider"><?= htmlspecialchars($current_role) ?></p>
            </div>
            <div class="w-10 h-10 rounded-full bg-primary text-white flex items-center justify-center font-bold font-title-lg shadow-sm">
                <?= htmlspecialchars($initials) ?>
            </div>
        </div>
    </div>
</header>

<script>
    // Theme Toggle Logic
    const themeToggleBtn = document.getElementById('theme-toggle');
    const htmlElement = document.documentElement;

    // Check initial load
    if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
        htmlElement.classList.add('dark');
        htmlElement.classList.remove('light');
    } else {
        htmlElement.classList.remove('dark');
        htmlElement.classList.add('light');
    }

    themeToggleBtn.addEventListener('click', () => {
        if (htmlElement.classList.contains('dark')) {
            htmlElement.classList.remove('dark');
            htmlElement.classList.add('light');
            localStorage.theme = 'light';
        } else {
            htmlElement.classList.add('dark');
            htmlElement.classList.remove('light');
            localStorage.theme = 'dark';
        }
    });

    // Mobile Sidebar Toggle Logic
    const mobileMenuBtn = document.getElementById('mobile-menu-btn');
    const sidebar = document.getElementById('sidebar');
    const sidebarOverlay = document.getElementById('sidebar-overlay');

    if (mobileMenuBtn && sidebar && sidebarOverlay) {
        function toggleSidebar() {
            sidebar.classList.toggle('-translate-x-full');
            sidebarOverlay.classList.toggle('hidden');
        }

        mobileMenuBtn.addEventListener('click', toggleSidebar);
        sidebarOverlay.addEventListener('click', toggleSidebar);
    }

    // Header Icons Interactive Logic using SweetAlert2
    function showNotifications() {
        if(typeof Swal === 'undefined') return alert('Notifications: You have 2 new alerts.');
        Swal.fire({
            title: 'Notifications',
            html: `
            <div class="text-left space-y-3 mt-2">
                <div class="p-3 bg-blue-50 border border-blue-100 rounded-lg">
                    <p class="text-sm font-bold text-blue-800">New Booking</p>
                    <p class="text-xs text-blue-600">John Doe booked the Grand Ballroom.</p>
                </div>
                <div class="p-3 bg-red-50 border border-red-100 rounded-lg">
                    <p class="text-sm font-bold text-red-800">Low Inventory</p>
                    <p class="text-xs text-red-600">Towels are running low in housekeeping.</p>
                </div>
            </div>`,
            showConfirmButton: false,
            showCloseButton: true,
            position: 'top-end',
            toast: true,
            timer: 5000,
            timerProgressBar: true
        });
    }

    function showMessages() {
        if(typeof Swal === 'undefined') return alert('Messages: You have 3 unread messages.');
        Swal.fire({
            title: 'Messages',
            html: `
            <div class="text-left space-y-3 mt-2">
                <div class="flex gap-3 items-center p-2 hover:bg-gray-50 rounded-lg cursor-pointer">
                    <div class="w-8 h-8 rounded-full bg-primary/10 text-primary flex items-center justify-center font-bold text-xs">SA</div>
                    <div class="flex-1"><p class="text-sm font-bold text-gray-800">System Admin</p><p class="text-xs text-gray-500 truncate w-48">Server backup completed successfully.</p></div>
                </div>
                <div class="flex gap-3 items-center p-2 hover:bg-gray-50 rounded-lg cursor-pointer">
                    <div class="w-8 h-8 rounded-full bg-green-100 text-green-700 flex items-center justify-center font-bold text-xs">HR</div>
                    <div class="flex-1"><p class="text-sm font-bold text-gray-800">HR Dept</p><p class="text-xs text-gray-500 truncate w-48">Please review the new staff schedule.</p></div>
                </div>
            </div>`,
            showConfirmButton: false,
            showCloseButton: true,
            position: 'top-end',
            customClass: { popup: 'mt-16 mr-4' }
        });
    }

    function showProfile() {
        if(typeof Swal === 'undefined') return alert('Profile clicked');
        Swal.fire({
            html: `
            <div class="flex flex-col items-center pt-4">
                <div class="w-16 h-16 rounded-full bg-primary text-white flex items-center justify-center font-extrabold text-2xl shadow-lg mb-3"><?= htmlspecialchars($initials) ?></div>
                <h3 class="font-extrabold text-lg text-gray-800"><?= htmlspecialchars($current_username) ?></h3>
                <p class="text-xs font-bold text-primary uppercase tracking-widest mb-4"><?= htmlspecialchars($current_role) ?></p>
                <div class="w-full flex flex-col gap-2">
                    <button onclick="window.location.href='settings.php'" class="w-full py-2.5 bg-gray-50 hover:bg-gray-100 rounded-xl text-sm font-semibold text-gray-700 transition-colors border border-gray-200">Settings</button>
                    <button onclick="window.location.href='../api/auth.php?action=logout'" class="w-full py-2.5 bg-red-50 hover:bg-red-100 rounded-xl text-sm font-bold text-red-600 transition-colors border border-red-200 flex items-center justify-center gap-2">
                        <span class="material-symbols-outlined text-[18px]">logout</span> Sign Out
                    </button>
                </div>
            </div>`,
            showConfirmButton: false,
            showCloseButton: true,
            width: 300,
            position: 'top-end',
            customClass: { popup: 'mt-16 mr-4 rounded-3xl' }
        });
    }

    // Global Search interaction
    const searchInput = document.getElementById('global-search');
    if (searchInput) {
        searchInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                if(this.value.trim() !== '') {
                    if(typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'info',
                            title: 'Searching...',
                            text: 'Searching for: ' + this.value,
                            timer: 1500,
                            showConfirmButton: false
                        });
                    }
                    this.value = '';
                }
            }
        });
    }
</script>
