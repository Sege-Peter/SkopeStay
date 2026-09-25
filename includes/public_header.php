    <!-- Desktop Navigation -->
    <header id="main-nav" class="fixed top-0 w-full z-[100] transition-all duration-300 py-4">
        <div class="max-w-7xl mx-auto px-6">
            <div class="glass rounded-2xl px-6 py-3 flex justify-between items-center shadow-lg transition-all duration-300">
                <!-- Logo -->
                <a href="index.php" class="flex items-center gap-3">
                    <img src="assets/images/skopestay logo.png" alt="SkopeStay Logo" class="h-10 w-auto mix-blend-multiply" onerror="this.outerHTML='<span class=\'material-symbols-outlined text-gold text-4xl\'>apartment</span>'">
                    <span class="font-poppins font-bold text-xl text-navy tracking-tight menu-text">SkopeStay</span>
                </a>

                <!-- Desktop Links (Hidden on mobile) -->
                <nav class="hidden lg:flex items-center gap-8 font-inter font-medium text-sm text-navy menu-text">
                    <a href="index.php" class="hover:text-gold transition-colors">Home</a>
                    
                    <!-- Rooms Mega Menu -->
                    <div class="nav-item relative py-4">
                        <a href="index.php#rooms" class="hover:text-gold transition-colors flex items-center gap-1">Accommodations <span class="material-symbols-outlined text-[16px]">expand_more</span></a>
                        <div class="mega-menu absolute top-full left-1/2 -translate-x-1/2 w-[600px] glass p-6 rounded-2xl shadow-2xl border border-white/50 text-navy">
                            <div class="grid grid-cols-2 gap-6">
                                <div>
                                    <h4 class="font-poppins font-bold text-gold mb-4 border-b border-gray-200 pb-2">Suites & Rooms</h4>
                                    <ul class="space-y-3">
                                        <li><a href="index.php#rooms" class="hover:text-gold flex items-center gap-2"><span class="material-symbols-outlined text-[18px]">king_bed</span> Executive Suite</a></li>
                                        <li><a href="index.php#rooms" class="hover:text-gold flex items-center gap-2"><span class="material-symbols-outlined text-[18px]">bed</span> Deluxe Room</a></li>
                                        <li><a href="index.php#rooms" class="hover:text-gold flex items-center gap-2"><span class="material-symbols-outlined text-[18px]">single_bed</span> Standard Twin</a></li>
                                    </ul>
                                </div>
                                <div class="relative rounded-xl overflow-hidden img-zoom-container h-40">
                                    <img src="https://images.unsplash.com/photo-1590490360182-c33d57733427?q=80&w=1974&auto=format&fit=crop" class="w-full h-full object-cover" alt="Featured Room">
                                    <div class="absolute inset-0 bg-gradient-to-t from-navy to-transparent flex items-end p-4">
                                        <span class="text-white font-bold">Book Direct & Save 15%</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Venues Mega Menu -->
                    <div class="nav-item relative py-4">
                        <a href="index.php#halls" class="hover:text-gold transition-colors flex items-center gap-1">Venues & Events <span class="material-symbols-outlined text-[16px]">expand_more</span></a>
                        <div class="mega-menu absolute top-full left-1/2 -translate-x-1/2 w-[400px] glass p-6 rounded-2xl shadow-2xl border border-white/50 text-navy">
                            <h4 class="font-poppins font-bold text-gold mb-4 border-b border-gray-200 pb-2">Event Spaces</h4>
                            <ul class="space-y-3">
                                <li><a href="index.php#halls" class="hover:text-gold flex items-center gap-2"><span class="material-symbols-outlined text-[18px]">celebration</span> Grand Ballroom</a></li>
                                <li><a href="index.php#halls" class="hover:text-gold flex items-center gap-2"><span class="material-symbols-outlined text-[18px]">meeting_room</span> Corporate Boardroom</a></li>
                                <li><a href="index.php#halls" class="hover:text-gold flex items-center gap-2"><span class="material-symbols-outlined text-[18px]">park</span> Outdoor Garden</a></li>
                            </ul>
                        </div>
                    </div>

                    <a href="index.php#pool" class="hover:text-gold transition-colors">Pool & Spa</a>
                    <a href="index.php#restaurant" class="hover:text-gold transition-colors">Dining</a>
                    <a href="about.php" class="hover:text-gold transition-colors">About Us</a>
                </nav>

                <!-- Action Buttons -->
                <div class="hidden lg:flex items-center gap-4">
                    <a href="auth/login.php" class="text-navy font-semibold hover:text-gold transition-colors text-sm menu-text">Guest Portal</a>
                    <button onclick="window.location.href='index.php?book=true'" class="bg-navy text-white px-6 py-2.5 rounded-xl font-poppins font-semibold hover:bg-gold transition-colors shadow-lg hover:shadow-gold/50 flex items-center gap-2">
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
            <a href="index.php#rooms" onclick="toggleMobileMenu()" class="hover:text-gold flex items-center gap-3"><span class="material-symbols-outlined">king_bed</span> Accommodations</a>
            <a href="index.php#halls" onclick="toggleMobileMenu()" class="hover:text-gold flex items-center gap-3"><span class="material-symbols-outlined">event_seat</span> Venues</a>
            <a href="index.php#pool" onclick="toggleMobileMenu()" class="hover:text-gold flex items-center gap-3"><span class="material-symbols-outlined">pool</span> Pool & Spa</a>
            <a href="index.php#restaurant" onclick="toggleMobileMenu()" class="hover:text-gold flex items-center gap-3"><span class="material-symbols-outlined">restaurant</span> Dining</a>
            <a href="about.php" onclick="toggleMobileMenu()" class="hover:text-gold flex items-center gap-3"><span class="material-symbols-outlined">info</span> About Us</a>
        </div>
        <div class="p-6 border-t border-gray-100 bg-lightgray">
            <a href="auth/login.php" class="block w-full text-center py-3 mb-3 border-2 border-navy text-navy rounded-xl font-bold hover:bg-navy hover:text-white transition-colors">Login / Register</a>
            <button onclick="window.location.href='index.php?book=true'" class="w-full bg-gold text-white py-3 rounded-xl font-bold shadow-lg shadow-gold/30 hover:bg-yellow-600 transition-colors">Book Now</button>
        </div>
    </div>
    
    <script>
        function toggleMobileMenu() {
            const menu = document.getElementById('mobile-menu');
            const overlay = document.getElementById('mobile-overlay');
            if(menu) menu.classList.toggle('open');
            if(overlay) overlay.classList.toggle('open');
        }
        
        // Handle navbar scroll effect
        window.addEventListener('scroll', function() {
            const nav = document.getElementById('main-nav');
            const texts = document.querySelectorAll('.menu-text');
            if (window.scrollY > 50) {
                nav.classList.add('bg-white/95', 'backdrop-blur-md', 'shadow-md');
            } else {
                nav.classList.remove('bg-white/95', 'backdrop-blur-md', 'shadow-md');
            }
        });
    </script>
