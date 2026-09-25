<?php
require_once 'includes/config.php';

// Fetch Live Real-Time Statistics
$total_rooms = $pdo->query("SELECT COUNT(*) FROM rooms")->fetchColumn() ?: 0;
$occupied_rooms = $pdo->query("SELECT COUNT(*) FROM rooms WHERE status = 'Occupied'")->fetchColumn() ?: 0;
$available_rooms = max(0, $total_rooms - $occupied_rooms);

$total_halls = $pdo->query("SELECT COUNT(*) FROM halls")->fetchColumn() ?: 0;
$booked_halls = $pdo->query("SELECT COUNT(DISTINCT hall_id) FROM hall_bookings WHERE event_date = CURDATE() AND status != 'Cancelled'")->fetchColumn() ?: 0;
$available_halls = max(0, $total_halls - $booked_halls);

$pool_capacity = 100;
$pool_visitors_today = $pdo->query("SELECT COALESCE(SUM(number_of_guests),0) FROM pool_passes WHERE visit_date = CURDATE()")->fetchColumn() ?: 0;
$available_pool_slots = max(0, $pool_capacity - $pool_visitors_today);

$total_tables = 20;
$occupied_tables = $pdo->query("SELECT COUNT(*) FROM restaurant_orders WHERE DATE(created_at) = CURDATE() AND status NOT IN ('Served','Paid','Completed','Cancelled')")->fetchColumn() ?: 0;
$available_tables = max(0, $total_tables - $occupied_tables);

// Fetch Accommodations (Suites & Rooms)
$featured_rooms = $pdo->query("SELECT * FROM rooms ORDER BY price DESC LIMIT 6")->fetchAll(PDO::FETCH_ASSOC);

// Fetch Halls & Venues
$halls = $pdo->query("SELECT * FROM halls LIMIT 4")->fetchAll(PDO::FETCH_ASSOC);

// Fetch Dining Items
try {
    $restaurant_items = $pdo->query("SELECT * FROM restaurant_items WHERE status = 'Available' ORDER BY id ASC")->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $restaurant_items = [];
}

// High-Resolution Fallback Imagery for 5-Star Luxury Feel
$room_imagery = [
    'Presidential Suite' => 'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?q=80&w=1200&auto=format&fit=crop',
    'Executive Suite'    => 'https://images.unsplash.com/photo-1590490360182-c33d57733427?q=80&w=1200&auto=format&fit=crop',
    'Honeymoon Suite'    => 'https://images.unsplash.com/photo-1578683010236-d716f9a3f461?q=80&w=1200&auto=format&fit=crop',
    'Family Suite'       => 'https://images.unsplash.com/photo-1566665797739-1674de7a421a?q=80&w=1200&auto=format&fit=crop',
    'Deluxe King'        => 'https://images.unsplash.com/photo-1611892440504-42a792e24d32?q=80&w=1200&auto=format&fit=crop',
    'Single Standard'    => 'https://images.unsplash.com/photo-1595526114035-0d45ed16cfbf?q=80&w=1200&auto=format&fit=crop'
];

$hall_imagery = [
    'https://images.unsplash.com/photo-1519167758481-83f550bb49b3?q=80&w=1600&auto=format&fit=crop',
    'https://images.unsplash.com/photo-1497366216548-37526070297c?q=80&w=1200&auto=format&fit=crop',
    'https://images.unsplash.com/photo-1530103862676-de8892795cfa?q=80&w=1200&auto=format&fit=crop'
];
?>
<!DOCTYPE html>
<html lang="en" class="scroll-smooth overflow-x-hidden w-full">
<head>
    <title>SkopeStay | Luxury Resort, Suites & Smart Bookings</title>
<?php include 'includes/public_head.php'; ?>
</head>
<body class="text-navy relative flex flex-col min-h-screen">
<?php include 'includes/public_header.php'; ?>

    <!-- =========================================================================
         1. CINEMATIC HERO SECTION
         ========================================================================= -->
    <section class="relative min-h-[92vh] lg:min-h-screen w-full overflow-hidden flex items-center justify-center bg-navy pt-24 pb-20 lg:py-0">
        <!-- Parallax Background Layer -->
        <div class="absolute inset-0 z-0">
            <img src="https://images.unsplash.com/photo-1542314831-c53cd3816002?q=80&w=2070&auto=format&fit=crop" 
                 class="w-full h-full object-cover animate-slow-zoom opacity-70" 
                 alt="SkopeStay Luxury Resort Exterior">
            <!-- Multi-Stage Vignette Gradients for Perfect Text Legibility -->
            <div class="absolute inset-0 bg-gradient-to-t from-navy via-navy/60 to-navy/40"></div>
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_center,transparent_0%,rgba(11,19,43,0.7)_100%)]"></div>
        </div>

        <!-- Hero Content Block -->
        <div class="relative z-10 text-center px-4 sm:px-6 max-w-5xl mx-auto my-auto" data-aos="fade-up" data-aos-duration="1200">
            <!-- 5-Star Status Pill -->
            <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white/10 backdrop-blur-md border border-white/20 text-gold text-xs sm:text-sm font-brand font-bold uppercase tracking-[0.25em] mb-6 shadow-lg">
                <span class="material-symbols-outlined text-[16px] text-gold">hotel_class</span>
                <span>5-Star Luxury Resort & Suites</span>
            </div>

            <!-- Grand Headline -->
            <h1 class="font-display text-4xl sm:text-6xl md:text-7xl lg:text-8xl font-bold text-white leading-[1.08] mb-6 drop-shadow-2xl">
                Where Timeless Elegance<br>
                <span class="gold-gradient-text italic font-normal">Meets Smart Comfort.</span>
            </h1>

            <!-- Subtitle -->
            <p class="font-inter text-gray-200 text-base sm:text-lg md:text-xl max-w-2xl mx-auto mb-10 leading-relaxed font-light">
                Discover bespoke sanctuary suites, Michelin-inspired dining, and panoramic skyline infinity leisure—elevated with instant, effortless reservations.
            </p>
            
            <!-- Hero CTAs -->
            <div class="flex flex-col sm:flex-row gap-4 justify-center items-center max-w-md mx-auto sm:max-w-none">
                <button onclick="openBookingWizard('Room')" 
                        class="w-full sm:w-auto gold-gradient-bg text-navy px-8 py-4 rounded-xl font-ui font-bold text-base hover:scale-105 active:scale-95 transition-all duration-300 shadow-gold-glow flex items-center justify-center gap-2 group">
                    <span class="material-symbols-outlined text-xl group-hover:rotate-12 transition-transform">calendar_month</span>
                    <span>Reserve Your Stay</span>
                </button>
                <a href="#rooms" 
                   class="w-full sm:w-auto glass-dark-luxury text-white hover:text-gold px-8 py-4 rounded-xl font-ui font-semibold text-base hover:bg-white/15 transition-all duration-300 flex items-center justify-center gap-2">
                    <span>Explore Suites</span>
                    <span class="material-symbols-outlined text-lg">arrow_downward</span>
                </a>
            </div>

            <!-- Trust Micro-Badges -->
            <div class="mt-12 flex flex-wrap justify-center items-center gap-6 sm:gap-10 text-gray-300 text-xs sm:text-sm font-ui opacity-90">
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-gold text-lg">verified_user</span>
                    <span>Best Direct Rate</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-gold text-lg">lock_clock</span>
                    <span>Instant Confirmation</span>
                </div>
                <div class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-gold text-lg">room_service</span>
                    <span>24/7 Concierge Service</span>
                </div>
            </div>
        </div>

        <!-- Floating Live Status Widget (Desktop only) -->
        <div class="absolute bottom-10 right-8 lg:right-16 glass-dark-luxury p-3.5 px-5 rounded-2xl hidden lg:flex items-center gap-3.5 animate-float shadow-2xl border border-white/15" data-aos="fade-left" data-aos-delay="400">
            <div class="relative flex h-3.5 w-3.5">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-3.5 w-3.5 bg-emerald-500"></span>
            </div>
            <div class="text-left">
                <span class="block text-[10px] text-gray-400 font-brand tracking-widest uppercase">Live Property Status</span>
                <span class="block text-white font-ui font-bold text-xs"><?= $available_rooms ?> Luxury Suites Available Today</span>
            </div>
        </div>
    </section>

    <!-- =========================================================================
         2. MULTI-SERVICE QUICK RESERVATION BAR (RESPONSIVE)
         ========================================================================= -->
    <div class="relative z-30 max-w-6xl mx-auto -mt-10 sm:-mt-14 px-4 sm:px-6 w-full" data-aos="fade-up" data-aos-delay="150">
        <div class="glass-dark-luxury rounded-3xl p-5 sm:p-6 shadow-2xl border border-white/20 text-white">
            <form onsubmit="event.preventDefault(); triggerQuickSearch();" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 items-center">
                <!-- Check In Date -->
                <div class="bg-white/5 border border-white/10 rounded-2xl p-3 hover:border-gold/50 transition-colors">
                    <label class="text-[10px] text-gold font-brand uppercase tracking-wider block mb-1 flex items-center gap-1 font-bold">
                        <span class="material-symbols-outlined text-xs">calendar_today</span> Check-In
                    </label>
                    <input type="date" id="quick-check-in" value="<?= date('Y-m-d') ?>" min="<?= date('Y-m-d') ?>" class="w-full bg-transparent text-white font-ui font-semibold text-sm border-none p-0 focus:ring-0 cursor-pointer">
                </div>

                <!-- Check Out Date -->
                <div class="bg-white/5 border border-white/10 rounded-2xl p-3 hover:border-gold/50 transition-colors">
                    <label class="text-[10px] text-gold font-brand uppercase tracking-wider block mb-1 flex items-center gap-1 font-bold">
                        <span class="material-symbols-outlined text-xs">event</span> Check-Out
                    </label>
                    <input type="date" id="quick-check-out" value="<?= date('Y-m-d', strtotime('+1 day')) ?>" min="<?= date('Y-m-d', strtotime('+1 day')) ?>" class="w-full bg-transparent text-white font-ui font-semibold text-sm border-none p-0 focus:ring-0 cursor-pointer">
                </div>

                <!-- Guests -->
                <div class="bg-white/5 border border-white/10 rounded-2xl p-3 hover:border-gold/50 transition-colors">
                    <label class="text-[10px] text-gold font-brand uppercase tracking-wider block mb-1 flex items-center gap-1 font-bold">
                        <span class="material-symbols-outlined text-xs">group</span> Guests
                    </label>
                    <select id="quick-guests" class="w-full bg-transparent text-white font-ui font-semibold text-sm border-none p-0 focus:ring-0 cursor-pointer appearance-none">
                        <option value="1" class="text-navy">1 Adult Guest</option>
                        <option value="2" selected class="text-navy">2 Adult Guests</option>
                        <option value="3" class="text-navy">3 Guests</option>
                        <option value="4" class="text-navy">Family (4+ Guests)</option>
                    </select>
                </div>

                <!-- Facility Service -->
                <div class="bg-white/5 border border-white/10 rounded-2xl p-3 hover:border-gold/50 transition-colors">
                    <label class="text-[10px] text-gold font-brand uppercase tracking-wider block mb-1 flex items-center gap-1 font-bold">
                        <span class="material-symbols-outlined text-xs">apartment</span> Experience
                    </label>
                    <select id="quick-service" class="w-full bg-transparent text-white font-ui font-semibold text-sm border-none p-0 focus:ring-0 cursor-pointer appearance-none">
                        <option value="Room" selected class="text-navy">Suites & Rooms</option>
                        <option value="Hall" class="text-navy">Banquet & Event Halls</option>
                        <option value="Restaurant" class="text-navy">Restaurant Table</option>
                        <option value="Pool" class="text-navy">Infinity Pool Pass</option>
                    </select>
                </div>

                <!-- Search CTA Button -->
                <div class="sm:col-span-2 lg:col-span-1">
                    <button type="submit" class="w-full gold-gradient-bg text-navy font-ui font-bold h-[54px] rounded-2xl shadow-gold-sm hover:scale-[1.02] active:scale-[0.98] transition-all duration-300 flex items-center justify-center gap-2">
                        <span class="material-symbols-outlined font-bold">search</span>
                        <span>Check Rates</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Main Body Container -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 py-14 sm:py-20 space-y-24">
        
        <!-- =====================================================================
             3. LIVE REAL-TIME PROPERTY METRICS
             ===================================================================== -->
        <section data-aos="fade-up" class="w-full">
            <div class="text-center mb-10">
                <span class="font-brand text-gold tracking-[0.2em] text-xs font-bold uppercase block mb-2">Live Availability Metrics</span>
                <h2 class="font-display text-3xl sm:text-4xl font-bold text-navy">Real-Time Facility Status</h2>
                <div class="w-16 h-1 bg-gold mx-auto mt-3 rounded-full"></div>
            </div>

            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
                <!-- Rooms Free -->
                <div class="bg-white rounded-3xl p-5 sm:p-7 shadow-luxury hover-lift text-center border border-gray-100 flex flex-col items-center">
                    <div class="w-14 h-14 rounded-2xl bg-navy/5 text-navy flex items-center justify-center mb-3">
                        <span class="material-symbols-outlined text-3xl">king_bed</span>
                    </div>
                    <span class="text-3xl sm:text-4xl font-display font-bold text-navy counter mb-1" data-target="<?= $available_rooms ?>">0</span>
                    <span class="text-xs font-brand uppercase tracking-wider text-gray-400 font-bold">Suites Available</span>
                    <span class="text-[11px] text-emerald-600 font-medium mt-1">Ready for check-in</span>
                </div>

                <!-- Halls Open -->
                <div class="bg-white rounded-3xl p-5 sm:p-7 shadow-luxury hover-lift text-center border border-gray-100 flex flex-col items-center">
                    <div class="w-14 h-14 rounded-2xl bg-gold/15 text-gold-dark flex items-center justify-center mb-3">
                        <span class="material-symbols-outlined text-3xl">event_seat</span>
                    </div>
                    <span class="text-3xl sm:text-4xl font-display font-bold text-gold-dark counter mb-1" data-target="<?= $available_halls ?>">0</span>
                    <span class="text-xs font-brand uppercase tracking-wider text-gray-400 font-bold">Venues Available</span>
                    <span class="text-[11px] text-gray-500 font-medium mt-1">Corporate & Banquets</span>
                </div>

                <!-- Pool Slots -->
                <div class="bg-white rounded-3xl p-5 sm:p-7 shadow-luxury hover-lift text-center border border-gray-100 flex flex-col items-center">
                    <div class="w-14 h-14 rounded-2xl bg-blue-500/10 text-blue-600 flex items-center justify-center mb-3">
                        <span class="material-symbols-outlined text-3xl">pool</span>
                    </div>
                    <span class="text-3xl sm:text-4xl font-display font-bold text-blue-600 counter mb-1" data-target="<?= $available_pool_slots ?>">0</span>
                    <span class="text-xs font-brand uppercase tracking-wider text-gray-400 font-bold">Pool Day Slots</span>
                    <span class="text-[11px] text-blue-500 font-medium mt-1">Heated Infinity Pool</span>
                </div>

                <!-- Dining Tables -->
                <div class="bg-white rounded-3xl p-5 sm:p-7 shadow-luxury hover-lift text-center border border-gray-100 flex flex-col items-center">
                    <div class="w-14 h-14 rounded-2xl bg-amber-500/10 text-amber-600 flex items-center justify-center mb-3">
                        <span class="material-symbols-outlined text-3xl">table_restaurant</span>
                    </div>
                    <span class="text-3xl sm:text-4xl font-display font-bold text-amber-600 counter mb-1" data-target="<?= $available_tables ?>">0</span>
                    <span class="text-xs font-brand uppercase tracking-wider text-gray-400 font-bold">Tables Open</span>
                    <span class="text-[11px] text-amber-600 font-medium mt-1">Chef Tasting Tonight</span>
                </div>
            </div>
        </section>

        <!-- =====================================================================
             4. LUXURY ACCOMMODATIONS (SUITES & ROOMS)
             ========================================================================= -->
        <section id="rooms" class="scroll-mt-28">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-end mb-12 gap-4" data-aos="fade-right">
                <div>
                    <span class="font-brand text-gold tracking-[0.2em] text-xs font-bold uppercase block mb-2">Rest & Rejuvenate</span>
                    <h2 class="font-display text-3xl sm:text-5xl font-bold text-navy">Signature Suites & Sanctuaries</h2>
                    <p class="text-gray-500 text-sm sm:text-base mt-2 max-w-xl">Each accommodation is crafted with bespoke furnishings, marble bathrooms, and panoramic vistas.</p>
                </div>
                <div class="hidden md:flex items-center gap-3">
                    <button onclick="openBookingWizard('Room')" class="bg-navy hover:bg-gold text-white hover:text-navy px-6 py-3 rounded-xl font-ui font-semibold text-xs tracking-wider transition-colors shadow-md flex items-center gap-2">
                        <span>Check All Availability</span>
                        <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
                    </button>
                </div>
            </div>

            <!-- Suites Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php foreach ($featured_rooms as $index => $room): 
                    $roomType = $room['type'];
                    $imagePath = $room_imagery[$roomType] ?? 'https://images.unsplash.com/photo-1590490360182-c33d57733427?q=80&w=1200&auto=format&fit=crop';
                    $occupancy = $room['max_occupancy'] ?? 2;
                    $featuresList = !empty($room['features']) ? explode(',', $room['features']) : ['King Bed', 'Rainforest Shower', 'Smart TV', 'Free Wi-Fi'];
                ?>
                <div class="bg-white rounded-[2rem] overflow-hidden shadow-luxury hover-lift border border-gray-100 flex flex-col group transition-all duration-300" data-aos="fade-up" data-aos-delay="<?= ($index % 3) * 150 ?>">
                    <!-- Suite Image Container -->
                    <div class="relative h-64 sm:h-72 img-zoom-container">
                        <img src="<?= htmlspecialchars($imagePath) ?>" 
                             class="w-full h-full object-cover" 
                             loading="lazy" 
                             alt="<?= htmlspecialchars($roomType) ?>">
                        
                        <!-- Rating Badge -->
                        <div class="absolute top-4 right-4 bg-white/90 backdrop-blur-md px-3 py-1 rounded-full text-xs font-ui font-bold text-navy shadow-md flex items-center gap-1">
                            <span class="material-symbols-outlined text-xs text-gold">star</span>
                            <span>4.96</span>
                        </div>

                        <!-- Badge for Top Suite -->
                        <?php if($index === 0): ?>
                        <div class="absolute top-4 left-4 gold-gradient-bg text-navy px-3.5 py-1 rounded-full text-[10px] font-brand font-bold uppercase tracking-wider shadow-md">
                            Flagship Suite
                        </div>
                        <?php endif; ?>

                        <!-- Availability Indicator -->
                        <div class="absolute bottom-3 left-4 bg-navy/80 backdrop-blur-md px-3 py-1 rounded-lg text-[11px] font-ui text-emerald-400 font-semibold border border-white/10">
                            Room <?= htmlspecialchars($room['room_number']) ?> • <?= htmlspecialchars($room['status']) ?>
                        </div>
                    </div>

                    <!-- Suite Details Content -->
                    <div class="p-6 sm:p-7 flex flex-col flex-1">
                        <div class="flex justify-between items-start mb-2">
                            <h3 class="text-xl sm:text-2xl font-display font-bold text-navy group-hover:text-gold transition-colors">
                                <?= htmlspecialchars($roomType) ?>
                            </h3>
                        </div>

                        <p class="text-gray-500 text-xs sm:text-sm line-clamp-2 mb-4 leading-relaxed">
                            <?= htmlspecialchars($room['description'] ?? 'Luxurious suite offering bespoke decor, panoramic views, and premium hotel amenities.') ?>
                        </p>

                        <!-- Key Spec Chips -->
                        <div class="flex items-center gap-3 text-xs text-gray-500 font-ui mb-5">
                            <span class="flex items-center gap-1">
                                <span class="material-symbols-outlined text-[16px] text-gold">person</span> 
                                <?= $occupancy ?> Guests
                            </span>
                            <span class="w-1 h-1 bg-gray-300 rounded-full"></span>
                            <span class="flex items-center gap-1">
                                <span class="material-symbols-outlined text-[16px] text-gold">king_bed</span> 
                                Floor <?= htmlspecialchars($room['floor'] ?? '1') ?>
                            </span>
                            <span class="w-1 h-1 bg-gray-300 rounded-full"></span>
                            <span class="flex items-center gap-1">
                                <span class="material-symbols-outlined text-[16px] text-gold">wifi</span> 
                                Fiber Wi-Fi
                            </span>
                        </div>

                        <!-- Amenities List -->
                        <div class="border-t border-gray-100 pt-4 mb-6">
                            <div class="flex flex-wrap gap-1.5">
                                <?php foreach(array_slice($featuresList, 0, 3) as $feat): ?>
                                <span class="text-[11px] font-ui bg-gray-50 border border-gray-100 text-gray-600 px-2.5 py-1 rounded-md">
                                    <?= htmlspecialchars(trim($feat)) ?>
                                </span>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <!-- Price & Booking CTA -->
                        <div class="mt-auto border-t border-gray-100 pt-5 flex justify-between items-center">
                            <div>
                                <span class="text-[11px] text-gray-400 font-brand uppercase tracking-wider block">Nightly Rate</span>
                                <div class="flex items-baseline gap-1">
                                    <span class="text-xl sm:text-2xl font-display font-bold text-navy">KSh <?= number_format($room['price'], 0) ?></span>
                                    <span class="text-[11px] text-gray-400">/ night</span>
                                </div>
                            </div>
                            <button onclick="openBookingWizard('Room')" 
                                    class="px-5 py-2.5 rounded-xl bg-navy text-white hover:bg-gold hover:text-navy transition-all duration-300 font-ui font-semibold text-xs shadow-md flex items-center gap-1.5 group-hover:scale-105">
                                <span>Reserve</span>
                                <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
                            </button>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            
            <!-- Mobile View All CTA -->
            <div class="md:hidden mt-8 text-center">
                <button onclick="openBookingWizard('Room')" class="w-full bg-navy text-white py-3.5 rounded-xl font-ui font-bold text-sm shadow-md">
                    Explore All Available Suites
                </button>
            </div>
        </section>

        <!-- =====================================================================
             5. BENTO GRID: WORLD-CLASS VENUES & EVENTS
             ===================================================================== -->
        <section id="halls" class="scroll-mt-28">
            <div class="text-center mb-12" data-aos="fade-up">
                <span class="font-brand text-gold tracking-[0.2em] text-xs font-bold uppercase block mb-2">Conferences & Celebrations</span>
                <h2 class="font-display text-3xl sm:text-5xl font-bold text-navy">Grand Venues & Event Spaces</h2>
                <div class="w-16 h-1 bg-gold mx-auto mt-3 rounded-full"></div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                <!-- Flagship Main Venue (8 Cols) -->
                <?php if(isset($halls[0])): ?>
                <div class="lg:col-span-8 rounded-[2.5rem] overflow-hidden relative group min-h-[420px] sm:min-h-[500px] hover-lift shadow-luxury" data-aos="fade-right">
                    <img src="<?= $hall_imagery[0] ?>" class="w-full h-full object-cover transition-transform duration-1000 group-hover:scale-105" loading="lazy" alt="<?= htmlspecialchars($halls[0]['name']) ?>">
                    <div class="absolute inset-0 bg-gradient-to-t from-navy via-navy/50 to-transparent"></div>
                    
                    <div class="absolute bottom-0 left-0 p-6 sm:p-10 md:p-12 w-full text-white">
                        <div class="gold-gradient-bg text-navy px-3.5 py-1 rounded-full text-[10px] font-brand tracking-widest uppercase font-bold inline-block mb-3 shadow">
                            Flagship Venue
                        </div>
                        <h3 class="text-3xl sm:text-4xl md:text-5xl font-display font-bold text-white mb-3">
                            <?= htmlspecialchars($halls[0]['name']) ?>
                        </h3>
                        <p class="text-gray-200 text-sm sm:text-base mb-6 max-w-xl font-light line-clamp-2 sm:line-clamp-3">
                            <?= htmlspecialchars($halls[0]['description'] ?? 'Host prestigious international summits, gala weddings, and executive keynotes with state-of-the-art audiovisual fidelity.') ?>
                        </p>
                        
                        <div class="flex flex-wrap items-center justify-between gap-4 pt-4 border-t border-white/20">
                            <div class="flex items-center gap-6">
                                <div class="flex items-center gap-2">
                                    <span class="material-symbols-outlined text-gold">groups</span>
                                    <span class="font-bold text-base sm:text-lg"><?= htmlspecialchars($halls[0]['capacity']) ?> Guests</span>
                                </div>
                                <div class="w-px h-5 bg-white/30 hidden sm:block"></div>
                                <div class="flex items-center gap-2">
                                    <span class="material-symbols-outlined text-gold">payments</span>
                                    <span class="font-bold text-base sm:text-lg">KSh <?= number_format($halls[0]['daily_rate'], 0) ?>/day</span>
                                </div>
                            </div>
                            <button onclick="openBookingWizard('Hall')" class="bg-white text-navy hover:bg-gold hover:text-navy px-6 py-3 rounded-xl font-ui font-bold text-xs tracking-wider transition-all duration-300 shadow-lg">
                                Reserve Hall
                            </button>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Secondary Venues (4 Cols) -->
                <div class="lg:col-span-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-1 gap-6">
                    <!-- Venue 2 -->
                    <?php if(isset($halls[1])): ?>
                    <div class="bg-navy rounded-[2.5rem] p-7 text-white relative overflow-hidden group hover-lift shadow-luxury flex flex-col justify-between" data-aos="fade-left" data-aos-delay="100">
                        <div class="absolute -right-8 -top-8 w-32 h-32 bg-gold/20 rounded-full blur-2xl"></div>
                        <div>
                            <span class="text-gold font-brand text-[10px] tracking-widest font-bold uppercase block mb-2">Executive Summit</span>
                            <h3 class="text-2xl font-display font-bold mb-2"><?= htmlspecialchars($halls[1]['name']) ?></h3>
                            <p class="text-gray-300 text-xs sm:text-sm line-clamp-2 leading-relaxed mb-4">
                                <?= htmlspecialchars($halls[1]['description'] ?? 'Intimate setting equipped with 4K teleconferencing and soundproofing.') ?>
                            </p>
                        </div>
                        <div class="flex justify-between items-center pt-4 border-t border-white/10">
                            <div>
                                <span class="text-[10px] text-gray-400 uppercase font-brand block">Capacity <?= htmlspecialchars($halls[1]['capacity']) ?></span>
                                <span class="font-display font-bold text-lg text-gold">KSh <?= number_format($halls[1]['daily_rate'], 0) ?></span>
                            </div>
                            <button onclick="openBookingWizard('Hall')" class="w-10 h-10 rounded-full border border-white/20 flex items-center justify-center hover:bg-gold hover:text-navy hover:border-transparent transition-all">
                                <span class="material-symbols-outlined text-lg">arrow_forward</span>
                            </button>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Venue 3 -->
                    <?php if(isset($halls[2])): ?>
                    <div class="rounded-[2.5rem] overflow-hidden relative group hover-lift shadow-luxury min-h-[220px]" data-aos="fade-left" data-aos-delay="200">
                        <img src="<?= $hall_imagery[1] ?>" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110" loading="lazy" alt="<?= htmlspecialchars($halls[2]['name']) ?>">
                        <div class="absolute inset-0 bg-navy/60 backdrop-blur-[2px] flex flex-col justify-end p-6 text-white transition-all group-hover:bg-navy/70">
                            <span class="text-gold text-[10px] uppercase font-brand font-bold">Outdoor Elegance</span>
                            <h3 class="text-xl font-display font-bold mb-1"><?= htmlspecialchars($halls[2]['name']) ?></h3>
                            <div class="flex justify-between items-center mt-3">
                                <span class="text-xs text-gray-200">Up to <?= htmlspecialchars($halls[2]['capacity']) ?> Attendees</span>
                                <button onclick="openBookingWizard('Hall')" class="glass-luxury text-navy px-4 py-1.5 rounded-full font-ui font-semibold text-xs hover:bg-gold transition-colors">
                                    Book Ground
                                </button>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </section>

    </main>

    <!-- =========================================================================
         6. THE INFINITY ESCAPE (POOL & WELLNESS SPA PARALLAX)
         ========================================================================= -->
    <section id="pool" class="relative py-20 lg:py-28 overflow-hidden bg-navy my-10">
        <!-- Parallax Backdrop Image -->
        <div class="absolute inset-0 z-0">
            <img src="https://images.unsplash.com/photo-1576013551627-0cc20b96c2a7?q=80&w=2070&auto=format&fit=crop" 
                 class="w-full h-full object-cover opacity-50" 
                 loading="lazy" 
                 alt="SkopeStay Heated Infinity Pool">
            <div class="absolute inset-0 pool-overlay"></div>
            <!-- Ripple element -->
            <div class="absolute top-1/2 left-1/3 w-[600px] h-[600px] -mt-[300px] -ml-[300px] rounded-full border border-white/10 animate-ripple pointer-events-none"></div>
        </div>
        
        <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
            <!-- Left Narrative -->
            <div class="lg:col-span-7" data-aos="fade-right">
                <span class="font-brand text-gold tracking-[0.2em] text-xs font-bold uppercase block mb-3">Hydrotherapy & Skyline Leisure</span>
                <h2 class="font-display text-4xl sm:text-5xl md:text-6xl font-bold text-white mb-6 leading-tight">
                    The Horizon Infinity Escape
                </h2>
                <p class="text-gray-300 text-base sm:text-lg mb-8 max-w-xl font-light leading-relaxed">
                    Float above the world in our temperature-controlled 50-meter infinity pool. Featuring poolside cabanas, artisanal tropical mixology, and hydrotherapy jets for ultimate serenity.
                </p>
                
                <div class="flex flex-col sm:flex-row gap-4">
                    <button onclick="openBookingWizard('Pool')" 
                            class="gold-gradient-bg text-navy px-8 py-4 rounded-xl font-ui font-bold shadow-gold-sm hover:scale-105 active:scale-95 transition-all flex items-center justify-center gap-2">
                        <span class="material-symbols-outlined text-xl">water</span>
                        <span>Get Daily Pool Pass</span>
                    </button>
                    <div class="glass-dark-luxury text-white px-6 py-4 rounded-xl flex items-center gap-3.5 border border-white/15">
                        <div class="w-10 h-10 rounded-xl bg-gold/15 text-gold flex items-center justify-center">
                            <span class="material-symbols-outlined text-xl">schedule</span>
                        </div>
                        <div>
                            <span class="block text-[10px] uppercase font-brand tracking-wider text-gray-400">Open Daily</span>
                            <span class="font-ui font-bold text-sm text-white">06:00 AM — 10:00 PM</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Right Capacity Gauge Card -->
            <div class="lg:col-span-5 lg:justify-self-end w-full max-w-md" data-aos="fade-left">
                <div class="glass-dark-luxury p-8 rounded-3xl border border-white/20 shadow-2xl relative overflow-hidden">
                    <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-gold via-yellow-300 to-gold"></div>
                    
                    <div class="flex justify-between items-center mb-6">
                        <h4 class="text-white font-display font-bold text-xl">Live Pool Capacity</h4>
                        <div class="flex h-3.5 w-3.5 relative">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-3.5 w-3.5 bg-emerald-500"></span>
                        </div>
                    </div>

                    <?php 
                        $pool_percent = min(100, round((($pool_capacity - $available_pool_slots) / $pool_capacity) * 100));
                    ?>
                    <div class="flex items-baseline gap-3 mb-2">
                        <div class="text-5xl sm:text-6xl font-display font-bold text-white"><?= $pool_percent ?>%</div>
                        <span class="text-xs text-emerald-400 font-semibold bg-emerald-900/40 px-2.5 py-1 rounded-full border border-emerald-500/20">Optimal Conditions</span>
                    </div>

                    <!-- Visual Capacity Meter -->
                    <div class="w-full bg-white/10 rounded-full h-2.5 mb-6 overflow-hidden">
                        <div class="bg-gradient-to-r from-blue-400 to-gold h-full rounded-full transition-all duration-1000" style="width: <?= max(5, $pool_percent) ?>%"></div>
                    </div>

                    <p class="text-gray-300 text-xs sm:text-sm mb-6 leading-relaxed">
                        <?= $available_pool_slots ?> visitor passes remaining today. Sun loungers and towel service provided with every pass.
                    </p>
                    
                    <!-- Pricing Details -->
                    <div class="space-y-3 pt-4 border-t border-white/10">
                        <div class="flex justify-between items-center text-sm text-white">
                            <span class="text-gray-300">Adult Day Access Pass</span>
                            <span class="font-ui font-bold text-gold text-base">KSh 1,000</span>
                        </div>
                        <div class="flex justify-between items-center text-sm text-white">
                            <span class="text-gray-300">Junior Day Access (Under 12)</span>
                            <span class="font-ui font-bold text-gold text-base">KSh 500</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- =========================================================================
         7. SIGNATURE FINE DINING & GASTRONOMY
         ========================================================================= -->
    <section id="restaurant" class="max-w-7xl mx-auto px-4 sm:px-6 py-16 scroll-mt-28">
        <div class="text-center mb-14" data-aos="fade-up">
            <span class="font-brand text-gold tracking-[0.2em] text-xs font-bold uppercase block mb-2">Artisanal Gastronomy</span>
            <h2 class="font-display text-3xl sm:text-5xl font-bold text-navy">Signature Culinary Offerings</h2>
            <p class="text-gray-500 text-sm sm:text-base mt-2 max-w-xl mx-auto">From sunrise continental pastries to flame-seared dry-aged steaks and coastal delicacies.</p>
            <div class="w-16 h-1 bg-gold mx-auto mt-4 rounded-full"></div>

            <!-- Dynamic Category Filter Tabs -->
            <div class="flex flex-wrap justify-center gap-2 sm:gap-3 mt-8">
                <button onclick="filterMenu('all', this)" class="menu-tab-btn px-5 py-2 rounded-full bg-navy text-white font-ui font-semibold text-xs shadow-md transition-all">All Delicacies</button>
                <button onclick="filterMenu('Breakfast', this)" class="menu-tab-btn px-5 py-2 rounded-full bg-white text-navy border border-gray-200 hover:border-gold font-ui font-semibold text-xs transition-all">Breakfast</button>
                <button onclick="filterMenu('Main Course', this)" class="menu-tab-btn px-5 py-2 rounded-full bg-white text-navy border border-gray-200 hover:border-gold font-ui font-semibold text-xs transition-all">Main Course</button>
                <button onclick="filterMenu('Desserts', this)" class="menu-tab-btn px-5 py-2 rounded-full bg-white text-navy border border-gray-200 hover:border-gold font-ui font-semibold text-xs transition-all">Desserts</button>
                <button onclick="filterMenu('Beverages', this)" class="menu-tab-btn px-5 py-2 rounded-full bg-white text-navy border border-gray-200 hover:border-gold font-ui font-semibold text-xs transition-all">Beverages</button>
            </div>
        </div>

        <!-- Menu Cards Grid -->
        <div id="restaurant-grid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 sm:gap-8">
            <?php foreach ($restaurant_items as $index => $item): 
                $categorySlug = htmlspecialchars($item['category']);
            ?>
            <div class="menu-item-card bg-white rounded-3xl p-4 shadow-luxury hover-lift group border border-gray-100 flex flex-col transition-all duration-300" 
                 data-category="<?= $categorySlug ?>"
                 data-aos="fade-up" 
                 data-aos-delay="<?= ($index % 4) * 100 ?>">
                
                <div class="relative h-48 rounded-2xl overflow-hidden mb-4 bg-gray-100 img-zoom-container">
                    <?php if(!empty($item['image_url'])): ?>
                        <img src="<?= htmlspecialchars($item['image_url']) ?>" 
                             class="w-full h-full object-cover" 
                             loading="lazy" 
                             alt="<?= htmlspecialchars($item['name']) ?>">
                    <?php else: ?>
                        <div class="w-full h-full flex items-center justify-center bg-navy/5 text-4xl">🍽️</div>
                    <?php endif; ?>
                    
                    <div class="absolute top-2.5 right-2.5 bg-navy/80 backdrop-blur-md px-2.5 py-1 rounded-lg text-[10px] font-brand uppercase tracking-wider text-gold font-bold">
                        <?= htmlspecialchars($item['category']) ?>
                    </div>
                </div>

                <div class="px-2 flex flex-col flex-1">
                    <h4 class="font-display font-bold text-lg text-navy mb-1 group-hover:text-gold transition-colors leading-snug">
                        <?= htmlspecialchars($item['name']) ?>
                    </h4>
                    <p class="text-xs text-gray-500 mb-4 line-clamp-2 leading-relaxed">
                        <?= htmlspecialchars($item['description'] ?? 'Carefully prepared by our executive culinary masters.') ?>
                    </p>
                    
                    <div class="mt-auto pt-3 border-t border-gray-100 flex justify-between items-center">
                        <span class="font-ui font-bold text-gold text-lg">KSh <?= number_format($item['price'], 0) ?></span>
                        <button onclick="openBookingWizard('Restaurant')" class="w-9 h-9 rounded-full bg-navy/5 text-navy group-hover:bg-gold group-hover:text-navy transition-colors flex items-center justify-center" title="Order / Reserve Table">
                            <span class="material-symbols-outlined text-[18px]">add</span>
                        </button>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <!-- Dining Reservation Action -->
        <div class="mt-14 flex flex-col sm:flex-row justify-center items-center gap-4 text-center">
            <button onclick="openBookingWizard('Restaurant')" class="bg-navy hover:bg-gold text-white hover:text-navy px-8 py-4 rounded-xl font-ui font-bold text-sm tracking-wide transition-all duration-300 shadow-luxury flex items-center gap-2">
                <span class="material-symbols-outlined text-lg">table_restaurant</span>
                <span>Reserve a Dining Table</span>
            </button>
            <a href="tel:+254742380183" class="px-6 py-4 rounded-xl border border-gray-300 hover:border-gold text-navy font-ui font-semibold text-sm transition-colors flex items-center gap-2">
                <span class="material-symbols-outlined text-gold text-lg">call</span>
                <span>Chef Tasting Inquiries: +254 742 380 183</span>
            </a>
        </div>
    </section>

    <!-- =========================================================================
         8. RESORT AMENITIES & WHY CHOOSE US
         ========================================================================= -->
    <section id="amenities" class="bg-navy text-white py-20 relative overflow-hidden">
        <div class="absolute -top-20 -left-20 w-80 h-80 bg-gold/10 rounded-full blur-[90px] pointer-events-none"></div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 relative z-10">
            <div class="text-center mb-16" data-aos="fade-up">
                <span class="font-brand text-gold tracking-[0.2em] text-xs font-bold uppercase block mb-2">Excellence in Hospitality</span>
                <h2 class="font-display text-3xl sm:text-5xl font-bold">Unrivaled Resort Privileges</h2>
                <div class="w-16 h-1 bg-gold mx-auto mt-3 rounded-full"></div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Amenity 1 -->
                <div class="glass-dark-luxury p-8 rounded-3xl border border-white/10 hover-lift flex gap-5 items-start">
                    <div class="w-12 h-12 rounded-2xl bg-gold/15 text-gold flex items-center justify-center shrink-0 border border-gold/30">
                        <span class="material-symbols-outlined text-2xl">concierge</span>
                    </div>
                    <div>
                        <h4 class="font-display font-bold text-xl text-white mb-2">24/7 Private Butler</h4>
                        <p class="text-gray-400 text-sm leading-relaxed">Dedicated concierge personnel on standby around the clock for bespoke guest itineraries.</p>
                    </div>
                </div>

                <!-- Amenity 2 -->
                <div class="glass-dark-luxury p-8 rounded-3xl border border-white/10 hover-lift flex gap-5 items-start">
                    <div class="w-12 h-12 rounded-2xl bg-gold/15 text-gold flex items-center justify-center shrink-0 border border-gold/30">
                        <span class="material-symbols-outlined text-2xl">wifi</span>
                    </div>
                    <div>
                        <h4 class="font-display font-bold text-xl text-white mb-2">High-Speed Fiber</h4>
                        <p class="text-gray-400 text-sm leading-relaxed">Gigabit wireless connectivity across every suite, boardroom, and poolside cabana.</p>
                    </div>
                </div>

                <!-- Amenity 3 -->
                <div class="glass-dark-luxury p-8 rounded-3xl border border-white/10 hover-lift flex gap-5 items-start">
                    <div class="w-12 h-12 rounded-2xl bg-gold/15 text-gold flex items-center justify-center shrink-0 border border-gold/30">
                        <span class="material-symbols-outlined text-2xl">spa</span>
                    </div>
                    <div>
                        <h4 class="font-display font-bold text-xl text-white mb-2">Hydro & Wellness Spa</h4>
                        <p class="text-gray-400 text-sm leading-relaxed">Rejuvenate with custom aromatherapy treatments, deep-tissue massage, and sauna chambers.</p>
                    </div>
                </div>

                <!-- Amenity 4 -->
                <div class="glass-dark-luxury p-8 rounded-3xl border border-white/10 hover-lift flex gap-5 items-start">
                    <div class="w-12 h-12 rounded-2xl bg-gold/15 text-gold flex items-center justify-center shrink-0 border border-gold/30">
                        <span class="material-symbols-outlined text-2xl">local_taxi</span>
                    </div>
                    <div>
                        <h4 class="font-display font-bold text-xl text-white mb-2">Airport Chauffeur</h4>
                        <p class="text-gray-400 text-sm leading-relaxed">Seamless luxury transfers to and from international terminals in private executive vehicles.</p>
                    </div>
                </div>

                <!-- Amenity 5 -->
                <div class="glass-dark-luxury p-8 rounded-3xl border border-white/10 hover-lift flex gap-5 items-start">
                    <div class="w-12 h-12 rounded-2xl bg-gold/15 text-gold flex items-center justify-center shrink-0 border border-gold/30">
                        <span class="material-symbols-outlined text-2xl">shield</span>
                    </div>
                    <div>
                        <h4 class="font-display font-bold text-xl text-white mb-2">Precision Security</h4>
                        <p class="text-gray-400 text-sm leading-relaxed">Gated estate with biometric room access and around-the-clock trained security officers.</p>
                    </div>
                </div>

                <!-- Amenity 6 -->
                <div class="glass-dark-luxury p-8 rounded-3xl border border-white/10 hover-lift flex gap-5 items-start">
                    <div class="w-12 h-12 rounded-2xl bg-gold/15 text-gold flex items-center justify-center shrink-0 border border-gold/30">
                        <span class="material-symbols-outlined text-2xl">smart_toy</span>
                    </div>
                    <div>
                        <h4 class="font-display font-bold text-xl text-white mb-2">AI Guest Concierge</h4>
                        <p class="text-gray-400 text-sm leading-relaxed">Automated instant check-in, digital keycards, and one-tap room service via your mobile device.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- =========================================================================
         9. GUEST REVIEWS & TESTIMONIALS
         ========================================================================= -->
    <section class="py-20 bg-lightgray">
        <div class="max-w-7xl mx-auto px-4 sm:px-6">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-end mb-14" data-aos="fade-right">
                <div>
                    <span class="font-brand text-gold tracking-[0.2em] text-xs font-bold uppercase block mb-2">Distinguished Guests</span>
                    <h2 class="font-display text-3xl sm:text-5xl font-bold text-navy">Words from Our Visitors</h2>
                </div>
                <div class="flex gap-3 mt-4 md:mt-0" data-aos="fade-left">
                    <button class="swiper-btn-prev w-11 h-11 rounded-full border border-gray-300 flex items-center justify-center hover:bg-gold hover:border-gold hover:text-navy transition-all" aria-label="Previous Review">
                        <span class="material-symbols-outlined text-lg">arrow_back</span>
                    </button>
                    <button class="swiper-btn-next w-11 h-11 rounded-full border border-gray-300 flex items-center justify-center hover:bg-gold hover:border-gold hover:text-navy transition-all" aria-label="Next Review">
                        <span class="material-symbols-outlined text-lg">arrow_forward</span>
                    </button>
                </div>
            </div>

            <!-- Swiper Carousel -->
            <div class="swiper testimonial-swiper" data-aos="fade-up">
                <div class="swiper-wrapper">
                    <!-- Review 1 -->
                    <div class="swiper-slide">
                        <div class="bg-white p-8 sm:p-10 rounded-[2.5rem] shadow-luxury border border-gray-100 h-full flex flex-col justify-between">
                            <div>
                                <div class="flex text-gold mb-5">
                                    <span class="material-symbols-outlined text-xl">star</span>
                                    <span class="material-symbols-outlined text-xl">star</span>
                                    <span class="material-symbols-outlined text-xl">star</span>
                                    <span class="material-symbols-outlined text-xl">star</span>
                                    <span class="material-symbols-outlined text-xl">star</span>
                                </div>
                                <p class="text-gray-700 text-base sm:text-lg italic leading-relaxed mb-6 font-light">
                                    "The presidential suite was simply extraordinary. Instant booking, exquisite dinner by the ocean breeze, and impeccable room service. We will return every summer."
                                </p>
                            </div>
                            <div class="flex items-center gap-4 pt-4 border-t border-gray-100">
                                <div class="w-12 h-12 rounded-full bg-navy text-gold flex items-center justify-center font-display font-bold text-lg">PO</div>
                                <div>
                                    <h5 class="font-ui font-bold text-navy text-sm">Peter Ogutu</h5>
                                    <span class="text-xs text-gray-400">Regional Technology Director</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Review 2 -->
                    <div class="swiper-slide">
                        <div class="bg-white p-8 sm:p-10 rounded-[2.5rem] shadow-luxury border border-gray-100 h-full flex flex-col justify-between">
                            <div>
                                <div class="flex text-gold mb-5">
                                    <span class="material-symbols-outlined text-xl">star</span>
                                    <span class="material-symbols-outlined text-xl">star</span>
                                    <span class="material-symbols-outlined text-xl">star</span>
                                    <span class="material-symbols-outlined text-xl">star</span>
                                    <span class="material-symbols-outlined text-xl">star</span>
                                </div>
                                <p class="text-gray-700 text-base sm:text-lg italic leading-relaxed mb-6 font-light">
                                    "We held our annual corporate conference in The Grand Ballroom. Audiovisual setup, catering, and guest accommodation were executed with flawless military precision."
                                </p>
                            </div>
                            <div class="flex items-center gap-4 pt-4 border-t border-gray-100">
                                <div class="w-12 h-12 rounded-full bg-gold text-navy flex items-center justify-center font-display font-bold text-lg">SW</div>
                                <div>
                                    <h5 class="font-ui font-bold text-navy text-sm">Sarah Wanjiku</h5>
                                    <span class="text-xs text-gray-400">Chief Executive Officer</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Review 3 -->
                    <div class="swiper-slide">
                        <div class="bg-white p-8 sm:p-10 rounded-[2.5rem] shadow-luxury border border-gray-100 h-full flex flex-col justify-between">
                            <div>
                                <div class="flex text-gold mb-5">
                                    <span class="material-symbols-outlined text-xl">star</span>
                                    <span class="material-symbols-outlined text-xl">star</span>
                                    <span class="material-symbols-outlined text-xl">star</span>
                                    <span class="material-symbols-outlined text-xl">star</span>
                                    <span class="material-symbols-outlined text-xl">star</span>
                                </div>
                                <p class="text-gray-700 text-base sm:text-lg italic leading-relaxed mb-6 font-light">
                                    "The heated infinity pool offers one of the best views in the country. Our kids had an unforgettable weekend. The food is 10/10."
                                </p>
                            </div>
                            <div class="flex items-center gap-4 pt-4 border-t border-gray-100">
                                <div class="w-12 h-12 rounded-full bg-navy text-white flex items-center justify-center font-display font-bold text-lg">DK</div>
                                <div>
                                    <h5 class="font-ui font-bold text-navy text-sm">David Kibet</h5>
                                    <span class="text-xs text-gray-400">Architect & Traveler</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- =========================================================================
         10. DIRECT CONCIERGE INQUIRY & CONTACT SECTION
         ========================================================================= -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 py-16">
        <div class="glass-dark-luxury rounded-[2.5rem] p-8 sm:p-12 md:p-16 text-white border border-white/15 shadow-2xl relative overflow-hidden" data-aos="fade-up">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 items-center">
                <div class="lg:col-span-6 space-y-5">
                    <span class="font-brand text-gold tracking-[0.2em] text-xs font-bold uppercase block">Bespoke Concierge</span>
                    <h2 class="font-display text-3xl sm:text-4xl md:text-5xl font-bold leading-tight">
                        Planning an Event or Private Stay?
                    </h2>
                    <p class="text-gray-300 text-sm sm:text-base font-light leading-relaxed">
                        Our guest relation specialists are available to customize your suite package, schedule private tasting menus, or coordinate banquet facilities.
                    </p>
                    
                    <div class="space-y-3 pt-2 text-sm text-gray-300 font-ui">
                        <div class="flex items-center gap-3">
                            <span class="material-symbols-outlined text-gold">location_on</span>
                            <span>SkopeStay International Luxury Resort, Kenya</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="material-symbols-outlined text-gold">call</span>
                            <span>Direct Line: +254 742 380 183</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="material-symbols-outlined text-gold">mail</span>
                            <span>concierge@skopestay.com</span>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-6 bg-white rounded-3xl p-6 sm:p-8 text-navy shadow-xl">
                    <form onsubmit="handleDirectInquiry(event)" class="space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-brand font-bold uppercase tracking-wider text-gray-500 mb-1">Your Name *</label>
                                <input type="text" id="inq-name" required placeholder="e.g. Jane Doe" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm font-ui focus:outline-none focus:border-gold">
                            </div>
                            <div>
                                <label class="block text-xs font-brand font-bold uppercase tracking-wider text-gray-500 mb-1">Phone Number *</label>
                                <input type="tel" id="inq-phone" required placeholder="0712 345 678" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm font-ui focus:outline-none focus:border-gold">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-brand font-bold uppercase tracking-wider text-gray-500 mb-1">Email Address *</label>
                                <input type="email" id="inq-email" required placeholder="jane@example.com" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm font-ui focus:outline-none focus:border-gold">
                            </div>
                            <div>
                                <label class="block text-xs font-brand font-bold uppercase tracking-wider text-gray-500 mb-1">Inquiry Type</label>
                                <select id="inq-type" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm font-ui focus:outline-none focus:border-gold">
                                    <option value="Suite Booking">Suite Reservation</option>
                                    <option value="Hall & Event">Banquet or Wedding</option>
                                    <option value="Corporate Meeting">Corporate Boardroom</option>
                                    <option value="Dining Reservation">Dining & Chef Tasting</option>
                                    <option value="Other">General Inquiry</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-brand font-bold uppercase tracking-wider text-gray-500 mb-1">Message or Special Requests</label>
                            <textarea id="inq-msg" rows="3" placeholder="Tell us how we can make your visit memorable..." class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm font-ui focus:outline-none focus:border-gold"></textarea>
                        </div>

                        <button type="submit" id="inq-btn" class="w-full gold-gradient-bg text-navy font-ui font-bold py-3.5 rounded-xl shadow-md hover:scale-[1.01] active:scale-[0.99] transition-all flex items-center justify-center gap-2">
                            <span class="material-symbols-outlined text-lg">send</span>
                            <span>Transmit Inquiry to Concierge</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- =========================================================================
         11. MULTI-STEP RESERVATION WIZARD MODAL (FULL RESPONSIVE)
         ========================================================================= -->
    <div id="booking-modal" class="fixed inset-0 z-[200] hidden flex items-center justify-center p-3 sm:p-4 overflow-y-auto">
        <!-- Backdrop Blur -->
        <div onclick="closeBookingWizard()" class="fixed inset-0 bg-navy/85 backdrop-blur-md transition-opacity"></div>
        
        <div class="relative w-full max-w-2xl bg-white rounded-3xl shadow-2xl overflow-hidden flex flex-col max-h-[92vh] z-10 animate-fade-in border border-white/20">
            <!-- Modal Header -->
            <div class="bg-navy text-white p-5 sm:p-6 flex justify-between items-center shrink-0">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-gold/20 text-gold flex items-center justify-center">
                        <span class="material-symbols-outlined text-xl">calendar_month</span>
                    </div>
                    <div>
                        <h3 class="font-display font-bold text-lg sm:text-xl text-white">Smart Reservation System</h3>
                        <p class="text-gray-400 text-xs font-brand" id="wizard-subtitle">Step 1: Select Experience</p>
                    </div>
                </div>
                <button onclick="closeBookingWizard()" class="w-8 h-8 rounded-full bg-white/10 text-gray-300 hover:text-white hover:bg-white/20 flex items-center justify-center transition-colors">
                    <span class="material-symbols-outlined text-xl">close</span>
                </button>
            </div>

            <!-- Animated Gold Progress Bar -->
            <div class="w-full bg-gray-100 h-1 shrink-0">
                <div id="wizard-progress" class="bg-gold h-1 w-1/4 transition-all duration-400"></div>
            </div>

            <!-- Modal Scrollable Body -->
            <div class="p-6 sm:p-8 overflow-y-auto flex-1 font-ui">
                <!-- STEP 1: Select Service -->
                <div id="step-1" class="wizard-step space-y-3">
                    <label class="flex items-center gap-4 p-4 border-2 border-gray-100 rounded-2xl cursor-pointer hover:border-gold transition-colors group">
                        <input type="radio" name="service" value="Room" class="w-5 h-5 text-gold focus:ring-gold" checked>
                        <div class="w-12 h-12 bg-navy/5 group-hover:bg-gold/15 text-navy group-hover:text-gold-dark rounded-xl flex items-center justify-center transition-colors">
                            <span class="material-symbols-outlined text-2xl">king_bed</span>
                        </div>
                        <div class="flex-1">
                            <span class="font-bold text-base sm:text-lg block text-navy">Suites & Guest Rooms</span>
                            <span class="text-xs text-gray-500">Overnight stays, executive suites, and penthouses</span>
                        </div>
                    </label>

                    <label class="flex items-center gap-4 p-4 border-2 border-gray-100 rounded-2xl cursor-pointer hover:border-gold transition-colors group">
                        <input type="radio" name="service" value="Hall" class="w-5 h-5 text-gold focus:ring-gold">
                        <div class="w-12 h-12 bg-navy/5 group-hover:bg-gold/15 text-navy group-hover:text-gold-dark rounded-xl flex items-center justify-center transition-colors">
                            <span class="material-symbols-outlined text-2xl">event_seat</span>
                        </div>
                        <div class="flex-1">
                            <span class="font-bold text-base sm:text-lg block text-navy">Banquets & Event Venues</span>
                            <span class="text-xs text-gray-500">Grand ballroom, executive boardrooms, garden grounds</span>
                        </div>
                    </label>

                    <label class="flex items-center gap-4 p-4 border-2 border-gray-100 rounded-2xl cursor-pointer hover:border-gold transition-colors group">
                        <input type="radio" name="service" value="Restaurant" class="w-5 h-5 text-gold focus:ring-gold">
                        <div class="w-12 h-12 bg-navy/5 group-hover:bg-gold/15 text-navy group-hover:text-gold-dark rounded-xl flex items-center justify-center transition-colors">
                            <span class="material-symbols-outlined text-2xl">restaurant</span>
                        </div>
                        <div class="flex-1">
                            <span class="font-bold text-base sm:text-lg block text-navy">Dining Table Reservation</span>
                            <span class="text-xs text-gray-500">Fine dining, chef tasting, terrace seating</span>
                        </div>
                    </label>

                    <label class="flex items-center gap-4 p-4 border-2 border-gray-100 rounded-2xl cursor-pointer hover:border-gold transition-colors group">
                        <input type="radio" name="service" value="Pool" class="w-5 h-5 text-gold focus:ring-gold">
                        <div class="w-12 h-12 bg-navy/5 group-hover:bg-gold/15 text-navy group-hover:text-gold-dark rounded-xl flex items-center justify-center transition-colors">
                            <span class="material-symbols-outlined text-2xl">water</span>
                        </div>
                        <div class="flex-1">
                            <span class="font-bold text-base sm:text-lg block text-navy">Infinity Pool Day Pass</span>
                            <span class="text-xs text-gray-500">Temperature-controlled swimming with lounge service</span>
                        </div>
                    </label>
                </div>

                <!-- STEP 2: Dates & Occupancy -->
                <div id="step-2" class="wizard-step hidden space-y-5">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-brand font-bold uppercase tracking-wider text-navy mb-1.5">Check-In / Event Date *</label>
                            <input id="wiz-check-in" type="date" value="<?= date('Y-m-d') ?>" min="<?= date('Y-m-d') ?>" class="w-full border-2 border-gray-100 rounded-xl p-3 text-sm focus:border-gold focus:ring-0">
                        </div>
                        <div>
                            <label class="block text-xs font-brand font-bold uppercase tracking-wider text-navy mb-1.5">Check-Out Date</label>
                            <input id="wiz-check-out" type="date" value="<?= date('Y-m-d', strtotime('+1 day')) ?>" min="<?= date('Y-m-d', strtotime('+1 day')) ?>" class="w-full border-2 border-gray-100 rounded-xl p-3 text-sm focus:border-gold focus:ring-0">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-brand font-bold uppercase tracking-wider text-navy mb-1.5">Number of Guests</label>
                        <input id="wiz-guests" type="number" min="1" max="100" value="2" class="w-full border-2 border-gray-100 rounded-xl p-3 text-sm focus:border-gold focus:ring-0">
                        <span class="text-[11px] text-gray-400 mt-1 block">Rates and capacity adjust automatically based on total party size.</span>
                    </div>
                </div>

                <!-- STEP 3: Available Options Display -->
                <div id="step-3" class="wizard-step hidden flex flex-col py-2">
                    <div id="step3-loading" class="flex flex-col items-center justify-center py-12">
                        <div class="w-12 h-12 border-4 border-gold border-t-transparent rounded-full animate-spin mb-4"></div>
                        <h4 class="font-display font-bold text-lg text-navy">Checking Property Availability...</h4>
                        <p class="text-xs text-gray-400 mt-1">Connecting to live reservation engine</p>
                    </div>
                    <div id="step3-options" class="w-full hidden space-y-3">
                        <!-- Populated by JavaScript -->
                    </div>
                </div>

                <!-- STEP 4: Guest Contact Information -->
                <div id="step-4" class="wizard-step hidden space-y-4">
                    <div class="bg-emerald-50 text-emerald-800 p-4 rounded-xl border border-emerald-200 text-xs sm:text-sm flex items-center gap-3">
                        <span class="material-symbols-outlined text-emerald-600">verified</span>
                        <span>Suite/Service confirmed! Please finalize your guest credentials.</span>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-brand font-bold uppercase tracking-wider text-navy mb-1">First Name *</label>
                            <input id="wiz-first-name" type="text" placeholder="e.g. Jane" class="w-full border-2 border-gray-100 rounded-xl p-3 text-sm focus:border-gold focus:ring-0">
                        </div>
                        <div>
                            <label class="block text-xs font-brand font-bold uppercase tracking-wider text-navy mb-1">Last Name *</label>
                            <input id="wiz-last-name" type="text" placeholder="e.g. Kamau" class="w-full border-2 border-gray-100 rounded-xl p-3 text-sm focus:border-gold focus:ring-0">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-brand font-bold uppercase tracking-wider text-navy mb-1">Email Address *</label>
                        <input id="wiz-email" type="email" placeholder="e.g. jane@example.com" class="w-full border-2 border-gray-100 rounded-xl p-3 text-sm focus:border-gold focus:ring-0">
                    </div>
                    <div>
                        <label class="block text-xs font-brand font-bold uppercase tracking-wider text-navy mb-1">Phone / WhatsApp Number *</label>
                        <input id="wiz-phone" type="tel" placeholder="e.g. 0712 345 678" class="w-full border-2 border-gray-100 rounded-xl p-3 text-sm focus:border-gold focus:ring-0">
                    </div>
                </div>
            </div>

            <!-- Modal Footer Controls -->
            <div class="p-4 sm:p-6 border-t border-gray-100 flex justify-between items-center bg-gray-50 shrink-0">
                <button id="wiz-btn-back" onclick="wizardGoBack()" class="px-5 py-2.5 font-ui font-bold text-sm text-gray-500 hover:text-navy hidden transition-colors">
                    Back
                </button>
                <div class="flex-1"></div>
                <button id="wiz-btn-next" onclick="wizardGoNext()" class="bg-navy hover:bg-gold text-white hover:text-navy px-8 py-3 rounded-xl font-ui font-bold text-sm transition-all shadow-md">
                    Next Step
                </button>
            </div>
        </div>
    </div>

<?php include 'includes/public_footer.php'; ?>

    <!-- Interactive Scripts & Logic -->
    <script>
        // Swiper Testimonials Slider
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof Swiper !== 'undefined') {
                new Swiper('.testimonial-swiper', {
                    slidesPerView: 1,
                    spaceBetween: 24,
                    loop: true,
                    autoplay: {
                        delay: 6000,
                        disableOnInteraction: false,
                    },
                    breakpoints: {
                        768: { slidesPerView: 2, spaceBetween: 30 }
                    },
                    navigation: {
                        nextEl: '.swiper-btn-next',
                        prevEl: '.swiper-btn-prev',
                    }
                });
            }
        });

        // Animated Metric Counters
        const counters = document.querySelectorAll('.counter');
        const counterObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const counter = entry.target;
                    const target = +counter.getAttribute('data-target');
                    let count = 0;
                    const inc = Math.max(1, Math.ceil(target / 40));
                    const update = () => {
                        count += inc;
                        if (count < target) {
                            counter.innerText = count;
                            requestAnimationFrame(update);
                        } else {
                            counter.innerText = target;
                        }
                    };
                    update();
                    counterObserver.unobserve(counter);
                }
            });
        }, { threshold: 0.5 });
        counters.forEach(c => counterObserver.observe(c));

        // Restaurant Dynamic Category Filter
        function filterMenu(category, btn) {
            document.querySelectorAll('.menu-tab-btn').forEach(b => {
                b.classList.remove('bg-navy', 'text-white');
                b.classList.add('bg-white', 'text-navy');
            });
            btn.classList.add('bg-navy', 'text-white');
            btn.classList.remove('bg-white', 'text-navy');

            const cards = document.querySelectorAll('.menu-item-card');
            cards.forEach(card => {
                const cardCat = card.getAttribute('data-category');
                if (category === 'all' || cardCat.toLowerCase() === category.toLowerCase()) {
                    card.style.display = 'flex';
                } else {
                    card.style.display = 'none';
                }
            });
        }

        // Quick Search Bar Bridge
        function triggerQuickSearch() {
            const checkIn = document.getElementById('quick-check-in')?.value;
            const checkOut = document.getElementById('quick-check-out')?.value;
            const guests = document.getElementById('quick-guests')?.value;
            const service = document.getElementById('quick-service')?.value;

            if (document.getElementById('wiz-check-in')) document.getElementById('wiz-check-in').value = checkIn;
            if (document.getElementById('wiz-check-out')) document.getElementById('wiz-check-out').value = checkOut;
            if (document.getElementById('wiz-guests')) document.getElementById('wiz-guests').value = guests;

            openBookingWizard(service);
            showStep(2); // Jump straight to confirmation of dates
        }

        // Direct Concierge Inquiry Handler
        function handleDirectInquiry(e) {
            e.preventDefault();
            const name = document.getElementById('inq-name').value.trim();
            const phone = document.getElementById('inq-phone').value.trim();
            const email = document.getElementById('inq-email').value.trim();
            const type = document.getElementById('inq-type').value;
            const msg = document.getElementById('inq-msg').value.trim();

            const btn = document.getElementById('inq-btn');
            btn.disabled = true;
            btn.innerHTML = '<span class="material-symbols-outlined animate-spin text-lg">sync</span> Transmitting...';

            fetch('api/public.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    action: 'contact',
                    full_name: name,
                    phone: phone,
                    email: email,
                    inquiry_type: type,
                    message: msg
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Inquiry Received',
                        text: 'Thank you, ' + name + '! Our head concierge will contact you shortly.',
                        confirmButtonColor: '#0B132B'
                    });
                    document.getElementById('inq-name').value = '';
                    document.getElementById('inq-phone').value = '';
                    document.getElementById('inq-email').value = '';
                    document.getElementById('inq-msg').value = '';
                } else {
                    Swal.fire({ icon: 'error', title: 'Error', text: data.message, confirmButtonColor: '#0B132B' });
                }
            })
            .catch(() => {
                Swal.fire({ icon: 'error', title: 'Transmission Error', text: 'Could not send message. Please reach us via WhatsApp directly.', confirmButtonColor: '#0B132B' });
            })
            .finally(() => {
                btn.disabled = false;
                btn.innerHTML = '<span class="material-symbols-outlined text-lg">send</span> Transmit Inquiry to Concierge';
            });
        }

        // Multi-Step Reservation Wizard Logic
        let currentStep = 1;
        const totalSteps = 4;

        function openBookingWizard(presetService = null) {
            const modal = document.getElementById('booking-modal');
            if (!modal) return;
            modal.classList.remove('hidden');
            
            if (presetService) {
                const radios = document.getElementsByName('service');
                for (let i = 0; i < radios.length; i++) {
                    if (radios[i].value === presetService) {
                        radios[i].checked = true;
                        break;
                    }
                }
            }
            showStep(1);
        }

        function closeBookingWizard() {
            const modal = document.getElementById('booking-modal');
            if (modal) modal.classList.add('hidden');
            setTimeout(() => { showStep(1); }, 250);
        }

        function updateWizardUI() {
            const progress = document.getElementById('wizard-progress');
            if (progress) progress.style.width = ((currentStep / totalSteps) * 100) + '%';
            
            const backBtn = document.getElementById('wiz-btn-back');
            if (backBtn) backBtn.style.display = currentStep > 1 ? 'block' : 'none';
            
            const nextBtn = document.getElementById('wiz-btn-next');
            if (nextBtn) {
                if (currentStep === totalSteps) {
                    nextBtn.style.display = 'block';
                    nextBtn.innerText = 'Confirm Reservation';
                    nextBtn.classList.add('gold-gradient-bg', 'text-navy');
                    nextBtn.classList.remove('bg-navy', 'text-white');
                } else if (currentStep === 3) {
                    nextBtn.style.display = 'none';
                    if (backBtn) backBtn.style.display = 'none';
                } else {
                    nextBtn.style.display = 'block';
                    nextBtn.innerText = 'Continue';
                    nextBtn.classList.remove('gold-gradient-bg', 'text-navy');
                    nextBtn.classList.add('bg-navy', 'text-white');
                }
            }

            const subtitles = {
                1: 'Step 1: Select Experience',
                2: 'Step 2: Dates & Guest Count',
                3: 'Step 3: Checking Live Availability',
                4: 'Step 4: Guest Credentials'
            };
            const subtitleEl = document.getElementById('wizard-subtitle');
            if (subtitleEl) subtitleEl.innerText = subtitles[currentStep];
        }

        function showStep(step) {
            document.querySelectorAll('.wizard-step').forEach(el => el.classList.add('hidden'));
            const targetEl = document.getElementById('step-' + step);
            if (targetEl) targetEl.classList.remove('hidden');
            
            currentStep = step;
            updateWizardUI();

            if (step === 3) {
                const loadingEl = document.getElementById('step3-loading');
                const optionsEl = document.getElementById('step3-options');
                if (loadingEl) loadingEl.classList.remove('hidden');
                if (optionsEl) {
                    optionsEl.classList.add('hidden');
                    optionsEl.innerHTML = '';
                }

                const service = document.querySelector('[name="service"]:checked')?.value || 'Room';
                const checkIn = document.getElementById('wiz-check-in')?.value || '';
                const checkOut = document.getElementById('wiz-check-out')?.value || '';
                const guests = document.getElementById('wiz-guests')?.value || 2;

                fetch('api/public.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ action: 'get_availability', service, check_in: checkIn, check_out: checkOut, guests })
                })
                .then(r => r.json())
                .then(json => {
                    if (loadingEl) loadingEl.classList.add('hidden');
                    if (optionsEl) optionsEl.classList.remove('hidden');
                    
                    if (json.success && json.options && json.options.length > 0) {
                        let html = '';
                        json.options.forEach((opt, idx) => {
                            const checked = idx === 0 ? 'checked' : '';
                            html += `
                            <label class="flex items-center justify-between p-4 border-2 border-gray-100 rounded-2xl cursor-pointer hover:border-gold transition-colors">
                                <div class="flex items-center gap-4">
                                    <input type="radio" name="target_option" value="${opt.id}" data-total="${opt.total_price}" class="w-5 h-5 text-gold focus:ring-gold" ${checked}>
                                    <div>
                                        <span class="font-display font-bold text-base block text-navy">${opt.name}</span>
                                        <span class="text-xs text-gray-500">${opt.description || ''}</span>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <span class="font-ui font-bold text-gold text-lg block">KSh ${Number(opt.total_price).toLocaleString()}</span>
                                    <span class="text-[10px] uppercase font-brand text-gray-400">Total Rate</span>
                                </div>
                            </label>`;
                        });
                        optionsEl.innerHTML = html;
                        const nextBtn = document.getElementById('wiz-btn-next');
                        if (nextBtn) nextBtn.style.display = 'block';
                        const backBtn = document.getElementById('wiz-btn-back');
                        if (backBtn) backBtn.style.display = 'block';
                    } else {
                        if (optionsEl) {
                            optionsEl.innerHTML = '<div class="text-center py-8 text-amber-600 font-ui font-semibold"><span class="material-symbols-outlined text-4xl block mb-2">event_busy</span>No availability found for this selection. Please adjust dates or party size.</div>';
                        }
                        const backBtn = document.getElementById('wiz-btn-back');
                        if (backBtn) backBtn.style.display = 'block';
                    }
                })
                .catch(() => {
                    if (loadingEl) loadingEl.classList.add('hidden');
                    if (optionsEl) {
                        optionsEl.classList.remove('hidden');
                        optionsEl.innerHTML = '<div class="text-center text-red-500 py-6 font-semibold">Network error fetching availability. Please try again.</div>';
                    }
                    const backBtn = document.getElementById('wiz-btn-back');
                    if (backBtn) backBtn.style.display = 'block';
                });
            }
        }

        function wizardGoNext() {
            if (currentStep === totalSteps) {
                const service = document.querySelector('[name="service"]:checked')?.value || 'Room';
                const checkIn = document.getElementById('wiz-check-in')?.value || '';
                const checkOut = document.getElementById('wiz-check-out')?.value || '';
                const guests = document.getElementById('wiz-guests')?.value || 1;
                const firstName = document.getElementById('wiz-first-name')?.value.trim() || '';
                const lastName = document.getElementById('wiz-last-name')?.value.trim() || '';
                const email = document.getElementById('wiz-email')?.value.trim() || '';
                const phone = document.getElementById('wiz-phone')?.value.trim() || '';

                if (!firstName || !email || !phone) {
                    Swal.fire({ icon: 'warning', title: 'Details Required', text: 'Please provide your name, email, and telephone number.', confirmButtonColor: '#0B132B' });
                    return;
                }

                const targetOption = document.querySelector('[name="target_option"]:checked');
                const target_id = targetOption ? targetOption.value : null;
                const total = targetOption ? parseFloat(targetOption.getAttribute('data-total')) : 0;

                const nextBtn = document.getElementById('wiz-btn-next');
                nextBtn.innerHTML = '<span class="material-symbols-outlined animate-spin text-sm">sync</span> Finalizing...';
                nextBtn.disabled = true;

                fetch('api/public.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        action: 'book', service, target_id, total, check_in: checkIn, check_out: checkOut,
                        guests, first_name: firstName, last_name: lastName, email, phone
                    })
                })
                .then(r => r.json())
                .then(json => {
                    if (json.success) {
                        closeBookingWizard();
                        Swal.fire({
                            icon: 'success',
                            title: 'Reservation Confirmed!',
                            html: `<p style="color:#6b7280">Thank you, <strong>${firstName}</strong>! Your luxury reservation is confirmed.</p>
                                   <div style="margin-top:14px;background:#f8fafc;border:1px solid #e2e8f0;border-radius:14px;padding:14px;text-align:left;font-size:14px">
                                     <p><strong>Booking Ref:</strong> <span style="color:#0B132B;font-weight:700">${json.ref}</span></p>
                                     <p><strong>Service:</strong> ${service}</p>
                                     ${json.total > 0 ? `<p><strong>Amount:</strong> KSh ${Number(json.total).toLocaleString()}</p>` : ''}
                                   </div>
                                   <p style="font-size:12px;color:#9ca3af;margin-top:12px">An electronic confirmation has been dispatched to ${email}</p>`,
                            showCancelButton: true,
                            confirmButtonColor: '#0B132B',
                            confirmButtonText: 'Download Reservation Folio',
                            cancelButtonText: 'Close',
                            customClass: { popup: 'font-ui' }
                        }).then((result) => {
                            if (result.isConfirmed && json.booking_id) {
                                window.open(`modules/print_receipt.php?type=booking&id=${json.booking_id}`, '_blank');
                            }
                        });
                    } else {
                        Swal.fire({ icon: 'error', title: 'Booking Issue', text: json.message, confirmButtonColor: '#0B132B' });
                    }
                })
                .catch(() => {
                    Swal.fire({ icon: 'error', title: 'Connection Error', text: 'Unable to reach the server. Please try again.', confirmButtonColor: '#0B132B' });
                })
                .finally(() => {
                    nextBtn.innerHTML = 'Confirm Reservation';
                    nextBtn.disabled = false;
                });

            } else {
                showStep(currentStep + 1);
            }
        }

        function wizardGoBack() {
            if (currentStep > 1) showStep(currentStep - 1);
        }

        // URL Parameter Trigger
        if (new URLSearchParams(window.location.search).get('book') === 'true') {
            setTimeout(() => { openBookingWizard(); }, 400);
        }
    </script>
</body>
</html>
