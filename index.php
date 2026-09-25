<?php
require_once 'includes/config.php';

// Fetch Live Stats
$total_rooms = $pdo->query("SELECT COUNT(*) FROM rooms")->fetchColumn() ?: 0;
$occupied_rooms = $pdo->query("SELECT COUNT(*) FROM rooms WHERE status = 'Occupied'")->fetchColumn() ?: 0;
$available_rooms = max(0, $total_rooms - $occupied_rooms);

$total_halls = $pdo->query("SELECT COUNT(*) FROM halls")->fetchColumn() ?: 0;
$booked_halls = $pdo->query("SELECT COUNT(DISTINCT hall_id) FROM hall_bookings WHERE event_date = CURDATE() AND status != 'Cancelled'")->fetchColumn() ?: 0;
$available_halls = max(0, $total_halls - $booked_halls);

$pool_capacity = 100;
$pool_visitors_today = $pdo->query("SELECT COALESCE(SUM(number_of_guests),0) FROM pool_passes WHERE visit_date = CURDATE()")->fetchColumn();
$available_pool_slots = max(0, $pool_capacity - $pool_visitors_today);

$total_tables = 20;
$occupied_tables = $pdo->query("SELECT COUNT(*) FROM restaurant_orders WHERE DATE(created_at) = CURDATE() AND status != 'Served'")->fetchColumn() ?: 0;
$available_tables = max(0, $total_tables - $occupied_tables);

// Fetch Featured Data
$featured_rooms = $pdo->query("SELECT * FROM rooms LIMIT 3")->fetchAll();
$halls = $pdo->query("SELECT * FROM halls LIMIT 3")->fetchAll();
try {
    $restaurant_items = $pdo->query("SELECT * FROM restaurant_items WHERE status = 'Available' LIMIT 8")->fetchAll();
} catch (Exception $e) {
    $restaurant_items = [];
}

// Fallback High-Res Unsplash Images for Luxury Feel
$room_images = [
    'https://images.unsplash.com/photo-1590490360182-c33d57733427?q=80&w=1974&auto=format&fit=crop',
    'https://images.unsplash.com/photo-1611892440504-42a792e24d32?q=80&w=2070&auto=format&fit=crop',
    'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?q=80&w=2070&auto=format&fit=crop'
];
$hall_images = [
    'https://images.unsplash.com/photo-1519167758481-83f550bb49b3?q=80&w=2098&auto=format&fit=crop',
    'https://images.unsplash.com/photo-1497366216548-37526070297c?q=80&w=2069&auto=format&fit=crop',
    'https://images.unsplash.com/photo-1530103862676-de8892795cfa?q=80&w=2070&auto=format&fit=crop'
];
?>
<!DOCTYPE html>
<html lang="en" class="scroll-smooth overflow-x-hidden w-full">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>SkopeStay | Smart Hospitality, Seamless Bookings</title>
    
    <!-- Fonts: Poppins, Inter, Montserrat -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Montserrat:wght@500;600;700;800&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Material Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    
    <!-- AOS CSS -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    
    <!-- Swiper CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css" />
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        navy: '#0F172A',
                        gold: '#F59E0B',
                        lightgray: '#F8FAFC',
                        surface: 'rgba(255, 255, 255, 0.05)',
                        'surface-dark': 'rgba(15, 23, 42, 0.8)'
                    },
                    fontFamily: {
                        poppins: ['Poppins', 'sans-serif'],
                        inter: ['Inter', 'sans-serif'],
                        montserrat: ['Montserrat', 'sans-serif']
                    },
                    animation: {
                        'slow-zoom': 'slowZoom 20s ease-in-out infinite alternate',
                        'ripple': 'ripple 4s linear infinite',
                        'float': 'float 4s ease-in-out infinite',
                        'slide-in': 'slideIn 0.3s ease-out forwards'
                    },
                    keyframes: {
                        slowZoom: {
                            '0%': { transform: 'scale(1)' },
                            '100%': { transform: 'scale(1.15)' }
                        },
                        ripple: {
                            '0%': { transform: 'scale(0.8)', opacity: '0.5' },
                            '100%': { transform: 'scale(2.5)', opacity: '0' }
                        },
                        float: {
                            '0%, 100%': { transform: 'translateY(0)' },
                            '50%': { transform: 'translateY(-15px)' }
                        },
                        slideIn: {
                            '0%': { transform: 'translateX(-100%)' },
                            '100%': { transform: 'translateX(0)' }
                        }
                    }
                }
            }
        }
    </script>
    <style>
        html, body { 
            max-width: 100%; 
            overflow-x: hidden; 
        }
        body { font-family: 'Inter', sans-serif; background-color: #F8FAFC; }
        h1, h2, h3, h4, h5, h6 { font-family: 'Poppins', sans-serif; }
        .font-accent { font-family: 'Montserrat', sans-serif; }
        
        /* Premium Glassmorphism */
        .glass {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
        .glass-dark {
            background: rgba(15, 23, 42, 0.85);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        /* 3D Hover Lift */
        .hover-lift { transition: transform 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275), box-shadow 0.4s ease; }
        .hover-lift:hover { transform: translateY(-10px); box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); }
        
        /* Image Zoom on Hover */
        .img-zoom-container { overflow: hidden; }
        .img-zoom-container img { transition: transform 0.8s ease; }
        .img-zoom-container:hover img { transform: scale(1.1); }
        
        /* Hide scrollbar */
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
        
        /* Skeleton Loader */
        .skeleton {
            background: linear-gradient(90deg, #e2e8f0 25%, #f1f5f9 50%, #e2e8f0 75%);
            background-size: 200% 100%;
            animation: skeletonLoading 1.5s infinite;
        }
        @keyframes skeletonLoading {
            0% { background-position: 200% 0; }
            100% { background-position: -200% 0; }
        }

        /* Mega Menu */
        .mega-menu { display: none; opacity: 0; transition: opacity 0.3s ease; }
        .nav-item:hover .mega-menu { display: block; opacity: 1; animation: fadeIn 0.3s forwards; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        
        /* Mobile Nav Override */
        #mobile-menu { transform: translateX(-100%); transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1); }
        #mobile-menu.open { transform: translateX(0); }
        #mobile-overlay { opacity: 0; pointer-events: none; transition: opacity 0.3s ease; }
        #mobile-overlay.open { opacity: 1; pointer-events: auto; }
        
        /* Bottom Action Bar spacing */
        body { padding-bottom: 70px; }
        @media(min-width: 768px) { body { padding-bottom: 0; } }

        /* Pool Ripple Overlay */
        .pool-overlay {
            background: radial-gradient(circle at center, transparent 30%, rgba(15,23,42,0.8) 100%);
        }
    </style>
</head>
<body class="text-navy relative">

    <!-- Desktop Navigation -->
    <header id="main-nav" class="fixed top-0 w-full z-[100] transition-all duration-300 py-4">
        <div class="max-w-7xl mx-auto px-6">
            <div class="glass rounded-2xl px-6 py-3 flex justify-between items-center shadow-lg transition-all duration-300">
                <!-- Logo -->
                <a href="index.php" class="flex items-center gap-3">
                    <img src="assets/images/skopestay logo.png" alt="SkopeStay Logo" class="h-10 w-auto" onerror="this.outerHTML='<span class=\'material-symbols-outlined text-gold text-4xl\'>apartment</span>'">
                    <span class="font-poppins font-bold text-xl text-navy tracking-tight menu-text">SkopeStay</span>
                </a>

                <!-- Desktop Links (Hidden on mobile) -->
                <nav class="hidden lg:flex items-center gap-8 font-inter font-medium text-sm text-navy menu-text">
                    <a href="index.php" class="hover:text-gold transition-colors">Home</a>
                    
                    <!-- Rooms Mega Menu -->
                    <div class="nav-item relative py-4">
                        <a href="#rooms" class="hover:text-gold transition-colors flex items-center gap-1">Accommodations <span class="material-symbols-outlined text-[16px]">expand_more</span></a>
                        <div class="mega-menu absolute top-full left-1/2 -translate-x-1/2 w-[600px] glass p-6 rounded-2xl shadow-2xl border border-white/50 text-navy">
                            <div class="grid grid-cols-2 gap-6">
                                <div>
                                    <h4 class="font-poppins font-bold text-gold mb-4 border-b border-gray-200 pb-2">Suites & Rooms</h4>
                                    <ul class="space-y-3">
                                        <li><a href="#rooms" class="hover:text-gold flex items-center gap-2"><span class="material-symbols-outlined text-[18px]">king_bed</span> Executive Suite</a></li>
                                        <li><a href="#rooms" class="hover:text-gold flex items-center gap-2"><span class="material-symbols-outlined text-[18px]">bed</span> Deluxe Room</a></li>
                                        <li><a href="#rooms" class="hover:text-gold flex items-center gap-2"><span class="material-symbols-outlined text-[18px]">single_bed</span> Standard Twin</a></li>
                                    </ul>
                                </div>
                                <div class="relative rounded-xl overflow-hidden img-zoom-container h-40">
                                    <img src="<?= $room_images[0] ?>" class="w-full h-full object-cover" alt="Featured Room">
                                    <div class="absolute inset-0 bg-gradient-to-t from-navy to-transparent flex items-end p-4">
                                        <span class="text-white font-bold">Book Direct & Save 15%</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Venues Mega Menu -->
                    <div class="nav-item relative py-4">
                        <a href="#halls" class="hover:text-gold transition-colors flex items-center gap-1">Venues & Events <span class="material-symbols-outlined text-[16px]">expand_more</span></a>
                        <div class="mega-menu absolute top-full left-1/2 -translate-x-1/2 w-[400px] glass p-6 rounded-2xl shadow-2xl border border-white/50 text-navy">
                            <h4 class="font-poppins font-bold text-gold mb-4 border-b border-gray-200 pb-2">Event Spaces</h4>
                            <ul class="space-y-3">
                                <li><a href="#halls" class="hover:text-gold flex items-center gap-2"><span class="material-symbols-outlined text-[18px]">celebration</span> Grand Ballroom</a></li>
                                <li><a href="#halls" class="hover:text-gold flex items-center gap-2"><span class="material-symbols-outlined text-[18px]">meeting_room</span> Corporate Boardroom</a></li>
                                <li><a href="#halls" class="hover:text-gold flex items-center gap-2"><span class="material-symbols-outlined text-[18px]">park</span> Outdoor Garden</a></li>
                            </ul>
                        </div>
                    </div>

                    <a href="#pool" class="hover:text-gold transition-colors">Pool & Spa</a>
                    <a href="#restaurant" class="hover:text-gold transition-colors">Dining</a>
                    <a href="#contact" class="hover:text-gold transition-colors">Contact</a>
                </nav>

                <!-- Action Buttons -->
                <div class="hidden lg:flex items-center gap-4">
                    <a href="auth/login.php" class="text-navy font-semibold hover:text-gold transition-colors text-sm menu-text">Guest Portal</a>
                    <button onclick="openBookingWizard()" class="bg-navy text-white px-6 py-2.5 rounded-xl font-poppins font-semibold hover:bg-gold transition-colors shadow-lg hover:shadow-gold/50 flex items-center gap-2">
                        <span class="material-symbols-outlined text-[18px]">calendar_month</span> Book Now
                    </button>
                </div>

                <!-- Mobile Hamburger -->
                <button onclick="toggleMobileMenu()" class="lg:hidden text-navy hover:text-gold transition-colors menu-text">
                    <span class="material-symbols-outlined text-3xl">menu</span>
                </button>
            </div>
        </div>
    </header>

    <!-- Mobile Slide-in Menu & Overlay -->
    <div id="mobile-overlay" onclick="toggleMobileMenu()" class="fixed inset-0 bg-navy/60 backdrop-blur-sm z-[110]"></div>
    <div id="mobile-menu" class="fixed top-0 left-0 h-full w-[300px] bg-white z-[120] shadow-2xl overflow-y-auto flex flex-col">
        <div class="p-6 border-b border-gray-100 flex justify-between items-center">
            <img src="assets/images/skopestay logo.png" alt="Logo" class="h-8 w-auto" onerror="this.outerHTML='<span class=\'font-poppins font-bold text-xl text-navy\'>SkopeStay</span>'">
            <button onclick="toggleMobileMenu()" class="text-gray-500 hover:text-navy">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>
        <div class="flex-1 p-6 flex flex-col gap-6 font-poppins text-lg text-navy">
            <a href="index.php" onclick="toggleMobileMenu()" class="hover:text-gold flex items-center gap-3"><span class="material-symbols-outlined">home</span> Home</a>
            <a href="#rooms" onclick="toggleMobileMenu()" class="hover:text-gold flex items-center gap-3"><span class="material-symbols-outlined">king_bed</span> Accommodations</a>
            <a href="#halls" onclick="toggleMobileMenu()" class="hover:text-gold flex items-center gap-3"><span class="material-symbols-outlined">event_seat</span> Venues</a>
            <a href="#pool" onclick="toggleMobileMenu()" class="hover:text-gold flex items-center gap-3"><span class="material-symbols-outlined">pool</span> Pool & Spa</a>
            <a href="#restaurant" onclick="toggleMobileMenu()" class="hover:text-gold flex items-center gap-3"><span class="material-symbols-outlined">restaurant</span> Dining</a>
            <a href="#gallery" onclick="toggleMobileMenu()" class="hover:text-gold flex items-center gap-3"><span class="material-symbols-outlined">photo_library</span> Gallery</a>
            <a href="#contact" onclick="toggleMobileMenu()" class="hover:text-gold flex items-center gap-3"><span class="material-symbols-outlined">call</span> Contact</a>
        </div>
        <div class="p-6 border-t border-gray-100 bg-lightgray">
            <a href="auth/login.php" class="block w-full text-center py-3 mb-3 border-2 border-navy text-navy rounded-xl font-bold hover:bg-navy hover:text-white transition-colors">Login / Register</a>
            <button onclick="toggleMobileMenu(); openBookingWizard()" class="w-full bg-gold text-white py-3 rounded-xl font-bold shadow-lg shadow-gold/30 hover:bg-yellow-600 transition-colors">Book Now</button>
        </div>
    </div>

    <!-- Cinematic Hero Section -->
    <section class="relative h-[100vh] w-full overflow-hidden flex items-center justify-center bg-navy">
        <!-- Video/Image Background -->
        <div class="absolute inset-0 z-0">
            <img src="https://images.unsplash.com/photo-1542314831-c53cd3816002?q=80&w=2070&auto=format&fit=crop" class="w-full h-full object-cover animate-slow-zoom opacity-80" alt="Luxury Resort">
            <div class="absolute inset-0 bg-gradient-to-t from-navy via-navy/50 to-transparent"></div>
        </div>

        <!-- Content -->
        <div class="relative z-10 text-center px-6 max-w-5xl mx-auto mt-20 pb-24" data-aos="fade-up" data-aos-duration="1500">
            <span class="font-montserrat text-gold tracking-[0.2em] text-sm md:text-base font-bold uppercase mb-4 block">Experience True Elegance</span>
            <h1 class="font-poppins text-5xl md:text-7xl lg:text-8xl font-bold text-white leading-tight mb-6 drop-shadow-2xl">
                Smart Hospitality,<br><span class="text-transparent bg-clip-text bg-gradient-to-r from-gold to-yellow-300">Seamless Bookings.</span>
            </h1>
            <p class="font-inter text-gray-300 text-lg md:text-xl max-w-2xl mx-auto mb-10">
                Redefining your stay with AI-driven concierge services, precision room management, and world-class culinary experiences.
            </p>
            
            <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                <button onclick="openBookingWizard()" class="w-full sm:w-auto bg-gold text-white px-8 py-4 rounded-full font-bold text-lg hover:scale-105 transition-transform shadow-[0_0_20px_rgba(245,158,11,0.5)] flex items-center justify-center gap-2">
                    <span class="material-symbols-outlined">vpn_key</span> Check Availability
                </button>
                <a href="#rooms" class="w-full sm:w-auto glass text-white px-8 py-4 rounded-full font-bold text-lg hover:bg-white hover:text-navy transition-colors flex items-center justify-center gap-2">
                    Explore Property <span class="material-symbols-outlined">arrow_downward</span>
                </a>
            </div>
        </div>

        <!-- Floating Live Stats Badge -->
        <div class="absolute bottom-10 lg:bottom-20 right-6 lg:right-20 glass-dark p-4 rounded-2xl flex items-center gap-4 animate-float hidden md:flex" data-aos="fade-left" data-aos-delay="500">
            <div class="relative flex h-4 w-4">
              <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
              <span class="relative inline-flex rounded-full h-4 w-4 bg-green-500"></span>
            </div>
            <div>
                <span class="block text-xs text-gray-400 font-montserrat tracking-widest uppercase">Live Status</span>
                <span class="block text-white font-poppins font-bold"><?= $available_rooms ?> Rooms Available Today</span>
            </div>
        </div>
    </section>

    <!-- Quick Search Widget (Desktop embedded, Mobile hidden - handled by wizard) -->
    <div class="relative z-20 max-w-6xl mx-auto -mt-16 px-6 hidden lg:block" data-aos="fade-up" data-aos-delay="200">
        <div class="glass-dark rounded-3xl p-4 shadow-2xl border border-white/10 flex items-center justify-between">
            <div class="flex items-center gap-6 px-6 w-full">
                <div class="flex-1">
                    <label class="text-xs text-gray-400 font-montserrat uppercase tracking-wider mb-1 block">Check In</label>
                    <input type="date" class="w-full bg-transparent text-white font-bold border-none p-0 focus:ring-0 cursor-pointer">
                </div>
                <div class="w-px h-10 bg-white/20"></div>
                <div class="flex-1">
                    <label class="text-xs text-gray-400 font-montserrat uppercase tracking-wider mb-1 block">Check Out</label>
                    <input type="date" class="w-full bg-transparent text-white font-bold border-none p-0 focus:ring-0 cursor-pointer">
                </div>
                <div class="w-px h-10 bg-white/20"></div>
                <div class="flex-1">
                    <label class="text-xs text-gray-400 font-montserrat uppercase tracking-wider mb-1 block">Guests</label>
                    <select class="w-full bg-transparent text-white font-bold border-none p-0 focus:ring-0 cursor-pointer appearance-none">
                        <option class="text-navy">1 Guest</option>
                        <option class="text-navy">2 Guests</option>
                        <option class="text-navy">Family (4)</option>
                    </select>
                </div>
                <div class="w-px h-10 bg-white/20"></div>
                <div class="flex-1">
                    <label class="text-xs text-gray-400 font-montserrat uppercase tracking-wider mb-1 block">Service</label>
                    <select class="w-full bg-transparent text-white font-bold border-none p-0 focus:ring-0 cursor-pointer appearance-none">
                        <option class="text-navy">Rooms & Suites</option>
                        <option class="text-navy">Venues</option>
                        <option class="text-navy">Restaurant</option>
                    </select>
                </div>
            </div>
            <button onclick="openBookingWizard()" class="bg-gold text-white h-16 px-8 rounded-2xl font-bold hover:bg-yellow-500 transition-colors shadow-lg flex-shrink-0">
                Search
            </button>
        </div>
    </div>

    <main class="max-w-7xl mx-auto px-6 py-12 space-y-16">
        
        <!-- Live Dashboard Data Section -->
        <section data-aos="fade-up">
            <div class="text-center mb-12">
                <span class="font-montserrat text-gold tracking-widest text-sm font-bold uppercase block mb-2">Real-Time Metrics</span>
                <h2 class="font-poppins text-3xl md:text-4xl font-bold text-navy">Facility Availability</h2>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-8">
                <div class="bg-white rounded-3xl p-6 shadow-xl hover-lift text-center border border-gray-100">
                    <div class="w-16 h-16 mx-auto bg-navy/5 rounded-full flex items-center justify-center text-navy mb-4">
                        <span class="material-symbols-outlined text-3xl">king_bed</span>
                    </div>
                    <span class="text-4xl font-bold text-navy counter block mb-1" data-target="<?= $available_rooms ?>">0</span>
                    <span class="text-sm text-gray-500 font-montserrat uppercase tracking-wider">Rooms Free</span>
                </div>
                <div class="bg-white rounded-3xl p-6 shadow-xl hover-lift text-center border border-gray-100">
                    <div class="w-16 h-16 mx-auto bg-gold/10 rounded-full flex items-center justify-center text-gold mb-4">
                        <span class="material-symbols-outlined text-3xl">event_seat</span>
                    </div>
                    <span class="text-4xl font-bold text-gold counter block mb-1" data-target="<?= $available_halls ?>">0</span>
                    <span class="text-sm text-gray-500 font-montserrat uppercase tracking-wider">Halls Open</span>
                </div>
                <div class="bg-white rounded-3xl p-6 shadow-xl hover-lift text-center border border-gray-100">
                    <div class="w-16 h-16 mx-auto bg-blue-500/10 rounded-full flex items-center justify-center text-blue-500 mb-4">
                        <span class="material-symbols-outlined text-3xl">pool</span>
                    </div>
                    <span class="text-4xl font-bold text-blue-500 counter block mb-1" data-target="<?= $available_pool_slots ?>">0</span>
                    <span class="text-sm text-gray-500 font-montserrat uppercase tracking-wider">Pool Slots</span>
                </div>
                <div class="bg-white rounded-3xl p-6 shadow-xl hover-lift text-center border border-gray-100">
                    <div class="w-16 h-16 mx-auto bg-red-500/10 rounded-full flex items-center justify-center text-red-500 mb-4">
                        <span class="material-symbols-outlined text-3xl">restaurant</span>
                    </div>
                    <span class="text-4xl font-bold text-red-500 counter block mb-1" data-target="<?= $available_tables ?>">0</span>
                    <span class="text-sm text-gray-500 font-montserrat uppercase tracking-wider">Tables Free</span>
                </div>
            </div>
        </section>

        <!-- Premium Accommodations -->
        <section id="rooms">
            <div class="flex flex-col md:flex-row justify-between items-end mb-12" data-aos="fade-right">
                <div>
                    <span class="font-montserrat text-gold tracking-widest text-sm font-bold uppercase block mb-2">Rest & Rejuvenate</span>
                    <h2 class="font-poppins text-4xl md:text-5xl font-bold text-navy">Luxury Accommodations</h2>
                </div>
                <a href="#rooms" class="hidden md:flex items-center gap-2 font-bold text-navy hover:text-gold transition-colors">
                    View All Suites <span class="material-symbols-outlined">arrow_forward</span>
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <?php foreach ($featured_rooms as $index => $room): ?>
                <div class="bg-white rounded-[2rem] overflow-hidden shadow-lg hover-lift border border-gray-100 flex flex-col group" data-aos="fade-up" data-aos-delay="<?= $index * 150 ?>">
                    <div class="relative h-72 img-zoom-container">
                        <img src="<?= $room_images[$index % count($room_images)] ?>" class="w-full h-full object-cover" loading="lazy" alt="<?= htmlspecialchars($room['type']) ?>">
                        <div class="absolute top-4 right-4 bg-white/90 backdrop-blur px-4 py-1 rounded-full text-sm font-bold text-navy shadow">
                            <span class="material-symbols-outlined text-[14px] text-gold align-middle">star</span> 4.9
                        </div>
                        <?php if($index === 0): ?>
                        <div class="absolute top-4 left-4 bg-gold text-white px-3 py-1 rounded-full text-xs font-bold font-montserrat uppercase tracking-wider shadow">
                            Signature
                        </div>
                        <?php endif; ?>
                    </div>
                    <div class="p-8 flex flex-col flex-1">
                        <h3 class="text-2xl font-poppins font-bold text-navy mb-2"><?= htmlspecialchars($room['type']) ?></h3>
                        <div class="flex items-center gap-4 text-gray-500 text-sm mb-6">
                            <span class="flex items-center gap-1"><span class="material-symbols-outlined text-[18px]">person</span> 2 Guests</span>
                            <span class="w-1 h-1 bg-gray-300 rounded-full"></span>
                            <span class="flex items-center gap-1"><span class="material-symbols-outlined text-[18px]">aspect_ratio</span> 45m²</span>
                        </div>
                        <div class="flex gap-3 mb-8">
                            <div class="w-10 h-10 rounded-full bg-gray-50 flex items-center justify-center text-gray-600 border border-gray-100" title="Free WiFi"><span class="material-symbols-outlined text-[20px]">wifi</span></div>
                            <div class="w-10 h-10 rounded-full bg-gray-50 flex items-center justify-center text-gray-600 border border-gray-100" title="AC"><span class="material-symbols-outlined text-[20px]">ac_unit</span></div>
                            <div class="w-10 h-10 rounded-full bg-gray-50 flex items-center justify-center text-gray-600 border border-gray-100" title="Room Service"><span class="material-symbols-outlined text-[20px]">room_service</span></div>
                        </div>
                        <div class="mt-auto border-t border-gray-100 pt-6 flex justify-between items-center">
                            <div>
                                <span class="text-sm text-gray-500 block">Starting at</span>
                                <span class="text-2xl font-bold text-navy">KSh <?= number_format($room['price'], 0) ?></span>
                            </div>
                            <button onclick="openBookingWizard('Room')" class="w-12 h-12 rounded-full bg-navy text-white flex items-center justify-center group-hover:bg-gold transition-colors shadow-md">
                                <span class="material-symbols-outlined">arrow_forward</span>
                            </button>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
                <?php if(empty($featured_rooms)): ?>
                    <div class="col-span-full py-20 text-center text-gray-500">No accommodations available at the moment.</div>
                <?php endif; ?>
            </div>
            <a href="#rooms" class="md:hidden flex justify-center items-center gap-2 font-bold text-navy mt-8">
                View All Suites <span class="material-symbols-outlined">arrow_forward</span>
            </a>
        </section>

        <!-- Venues Section (Bento Grid) -->
        <section id="halls">
            <div class="text-center mb-12" data-aos="fade-up">
                <span class="font-montserrat text-gold tracking-widest text-sm font-bold uppercase block mb-2">Events & Conferences</span>
                <h2 class="font-poppins text-4xl md:text-5xl font-bold text-navy">World-Class Venues</h2>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                <?php if(isset($halls[0])): ?>
                <!-- Main Venue -->
                <div class="lg:col-span-8 rounded-[2rem] overflow-hidden relative group h-[500px] hover-lift" data-aos="fade-right">
                    <img src="<?= $hall_images[0] ?>" class="w-full h-full object-cover transition-transform duration-1000 group-hover:scale-105" loading="lazy" alt="<?= htmlspecialchars($halls[0]['name']) ?>">
                    <div class="absolute inset-0 bg-gradient-to-t from-navy/90 via-navy/40 to-transparent"></div>
                    <div class="absolute bottom-0 left-0 p-8 md:p-12 w-full">
                        <div class="bg-white/20 backdrop-blur-md border border-white/30 text-white px-4 py-1.5 rounded-full text-xs font-bold font-montserrat tracking-widest uppercase inline-block mb-4">Flagship Venue</div>
                        <h3 class="text-4xl md:text-5xl font-bold text-white mb-4 font-poppins"><?= htmlspecialchars($halls[0]['name']) ?></h3>
                        <p class="text-gray-200 text-lg mb-8 max-w-xl hidden md:block"><?= htmlspecialchars($halls[0]['description'] ?? 'Host your grand events in our most prestigious space.') ?></p>
                        
                        <div class="flex flex-wrap items-center gap-6 text-white">
                            <div class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-gold">groups</span>
                                <span class="font-bold text-xl"><?= htmlspecialchars($halls[0]['capacity']) ?></span>
                            </div>
                            <div class="w-px h-6 bg-white/30 hidden sm:block"></div>
                            <div class="flex items-center gap-2">
                                <span class="material-symbols-outlined text-gold">payments</span>
                                <span class="font-bold text-xl">KSh <?= number_format($halls[0]['daily_rate'], 0) ?>/day</span>
                            </div>
                            <button onclick="openBookingWizard('Hall')" class="ml-auto bg-white text-navy px-6 py-3 rounded-xl font-bold hover:bg-gold hover:text-white transition-colors shadow-lg">Reserve</button>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <div class="lg:col-span-4 grid grid-rows-2 gap-6">
                    <?php if(isset($halls[1])): ?>
                    <div class="bg-navy rounded-[2rem] p-8 text-white relative overflow-hidden group hover-lift" data-aos="fade-left" data-aos-delay="100">
                        <div class="absolute -right-10 -top-10 w-40 h-40 bg-gold/20 rounded-full blur-3xl"></div>
                        <span class="text-gold font-montserrat text-xs tracking-widest font-bold uppercase block mb-3">Corporate</span>
                        <h3 class="text-2xl font-poppins font-bold mb-2"><?= htmlspecialchars($halls[1]['name']) ?></h3>
                        <p class="text-gray-400 text-sm mb-6"><?= htmlspecialchars($halls[1]['description'] ?? 'Executive meeting space.') ?></p>
                        <div class="mt-auto flex justify-between items-center">
                            <span class="font-bold text-xl">KSh <?= number_format($halls[1]['daily_rate'], 0) ?></span>
                            <button onclick="openBookingWizard('Hall')" class="w-10 h-10 rounded-full border border-white/20 flex items-center justify-center hover:bg-gold hover:border-transparent transition-colors">
                                <span class="material-symbols-outlined">arrow_forward</span>
                            </button>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php if(isset($halls[2])): ?>
                    <div class="rounded-[2rem] overflow-hidden relative group hover-lift" data-aos="fade-left" data-aos-delay="200">
                        <img src="<?= $hall_images[1] ?>" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110" loading="lazy" alt="<?= htmlspecialchars($halls[2]['name']) ?>">
                        <div class="absolute inset-0 bg-navy/50 flex flex-col items-center justify-center text-center p-6 backdrop-blur-[2px] transition-all group-hover:bg-navy/60">
                            <h3 class="text-2xl font-poppins font-bold text-white mb-2"><?= htmlspecialchars($halls[2]['name']) ?></h3>
                            <p class="text-gray-200 text-sm mb-4">Ideal for outdoor receptions</p>
                            <button onclick="openBookingWizard('Hall')" class="glass text-white px-6 py-2 rounded-full font-bold hover:bg-gold transition-colors text-sm">Book Space</button>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </section>

    </main>

    <!-- Swimming Pool (Full Width Parallax) -->
    <section id="pool" class="relative py-16 overflow-hidden">
        <div class="absolute inset-0">
            <img src="https://images.unsplash.com/photo-1576013551627-0cc20b96c2a7?q=80&w=2070&auto=format&fit=crop" class="w-full h-full object-cover" loading="lazy" alt="Infinity Pool">
            <div class="absolute inset-0 pool-overlay"></div>
            <!-- Ripple Effect element -->
            <div class="absolute top-1/2 left-1/2 w-[800px] h-[800px] -mt-[400px] -ml-[400px] rounded-full border border-white/10 animate-ripple"></div>
        </div>
        
        <div class="relative z-10 max-w-7xl mx-auto px-6 grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
            <div data-aos="fade-right">
                <span class="font-montserrat text-gold tracking-widest text-sm font-bold uppercase block mb-2">Wellness & Leisure</span>
                <h2 class="font-poppins text-5xl md:text-6xl font-bold text-white mb-6">The Infinity Escape</h2>
                <p class="text-gray-300 text-lg mb-8 max-w-lg">Experience pure serenity in our temperature-controlled infinity pool offering panoramic skyline views. Full lounge service available.</p>
                
                <div class="flex flex-col sm:flex-row gap-4">
                    <button onclick="openBookingWizard('Pool')" class="bg-gold text-white px-8 py-4 rounded-xl font-bold shadow-[0_0_15px_rgba(245,158,11,0.4)] hover:bg-yellow-500 transition-colors flex items-center justify-center gap-2">
                        <span class="material-symbols-outlined">water</span> Get Pool Pass
                    </button>
                    <div class="glass text-white px-6 py-4 rounded-xl flex items-center gap-3">
                        <span class="material-symbols-outlined text-gold">schedule</span>
                        <div>
                            <span class="block text-xs uppercase tracking-wider opacity-70">Open Daily</span>
                            <span class="font-bold">06:00 AM - 10:00 PM</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="lg:justify-self-end" data-aos="fade-up">
                <div class="glass-dark p-8 rounded-3xl w-full max-w-sm border border-white/20 shadow-2xl relative overflow-hidden">
                    <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-gold to-yellow-200"></div>
                    <div class="flex justify-between items-center mb-6">
                        <h4 class="text-white font-poppins font-bold text-xl">Live Capacity</h4>
                        <div class="flex h-3 w-3 relative">
                          <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                          <span class="relative inline-flex rounded-full h-3 w-3 bg-green-500"></span>
                        </div>
                    </div>
                    <div class="text-6xl font-bold text-white mb-2"><?= min(100, round((($pool_capacity - $available_pool_slots) / $pool_capacity) * 100)) ?>%</div>
                    <p class="text-gray-400 text-sm mb-6">Occupancy right now. Perfect time for a quiet swim.</p>
                    
                    <div class="space-y-3">
                        <div class="flex justify-between items-center pb-3 border-b border-white/10 text-white">
                            <span>Adult Pass</span>
                            <span class="font-bold text-gold">KSh 1,000</span>
                        </div>
                        <div class="flex justify-between items-center text-white">
                            <span>Child Pass</span>
                            <span class="font-bold text-gold">KSh 500</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Fine Dining & Restaurant -->
    <section id="restaurant" class="bg-lightgray py-16">
        <div class="max-w-7xl mx-auto px-6">
            <div class="text-center mb-16" data-aos="fade-up">
                <span class="font-montserrat text-gold tracking-widest text-sm font-bold uppercase block mb-2">Culinary Excellence</span>
                <h2 class="font-poppins text-4xl md:text-5xl font-bold text-navy">Signature Dining</h2>
                
                <div class="flex flex-wrap justify-center gap-3 mt-8">
                    <button class="px-6 py-2 rounded-full bg-navy text-white font-bold text-sm shadow-lg">All Menu</button>
                    <button class="px-6 py-2 rounded-full bg-white text-navy border border-gray-200 hover:bg-gray-50 font-bold text-sm transition-colors">Breakfast</button>
                    <button class="px-6 py-2 rounded-full bg-white text-navy border border-gray-200 hover:bg-gray-50 font-bold text-sm transition-colors">Dinner</button>
                    <button class="px-6 py-2 rounded-full bg-white text-navy border border-gray-200 hover:bg-gray-50 font-bold text-sm transition-colors">Drinks</button>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                <?php foreach ($restaurant_items as $index => $item): ?>
                <div class="bg-white rounded-[2rem] p-4 shadow-lg hover-lift group border border-gray-50" data-aos="fade-up" data-aos-delay="<?= ($index % 4) * 100 ?>">
                    <div class="relative h-48 rounded-2xl overflow-hidden mb-4 bg-gray-100">
                        <?php if(!empty($item['image_url'])): ?>
                            <img src="<?= htmlspecialchars($item['image_url']) ?>" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110" loading="lazy" alt="<?= htmlspecialchars($item['name']) ?>">
                        <?php else: ?>
                            <div class="w-full h-full flex items-center justify-center overflow-hidden" style="background: linear-gradient(135deg,#f0f4ff 0%,#e8f0fe 100%)">
                                <span class="text-6xl select-none group-hover:scale-110 transition-transform duration-300"><?= htmlspecialchars($item['emoji'] ?? '🍽️') ?></span>
                                <?php if(!empty($item['badge'])): ?>
                                <div class="absolute top-2.5 right-2.5">
                                    <span class="bg-navy text-white text-[9px] font-extrabold px-2 py-0.5 rounded-full tracking-widest"><?= htmlspecialchars($item['badge']) ?></span>
                                </div>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                        

                    </div>
                    <div class="px-2 pb-2">
                        <div class="flex justify-between items-start mb-1">
                            <h4 class="font-bold font-poppins text-lg text-navy truncate flex-1" title="<?= htmlspecialchars($item['name']) ?>"><?= htmlspecialchars($item['name']) ?></h4>
                        </div>
                        <p class="text-sm text-gray-500 mb-3 truncate" title="<?= htmlspecialchars($item['description'] ?? '') ?>"><?= htmlspecialchars($item['description'] ?? 'Gourmet selection') ?></p>
                        <div class="flex justify-between items-center">
                            <span class="font-bold text-gold text-lg">KSh <?= number_format($item['price'], 0) ?></span>
                            <span class="text-xs font-montserrat uppercase tracking-wider text-gray-400 bg-gray-100 px-2 py-1 rounded-md"><?= htmlspecialchars($item['category']) ?></span>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
                <?php if(empty($restaurant_items)): ?>
                    <div class="col-span-full py-20 text-center text-gray-500 bg-white rounded-3xl border border-dashed border-gray-300">Digital menu loading soon.</div>
                <?php endif; ?>
            </div>
            
            <div class="mt-12 flex justify-center">
                <button onclick="openBookingWizard('Restaurant')" class="bg-navy text-white px-8 py-4 rounded-xl font-bold flex items-center gap-3 hover:bg-navy/90 transition-colors shadow-xl">
                    <span class="material-symbols-outlined">table_restaurant</span> Reserve a Table
                </button>
            </div>
        </div>
    </section>

    <!-- Testimonials Carousel -->
    <section class="py-16 bg-navy text-white relative overflow-hidden">
        <div class="absolute top-0 right-0 w-96 h-96 bg-gold/10 rounded-full blur-[100px]"></div>
        <div class="max-w-7xl mx-auto px-6 relative z-10">
            <div class="flex flex-col md:flex-row justify-between items-end mb-16">
                <div data-aos="fade-right">
                    <span class="font-montserrat text-gold tracking-widest text-sm font-bold uppercase block mb-2">Guest Voices</span>
                    <h2 class="font-poppins text-4xl md:text-5xl font-bold">Trusted by World Travelers</h2>
                </div>
                <div class="flex gap-4 mt-6 md:mt-0" data-aos="fade-left">
                    <button class="swiper-btn-prev w-12 h-12 rounded-full border border-white/20 flex items-center justify-center hover:bg-gold hover:border-gold transition-colors">
                        <span class="material-symbols-outlined">arrow_back</span>
                    </button>
                    <button class="swiper-btn-next w-12 h-12 rounded-full border border-white/20 flex items-center justify-center hover:bg-gold hover:border-gold transition-colors">
                        <span class="material-symbols-outlined">arrow_forward</span>
                    </button>
                </div>
            </div>

            <!-- Swiper -->
            <div class="swiper testimonial-swiper" data-aos="fade-up">
                <div class="swiper-wrapper">
                    <div class="swiper-slide">
                        <div class="glass-dark p-10 rounded-[2.5rem] border border-white/10 h-full flex flex-col">
                            <span class="material-symbols-outlined text-gold text-5xl mb-6 opacity-50">format_quote</span>
                            <p class="text-xl italic text-gray-300 mb-8 flex-1">"The automated check-in and the quality of the ballroom for our corporate event was unmatched. Precision hospitality at its finest."</p>
                            <div class="flex items-center gap-4">
                                <div class="w-14 h-14 rounded-full bg-gray-600 border-2 border-gold"></div>
                                <div>
                                    <h5 class="font-bold font-poppins text-lg">Peter O.</h5>
                                    <span class="text-sm text-gray-400">Regional Tech Lead</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="swiper-slide">
                        <div class="glass-dark p-10 rounded-[2.5rem] border border-white/10 h-full flex flex-col">
                            <span class="material-symbols-outlined text-gold text-5xl mb-6 opacity-50">format_quote</span>
                            <p class="text-xl italic text-gray-300 mb-8 flex-1">"A wonderful family weekend! The infinity pool is fantastic and the kids loved the dynamic menu options. Highly recommended."</p>
                            <div class="flex items-center gap-4">
                                <div class="w-14 h-14 rounded-full bg-gray-600 border-2 border-gold"></div>
                                <div>
                                    <h5 class="font-bold font-poppins text-lg">Sarah M.</h5>
                                    <span class="text-sm text-gray-400">Architect</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="swiper-slide">
                        <div class="glass-dark p-10 rounded-[2.5rem] border border-white/10 h-full flex flex-col">
                            <span class="material-symbols-outlined text-gold text-5xl mb-6 opacity-50">format_quote</span>
                            <p class="text-xl italic text-gray-300 mb-8 flex-1">"From the AI booking system to the room service, everything was flawless. The premium suite exceeded all my expectations."</p>
                            <div class="flex items-center gap-4">
                                <div class="w-14 h-14 rounded-full bg-gray-600 border-2 border-gold"></div>
                                <div>
                                    <h5 class="font-bold font-poppins text-lg">David K.</h5>
                                    <span class="text-sm text-gray-400">CEO, Startup Inc.</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer id="contact" class="bg-navy text-white pt-24 pb-12 border-t border-white/10">
        <div class="max-w-7xl mx-auto px-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12 mb-16">
                <div class="space-y-6">
                    <img src="assets/images/skopestay logo.png" alt="Logo" class="h-12 w-auto brightness-0 invert" onerror="this.outerHTML='<h3 class=\'font-poppins font-bold text-2xl\'>SkopeStay</h3>'">
                    <p class="text-gray-400 text-sm leading-relaxed max-w-xs">Redefining luxury hospitality with precision engineering and world-class service.</p>
                    <div class="flex gap-4">
                        <a href="https://wa.me/?text=Check%20out%20SkopeStay%20hospitality%20platform" target="_blank" rel="noopener" class="w-10 h-10 rounded-full bg-white/5 flex items-center justify-center hover:bg-gold transition-colors" title="Share"><span class="material-symbols-outlined text-[20px]">share</span></a>
                        <a href="mailto:?subject=SkopeStay%20Hospitality&body=Check%20out%20https://skopestay.com" class="w-10 h-10 rounded-full bg-white/5 flex items-center justify-center hover:bg-gold transition-colors" title="Email us"><span class="material-symbols-outlined text-[20px]">mail</span></a>
                    </div>
                </div>
                
                <div>
                    <h4 class="font-poppins font-bold text-lg mb-6">Discover</h4>
                    <ul class="space-y-3 text-gray-400 text-sm">
                        <li><a href="#rooms" class="hover:text-gold transition-colors">Rooms & Suites</a></li>
                        <li><a href="#halls" class="hover:text-gold transition-colors">Meetings & Events</a></li>
                        <li><a href="#pool" class="hover:text-gold transition-colors">Pool & Wellness</a></li>
                        <li><a href="#restaurant" class="hover:text-gold transition-colors">Fine Dining</a></li>
                    </ul>
                </div>
                
                <div>
                    <h4 class="font-poppins font-bold text-lg mb-6">Corporate</h4>
                    <ul class="space-y-3 text-gray-400 text-sm">
                        <li><a href="about.php" class="hover:text-gold transition-colors">About Us</a></li>
                        <li><a href="careers.php" class="hover:text-gold transition-colors">Careers</a></li>
                        <li><a href="privacy.php" class="hover:text-gold transition-colors">Privacy Policy</a></li>
                        <li><a href="terms.php" class="hover:text-gold transition-colors">Terms of Service</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="font-poppins font-bold text-lg mb-6">Newsletter</h4>
                    <p class="text-gray-400 text-sm mb-4">Exclusive offers in your inbox.</p>
                    <form class="flex" onsubmit="event.preventDefault(); addToast('Subscribed to newsletter!');">
                        <input type="email" placeholder="Email Address" required class="bg-white/5 border border-white/10 rounded-l-xl px-4 py-3 w-full text-white text-sm focus:outline-none focus:border-gold">
                        <button type="submit" class="bg-gold text-white px-4 rounded-r-xl hover:bg-yellow-500 transition-colors">
                            <span class="material-symbols-outlined">send</span>
                        </button>
                    </form>
                </div>
            </div>
            
            <div class="border-t border-white/10 pt-8 flex flex-col md:flex-row justify-between items-center gap-4">
                <p class="text-gray-500 text-sm">© <?= date('Y') ?> SkopeStay International. All Rights Reserved.</p>
                <div class="flex gap-6 text-gray-500 text-sm">
                    <span class="text-white/50 cursor-default">English</span>
                    <span class="text-white/50 cursor-default">KES</span>
                </div>
            </div>
        </div>
    </footer>

    <!-- Floating WhatsApp Button -->
    <a href="https://wa.me/254742380183" target="_blank" class="fixed bottom-8 right-6 z-[90] w-14 h-14 bg-[#25D366] text-white rounded-full flex items-center justify-center shadow-2xl hover:-translate-y-2 transition-transform animate-float hover:scale-110">
        <svg class="w-8 h-8 fill-current" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"></path></svg>
    </a>



    <!-- Multi-Step Booking Wizard Modal -->
    <div id="booking-modal" class="fixed inset-0 z-[200] hidden flex items-center justify-center">
        <div class="absolute inset-0 bg-navy/80 backdrop-blur-md"></div>
        
        <div class="relative w-full max-w-2xl bg-white rounded-3xl shadow-2xl overflow-hidden m-4 flex flex-col max-h-[90vh] animate-slide-in">
            <!-- Header -->
            <div class="bg-navy text-white p-6 flex justify-between items-center">
                <div>
                    <h3 class="font-poppins font-bold text-xl">Smart Reservation</h3>
                    <p class="text-gray-400 text-sm" id="wizard-subtitle">Step 1: Select Service</p>
                </div>
                <button onclick="closeBookingWizard()" class="text-gray-400 hover:text-white">
                    <span class="material-symbols-outlined text-3xl">close</span>
                </button>
            </div>

            <!-- Progress Bar -->
            <div class="w-full bg-gray-100 h-1">
                <div id="wizard-progress" class="bg-gold h-1 w-1/4 transition-all duration-500"></div>
            </div>

            <!-- Body -->
            <div class="p-8 overflow-y-auto flex-1">
                <!-- Step 1: Service -->
                <div id="step-1" class="wizard-step space-y-4">
                    <label class="flex items-center gap-4 p-4 border-2 border-gray-100 rounded-2xl cursor-pointer hover:border-gold transition-colors">
                        <input type="radio" name="service" value="Room" class="w-5 h-5 text-gold focus:ring-gold" checked>
                        <div class="w-12 h-12 bg-navy/5 rounded-xl flex items-center justify-center text-navy"><span class="material-symbols-outlined">king_bed</span></div>
                        <div>
                            <span class="font-bold font-poppins text-lg block">Room & Suite</span>
                            <span class="text-sm text-gray-500">Overnight stay reservations</span>
                        </div>
                    </label>
                    <label class="flex items-center gap-4 p-4 border-2 border-gray-100 rounded-2xl cursor-pointer hover:border-gold transition-colors">
                        <input type="radio" name="service" value="Hall" class="w-5 h-5 text-gold focus:ring-gold">
                        <div class="w-12 h-12 bg-navy/5 rounded-xl flex items-center justify-center text-navy"><span class="material-symbols-outlined">event_seat</span></div>
                        <div>
                            <span class="font-bold font-poppins text-lg block">Hall & Venue</span>
                            <span class="text-sm text-gray-500">Events and conferences</span>
                        </div>
                    </label>
                    <label class="flex items-center gap-4 p-4 border-2 border-gray-100 rounded-2xl cursor-pointer hover:border-gold transition-colors">
                        <input type="radio" name="service" value="Restaurant" class="w-5 h-5 text-gold focus:ring-gold">
                        <div class="w-12 h-12 bg-navy/5 rounded-xl flex items-center justify-center text-navy"><span class="material-symbols-outlined">restaurant</span></div>
                        <div>
                            <span class="font-bold font-poppins text-lg block">Table Reservation</span>
                            <span class="text-sm text-gray-500">Fine dining experience</span>
                        </div>
                    </label>
                </div>

                <!-- Step 2: Date -->
                <div id="step-2" class="wizard-step hidden space-y-6">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-bold text-navy mb-2">Check-in Date</label>
                            <input id="wiz-check-in" type="date" class="w-full border-2 border-gray-100 rounded-xl p-3 focus:border-gold focus:ring-0">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-navy mb-2">Check-out Date</label>
                            <input id="wiz-check-out" type="date" class="w-full border-2 border-gray-100 rounded-xl p-3 focus:border-gold focus:ring-0">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-navy mb-2">Number of Guests</label>
                        <input id="wiz-guests" type="number" min="1" value="1" class="w-full border-2 border-gray-100 rounded-xl p-3 focus:border-gold focus:ring-0">
                    </div>
                </div>

                <!-- Step 3: Options Selection -->
                <div id="step-3" class="wizard-step hidden flex flex-col py-2">
                    <div id="step3-loading" class="flex flex-col items-center justify-center py-10">
                        <div class="relative w-24 h-24 mb-6">
                            <svg class="animate-spin w-full h-full text-gray-200" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span class="material-symbols-outlined absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 text-gold text-3xl">smart_toy</span>
                        </div>
                        <h4 class="font-poppins font-bold text-lg text-navy mb-2">Checking Availability...</h4>
                        <div class="w-64 h-4 rounded-full skeleton mt-4"></div>
                    </div>
                    <div id="step3-options" class="w-full hidden space-y-4">
                        <!-- Dynamic options will be rendered here -->
                    </div>
                </div>

                <!-- Step 4: Guest Details -->
                <div id="step-4" class="wizard-step hidden space-y-4">
                    <div class="bg-green-50 text-green-700 p-4 rounded-xl border border-green-200 mb-6 flex items-center gap-3">
                        <span class="material-symbols-outlined">check_circle</span>
                        <span class="font-bold">Availability Confirmed! Please enter your details.</span>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-bold text-navy mb-2">First Name *</label>
                            <input id="wiz-first-name" type="text" placeholder="e.g. Jane" class="w-full border-2 border-gray-100 rounded-xl p-3 focus:border-gold focus:ring-0">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-navy mb-2">Last Name *</label>
                            <input id="wiz-last-name" type="text" placeholder="e.g. Kamau" class="w-full border-2 border-gray-100 rounded-xl p-3 focus:border-gold focus:ring-0">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-navy mb-2">Email Address *</label>
                        <input id="wiz-email" type="email" placeholder="e.g. jane@example.com" class="w-full border-2 border-gray-100 rounded-xl p-3 focus:border-gold focus:ring-0">
                    </div>
                    <div>
                        <label class="block text-sm font-bold text-navy mb-2">Phone Number *</label>
                        <input id="wiz-phone" type="tel" placeholder="e.g. 0712345678" class="w-full border-2 border-gray-100 rounded-xl p-3 focus:border-gold focus:ring-0">
                    </div>
                </div>

            </div>

            <!-- Footer / Navigation -->
            <div class="p-6 border-t border-gray-100 flex justify-between bg-gray-50">
                <button id="wiz-btn-back" onclick="wizardGoBack()" class="px-6 py-3 font-bold text-gray-500 hover:text-navy hidden">Back</button>
                <div class="flex-1"></div>
                <button id="wiz-btn-next" onclick="wizardGoNext()" class="bg-navy text-white px-8 py-3 rounded-xl font-bold hover:bg-gold transition-colors shadow-lg">Next Step</button>
            </div>
        </div>
    </div>


    <!-- Scripts -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js"></script>
    <script>
        // Initialize AOS
        AOS.init({
            once: true,
            offset: 50,
            duration: 800,
            easing: 'ease-out-cubic',
        });

        // Initialize Swiper
        const swiper = new Swiper('.testimonial-swiper', {
            slidesPerView: 1,
            spaceBetween: 30,
            loop: true,
            autoplay: {
                delay: 5000,
                disableOnInteraction: false,
            },
            breakpoints: {
                768: { slidesPerView: 2 }
            },
            navigation: {
                nextEl: '.swiper-btn-next',
                prevEl: '.swiper-btn-prev',
            }
        });

        // Sticky Nav Effect
        window.addEventListener('scroll', () => {
            const nav = document.getElementById('main-nav');
            const innerContainer = nav.firstElementChild.firstElementChild;
            if(window.scrollY > 50) {
                nav.classList.add('py-2', 'bg-white/95', 'backdrop-blur-md', 'shadow-sm');
                nav.classList.remove('py-4');
                if (innerContainer.classList.contains('glass')) {
                    innerContainer.classList.remove('glass');
                    innerContainer.classList.add('bg-transparent', 'shadow-none', 'border-transparent');
                }
            } else {
                nav.classList.add('py-4');
                nav.classList.remove('py-2', 'bg-white/95', 'backdrop-blur-md', 'shadow-sm');
                innerContainer.classList.add('glass');
                innerContainer.classList.remove('bg-transparent', 'shadow-none', 'border-transparent');
            }
        });

        // Mobile Menu Toggle
        function toggleMobileMenu() {
            document.getElementById('mobile-menu').classList.toggle('open');
            document.getElementById('mobile-overlay').classList.toggle('open');
        }

        // Animated Counters
        const counters = document.querySelectorAll('.counter');
        counters.forEach(counter => {
            const updateCount = () => {
                const target = +counter.getAttribute('data-target');
                const count = +counter.innerText;
                const inc = target / 50;
                if(count < target) {
                    counter.innerText = Math.ceil(count + inc);
                    setTimeout(updateCount, 40);
                } else {
                    counter.innerText = target;
                }
            };
            
            // Observer to start animation when visible
            const observer = new IntersectionObserver((entries) => {
                if(entries[0].isIntersecting) {
                    updateCount();
                    observer.disconnect();
                }
            });
            observer.observe(counter);
        });

        // Toast Notification
        function addToast(message) {
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: message,
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                customClass: { popup: 'font-inter' }
            });
        }

        // Multi-Step Wizard Logic
        let currentStep = 1;
        const totalSteps = 4;

        function openBookingWizard(presetService = null) {
            document.getElementById('booking-modal').classList.remove('hidden');
            if(presetService) {
                const radios = document.getElementsByName('service');
                for(let i=0; i<radios.length; i++){
                    if(radios[i].value === presetService) {
                        radios[i].checked = true;
                        break;
                    }
                }
            }
            showStep(1);
        }

        function closeBookingWizard() {
            document.getElementById('booking-modal').classList.add('hidden');
            setTimeout(() => { showStep(1); }, 300); // Reset after close animation
        }

        function updateWizardUI() {
            // Update Progress
            document.getElementById('wizard-progress').style.width = ((currentStep / totalSteps) * 100) + '%';
            
            // Update Buttons
            document.getElementById('wiz-btn-back').style.display = currentStep > 1 ? 'block' : 'none';
            const nextBtn = document.getElementById('wiz-btn-next');
            
            if(currentStep === totalSteps) {
                nextBtn.style.display = 'block';
                nextBtn.innerText = 'Confirm Reservation';
                nextBtn.classList.replace('bg-navy', 'bg-gold');
            } else if(currentStep === 3) {
                nextBtn.style.display = 'none'; // Hidden during loading
                document.getElementById('wiz-btn-back').style.display = 'none';
            } else {
                nextBtn.style.display = 'block';
                nextBtn.innerText = 'Next Step';
                nextBtn.classList.replace('bg-gold', 'bg-navy');
            }

            // Subtitles
            const subtitles = {
                1: 'Step 1: Select Service',
                2: 'Step 2: Date & Guests',
                3: 'Step 3: AI Availability Check',
                4: 'Step 4: Guest Details'
            };
            document.getElementById('wizard-subtitle').innerText = subtitles[currentStep];
        }

        function showStep(step) {
            document.querySelectorAll('.wizard-step').forEach(el => el.classList.add('hidden'));
            document.getElementById('step-' + step).classList.remove('hidden');
            currentStep = step;
            updateWizardUI();

            if(step === 3) {
                const loadingEl = document.getElementById('step3-loading');
                const optionsEl = document.getElementById('step3-options');
                if(loadingEl) loadingEl.classList.remove('hidden');
                if(optionsEl) {
                    optionsEl.classList.add('hidden');
                    optionsEl.innerHTML = '';
                }

                const service = document.querySelector('[name="service"]:checked')?.value || 'Room';
                const checkIn = document.getElementById('wiz-check-in')?.value || '';
                const checkOut = document.getElementById('wiz-check-out')?.value || '';
                const guests = document.getElementById('wiz-guests')?.value || 1;

                fetch('api/public.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ action: 'get_availability', service, check_in: checkIn, check_out: checkOut, guests })
                })
                .then(r => r.json())
                .then(json => {
                    if(loadingEl) loadingEl.classList.add('hidden');
                    if(optionsEl) optionsEl.classList.remove('hidden');
                    
                    if (json.success && json.options && json.options.length > 0) {
                        let html = '';
                        json.options.forEach((opt, idx) => {
                            const checked = idx === 0 ? 'checked' : '';
                            html += `
                            <label class="flex items-center justify-between p-4 border-2 border-gray-100 rounded-2xl cursor-pointer hover:border-gold transition-colors">
                                <div class="flex items-center gap-4">
                                    <input type="radio" name="target_option" value="${opt.id}" data-total="${opt.total_price}" class="w-5 h-5 text-gold focus:ring-gold" ${checked}>
                                    <div>
                                        <span class="font-bold font-poppins text-lg block text-navy">${opt.name}</span>
                                        <span class="text-xs text-gray-500">${opt.description || ''}</span>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <span class="font-bold text-gold text-lg block">KSh ${Number(opt.total_price).toLocaleString()}</span>
                                    <span class="text-xs text-gray-400">Total</span>
                                </div>
                            </label>`;
                        });
                        optionsEl.innerHTML = html;
                        document.getElementById('wiz-btn-next').style.display = 'block';
                    } else {
                        if(optionsEl) optionsEl.innerHTML = '<div class="text-center text-red-500 font-bold p-4">No availability found for the selected dates/guests. Please go back and try again.</div>';
                    }
                })
                .catch(err => {
                    if(loadingEl) loadingEl.classList.add('hidden');
                    if(optionsEl) {
                        optionsEl.classList.remove('hidden');
                        optionsEl.innerHTML = '<div class="text-center text-red-500 font-bold p-4">Network error. Could not fetch availability.</div>';
                    }
                });
            }
        }

        function wizardGoNext() {
            if(currentStep === totalSteps) {
                // Collect all wizard data and POST to API
                const service   = document.querySelector('[name="service"]:checked')?.value || 'Room';
                const checkIn   = document.getElementById('wiz-check-in')?.value || '';
                const checkOut  = document.getElementById('wiz-check-out')?.value || '';
                const guests    = document.getElementById('wiz-guests')?.value || 1;
                const firstName = document.getElementById('wiz-first-name')?.value.trim() || '';
                const lastName  = document.getElementById('wiz-last-name')?.value.trim() || '';
                const email     = document.getElementById('wiz-email')?.value.trim() || '';
                const phone     = document.getElementById('wiz-phone')?.value.trim() || '';

                if (!firstName || !email || !phone) {
                    Swal.fire({ icon: 'warning', title: 'Missing Details', text: 'Please fill in your name, email, and phone number.', confirmButtonColor: '#0F172A' });
                    return;
                }

                const targetOption = document.querySelector('[name="target_option"]:checked');
                const target_id = targetOption ? targetOption.value : null;
                const total = targetOption ? parseFloat(targetOption.getAttribute('data-total')) : 0;

                const nextBtn = document.getElementById('wiz-btn-next');
                nextBtn.innerHTML = '<span class="material-symbols-outlined" style="font-size:18px;animation:spin 1s linear infinite">sync</span> Confirming...';
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
                            html: `<p style="color:#6b7280">Thank you, <strong>${firstName}</strong>! Your booking is confirmed.</p>
                                   <div style="margin-top:12px;background:#f9fafb;border-radius:12px;padding:12px;text-align:left;font-size:14px">
                                     <p><strong>Reference:</strong> ${json.ref}</p>
                                     <p><strong>Service:</strong> ${service}</p>
                                     ${json.total > 0 ? `<p><strong>Total:</strong> KSh ${Number(json.total).toLocaleString()}</p>` : ''}
                                   </div>
                                   <p style="font-size:12px;color:#9ca3af;margin-top:12px">Confirmation sent to ${email}</p>`,
                            showCancelButton: true,
                            confirmButtonColor: '#0F172A',
                            confirmButtonText: '<span class="material-symbols-outlined align-middle mr-1 text-[18px]">download</span> Download Ticket',
                            cancelButtonText: 'Done',
                            customClass: { popup: 'font-poppins' }
                        }).then((result) => {
                            if (result.isConfirmed && json.booking_id) {
                                window.open(`modules/print_receipt.php?type=booking&id=${json.booking_id}`, '_blank');
                            }
                        });
                    } else {
                        Swal.fire({ icon: 'error', title: 'Booking Failed', text: json.message, confirmButtonColor: '#0F172A' });
                    }
                })
                .catch(() => {
                    Swal.fire({ icon: 'error', title: 'Network Error', text: 'Could not reach the server. Please try again.', confirmButtonColor: '#0F172A' });
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
            if(currentStep > 1) showStep(currentStep - 1);
        }
    </script>
</body>
</html>
