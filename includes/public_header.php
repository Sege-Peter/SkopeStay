    <!-- Desktop & Tablet Header Navigation -->
    <header id="main-nav" class="fixed top-0 left-0 w-full z-[100] transition-all duration-300 py-3 sm:py-4">
        <div class="max-w-7xl mx-auto px-4 sm:px-6">
            <div id="nav-inner-container" class="glass-luxury rounded-2xl px-4 sm:px-6 py-2.5 sm:py-3 flex justify-between items-center shadow-luxury border border-white/60 transition-all duration-300">
                <!-- Brand Logo & Identity -->
                <a href="index.php" class="flex items-center gap-3 group focus:outline-none">
                    <div class="w-10 h-10 rounded-xl bg-navy flex items-center justify-center text-gold shadow-md group-hover:scale-105 transition-transform">
                        <span class="material-symbols-outlined text-2xl">apartment</span>
                    </div>
                    <div>
                        <span class="font-display font-bold text-xl sm:text-2xl text-navy tracking-tight block leading-tight">Skope<span class="text-gold">Stay</span></span>
                        <span class="text-[10px] uppercase font-brand tracking-[0.25em] text-gray-400 font-bold block">Resort & Suites</span>
                    </div>
                </a>

                <!-- Desktop Navigation Links (Large Screens) -->
                <nav class="hidden lg:flex items-center gap-7 font-ui font-medium text-sm text-navy">
                    <a href="index.php" class="hover:text-gold transition-colors py-2 flex items-center gap-1 font-semibold">Home</a>
                    
                    <!-- Accommodations Dropdown -->
                    <div class="nav-item relative py-2">
                        <a href="index.php#rooms" class="hover:text-gold transition-colors flex items-center gap-1 py-1">
                            Accommodations 
                            <span class="material-symbols-outlined text-[16px] text-gray-400 transition-transform duration-200">expand_more</span>
                        </a>
                        <div class="mega-menu absolute top-full left-1/2 -translate-x-1/2 w-[540px] glass-luxury p-5 rounded-2xl shadow-2xl border border-white/70 text-navy mt-1">
                            <div class="grid grid-cols-2 gap-4">
                                <div class="space-y-1">
                                    <h4 class="font-brand font-bold text-xs uppercase tracking-wider text-gold pb-2 border-b border-gray-100">Suites & Sanctuaries</h4>
                                    <a href="index.php#rooms" class="flex items-center gap-3 p-2 rounded-xl hover:bg-navy/5 transition-colors">
                                        <div class="w-8 h-8 rounded-lg bg-gold/10 text-gold flex items-center justify-center"><span class="material-symbols-outlined text-[18px]">workspace_premium</span></div>
                                        <div>
                                            <span class="font-semibold text-xs block text-navy">Presidential Penthouse</span>
                                            <span class="text-[11px] text-gray-400">Panoramic views & private chef</span>
                                        </div>
                                    </a>
                                    <a href="index.php#rooms" class="flex items-center gap-3 p-2 rounded-xl hover:bg-navy/5 transition-colors">
                                        <div class="w-8 h-8 rounded-lg bg-navy/5 text-navy flex items-center justify-center"><span class="material-symbols-outlined text-[18px]">king_bed</span></div>
                                        <div>
                                            <span class="font-semibold text-xs block text-navy">Deluxe King Suites</span>
                                            <span class="text-[11px] text-gray-400">Balcony & spa rainforest bath</span>
                                        </div>
                                    </a>
                                    <a href="index.php#rooms" class="flex items-center gap-3 p-2 rounded-xl hover:bg-navy/5 transition-colors">
                                        <div class="w-8 h-8 rounded-lg bg-navy/5 text-navy flex items-center justify-center"><span class="material-symbols-outlined text-[18px]">family_restroom</span></div>
                                        <div>
                                            <span class="font-semibold text-xs block text-navy">Family Garden Retreat</span>
                                            <span class="text-[11px] text-gray-400">Connecting suites & lounge</span>
                                        </div>
                                    </a>
                                </div>
                                <div class="relative rounded-xl overflow-hidden img-zoom-container h-full min-h-[160px]">
                                    <img src="https://images.unsplash.com/photo-1590490360182-c33d57733427?q=80&w=1200&auto=format&fit=crop" class="w-full h-full object-cover" alt="Featured Suite">
                                    <div class="absolute inset-0 bg-gradient-to-t from-navy via-navy/40 to-transparent flex flex-col justify-end p-4">
                                        <span class="text-[10px] uppercase tracking-wider font-brand text-gold font-bold">Direct Booking Privilege</span>
                                        <span class="text-white text-xs font-bold">Complimentary Breakfast Included</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Venues Dropdown -->
                    <div class="nav-item relative py-2">
                        <a href="index.php#halls" class="hover:text-gold transition-colors flex items-center gap-1 py-1">
                            Venues 
                            <span class="material-symbols-outlined text-[16px] text-gray-400 transition-transform duration-200">expand_more</span>
                        </a>
                        <div class="mega-menu absolute top-full left-1/2 -translate-x-1/2 w-[340px] glass-luxury p-4 rounded-2xl shadow-2xl border border-white/70 text-navy mt-1">
                            <h4 class="font-brand font-bold text-xs uppercase tracking-wider text-gold pb-2 border-b border-gray-100 mb-2">Banquet & Meetings</h4>
                            <div class="space-y-1">
                                <a href="index.php#halls" class="flex items-center gap-3 p-2 rounded-xl hover:bg-navy/5 transition-colors">
                                    <div class="w-8 h-8 rounded-lg bg-gold/10 text-gold flex items-center justify-center"><span class="material-symbols-outlined text-[18px]">celebration</span></div>
                                    <div>
                                        <span class="font-semibold text-xs block text-navy">The Grand Ballroom</span>
                                        <span class="text-[11px] text-gray-400">Up to 500 Guests • Crystal Chandeliers</span>
                                    </div>
                                </a>
                                <a href="index.php#halls" class="flex items-center gap-3 p-2 rounded-xl hover:bg-navy/5 transition-colors">
                                    <div class="w-8 h-8 rounded-lg bg-navy/5 text-navy flex items-center justify-center"><span class="material-symbols-outlined text-[18px]">groups</span></div>
                                    <div>
                                        <span class="font-semibold text-xs block text-navy">Executive Boardrooms</span>
                                        <span class="text-[11px] text-gray-400">4K Telepresence & High-Speed Fiber</span>
                                    </div>
                                </a>
                                <a href="index.php#halls" class="flex items-center gap-3 p-2 rounded-xl hover:bg-navy/5 transition-colors">
                                    <div class="w-8 h-8 rounded-lg bg-navy/5 text-navy flex items-center justify-center"><span class="material-symbols-outlined text-[18px]">yard</span></div>
                                    <div>
                                        <span class="font-semibold text-xs block text-navy">Oasis Garden Grounds</span>
                                        <span class="text-[11px] text-gray-400">Outdoor Receptions up to 1,000</span>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>

                    <a href="index.php#pool" class="hover:text-gold transition-colors py-2">Pool & Spa</a>
                    <a href="index.php#restaurant" class="hover:text-gold transition-colors py-2">Fine Dining</a>
                    <a href="about.php" class="hover:text-gold transition-colors py-2">About</a>
                </nav>

                <!-- Desktop Actions -->
                <div class="hidden lg:flex items-center gap-3.5">
                    <a href="auth/login.php" class="text-navy font-semibold text-xs tracking-wide hover:text-gold transition-colors px-3 py-2 flex items-center gap-1.5">
                        <span class="material-symbols-outlined text-[18px]">lock</span>
                        <span>Portal</span>
                    </a>
                    <button onclick="if(typeof openBookingWizard==='function'){openBookingWizard();}else{window.location.href='index.php?book=true';}" class="bg-navy hover:bg-gold text-white hover:text-navy px-5 py-2.5 rounded-xl font-ui font-semibold text-xs tracking-wide transition-all duration-300 shadow-md hover:shadow-gold-sm flex items-center gap-2">
                        <span class="material-symbols-outlined text-[17px]">calendar_month</span>
                        <span>Reserve</span>
                    </button>
                </div>

                <!-- Mobile Action & Hamburger Button -->
                <div class="flex items-center gap-2 lg:hidden">
                    <button onclick="if(typeof openBookingWizard==='function'){openBookingWizard();}else{window.location.href='index.php?book=true';}" class="bg-gold text-white font-ui font-bold text-xs px-3.5 py-1.5 rounded-lg shadow-sm hover:bg-yellow-600 transition-colors flex items-center gap-1">
                        <span class="material-symbols-outlined text-[16px]">calendar_month</span>
                        <span>Book</span>
                    </button>
                    <button onclick="toggleMobileMenu()" class="w-9 h-9 rounded-lg bg-navy/5 text-navy hover:bg-gold hover:text-white transition-colors flex items-center justify-center focus:outline-none" aria-label="Toggle Navigation">
                        <span class="material-symbols-outlined text-2xl">menu</span>
                    </button>
                </div>
            </div>
        </div>
    </header>

    <!-- Mobile Slide-In Navigation Drawer -->
    <div id="mobile-overlay" onclick="toggleMobileMenu()" class="fixed inset-0 bg-navy-dark/70 backdrop-blur-sm z-[110]"></div>
    <div id="mobile-menu" class="fixed top-0 left-0 h-full w-[85%] max-w-[320px] bg-white z-[120] shadow-2xl flex flex-col justify-between overflow-y-auto">
        <div>
            <!-- Mobile Drawer Header -->
            <div class="p-5 border-b border-gray-100 flex justify-between items-center bg-gray-50/70">
                <a href="index.php" class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg bg-navy text-gold flex items-center justify-center">
                        <span class="material-symbols-outlined text-xl">apartment</span>
                    </div>
                    <span class="font-display font-bold text-xl text-navy">Skope<span class="text-gold">Stay</span></span>
                </a>
                <button onclick="toggleMobileMenu()" class="w-8 h-8 rounded-full bg-gray-200/70 text-gray-600 hover:text-navy flex items-center justify-center">
                    <span class="material-symbols-outlined text-xl">close</span>
                </button>
            </div>

            <!-- Mobile Drawer Links -->
            <nav class="p-5 space-y-1 font-ui text-base text-navy">
                <a href="index.php" onclick="toggleMobileMenu()" class="flex items-center gap-3.5 p-3 rounded-xl hover:bg-navy/5 hover:text-gold transition-colors font-medium">
                    <span class="material-symbols-outlined text-xl text-gold">home</span>
                    <span>Home</span>
                </a>
                <a href="index.php#rooms" onclick="toggleMobileMenu()" class="flex items-center gap-3.5 p-3 rounded-xl hover:bg-navy/5 hover:text-gold transition-colors font-medium">
                    <span class="material-symbols-outlined text-xl text-gold">king_bed</span>
                    <span>Suites & Rooms</span>
                </a>
                <a href="index.php#halls" onclick="toggleMobileMenu()" class="flex items-center gap-3.5 p-3 rounded-xl hover:bg-navy/5 hover:text-gold transition-colors font-medium">
                    <span class="material-symbols-outlined text-xl text-gold">event_seat</span>
                    <span>Venues & Events</span>
                </a>
                <a href="index.php#pool" onclick="toggleMobileMenu()" class="flex items-center gap-3.5 p-3 rounded-xl hover:bg-navy/5 hover:text-gold transition-colors font-medium">
                    <span class="material-symbols-outlined text-xl text-gold">pool</span>
                    <span>Infinity Pool & Spa</span>
                </a>
                <a href="index.php#restaurant" onclick="toggleMobileMenu()" class="flex items-center gap-3.5 p-3 rounded-xl hover:bg-navy/5 hover:text-gold transition-colors font-medium">
                    <span class="material-symbols-outlined text-xl text-gold">restaurant</span>
                    <span>Fine Dining Menu</span>
                </a>
                <a href="about.php" onclick="toggleMobileMenu()" class="flex items-center gap-3.5 p-3 rounded-xl hover:bg-navy/5 hover:text-gold transition-colors font-medium">
                    <span class="material-symbols-outlined text-xl text-gold">info</span>
                    <span>About Resort</span>
                </a>
                <a href="careers.php" onclick="toggleMobileMenu()" class="flex items-center gap-3.5 p-3 rounded-xl hover:bg-navy/5 hover:text-gold transition-colors font-medium">
                    <span class="material-symbols-outlined text-xl text-gold">work</span>
                    <span>Careers</span>
                </a>
            </nav>
        </div>

        <!-- Mobile Drawer Bottom CTA -->
        <div class="p-5 border-t border-gray-100 bg-gray-50/80 space-y-3">
            <button onclick="toggleMobileMenu(); if(typeof openBookingWizard==='function'){openBookingWizard();}else{window.location.href='index.php?book=true';}" class="w-full bg-gold text-white font-ui font-bold py-3 rounded-xl shadow-md hover:bg-yellow-600 transition-colors flex items-center justify-center gap-2">
                <span class="material-symbols-outlined text-xl">calendar_month</span>
                <span>Check Availability</span>
            </button>
            <a href="auth/login.php" class="w-full block text-center py-2.5 border border-navy/20 text-navy rounded-xl font-ui font-semibold text-sm hover:bg-navy hover:text-white transition-colors">
                Staff / Guest Portal
            </a>
            <div class="pt-2 flex justify-center gap-4 text-gray-400 text-xs font-brand">
                <span>Reception: +254 742 380 183</span>
            </div>
        </div>
    </div>

    <!-- Mobile Persistent Bottom Navigation Dock (Native App Style) -->
    <div class="md:hidden fixed bottom-0 left-0 right-0 z-50 mobile-bottom-dock px-3 py-2 flex items-center justify-around safe-bottom">
        <a href="index.php" class="flex flex-col items-center text-gray-300 hover:text-gold transition-colors text-[10px] font-medium py-1">
            <span class="material-symbols-outlined text-xl">home</span>
            <span>Home</span>
        </a>
        <a href="index.php#rooms" class="flex flex-col items-center text-gray-300 hover:text-gold transition-colors text-[10px] font-medium py-1">
            <span class="material-symbols-outlined text-xl">bed</span>
            <span>Suites</span>
        </a>
        
        <!-- Highlighted Floating Center Button -->
        <button onclick="if(typeof openBookingWizard==='function'){openBookingWizard();}else{window.location.href='index.php?book=true';}" class="-mt-5 w-12 h-12 rounded-full gold-gradient-bg text-navy flex items-center justify-center shadow-gold-glow hover:scale-105 active:scale-95 transition-transform" aria-label="Book Reservation">
            <span class="material-symbols-outlined text-2xl font-bold">calendar_month</span>
        </button>

        <a href="index.php#halls" class="flex flex-col items-center text-gray-300 hover:text-gold transition-colors text-[10px] font-medium py-1">
            <span class="material-symbols-outlined text-xl">event_seat</span>
            <span>Venues</span>
        </a>
        <a href="index.php#restaurant" class="flex flex-col items-center text-gray-300 hover:text-gold transition-colors text-[10px] font-medium py-1">
            <span class="material-symbols-outlined text-xl">restaurant</span>
            <span>Dining</span>
        </a>
    </div>

    <!-- Navigation Scroll & Mobile Toggle Script -->
    <script>
        function toggleMobileMenu() {
            const menu = document.getElementById('mobile-menu');
            const overlay = document.getElementById('mobile-overlay');
            if (menu) menu.classList.toggle('open');
            if (overlay) overlay.classList.toggle('open');
        }

        window.addEventListener('scroll', function() {
            const nav = document.getElementById('main-nav');
            const inner = document.getElementById('nav-inner-container');
            if (!nav || !inner) return;

            if (window.scrollY > 40) {
                nav.classList.add('py-2');
                nav.classList.remove('py-3', 'sm:py-4');
                inner.classList.add('shadow-xl', 'bg-white/95');
                inner.classList.remove('bg-white/88');
            } else {
                nav.classList.remove('py-2');
                nav.classList.add('py-3', 'sm:py-4');
                inner.classList.remove('shadow-xl', 'bg-white/95');
                inner.classList.add('bg-white/88');
            }
        });
    </script>
