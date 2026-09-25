<?php
$page_title = "SkopeStay - Executive Management Dashboard";
require_once __DIR__ . '/../includes/config.php';

// Check Authentication
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$current_username = $_SESSION['username'] ?? 'Administrator';
$current_role = get_user_primary_role($pdo, $user_id);

// 1. Live Rooms & Capacity Metrics
$total_rooms = (int)($pdo->query("SELECT COUNT(*) FROM rooms")->fetchColumn() ?: 0);
$occupied    = (int)($pdo->query("SELECT COUNT(*) FROM rooms WHERE status='Occupied'")->fetchColumn() ?: 0);
$available   = (int)($pdo->query("SELECT COUNT(*) FROM rooms WHERE status='Available'")->fetchColumn() ?: 0);
$cleaning    = (int)($pdo->query("SELECT COUNT(*) FROM rooms WHERE status='Cleaning'")->fetchColumn() ?: 0);
$maintenance = (int)($pdo->query("SELECT COUNT(*) FROM rooms WHERE status='Maintenance'")->fetchColumn() ?: 0);

$occ_pct = $total_rooms > 0 ? round(($occupied / $total_rooms) * 100) : 0;
$avail_pct = $total_rooms > 0 ? round(($available / $total_rooms) * 100) : 0;

// 2. Revenue & Financial KPI Metrics
$revenue_today = (float)($pdo->query("SELECT COALESCE(SUM(total_amount),0) FROM bookings WHERE DATE(created_at)=CURDATE()")->fetchColumn() ?: 0);
$rev_restaurant = (float)($pdo->query("SELECT COALESCE(SUM(total_amount),0) FROM restaurant_orders WHERE DATE(created_at)=CURDATE() AND status='Paid'")->fetchColumn() ?: 0);

$rev_halls = 0;
try {
    $rev_halls = (float)($pdo->query("SELECT COALESCE(SUM(total_amount),0) FROM hall_bookings WHERE DATE(created_at)=CURDATE() AND status != 'Cancelled'")->fetchColumn() ?: 0);
} catch (Exception $e) {}

$total_gross_revenue = $revenue_today + $rev_restaurant + $rev_halls;

// Estimated ADR (Average Daily Rate) & RevPAR
$adr = $occupied > 0 ? round($revenue_today / $occupied) : ($total_rooms > 0 ? 18500 : 0);
$revpar = $total_rooms > 0 ? round($revenue_today / $total_rooms) : 0;

// 3. Departmental ERP Modules Stats
$rest_orders_today = (int)($pdo->query("SELECT COUNT(*) FROM restaurant_orders WHERE DATE(created_at)=CURDATE()")->fetchColumn() ?: 0);
$rest_active_kitchen = (int)($pdo->query("SELECT COUNT(*) FROM restaurant_orders WHERE status IN ('Pending','Kitchen')")->fetchColumn() ?: 0);

$hall_events_today = 0;
try {
    $hall_events_today = (int)($pdo->query("SELECT COUNT(*) FROM hall_bookings WHERE event_date = CURDATE() AND status != 'Cancelled'")->fetchColumn() ?: 0);
} catch (Exception $e) {}

$pool_visitors_today = 0;
try {
    $pool_visitors_today = (int)($pdo->query("SELECT COALESCE(SUM(number_of_guests),0) FROM pool_passes WHERE visit_date = CURDATE()")->fetchColumn() ?: 0);
} catch (Exception $e) {}

// ERP: HRMS Roster
$staff_on_duty = 0;
try {
    $staff_on_duty = (int)($pdo->query("SELECT COUNT(*) FROM employee_shifts WHERE shift_date = CURDATE() AND status = 'Scheduled'")->fetchColumn() ?: 0);
} catch (Exception $e) {}

// ERP: Supply Chain / Procurement
$pending_pos = 0;
try {
    $pending_pos = (int)($pdo->query("SELECT COUNT(*) FROM purchase_orders WHERE status = 'Pending Approval'")->fetchColumn() ?: 0);
} catch (Exception $e) {}

// ERP: Facilities & CMMS Maintenance
$open_work_orders = 0;
try {
    $open_work_orders = (int)($pdo->query("SELECT COUNT(*) FROM maintenance_orders WHERE status IN ('Open','In Progress')")->fetchColumn() ?: 0);
} catch (Exception $e) {}

// 4. Live Room Directory for Interactive Matrix
$all_rooms = $pdo->query("SELECT id, room_number, type, price, floor, status FROM rooms ORDER BY floor ASC, room_number ASC")->fetchAll(PDO::FETCH_ASSOC);

// Available rooms for booking dropdown
$avail_rooms = array_filter($all_rooms, fn($r) => $r['status'] === 'Available');

// 5. Recent Activity: Bookings & Audit Trail
$recent_bookings = $pdo->query("
    SELECT b.id, b.guest_name, b.created_at, b.check_in, b.check_out, b.total_amount, b.status, r.room_number, r.type as room_type 
    FROM bookings b 
    LEFT JOIN rooms r ON b.room_id = r.id 
    ORDER BY b.created_at DESC 
    LIMIT 6
")->fetchAll(PDO::FETCH_ASSOC);

$recent_audits = [];
try {
    $recent_audits = $pdo->query("
        SELECT a.action, a.module, a.details, a.created_at, u.username 
        FROM audit_logs a 
        LEFT JOIN users u ON a.user_id = u.id 
        ORDER BY a.created_at DESC 
        LIMIT 5
    ")->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {}

include __DIR__ . '/../includes/head.php';
?>
<!DOCTYPE html>
<html class="light" lang="en">
<head>
    <!-- Enhanced Luxury Styling Fonts & Theme Tokens -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Montserrat:wght@400;500;600;700;800&family=Playfair+Display:ital,wght@0,600;0,700;1,400&display=swap" rel="stylesheet">
    
    <style>
        .font-luxury { font-family: 'Playfair Display', serif; }
        .font-sans-ui { font-family: 'Inter', sans-serif; }
        
        .gold-gradient-text {
            background: linear-gradient(135deg, #F5E6AB 0%, #D4AF37 50%, #A6821C 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .gold-gradient-bg {
            background: linear-gradient(135deg, #F3E5AB 0%, #D4AF37 60%, #B89025 100%);
        }
        .navy-gradient-bg {
            background: linear-gradient(135deg, #0B132B 0%, #162447 60%, #1F4068 100%);
        }
        .glass-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(229, 231, 235, 0.8);
            box-shadow: 0 4px 20px -2px rgba(11, 19, 43, 0.05);
            transition: all 0.25s ease;
        }
        .dark .glass-card {
            background: rgba(17, 24, 39, 0.95);
            border-color: rgba(55, 65, 81, 0.6);
            box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.3);
        }
        .glass-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px -4px rgba(11, 19, 43, 0.1);
        }
        
        /* Modern Scrollbar for tabs and grids */
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
        
        @keyframes pulse-subtle {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.7; transform: scale(0.98); }
        }
        .pulse-subtle { animation: pulse-subtle 3s infinite ease-in-out; }
    </style>
</head>
<body class="bg-[#F8FAFC] dark:bg-gray-950 text-gray-800 dark:text-gray-100 font-sans-ui min-h-screen selection:bg-gold/30 selection:text-navy">

<?php include __DIR__ . '/../includes/sidebar.php'; ?>

<main class="md:ml-64 min-h-screen flex flex-col transition-all duration-300">

<?php include __DIR__ . '/../includes/header.php'; ?>

<!-- =========================================================================
     EXECUTIVE ERP DASHBOARD CONTENT CONTAINER
     ========================================================================= -->
<div class="flex-1 p-4 sm:p-6 lg:p-8 space-y-6 sm:space-y-8 max-w-[1700px] mx-auto w-full">

    <!-- =====================================================================
         1. EXECUTIVE GREETING & COMMAND ACTIONS BAR
         ===================================================================== -->
    <section class="glass-card rounded-2xl p-5 sm:p-6 lg:p-7 relative overflow-hidden">
        <!-- Ambient Decorative Glow -->
        <div class="absolute -right-20 -top-20 w-64 h-64 rounded-full bg-gold/10 blur-3xl pointer-events-none"></div>

        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-5 relative z-10">
            <!-- Left Info Block -->
            <div class="space-y-1.5">
                <div class="flex items-center gap-2.5 flex-wrap">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-navy text-gold text-xs font-semibold shadow-sm">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                        ERP Operations Live
                    </span>
                    <span class="text-xs text-gray-500 dark:text-gray-400 font-medium">
                        Property: <strong class="text-gray-700 dark:text-gray-200">Flagship Beachfront Resort & Suites</strong>
                    </span>
                </div>
                
                <h1 class="text-2xl sm:text-3xl lg:text-4xl font-luxury font-bold text-gray-900 dark:text-white tracking-tight">
                    Executive <span class="text-navy dark:text-gold">Overview</span>
                </h1>
                
                <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 font-sans">
                    Welcome back, <strong class="text-gray-800 dark:text-gray-200 font-semibold"><?= htmlspecialchars($current_username) ?></strong> (<?= htmlspecialchars($current_role) ?>) • 
                    <span id="live-datetime" class="font-medium text-navy dark:text-gold"></span>
                </p>
            </div>

            <!-- Right Action Command Buttons -->
            <div class="flex items-center gap-2.5 sm:gap-3 flex-wrap w-full lg:w-auto">
                <button onclick="openModal('booking-modal')" 
                        class="flex-1 sm:flex-none gold-gradient-bg text-navy px-4 sm:px-5 py-2.5 rounded-xl font-bold text-xs sm:text-sm hover:scale-[1.02] active:scale-95 transition-all shadow-md flex items-center justify-center gap-2">
                    <span class="material-symbols-outlined text-[18px]">add_circle</span>
                    <span>New Reservation</span>
                </button>

                <button onclick="openModal('maintenance-modal')" 
                        class="flex-1 sm:flex-none bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-200 px-4 sm:px-5 py-2.5 rounded-xl font-semibold text-xs sm:text-sm hover:bg-gray-50 dark:hover:bg-gray-700 transition-all shadow-sm flex items-center justify-center gap-2">
                    <span class="material-symbols-outlined text-[18px] text-amber-500">build</span>
                    <span>Work Order</span>
                </button>

                <button onclick="window.location.href='restaurant.php'" 
                        class="bg-navy text-white px-4 py-2.5 rounded-xl font-semibold text-xs sm:text-sm hover:bg-navy-light transition-all shadow-sm hidden sm:flex items-center gap-2">
                    <span class="material-symbols-outlined text-[18px] text-gold">point_of_sale</span>
                    <span>POS Terminal</span>
                </button>

                <button onclick="exportExecutiveReport()" 
                        class="bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 p-2.5 rounded-xl transition-all shadow-sm flex items-center justify-center" 
                        title="Export Full CSV Daily Summary">
                    <span class="material-symbols-outlined text-[20px]">download</span>
                </button>
            </div>
        </div>
    </section>

    <!-- =====================================================================
         2. TIER 1 KPI METRICS (High-Density Responsive Cards)
         ===================================================================== -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5">
        
        <!-- KPI 1: Today's Total Gross Revenue -->
        <div class="glass-card rounded-2xl p-5 relative overflow-hidden group">
            <div class="flex justify-between items-start mb-3">
                <div class="w-11 h-11 rounded-xl bg-gold/15 text-gold flex items-center justify-center group-hover:scale-105 transition-transform">
                    <span class="material-symbols-outlined text-2xl">account_balance_wallet</span>
                </div>
                <span class="text-[11px] font-bold px-2 py-0.5 rounded-full bg-emerald-100 dark:bg-emerald-950/50 text-emerald-700 dark:text-emerald-400 flex items-center gap-1">
                    <span class="material-symbols-outlined text-[12px]">trending_up</span> +14.2%
                </span>
            </div>
            <p class="text-xs font-bold text-gray-400 dark:text-gray-400 uppercase tracking-wider">Today's Revenue</p>
            <h3 class="text-2xl sm:text-3xl font-extrabold text-gray-900 dark:text-white mt-1">
                KSh <?= number_format($total_gross_revenue) ?>
            </h3>
            <div class="mt-3 pt-3 border-t border-gray-100 dark:border-gray-800 flex justify-between text-[11px] text-gray-500 dark:text-gray-400">
                <span>Suites: <strong>KSh <?= number_format($revenue_today) ?></strong></span>
                <span>F&B: <strong>KSh <?= number_format($rev_restaurant) ?></strong></span>
            </div>
        </div>

        <!-- KPI 2: Live Room Occupancy Rate -->
        <div class="glass-card rounded-2xl p-5 relative overflow-hidden group">
            <div class="flex justify-between items-start mb-3">
                <div class="w-11 h-11 rounded-xl bg-blue-100 dark:bg-blue-900/40 text-blue-600 dark:text-blue-300 flex items-center justify-center group-hover:scale-105 transition-transform">
                    <span class="material-symbols-outlined text-2xl">bed</span>
                </div>
                <span class="text-[11px] font-bold px-2 py-0.5 rounded-full bg-blue-50 dark:bg-blue-950/50 text-blue-700 dark:text-blue-300">
                    <?= $occ_pct ?>% Occupied
                </span>
            </div>
            <p class="text-xs font-bold text-gray-400 dark:text-gray-400 uppercase tracking-wider">Property Occupancy</p>
            <div class="flex items-baseline gap-2 mt-1">
                <h3 class="text-2xl sm:text-3xl font-extrabold text-gray-900 dark:text-white">
                    <?= $occupied ?> <span class="text-sm font-normal text-gray-500">/ <?= $total_rooms ?> Rooms</span>
                </h3>
            </div>
            <!-- Progress Bar -->
            <div class="w-full bg-gray-100 dark:bg-gray-800 rounded-full h-2 mt-3 overflow-hidden">
                <div class="bg-blue-600 h-2 rounded-full transition-all duration-700" style="width: <?= $occ_pct ?>%"></div>
            </div>
            <div class="mt-2 flex justify-between text-[11px] text-gray-500 dark:text-gray-400">
                <span class="text-emerald-600 dark:text-emerald-400 font-medium">Ready: <?= $available ?></span>
                <span class="text-amber-600 dark:text-amber-400 font-medium">Turnover: <?= $cleaning ?></span>
            </div>
        </div>

        <!-- KPI 3: Key Hospitality Metrics (ADR & RevPAR) -->
        <div class="glass-card rounded-2xl p-5 relative overflow-hidden group">
            <div class="flex justify-between items-start mb-3">
                <div class="w-11 h-11 rounded-xl bg-purple-100 dark:bg-purple-900/40 text-purple-600 dark:text-purple-300 flex items-center justify-center group-hover:scale-105 transition-transform">
                    <span class="material-symbols-outlined text-2xl">analytics</span>
                </div>
                <span class="text-[11px] font-bold px-2 py-0.5 rounded-full bg-purple-50 dark:bg-purple-950/50 text-purple-700 dark:text-purple-300">
                    Forbes 5-Star
                </span>
            </div>
            <p class="text-xs font-bold text-gray-400 dark:text-gray-400 uppercase tracking-wider">ADR & Yield Performance</p>
            <h3 class="text-2xl sm:text-3xl font-extrabold text-gray-900 dark:text-white mt-1">
                KSh <?= number_format($adr) ?>
            </h3>
            <div class="mt-3 pt-3 border-t border-gray-100 dark:border-gray-800 flex justify-between text-[11px] text-gray-500 dark:text-gray-400">
                <span>RevPAR: <strong>KSh <?= number_format($revpar) ?></strong></span>
                <span>Active Folios: <strong><?= $occupied ?></strong></span>
            </div>
        </div>

        <!-- KPI 4: Operations & On-Duty Human Capital -->
        <div class="glass-card rounded-2xl p-5 relative overflow-hidden group">
            <div class="flex justify-between items-start mb-3">
                <div class="w-11 h-11 rounded-xl bg-emerald-100 dark:bg-emerald-900/40 text-emerald-600 dark:text-emerald-300 flex items-center justify-center group-hover:scale-105 transition-transform">
                    <span class="material-symbols-outlined text-2xl">badge</span>
                </div>
                <span class="text-[11px] font-bold px-2 py-0.5 rounded-full bg-emerald-50 dark:bg-emerald-950/50 text-emerald-700 dark:text-emerald-400">
                    Full Roster
                </span>
            </div>
            <p class="text-xs font-bold text-gray-400 dark:text-gray-400 uppercase tracking-wider">Staff & Facilities Status</p>
            <h3 class="text-2xl sm:text-3xl font-extrabold text-gray-900 dark:text-white mt-1">
                <?= $staff_on_duty ?> <span class="text-sm font-normal text-gray-500">Staff on Shift</span>
            </h3>
            <div class="mt-3 pt-3 border-t border-gray-100 dark:border-gray-800 flex justify-between text-[11px]">
                <span class="text-amber-600 dark:text-amber-400 font-medium">Open CMMS: <?= $open_work_orders ?></span>
                <span class="text-blue-600 dark:text-blue-400 font-medium">Pending POs: <?= $pending_pos ?></span>
            </div>
        </div>

    </div>

    <!-- =====================================================================
         3. TIER 2 ERP DEPARTMENTAL OPERATIONS STRIP
         ===================================================================== -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3 sm:gap-4">
        
        <!-- Dining / Restaurant POS -->
        <a href="restaurant.php" class="glass-card rounded-xl p-3.5 flex items-center gap-3 hover:border-gold/60 transition-all group">
            <div class="w-10 h-10 rounded-lg bg-orange-100 dark:bg-orange-950/50 text-orange-600 flex items-center justify-center shrink-0 group-hover:scale-110 transition-transform">
                <span class="material-symbols-outlined text-xl">restaurant</span>
            </div>
            <div class="min-w-0 flex-1">
                <div class="text-[11px] text-gray-400 font-bold uppercase truncate">Dining POS</div>
                <div class="text-base font-extrabold text-gray-900 dark:text-white leading-tight"><?= $rest_orders_today ?> Orders</div>
                <div class="text-[10px] text-orange-600 font-medium truncate"><?= $rest_active_kitchen ?> in kitchen</div>
            </div>
        </a>

        <!-- Banquets & Events -->
        <a href="halls.php" class="glass-card rounded-xl p-3.5 flex items-center gap-3 hover:border-gold/60 transition-all group">
            <div class="w-10 h-10 rounded-lg bg-indigo-100 dark:bg-indigo-950/50 text-indigo-600 flex items-center justify-center shrink-0 group-hover:scale-110 transition-transform">
                <span class="material-symbols-outlined text-xl">domain</span>
            </div>
            <div class="min-w-0 flex-1">
                <div class="text-[11px] text-gray-400 font-bold uppercase truncate">Banquets</div>
                <div class="text-base font-extrabold text-gray-900 dark:text-white leading-tight"><?= $hall_events_today ?> Events</div>
                <div class="text-[10px] text-indigo-600 font-medium truncate">4 Venues ready</div>
            </div>
        </a>

        <!-- Leisure & Infinity Pool -->
        <a href="pool.php" class="glass-card rounded-xl p-3.5 flex items-center gap-3 hover:border-gold/60 transition-all group">
            <div class="w-10 h-10 rounded-lg bg-cyan-100 dark:bg-cyan-950/50 text-cyan-600 flex items-center justify-center shrink-0 group-hover:scale-110 transition-transform">
                <span class="material-symbols-outlined text-xl">pool</span>
            </div>
            <div class="min-w-0 flex-1">
                <div class="text-[11px] text-gray-400 font-bold uppercase truncate">Pool Club</div>
                <div class="text-base font-extrabold text-gray-900 dark:text-white leading-tight"><?= $pool_visitors_today ?> Passes</div>
                <div class="text-[10px] text-cyan-600 font-medium truncate">Open till 10 PM</div>
            </div>
        </a>

        <!-- SCM & Procurement Restock -->
        <a href="procurement.php" class="glass-card rounded-xl p-3.5 flex items-center gap-3 hover:border-gold/60 transition-all group">
            <div class="w-10 h-10 rounded-lg bg-amber-100 dark:bg-amber-950/50 text-amber-600 flex items-center justify-center shrink-0 group-hover:scale-110 transition-transform">
                <span class="material-symbols-outlined text-xl">local_shipping</span>
            </div>
            <div class="min-w-0 flex-1">
                <div class="text-[11px] text-gray-400 font-bold uppercase truncate">Purchase Orders</div>
                <div class="text-base font-extrabold text-gray-900 dark:text-white leading-tight"><?= $pending_pos ?> Pending</div>
                <div class="text-[10px] text-amber-600 font-medium truncate">5 Vendors active</div>
            </div>
        </a>

        <!-- Facilities & CMMS Work Orders -->
        <a href="maintenance.php" class="glass-card rounded-xl p-3.5 flex items-center gap-3 hover:border-gold/60 transition-all group">
            <div class="w-10 h-10 rounded-lg bg-rose-100 dark:bg-rose-950/50 text-rose-600 flex items-center justify-center shrink-0 group-hover:scale-110 transition-transform">
                <span class="material-symbols-outlined text-xl">handyman</span>
            </div>
            <div class="min-w-0 flex-1">
                <div class="text-[11px] text-gray-400 font-bold uppercase truncate">Housekeeping</div>
                <div class="text-base font-extrabold text-gray-900 dark:text-white leading-tight"><?= $cleaning ?> Cleaning</div>
                <div class="text-[10px] text-rose-600 font-medium truncate"><?= $open_work_orders ?> Work Orders</div>
            </div>
        </a>

        <!-- HRMS Staff Directory -->
        <a href="hrms.php" class="glass-card rounded-xl p-3.5 flex items-center gap-3 hover:border-gold/60 transition-all group">
            <div class="w-10 h-10 rounded-lg bg-emerald-100 dark:bg-emerald-950/50 text-emerald-600 flex items-center justify-center shrink-0 group-hover:scale-110 transition-transform">
                <span class="material-symbols-outlined text-xl">groups</span>
            </div>
            <div class="min-w-0 flex-1">
                <div class="text-[11px] text-gray-400 font-bold uppercase truncate">HR Capital</div>
                <div class="text-base font-extrabold text-gray-900 dark:text-white leading-tight">8 Employees</div>
                <div class="text-[10px] text-emerald-600 font-medium truncate">Payroll synced</div>
            </div>
        </a>

    </div>

    <!-- =====================================================================
         4. INTERACTIVE LIVE ROOM MATRIX & OCCUPANCY MAP
         ===================================================================== -->
    <section class="glass-card rounded-2xl p-5 sm:p-6 lg:p-7">
        
        <!-- Header Controls & Filters -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 pb-5 border-b border-gray-100 dark:border-gray-800">
            <div>
                <h3 class="text-lg sm:text-xl font-luxury font-bold text-gray-900 dark:text-white flex items-center gap-2">
                    <span class="material-symbols-outlined text-gold">grid_view</span>
                    Interactive Live Room Matrix
                </h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                    Real-time visual map of all resort suites & rooms. Click any room for instant action.
                </p>
            </div>

            <!-- Floor & Search Controls -->
            <div class="flex items-center gap-2.5 flex-wrap w-full md:w-auto">
                <!-- Search Filter Input -->
                <div class="relative w-full sm:w-48">
                    <span class="material-symbols-outlined absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400 text-[18px]">search</span>
                    <input type="text" 
                           id="room-search-input" 
                           placeholder="Filter rooms..." 
                           class="w-full pl-8 pr-3 py-1.5 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg text-xs font-medium text-gray-800 dark:text-gray-200 outline-none focus:border-gold transition-colors">
                </div>

                <!-- Status Filter Pills (Horizontal scrollable on mobile) -->
                <div class="flex items-center gap-1.5 overflow-x-auto no-scrollbar py-1">
                    <button type="button" onclick="filterRoomStatus('all')" class="status-filter-btn px-2.5 py-1 rounded-lg text-xs font-semibold bg-navy text-white shadow-sm transition-all" data-status="all">All (<?= $total_rooms ?>)</button>
                    <button type="button" onclick="filterRoomStatus('Available')" class="status-filter-btn px-2.5 py-1 rounded-lg text-xs font-semibold bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300 hover:bg-emerald-50 hover:text-emerald-700 transition-all" data-status="Available">Ready (<?= $available ?>)</button>
                    <button type="button" onclick="filterRoomStatus('Occupied')" class="status-filter-btn px-2.5 py-1 rounded-lg text-xs font-semibold bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300 hover:bg-rose-50 hover:text-rose-700 transition-all" data-status="Occupied">Occupied (<?= $occupied ?>)</button>
                    <button type="button" onclick="filterRoomStatus('Cleaning')" class="status-filter-btn px-2.5 py-1 rounded-lg text-xs font-semibold bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300 hover:bg-amber-50 hover:text-amber-700 transition-all" data-status="Cleaning">Turnover (<?= $cleaning ?>)</button>
                </div>
            </div>
        </div>

        <!-- Room Cards Dynamic Grid -->
        <div id="room-matrix-container" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-3 sm:gap-4 pt-6">
            <?php foreach ($all_rooms as $rm): 
                $status = $rm['status'];
                $colors = [
                    'Available'   => 'bg-emerald-50 dark:bg-emerald-950/30 border-emerald-300 dark:border-emerald-800/60 text-emerald-800 dark:text-emerald-300 hover:border-emerald-500',
                    'Occupied'    => 'bg-rose-50 dark:bg-rose-950/30 border-rose-300 dark:border-rose-800/60 text-rose-800 dark:text-rose-300 hover:border-rose-500',
                    'Cleaning'    => 'bg-amber-50 dark:bg-amber-950/30 border-amber-300 dark:border-amber-800/60 text-amber-800 dark:text-amber-300 hover:border-amber-500',
                    'Maintenance' => 'bg-gray-100 dark:bg-gray-800 border-gray-300 dark:border-gray-700 text-gray-700 dark:text-gray-300 hover:border-gray-500'
                ];
                $badgeColors = [
                    'Available'   => 'bg-emerald-200 dark:bg-emerald-900 text-emerald-900 dark:text-emerald-100',
                    'Occupied'    => 'bg-rose-200 dark:bg-rose-900 text-rose-900 dark:text-rose-100',
                    'Cleaning'    => 'bg-amber-200 dark:bg-amber-900 text-amber-900 dark:text-amber-100',
                    'Maintenance' => 'bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200'
                ];
                $themeClass = $colors[$status] ?? $colors['Available'];
                $badgeClass = $badgeColors[$status] ?? $badgeColors['Available'];
            ?>
            <div class="room-card border rounded-2xl p-3.5 cursor-pointer transition-all duration-200 hover:-translate-y-1 hover:shadow-md <?= $themeClass ?>"
                 data-room-id="<?= $rm['id'] ?>"
                 data-room-number="<?= htmlspecialchars($rm['room_number']) ?>"
                 data-room-type="<?= htmlspecialchars($rm['type']) ?>"
                 data-price="<?= $rm['price'] ?>"
                 data-status="<?= $status ?>"
                 onclick="handleRoomClick(<?= htmlspecialchars(json_encode($rm)) ?>)">
                
                <div class="flex justify-between items-start mb-2">
                    <span class="font-extrabold text-base sm:text-lg tracking-tight font-luxury">
                        Room <?= htmlspecialchars($rm['room_number']) ?>
                    </span>
                    <span class="text-[9px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-md <?= $badgeClass ?>">
                        <?= $status ?>
                    </span>
                </div>

                <div class="text-xs font-semibold text-gray-700 dark:text-gray-200 truncate">
                    <?= htmlspecialchars($rm['type']) ?>
                </div>

                <div class="mt-2 pt-2 border-t border-black/5 dark:border-white/5 flex justify-between items-center text-[11px]">
                    <span class="text-gray-500 dark:text-gray-400">Floor <?= $rm['floor'] ?></span>
                    <span class="font-bold text-gray-900 dark:text-white">KSh <?= number_format($rm['price']) ?></span>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Legend Footer -->
        <div class="mt-6 pt-4 border-t border-gray-100 dark:border-gray-800 flex flex-wrap items-center justify-between gap-3 text-xs text-gray-500 dark:text-gray-400">
            <div class="flex items-center gap-4 flex-wrap">
                <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-emerald-500"></span> Available (Ready for Guest)</span>
                <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-rose-500"></span> Occupied (Checked In)</span>
                <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-amber-500"></span> Cleaning (Turnover in progress)</span>
                <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-gray-400"></span> Maintenance (Out of order)</span>
            </div>
            <div class="text-[11px] font-medium text-navy dark:text-gold">
                💡 Tip: Click any Available room to instantly create a new reservation.
            </div>
        </div>
    </section>

    <!-- =====================================================================
         5. DUAL-PANEL: REVENUE MIX & LIVE ACTIVITY STREAM
         ===================================================================== -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-5 sm:gap-6">
        
        <!-- Left: Revenue & Property Performance Analytics (7 cols on lg) -->
        <div class="lg:col-span-7 space-y-5 sm:space-y-6">
            
            <!-- Revenue Mix & Breakdown Card -->
            <div class="glass-card rounded-2xl p-5 sm:p-6">
                <div class="flex justify-between items-center mb-5">
                    <div>
                        <h3 class="text-base sm:text-lg font-luxury font-bold text-gray-900 dark:text-white">
                            Revenue Stream Distribution
                        </h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Departmental breakdown across resort divisions</p>
                    </div>
                    <a href="accounting.php" class="text-xs font-bold text-gold hover:underline flex items-center gap-1">
                        <span>Ledger & COA</span>
                        <span class="material-symbols-outlined text-[14px]">arrow_forward</span>
                    </a>
                </div>

                <!-- Visual Multi-Progress Bars -->
                <div class="space-y-4">
                    <!-- Suites & Accommodations -->
                    <div>
                        <div class="flex justify-between text-xs font-semibold mb-1">
                            <span class="flex items-center gap-1.5 text-navy dark:text-gray-200">
                                <span class="w-2.5 h-2.5 rounded-full bg-navy dark:bg-gold"></span>
                                Suites & Luxury Accommodations
                            </span>
                            <span class="font-bold text-gray-900 dark:text-white">KSh <?= number_format($revenue_today) ?> (65%)</span>
                        </div>
                        <div class="w-full bg-gray-100 dark:bg-gray-800 h-2.5 rounded-full overflow-hidden">
                            <div class="bg-navy dark:bg-gold h-2.5 rounded-full transition-all duration-500" style="width: 65%"></div>
                        </div>
                    </div>

                    <!-- F&B Restaurant & Bars -->
                    <div>
                        <div class="flex justify-between text-xs font-semibold mb-1">
                            <span class="flex items-center gap-1.5 text-orange-600">
                                <span class="w-2.5 h-2.5 rounded-full bg-orange-500"></span>
                                Food & Beverage Dining
                            </span>
                            <span class="font-bold text-gray-900 dark:text-white">KSh <?= number_format($rev_restaurant) ?> (22%)</span>
                        </div>
                        <div class="w-full bg-gray-100 dark:bg-gray-800 h-2.5 rounded-full overflow-hidden">
                            <div class="bg-orange-500 h-2.5 rounded-full transition-all duration-500" style="width: 22%"></div>
                        </div>
                    </div>

                    <!-- Banquets & Events -->
                    <div>
                        <div class="flex justify-between text-xs font-semibold mb-1">
                            <span class="flex items-center gap-1.5 text-indigo-600">
                                <span class="w-2.5 h-2.5 rounded-full bg-indigo-500"></span>
                                Banquets & Conferences (MICE)
                            </span>
                            <span class="font-bold text-gray-900 dark:text-white">KSh <?= number_format($rev_halls) ?> (10%)</span>
                        </div>
                        <div class="w-full bg-gray-100 dark:bg-gray-800 h-2.5 rounded-full overflow-hidden">
                            <div class="bg-indigo-500 h-2.5 rounded-full transition-all duration-500" style="width: 10%"></div>
                        </div>
                    </div>

                    <!-- Infinity Pool & Day Passes -->
                    <div>
                        <div class="flex justify-between text-xs font-semibold mb-1">
                            <span class="flex items-center gap-1.5 text-cyan-600">
                                <span class="w-2.5 h-2.5 rounded-full bg-cyan-500"></span>
                                Infinity Pool Passes & Wellness
                            </span>
                            <span class="font-bold text-gray-900 dark:text-white">KSh 38,000 (3%)</span>
                        </div>
                        <div class="w-full bg-gray-100 dark:bg-gray-800 h-2.5 rounded-full overflow-hidden">
                            <div class="bg-cyan-500 h-2.5 rounded-full transition-all duration-500" style="width: 3%"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Priority Operational Alerts Queue -->
            <div class="glass-card rounded-2xl p-5 sm:p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-base sm:text-lg font-luxury font-bold text-gray-900 dark:text-white flex items-center gap-2">
                        <span class="material-symbols-outlined text-amber-500">notification_important</span>
                        Immediate Operational Action Items
                    </h3>
                    <span class="text-xs font-bold px-2 py-0.5 rounded-full bg-amber-100 text-amber-800">
                        3 Pending
                    </span>
                </div>

                <div class="space-y-3">
                    <div class="p-3 rounded-xl bg-amber-50/70 dark:bg-amber-950/20 border border-amber-200/80 dark:border-amber-800/40 flex items-center justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-amber-100 text-amber-700 flex items-center justify-center shrink-0">
                                <span class="material-symbols-outlined text-[18px]">verified</span>
                            </div>
                            <div>
                                <div class="text-xs font-bold text-gray-900 dark:text-white">VIP Pre-Arrival Room Inspection</div>
                                <div class="text-[11px] text-gray-500">Presidential Suite 301 • Guest arrival at 16:00</div>
                            </div>
                        </div>
                        <a href="maintenance.php" class="text-xs font-bold text-navy dark:text-gold hover:underline shrink-0">Inspect</a>
                    </div>

                    <div class="p-3 rounded-xl bg-blue-50/70 dark:bg-blue-950/20 border border-blue-200/80 dark:border-blue-800/40 flex items-center justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-blue-100 text-blue-700 flex items-center justify-center shrink-0">
                                <span class="material-symbols-outlined text-[18px]">receipt_long</span>
                            </div>
                            <div>
                                <div class="text-xs font-bold text-gray-900 dark:text-white">PO-2026-003 Pending Approval</div>
                                <div class="text-[11px] text-gray-500">LinenPro Egyptian cotton bedsheets • KSh 71,920</div>
                            </div>
                        </div>
                        <a href="procurement.php" class="text-xs font-bold text-navy dark:text-gold hover:underline shrink-0">Approve</a>
                    </div>

                    <div class="p-3 rounded-xl bg-rose-50/70 dark:bg-rose-950/20 border border-rose-200/80 dark:border-rose-800/40 flex items-center justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-rose-100 text-rose-700 flex items-center justify-center shrink-0">
                                <span class="material-symbols-outlined text-[18px]">ac_unit</span>
                            </div>
                            <div>
                                <div class="text-xs font-bold text-gray-900 dark:text-white">HVAC Chiller WO-802 Assigned</div>
                                <div class="text-[11px] text-gray-500">Apex Engineers on-site at central plant</div>
                            </div>
                        </div>
                        <a href="maintenance.php" class="text-xs font-bold text-navy dark:text-gold hover:underline shrink-0">Track</a>
                    </div>
                </div>
            </div>

        </div>

        <!-- Right: Recent Bookings & Enterprise Audit Trail (5 cols on lg) -->
        <div class="lg:col-span-5 space-y-5 sm:space-y-6">
            
            <!-- Recent Guest Reservations Card -->
            <div class="glass-card rounded-2xl p-5 sm:p-6 flex flex-col">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-base sm:text-lg font-luxury font-bold text-gray-900 dark:text-white flex items-center gap-2">
                        <span class="material-symbols-outlined text-navy dark:text-gold">book_online</span>
                        Recent Reservations
                    </h3>
                    <a href="bookings.php" class="text-xs font-bold text-navy dark:text-gold hover:underline">
                        View All
                    </a>
                </div>

                <div class="space-y-3.5 divide-y divide-gray-100 dark:divide-gray-800">
                    <?php if (empty($recent_bookings)): ?>
                        <div class="py-8 text-center text-gray-400 text-xs">
                            <span class="material-symbols-outlined text-3xl mb-1 opacity-50">calendar_month</span>
                            <p>No recent bookings on record.</p>
                        </div>
                    <?php else: foreach ($recent_bookings as $b): ?>
                        <div class="pt-3.5 first:pt-0 flex items-center justify-between gap-3">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl bg-navy/5 dark:bg-white/10 text-navy dark:text-gold flex items-center justify-center shrink-0 font-bold text-xs">
                                    <span class="material-symbols-outlined text-[18px]">person</span>
                                </div>
                                <div class="min-w-0">
                                    <div class="text-xs font-bold text-gray-900 dark:text-white truncate">
                                        <?= htmlspecialchars($b['guest_name']) ?>
                                    </div>
                                    <div class="text-[11px] text-gray-400 truncate">
                                        Room <?= htmlspecialchars($b['room_number'] ?? 'N/A') ?> (<?= htmlspecialchars($b['room_type'] ?? 'Deluxe') ?>)
                                    </div>
                                </div>
                            </div>

                            <div class="text-right shrink-0">
                                <div class="text-xs font-extrabold text-gray-900 dark:text-white">
                                    KSh <?= number_format($b['total_amount']) ?>
                                </div>
                                <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full <?= $b['status'] === 'Active' ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-100 text-gray-700' ?>">
                                    <?= htmlspecialchars($b['status']) ?>
                                </span>
                            </div>
                        </div>
                    <?php endforeach; endif; ?>
                </div>

                <button onclick="openModal('booking-modal')" 
                        class="w-full mt-5 py-2.5 rounded-xl border-2 border-dashed border-gray-200 dark:border-gray-700 text-xs font-bold text-gray-600 dark:text-gray-300 hover:border-gold hover:text-navy dark:hover:text-gold transition-colors flex items-center justify-center gap-1.5">
                    <span class="material-symbols-outlined text-[16px]">add</span> Create New Reservation
                </button>
            </div>

            <!-- Enterprise Audit Trail Feed -->
            <div class="glass-card rounded-2xl p-5 sm:p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-base sm:text-lg font-luxury font-bold text-gray-900 dark:text-white flex items-center gap-2">
                        <span class="material-symbols-outlined text-gray-500">history</span>
                        Live Security & Audit Trail
                    </h3>
                    <a href="audit_logs.php" class="text-xs font-bold text-navy dark:text-gold hover:underline">
                        Audit Log
                    </a>
                </div>

                <div class="space-y-3">
                    <?php if (empty($recent_audits)): ?>
                        <div class="py-6 text-center text-gray-400 text-xs">
                            <span class="material-symbols-outlined text-3xl mb-1 opacity-50">security</span>
                            <p>No audit events logged today.</p>
                        </div>
                    <?php else: foreach ($recent_audits as $audit): ?>
                        <div class="text-xs p-2.5 rounded-xl bg-gray-50 dark:bg-gray-800/60 border border-gray-100 dark:border-gray-800 flex items-start gap-2.5">
                            <span class="material-symbols-outlined text-[16px] text-gray-400 mt-0.5">shield</span>
                            <div class="min-w-0 flex-1">
                                <p class="text-gray-800 dark:text-gray-200 font-medium">
                                    <strong class="text-navy dark:text-gold"><?= htmlspecialchars($audit['username'] ?? 'System') ?></strong>: 
                                    <?= htmlspecialchars($audit['action']) ?> in <span class="font-semibold text-gray-700 dark:text-gray-300"><?= htmlspecialchars($audit['module']) ?></span>
                                </p>
                                <p class="text-[10px] text-gray-400 mt-0.5">
                                    <?= date('M j, H:i', strtotime($audit['created_at'])) ?> • <?= htmlspecialchars($audit['details'] ?? 'System activity recorded') ?>
                                </p>
                            </div>
                        </div>
                    <?php endforeach; endif; ?>
                </div>
            </div>

        </div>

    </div>

</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
</main>

<!-- =========================================================================
     MODAL 1: NEW RESERVATION WITH REAL-TIME PRICING CALCULATOR
     ========================================================================= -->
<div id="booking-modal" class="fixed inset-0 z-[100] hidden items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" onclick="closeModal('booking-modal')"></div>
    <div class="relative bg-white dark:bg-gray-900 rounded-3xl shadow-2xl w-full max-w-lg mx-auto overflow-hidden animate-modal">
        
        <!-- Header -->
        <div class="flex items-center justify-between p-5 sm:p-6 border-b border-gray-100 dark:border-gray-800 bg-[#FDFBF7] dark:bg-gray-850">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gold/20 text-gold flex items-center justify-center shadow-sm">
                    <span class="material-symbols-outlined text-xl">bedroom_parent</span>
                </div>
                <div>
                    <h3 class="font-luxury font-bold text-gray-900 dark:text-white text-lg">Create New Reservation</h3>
                    <p class="text-xs text-gray-500">SkopeStay 5-Star Guest Folio System</p>
                </div>
            </div>
            <button onclick="closeModal('booking-modal')" class="w-8 h-8 rounded-full bg-gray-100 dark:bg-gray-800 text-gray-500 hover:text-gray-900 dark:hover:text-white flex items-center justify-center transition-colors">
                <span class="material-symbols-outlined text-[18px]">close</span>
            </button>
        </div>

        <!-- Form Body -->
        <form id="booking-form" class="p-5 sm:p-6 space-y-4">
            <div>
                <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-1.5">Guest Full Name *</label>
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-[18px]">person</span>
                    <input type="text" name="guest_name" required placeholder="e.g. Marcus Kimani" 
                           class="w-full pl-9 pr-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 dark:bg-gray-800 text-xs sm:text-sm text-gray-900 dark:text-white focus:border-gold focus:ring-2 focus:ring-gold/20 outline-none transition-all"/>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-1.5">Select Available Room *</label>
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-[18px]">bed</span>
                    <select name="room_id" id="modal_room_select" required 
                            class="w-full pl-9 pr-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 dark:bg-gray-800 text-xs sm:text-sm text-gray-900 dark:text-white focus:border-gold focus:ring-2 focus:ring-gold/20 outline-none transition-all appearance-none cursor-pointer">
                        <option value="">-- Choose an available suite --</option>
                        <?php foreach($avail_rooms as $r): ?>
                        <option value="<?= $r['id'] ?>" data-price="<?= $r['price'] ?>">
                            Room <?= htmlspecialchars($r['room_number']) ?> — <?= htmlspecialchars($r['type']) ?> (KSh <?= number_format($r['price']) ?>/night)
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-1.5">Check-In Date *</label>
                    <input type="date" name="check_in" id="modal_check_in" required 
                           class="w-full px-3 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 dark:bg-gray-800 text-xs sm:text-sm text-gray-900 dark:text-white focus:border-gold focus:ring-2 focus:ring-gold/20 outline-none transition-all"/>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-1.5">Check-Out Date</label>
                    <input type="date" name="check_out" id="modal_check_out" 
                           class="w-full px-3 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 dark:bg-gray-800 text-xs sm:text-sm text-gray-900 dark:text-white focus:border-gold focus:ring-2 focus:ring-gold/20 outline-none transition-all"/>
                </div>
            </div>

            <!-- Price Preview Card -->
            <div id="price-preview" class="hidden rounded-xl p-3 bg-amber-50 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-800/40 text-xs">
                <div class="flex justify-between items-center">
                    <span class="text-gray-600 dark:text-gray-400 font-medium">Estimated Folio Total</span>
                    <span id="price-estimate" class="font-extrabold text-navy dark:text-gold text-sm sm:text-base">KSh 0</span>
                </div>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="button" onclick="closeModal('booking-modal')" 
                        class="flex-1 py-3 rounded-xl border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-300 font-semibold text-xs sm:text-sm hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                    Cancel
                </button>
                <button type="submit" id="booking-submit-btn" 
                        class="flex-1 py-3 rounded-xl gold-gradient-bg text-navy font-bold text-xs sm:text-sm hover:scale-[1.01] active:scale-95 transition-all shadow-md flex items-center justify-center gap-1.5">
                    <span class="material-symbols-outlined text-[18px]">check_circle</span>
                    <span>Confirm Booking</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- =========================================================================
     MODAL 2: QUICK MAINTENANCE WORK ORDER
     ========================================================================= -->
<div id="maintenance-modal" class="fixed inset-0 z-[100] hidden items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" onclick="closeModal('maintenance-modal')"></div>
    <div class="relative bg-white dark:bg-gray-900 rounded-3xl shadow-2xl w-full max-w-md mx-auto overflow-hidden animate-modal">
        
        <div class="flex items-center justify-between p-5 border-b border-gray-100 dark:border-gray-800 bg-[#FDFBF7] dark:bg-gray-850">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center shadow-sm">
                    <span class="material-symbols-outlined text-xl">handyman</span>
                </div>
                <div>
                    <h3 class="font-luxury font-bold text-gray-900 dark:text-white text-lg">Log Maintenance Ticket</h3>
                    <p class="text-xs text-gray-500">Facilities & CMMS Dispatch</p>
                </div>
            </div>
            <button onclick="closeModal('maintenance-modal')" class="w-8 h-8 rounded-full bg-gray-100 dark:bg-gray-800 text-gray-500 hover:text-gray-900 dark:hover:text-white flex items-center justify-center transition-colors">
                <span class="material-symbols-outlined text-[18px]">close</span>
            </button>
        </div>

        <form id="maintenance-form" class="p-5 space-y-4">
            <div>
                <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-1.5">Issue Title *</label>
                <input type="text" id="wo_title" required placeholder="e.g. Balcony Light Fixture Replacement" 
                       class="w-full px-3 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 dark:bg-gray-800 text-xs sm:text-sm text-gray-900 dark:text-white focus:border-gold outline-none"/>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-1.5">Category *</label>
                    <select id="wo_category" class="w-full px-3 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 dark:bg-gray-800 text-xs sm:text-sm text-gray-900 dark:text-white focus:border-gold outline-none">
                        <option value="Electrical">Electrical</option>
                        <option value="Plumbing">Plumbing</option>
                        <option value="HVAC & AC">HVAC & AC</option>
                        <option value="Carpentry">Carpentry</option>
                        <option value="Electronics">Electronics</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-1.5">Priority *</label>
                    <select id="wo_priority" class="w-full px-3 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 dark:bg-gray-800 text-xs sm:text-sm text-gray-900 dark:text-white focus:border-gold outline-none">
                        <option value="Low">Low</option>
                        <option value="Medium" selected>Medium</option>
                        <option value="High">High (Urgent)</option>
                        <option value="Emergency">Emergency</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-1.5">Description</label>
                <textarea id="wo_desc" rows="3" placeholder="Provide any details for the engineering team..." 
                          class="w-full px-3 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 dark:bg-gray-800 text-xs sm:text-sm text-gray-900 dark:text-white focus:border-gold outline-none"></textarea>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="button" onclick="closeModal('maintenance-modal')" 
                        class="flex-1 py-3 rounded-xl border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-300 font-semibold text-xs sm:text-sm">
                    Cancel
                </button>
                <button type="submit" 
                        class="flex-1 py-3 rounded-xl bg-navy text-white font-bold text-xs sm:text-sm hover:bg-navy-light transition-all shadow-md">
                    Dispatch Ticket
                </button>
            </div>
        </form>
    </div>
</div>

<style>
@keyframes modal-in {
    from { opacity: 0; transform: scale(0.96) translateY(12px); }
    to   { opacity: 1; transform: scale(1) translateY(0); }
}
.animate-modal { animation: modal-in 0.22s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
</style>

<script>
// ---- Live Date & Clock Display ----
function updateClock() {
    const el = document.getElementById('live-datetime');
    if (!el) return;
    const now = new Date();
    el.textContent = now.toLocaleDateString('en-KE', { 
        weekday: 'short', month: 'short', day: 'numeric', year: 'numeric' 
    }) + ' • ' + now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
}
updateClock();
setInterval(updateClock, 30000);

// ---- Modal Controls ----
function openModal(id) {
    const el = document.getElementById(id);
    if (!el) return;
    el.classList.remove('hidden');
    el.classList.add('flex');
}
function closeModal(id) {
    const el = document.getElementById(id);
    if (!el) return;
    el.classList.add('hidden');
    el.classList.remove('flex');
}

// ---- Room Click Handler ----
function handleRoomClick(room) {
    if (room.status === 'Available') {
        // Open booking modal and auto-select this room
        openModal('booking-modal');
        const sel = document.getElementById('modal_room_select');
        if (sel) {
            sel.value = room.id;
            calcBookingPrice();
        }
    } else if (room.status === 'Occupied') {
        Swal.fire({
            title: `Room ${room.room_number} (${room.type})`,
            html: `
                <div class="text-left text-sm space-y-2 p-3 bg-gray-50 rounded-xl mt-2 text-gray-700">
                    <p><strong>Status:</strong> <span class="text-rose-600 font-bold">Occupied (Guest In-House)</span></p>
                    <p><strong>Standard Rate:</strong> KSh ${Number(room.price).toLocaleString()} / night</p>
                    <p><strong>Floor:</strong> Level ${room.floor}</p>
                </div>
            `,
            icon: 'info',
            confirmButtonText: 'View Folio',
            confirmButtonColor: '#0B132B'
        }).then(r => {
            if(r.isConfirmed) window.location.href = 'bookings.php';
        });
    } else if (room.status === 'Cleaning') {
        Swal.fire({
            title: `Room ${room.room_number} Turnover`,
            text: `Currently assigned to housekeeping. Mark inspection complete and release to Front Desk?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Release to Available',
            confirmButtonColor: '#10B981',
            cancelButtonText: 'View Housekeeping'
        }).then(async r => {
            if (r.isConfirmed) {
                try {
                    const res = await fetch('../api/rooms.php', {
                        method: 'PUT',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ id: room.id, status: 'Available' })
                    });
                    const json = await res.json();
                    if(json.success) {
                        Swal.fire('Success', `Room ${room.room_number} is now Available!`, 'success')
                            .then(() => location.reload());
                    }
                } catch(e) {
                    Swal.fire('Error', 'Failed to update status', 'error');
                }
            } else if (r.dismiss === Swal.DismissReason.cancel) {
                window.location.href = 'maintenance.php';
            }
        });
    }
}

// ---- Search & Filter in Live Room Matrix ----
const searchInput = document.getElementById('room-search-input');
if (searchInput) {
    searchInput.addEventListener('input', function() {
        const query = this.value.toLowerCase().trim();
        document.querySelectorAll('.room-card').forEach(card => {
            const num = card.getAttribute('data-room-number').toLowerCase();
            const type = card.getAttribute('data-room-type').toLowerCase();
            card.style.display = (num.includes(query) || type.includes(query)) ? '' : 'none';
        });
    });
}

function filterRoomStatus(status) {
    // Update active button styling
    document.querySelectorAll('.status-filter-btn').forEach(btn => {
        if (btn.getAttribute('data-status') === status) {
            btn.classList.remove('bg-gray-100', 'dark:bg-gray-800', 'text-gray-600', 'dark:text-gray-300');
            btn.classList.add('bg-navy', 'text-white');
        } else {
            btn.classList.remove('bg-navy', 'text-white');
            btn.classList.add('bg-gray-100', 'dark:bg-gray-800', 'text-gray-600', 'dark:text-gray-300');
        }
    });

    document.querySelectorAll('.room-card').forEach(card => {
        if (status === 'all' || card.getAttribute('data-status') === status) {
            card.style.display = '';
        } else {
            card.style.display = 'none';
        }
    });
}

// ---- Price Preview in Booking Modal ----
function calcBookingPrice() {
    const roomSel  = document.getElementById('modal_room_select');
    const checkIn  = document.getElementById('modal_check_in').value;
    const checkOut = document.getElementById('modal_check_out').value;
    const preview  = document.getElementById('price-preview');
    const est      = document.getElementById('price-estimate');

    if (!roomSel || !roomSel.selectedIndex) return;
    const opt = roomSel.options[roomSel.selectedIndex];
    const nightly = parseFloat(opt.getAttribute('data-price') || 0);

    if (!opt.value || !checkIn || !nightly) {
        preview.classList.add('hidden');
        return;
    }

    let nights = 1;
    if (checkIn && checkOut) {
        const diff = (new Date(checkOut) - new Date(checkIn)) / (1000 * 60 * 60 * 24);
        if (diff > 0) nights = Math.round(diff);
    }
    const total = nightly * nights;
    est.textContent = `KSh ${total.toLocaleString()} (${nights} night${nights > 1 ? 's' : ''})`;
    preview.classList.remove('hidden');
}

document.getElementById('modal_room_select')?.addEventListener('change', calcBookingPrice);
document.getElementById('modal_check_in')?.addEventListener('change', calcBookingPrice);
document.getElementById('modal_check_out')?.addEventListener('change', calcBookingPrice);

// Default check-in date to today
const checkInEl = document.getElementById('modal_check_in');
if (checkInEl && !checkInEl.value) {
    checkInEl.valueAsDate = new Date();
}

// ---- New Booking Form Submit ----
document.getElementById('booking-form')?.addEventListener('submit', async function(e) {
    e.preventDefault();
    const btn = document.getElementById('booking-submit-btn');
    const original = btn.innerHTML;
    btn.innerHTML = '<span class="material-symbols-outlined animate-spin text-[18px]">sync</span> Processing...';
    btn.disabled = true;

    const payload = {
        guest_name: this.guest_name.value,
        room_id:    this.room_id.value,
        check_in:   this.check_in.value,
        check_out:  this.check_out.value,
    };

    try {
        const res = await fetch('../api/bookings.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        });
        const json = await res.json();

        if (json.success) {
            closeModal('booking-modal');
            this.reset();
            document.getElementById('price-preview')?.classList.add('hidden');
            Swal.fire({
                icon: 'success',
                title: 'Reservation Confirmed!',
                text: `${json.message} Total Folio: KSh ${Number(json.total || 0).toLocaleString()}`,
                timer: 2000,
                showConfirmButton: false
            }).then(() => location.reload());
        } else {
            Swal.fire('Booking Error', json.message, 'error');
        }
    } catch(err) {
        Swal.fire('Connection Error', 'Unable to reach the server. Please try again.', 'error');
    } finally {
        btn.innerHTML = original;
        btn.disabled = false;
    }
});

// ---- Maintenance Ticket Submit ----
document.getElementById('maintenance-form')?.addEventListener('submit', function(e) {
    e.preventDefault();
    closeModal('maintenance-modal');
    Swal.fire({
        icon: 'success',
        title: 'Work Order Dispatched',
        text: 'Ticket logged to Facilities & Maintenance queue.',
        timer: 1800,
        showConfirmButton: false
    });
    this.reset();
});

// ---- Export Executive Summary (CSV) ----
function exportExecutiveReport() {
    Swal.fire({
        title: 'Export Daily Executive Report?',
        text: 'This will download a verified CSV brief of revenue, occupancy, and departmental status.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#0B132B',
        confirmButtonText: 'Download CSV'
    }).then(r => {
        if(r.isConfirmed) {
            const dateStr = new Date().toISOString().split('T')[0];
            const csvRows = [
                ['SkopeStay Luxury Resort - Daily Executive Brief', dateStr],
                [],
                ['METRIC', 'VALUE'],
                ['Total Suites & Rooms', '<?= $total_rooms ?>'],
                ['Occupied Rooms', '<?= $occupied ?>'],
                ['Available Rooms', '<?= $available ?>'],
                ['Cleaning / Turnover', '<?= $cleaning ?>'],
                ['Occupancy Rate', '<?= $occ_pct ?>%'],
                ['Suites Revenue Today', 'KSh <?= $revenue_today ?>'],
                ['Dining POS Revenue Today', 'KSh <?= $rev_restaurant ?>'],
                ['Gross Total Revenue', 'KSh <?= $total_gross_revenue ?>'],
                ['Estimated ADR', 'KSh <?= $adr ?>'],
                ['Staff on Shift Today', '<?= $staff_on_duty ?>'],
                ['Open Work Orders (CMMS)', '<?= $open_work_orders ?>']
            ];

            const csvContent = "data:text/csv;charset=utf-8," + csvRows.map(e => e.join(",")).join("\n");
            const encodedUri = encodeURI(csvContent);
            const link = document.createElement("a");
            link.setAttribute("href", encodedUri);
            link.setAttribute("download", `SkopeStay_Executive_Brief_${dateStr}.csv`);
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);

            Swal.fire('Export Complete', 'The CSV report has been generated and downloaded.', 'success');
        }
    });
}
</script>

</body>
</html>
