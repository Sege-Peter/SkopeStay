<?php
require_once '../includes/config.php';

// Auth Check (Assuming staff role check usually goes here)

?>
<!DOCTYPE html>
<html class="light" lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>SkopeStay KDS | Professional Kitchen Display System</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=Geist:wght@400;500;600&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
            vertical-align: middle;
        }
        .urgent-pulse { animation: pulse-red 2s cubic-bezier(0.4, 0, 0.6, 1) infinite; }
        @keyframes pulse-red { 0%, 100% { opacity: 1; } 50% { opacity: .7; } }
        .kds-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 1.5rem; }
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
    </style>
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "secondary-fixed": "#ffddb8", "on-secondary-container": "#684000", "background": "#f8f9ff",
                        "outline": "#76777d", "on-surface-variant": "#45464d", "on-surface": "#0b1c30",
                        "surface-tint": "#565e74", "surface-container-low": "#eff4ff", "tertiary-container": "#001a42",
                        "surface-container-highest": "#d3e4fe", "primary-fixed": "#dae2fd", "on-primary-fixed-variant": "#3f465c",
                        "on-error-container": "#93000a", "on-secondary-fixed": "#2a1700", "on-tertiary-fixed-variant": "#004395",
                        "surface-container-lowest": "#ffffff", "on-primary-fixed": "#131b2e", "on-error": "#ffffff",
                        "surface-dim": "#cbdbf5", "on-tertiary-fixed": "#001a42", "on-background": "#0b1c30",
                        "primary-fixed-dim": "#bec6e0", "error-container": "#ffdad6", "surface-bright": "#f8f9ff",
                        "on-tertiary": "#ffffff", "tertiary-fixed": "#d8e2ff", "surface-container": "#e5eeff",
                        "secondary-fixed-dim": "#ffb95f", "outline-variant": "#c6c6cd", "inverse-primary": "#bec6e0",
                        "error": "#ba1a1a", "on-secondary": "#ffffff", "primary-container": "#131b2e",
                        "secondary-container": "#fea619", "surface-container-high": "#dce9ff", "primary": "#000000",
                        "on-secondary-fixed-variant": "#653e00", "tertiary-fixed-dim": "#adc6ff", "on-tertiary-container": "#3980f4",
                        "surface-variant": "#d3e4fe", "secondary": "#855300", "inverse-on-surface": "#eaf1ff",
                        "tertiary": "#000000", "on-primary-container": "#7c839b", "surface": "#f8f9ff",
                        "on-primary": "#ffffff", "inverse-surface": "#213145"
                    },
                    borderRadius: { "DEFAULT": "0.25rem", "lg": "0.5rem", "xl": "0.75rem", "full": "9999px" },
                    spacing: { "margin-desktop": "2.5rem", "xs": "0.25rem", "lg": "1.5rem", "base": "4px", "sm": "0.5rem", "md": "1rem", "margin-mobile": "1rem", "gutter": "1.5rem", "xl": "2rem", "2xl": "3rem" },
                    fontFamily: { "headline-md": ["Inter"], "headline-lg": ["Inter"], "title-lg": ["Inter"], "display-lg": ["Inter"], "body-md": ["Inter"], "body-lg": ["Inter"], "label-md": ["Geist"], "headline-lg-mobile": ["Inter"], "caption": ["Inter"] },
                    fontSize: { "headline-md": ["24px", {"lineHeight": "1.4", "fontWeight": "600"}], "headline-lg": ["32px", {"lineHeight": "1.3", "fontWeight": "600"}], "title-lg": ["20px", {"lineHeight": "1.5", "fontWeight": "600"}], "display-lg": ["48px", {"lineHeight": "1.2", "letterSpacing": "-0.02em", "fontWeight": "700"}], "body-md": ["14px", {"lineHeight": "1.5", "fontWeight": "400"}], "body-lg": ["16px", {"lineHeight": "1.6", "fontWeight": "400"}], "label-md": ["12px", {"lineHeight": "1.4", "letterSpacing": "0.05em", "fontWeight": "500"}], "headline-lg-mobile": ["24px", {"lineHeight": "1.3", "fontWeight": "600"}], "caption": ["12px", {"lineHeight": "1.4", "fontWeight": "400"}] }
                }
            }
        };
    </script>
</head>
<body class="bg-background text-on-surface font-body-md overflow-hidden h-screen flex">
    
<!-- SideNavBar -->
<aside class="fixed left-0 top-0 bottom-0 z-40 flex flex-col bg-primary-container dark:bg-primary-container text-on-primary-container shadow-lg h-full w-64">
    <div class="p-lg flex flex-col gap-sm">
        <div class="flex items-center gap-md">
            <div class="w-10 h-10 bg-secondary-container rounded-lg flex items-center justify-center text-on-secondary-container">
                <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">restaurant</span>
            </div>
            <div>
                <h2 class="text-headline-md font-headline-md text-secondary-container leading-none">Main Kitchen</h2>
                <p class="text-label-md font-label-md opacity-70">Station: Hot Line</p>
            </div>
        </div>
    </div>
    <nav class="flex-1 mt-md">
        <ul class="flex flex-col">
            <li class="flex items-center gap-md border-l-4 border-secondary-container bg-surface-container-low/10 text-secondary-container font-bold px-md py-sm translate-x-1 transition-transform cursor-pointer">
                <span class="material-symbols-outlined">dashboard</span>
                <span class="text-label-md font-label-md">KDS Dashboard</span>
            </li>
            <li onclick="window.location.href='restaurant.php'" class="flex items-center gap-md text-on-primary-container/70 px-md py-sm hover:bg-surface-container-highest/20 hover:text-on-primary-container transition-all cursor-pointer">
                <span class="material-symbols-outlined">point_of_sale</span>
                <span class="text-label-md font-label-md">Back to POS</span>
            </li>
            <li onclick="window.location.href='dashboard.php'" class="flex items-center gap-md text-on-primary-container/70 px-md py-sm hover:bg-surface-container-highest/20 hover:text-on-primary-container transition-all cursor-pointer">
                <span class="material-symbols-outlined">arrow_back</span>
                <span class="text-label-md font-label-md">Main Dashboard</span>
            </li>
        </ul>
    </nav>
</aside>

<!-- Main Content Wrapper -->
<main class="ml-64 flex flex-col w-full h-screen relative pb-12">
    <!-- TopNavBar -->
    <header class="flex justify-between items-center w-full px-margin-desktop py-md max-w-full shadow-md bg-surface dark:bg-inverse-surface sticky top-0 z-30">
        <div class="flex items-center gap-xl">
            <span class="text-title-lg font-title-lg font-black text-secondary dark:text-secondary-fixed">SkopeStay KDS</span>
            <nav class="hidden md:flex gap-lg">
                <a class="text-primary dark:text-inverse-primary border-b-2 border-secondary dark:border-secondary-fixed font-bold pb-1 transition-colors filter-btn" href="#" data-filter="All">All Orders</a>
                <a class="text-on-surface-variant dark:text-outline-variant font-medium hover:text-secondary dark:hover:text-secondary-fixed transition-colors filter-btn" href="#" data-filter="Dine-In">Dining Room</a>
                <a class="text-on-surface-variant dark:text-outline-variant font-medium hover:text-secondary dark:hover:text-secondary-fixed transition-colors filter-btn" href="#" data-filter="Room Service">Room Service</a>
            </nav>
        </div>
        <div class="flex items-center gap-lg">
            <div class="relative">
                <input class="bg-surface-container-low border-none rounded-full py-sm px-xl w-64 text-body-md focus:ring-2 focus:ring-secondary focus:outline-none" placeholder="Search orders..." type="text"/>
                <span class="material-symbols-outlined absolute left-sm top-1/2 -translate-y-1/2 text-on-surface-variant opacity-60">search</span>
            </div>
            <div class="text-xl font-bold font-mono" id="live-clock">00:00:00</div>
        </div>
    </header>

    <!-- Kitchen Vitals Bar -->
    <section class="px-margin-desktop py-md grid grid-cols-3 gap-lg border-b border-outline-variant bg-surface-container-lowest">
        <div class="flex items-center gap-md bg-surface-container rounded-xl p-md">
            <div class="bg-tertiary-fixed-dim p-sm rounded-lg">
                <span class="material-symbols-outlined text-on-tertiary-fixed">fiber_new</span>
            </div>
            <div>
                <p class="text-label-md font-label-md text-on-surface-variant">New Orders</p>
                <p class="text-headline-md font-headline-md text-primary" id="stat-new">0</p>
            </div>
        </div>
        <div class="flex items-center gap-md bg-surface-container rounded-xl p-md">
            <div class="bg-primary-fixed p-sm rounded-lg">
                <span class="material-symbols-outlined text-on-primary-fixed">receipt_long</span>
            </div>
            <div>
                <p class="text-label-md font-label-md text-on-surface-variant">In Progress</p>
                <p class="text-headline-md font-headline-md text-primary" id="stat-preparing">0</p>
            </div>
        </div>
        <div class="flex items-center gap-md bg-surface-container rounded-xl p-md">
            <div class="bg-secondary-fixed p-sm rounded-lg">
                <span class="material-symbols-outlined text-on-secondary-fixed">check_circle</span>
            </div>
            <div>
                <p class="text-label-md font-label-md text-on-surface-variant">Ready</p>
                <p class="text-headline-md font-headline-md text-primary" id="stat-ready">0</p>
            </div>
        </div>
    </section>

    <!-- Orders Grid Container -->
    <section class="flex-1 overflow-y-auto px-margin-desktop py-lg">
        <div class="kds-grid" id="kds-grid">
            <!-- Loading placeholder -->
            <div class="bg-surface-container-lowest border border-outline-variant rounded-xl flex flex-col overflow-hidden items-center justify-center border-dashed p-xl opacity-40 col-span-full h-64">
                <span class="material-symbols-outlined text-[48px] text-outline animate-spin">sync</span>
                <p class="text-label-md font-bold mt-md">Loading Orders...</p>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="fixed bottom-0 w-full flex justify-between items-center px-margin-desktop py-sm z-30 bg-surface-container-high dark:bg-surface-container-lowest border-t border-outline-variant dark:border-outline text-on-surface ml-0">
        <div class="flex items-center gap-xl ml-64">
            <p class="text-caption font-caption opacity-70">© 2026 SkopeStay Hospitality Systems - Kitchen Performance: 94%</p>
        </div>
        <div class="flex items-center gap-md">
            <div class="flex items-center gap-xs">
                <div class="w-2 h-2 rounded-full bg-green-500"></div>
                <span class="text-label-md font-label-md">System Online</span>
            </div>
        </div>
    </footer>
</main>

<script>
    let orders = [];
    let currentFilter = 'All';

    // Clock
    function updateClock() {
        const now = new Date();
        document.getElementById('live-clock').textContent = now.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', second: '2-digit' });
    }
    setInterval(updateClock, 1000);
    updateClock();

    // Filters
    document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            document.querySelectorAll('.filter-btn').forEach(b => {
                b.classList.remove('text-primary', 'dark:text-inverse-primary', 'border-b-2', 'border-secondary', 'dark:border-secondary-fixed', 'font-bold');
                b.classList.add('text-on-surface-variant', 'dark:text-outline-variant', 'font-medium');
            });
            btn.classList.add('text-primary', 'dark:text-inverse-primary', 'border-b-2', 'border-secondary', 'dark:border-secondary-fixed', 'font-bold');
            btn.classList.remove('text-on-surface-variant', 'dark:text-outline-variant', 'font-medium');
            currentFilter = btn.dataset.filter;
            renderOrders();
        });
    });

    // Fetch orders
    async function fetchOrders() {
        try {
            const res = await fetch('../api/kds.php');
            const data = await res.json();
            if(data.success) {
                orders = data.data;
                renderOrders();
            }
        } catch(e) {
            console.error(e);
        }
    }

    function renderOrders() {
        const grid = document.getElementById('kds-grid');
        
        let filtered = orders;
        if(currentFilter !== 'All') {
            filtered = orders.filter(o => o.order_type === currentFilter);
        }

        let newCount = 0, prepCount = 0, readyCount = 0;

        const html = filtered.map(order => {
            const type = order.order_type;
            const ref = type === 'Room Service' ? `Room ${order.room_number || order.room_id}` : `Table ${order.table_number}`;
            
            // Calculate wait time
            const createdAt = new Date(order.created_at.replace(/-/g, '/'));
            const diffMin = Math.floor((new Date() - createdAt) / 60000);
            const timeStr = `${String(createdAt.getHours()).padStart(2, '0')}:${String(createdAt.getMinutes()).padStart(2, '0')}`;
            
            let cardClass = "", headerClass = "", statusIcon = "", statusTitle = "", actionButtons = "";
            
            if(order.kitchen_status === 'New Order') {
                newCount++;
                cardClass = "border-on-tertiary-container/30";
                headerClass = "bg-on-tertiary-container text-white";
                statusIcon = "fiber_new";
                statusTitle = "New Order";
                if(diffMin > 10) cardClass += " urgent-pulse border-error";
                actionButtons = `<button onclick="updateStatus(${order.id}, 'Preparing')" class="flex-1 py-md bg-primary text-on-primary font-black hover:opacity-90 transition-opacity">START PREPARING</button>`;
            } 
            else if (order.kitchen_status === 'Preparing') {
                prepCount++;
                cardClass = "border-secondary-container/30";
                headerClass = "bg-secondary-container text-on-secondary-container";
                statusIcon = "timer";
                statusTitle = "In Progress";
                if(diffMin > 25) cardClass += " urgent-pulse border-error";
                actionButtons = `
                    <button class="py-md text-on-surface-variant font-bold hover:bg-surface-container transition-colors border-r border-outline-variant">Recall</button>
                    <button onclick="updateStatus(${order.id}, 'Ready')" class="py-md col-span-2 bg-secondary text-on-secondary font-black hover:opacity-90 transition-opacity">MARK READY</button>
                `;
            } 
            else if (order.kitchen_status === 'Ready') {
                readyCount++;
                cardClass = "border-secondary-fixed/50 opacity-80";
                headerClass = "bg-secondary-fixed text-on-secondary-fixed";
                statusIcon = "check_circle";
                statusTitle = "Ready";
                actionButtons = `<div class="flex-1 py-md text-center font-bold text-on-surface-variant">Awaiting Server Pickup</div>`;
            }

            const itemsHtml = order.items.map(item => `
                <li class="flex justify-between items-center text-body-lg">
                    <span class="font-bold text-primary">${item.quantity}x ${item.name}</span>
                    <span class="material-symbols-outlined text-secondary text-[20px]">restaurant_menu</span>
                </li>
            `).join('');

            return `
            <div class="bg-surface-container-lowest border ${cardClass} rounded-xl shadow-lg flex flex-col overflow-hidden transform hover:scale-[1.01] transition-all">
                <div class="${headerClass} px-md py-sm flex justify-between items-center">
                    <div class="flex items-center gap-sm">
                        <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">${statusIcon}</span>
                        <span class="text-label-md font-bold uppercase tracking-widest">${statusTitle}</span>
                    </div>
                    <span class="text-headline-md font-black">${timeStr}</span>
                </div>
                <div class="p-md flex flex-col gap-sm flex-1">
                    <div class="flex justify-between items-start">
                        <div>
                            <h3 class="text-title-lg font-bold text-primary">#ORD-${order.id}</h3>
                            <p class="text-label-md font-semibold text-secondary">${type} • ${ref}</p>
                        </div>
                        <span class="text-xs font-bold text-error">${diffMin > 0 ? diffMin + 'm ago' : 'Just now'}</span>
                    </div>
                    <div class="my-sm border-y border-outline-variant py-sm">
                        <ul class="flex flex-col gap-xs">${itemsHtml}</ul>
                    </div>
                </div>
                <div class="mt-auto flex ${order.kitchen_status==='Preparing' ? 'grid grid-cols-3' : ''} border-t border-outline-variant">
                    ${actionButtons}
                </div>
            </div>`;
        }).join('');

        if(html === '') {
            grid.innerHTML = `
                <div class="bg-surface-container-lowest border border-outline-variant rounded-xl flex flex-col overflow-hidden items-center justify-center border-dashed p-xl opacity-40 col-span-full h-64">
                    <span class="material-symbols-outlined text-[48px] text-outline">restaurant_menu</span>
                    <p class="text-label-md font-bold mt-md">No Active Orders</p>
                </div>
            `;
        } else {
            grid.innerHTML = html;
        }

        document.getElementById('stat-new').textContent = newCount;
        document.getElementById('stat-preparing').textContent = prepCount;
        document.getElementById('stat-ready').textContent = readyCount;
    }

    async function updateStatus(id, status) {
        try {
            const res = await fetch('../api/kds.php', {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id, kitchen_status: status })
            });
            const data = await res.json();
            if(data.success) {
                Swal.fire({icon:'success', title:'Updated', toast:true, position:'top-end', timer:1500, showConfirmButton:false});
                fetchOrders();
            } else {
                Swal.fire('Error', data.message, 'error');
            }
        } catch(e) {
            console.error(e);
        }
    }

    // Initial load and polling
    fetchOrders();
    setInterval(fetchOrders, 5000); // Poll every 5s

</script>
</body>
</html>
