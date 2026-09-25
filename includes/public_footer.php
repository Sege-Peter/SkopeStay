    <!-- Luxury Footer -->
    <footer id="contact" class="bg-navy text-white pt-20 pb-12 border-t border-white/10 mt-auto w-full relative z-10">
        <!-- Subtle Top Glow -->
        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-3/4 max-w-4xl h-px bg-gradient-to-r from-transparent via-gold/40 to-transparent"></div>
        
        <div class="max-w-7xl mx-auto px-4 sm:px-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-10 md:gap-12 mb-16">
                <!-- Brand Info -->
                <div class="space-y-5">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-gold/15 text-gold flex items-center justify-center border border-gold/30">
                            <span class="material-symbols-outlined text-2xl">apartment</span>
                        </div>
                        <div>
                            <span class="font-display font-bold text-2xl text-white tracking-tight block">Skope<span class="text-gold">Stay</span></span>
                            <span class="text-[10px] uppercase font-brand tracking-[0.25em] text-gray-400 font-bold block">Resort & Suites</span>
                        </div>
                    </div>
                    <p class="text-gray-400 text-sm leading-relaxed max-w-sm">
                        An architectural masterpiece blending coastal tranquility with smart luxury hospitality. Experience bespoke living, culinary brilliance, and effortless reservations.
                    </p>
                    <div class="flex gap-3 pt-2">
                        <a href="https://wa.me/254742380183" target="_blank" rel="noopener" class="w-9 h-9 rounded-xl bg-white/5 border border-white/10 flex items-center justify-center hover:bg-gold hover:text-navy hover:border-gold transition-all" title="WhatsApp Concierge">
                            <span class="material-symbols-outlined text-[18px]">chat</span>
                        </a>
                        <a href="mailto:concierge@skopestay.com" class="w-9 h-9 rounded-xl bg-white/5 border border-white/10 flex items-center justify-center hover:bg-gold hover:text-navy hover:border-gold transition-all" title="Email Concierge">
                            <span class="material-symbols-outlined text-[18px]">mail</span>
                        </a>
                        <a href="tel:+254742380183" class="w-9 h-9 rounded-xl bg-white/5 border border-white/10 flex items-center justify-center hover:bg-gold hover:text-navy hover:border-gold transition-all" title="Call Us Directly">
                            <span class="material-symbols-outlined text-[18px]">call</span>
                        </a>
                    </div>
                </div>
                
                <!-- Quick Navigation -->
                <div>
                    <h4 class="font-brand font-bold text-xs uppercase tracking-[0.2em] text-gold mb-5">Experiences</h4>
                    <ul class="space-y-3 text-gray-400 text-sm font-ui">
                        <li><a href="index.php#rooms" class="hover:text-gold transition-colors flex items-center gap-1.5"><span class="material-symbols-outlined text-xs text-gray-500">chevron_right</span> Suites & Sanctuaries</a></li>
                        <li><a href="index.php#halls" class="hover:text-gold transition-colors flex items-center gap-1.5"><span class="material-symbols-outlined text-xs text-gray-500">chevron_right</span> Grand Ballroom & Grounds</a></li>
                        <li><a href="index.php#pool" class="hover:text-gold transition-colors flex items-center gap-1.5"><span class="material-symbols-outlined text-xs text-gray-500">chevron_right</span> The Infinity Escape</a></li>
                        <li><a href="index.php#restaurant" class="hover:text-gold transition-colors flex items-center gap-1.5"><span class="material-symbols-outlined text-xs text-gray-500">chevron_right</span> Signature Fine Dining</a></li>
                        <li><a href="index.php#amenities" class="hover:text-gold transition-colors flex items-center gap-1.5"><span class="material-symbols-outlined text-xs text-gray-500">chevron_right</span> Resort Amenities</a></li>
                    </ul>
                </div>
                
                <!-- Corporate & Policies -->
                <div>
                    <h4 class="font-brand font-bold text-xs uppercase tracking-[0.2em] text-gold mb-5">The Resort</h4>
                    <ul class="space-y-3 text-gray-400 text-sm font-ui">
                        <li><a href="about.php" class="hover:text-gold transition-colors flex items-center gap-1.5"><span class="material-symbols-outlined text-xs text-gray-500">chevron_right</span> About SkopeStay</a></li>
                        <li><a href="careers.php" class="hover:text-gold transition-colors flex items-center gap-1.5"><span class="material-symbols-outlined text-xs text-gray-500">chevron_right</span> Career Opportunities</a></li>
                        <li><a href="privacy.php" class="hover:text-gold transition-colors flex items-center gap-1.5"><span class="material-symbols-outlined text-xs text-gray-500">chevron_right</span> Privacy Policy</a></li>
                        <li><a href="terms.php" class="hover:text-gold transition-colors flex items-center gap-1.5"><span class="material-symbols-outlined text-xs text-gray-500">chevron_right</span> Terms & Conditions</a></li>
                        <li><a href="auth/login.php" class="hover:text-gold transition-colors flex items-center gap-1.5"><span class="material-symbols-outlined text-xs text-gray-500">chevron_right</span> Staff & Guest Access</a></li>
                    </ul>
                </div>

                <!-- Newsletter & Direct Concierge -->
                <div>
                    <h4 class="font-brand font-bold text-xs uppercase tracking-[0.2em] text-gold mb-5">VIP Privilege Club</h4>
                    <p class="text-gray-400 text-sm mb-4 leading-relaxed">Subscribe for private seasonal invitations, chef tastings, and exclusive suite upgrades.</p>
                    <form class="space-y-3" onsubmit="event.preventDefault(); Swal.fire({toast:true, position:'top-end', icon:'success', title:'Welcome to the VIP Club!', showConfirmButton:false, timer:3000}); this.reset();">
                        <div class="relative">
                            <input type="email" placeholder="Your executive email address" required class="bg-white/5 border border-white/10 rounded-xl px-4 py-3 w-full text-white text-sm focus:outline-none focus:border-gold transition-colors pr-12">
                            <button type="submit" class="absolute right-1.5 top-1.5 bottom-1.5 px-3 bg-gold text-navy rounded-lg hover:bg-yellow-400 transition-colors flex items-center justify-center font-bold" aria-label="Subscribe">
                                <span class="material-symbols-outlined text-lg">arrow_forward</span>
                            </button>
                        </div>
                        <span class="text-[11px] text-gray-500 block">We respect your privacy. Unsubscribe anytime.</span>
                    </form>
                </div>
            </div>
            
            <!-- Bottom Copyright Bar -->
            <div class="border-t border-white/10 pt-8 flex flex-col sm:flex-row justify-between items-center gap-4 text-xs text-gray-500 font-ui">
                <p>© <?= date('Y') ?> SkopeStay Luxury Resort & Suites. All Rights Reserved.</p>
                <div class="flex items-center gap-6">
                    <span class="flex items-center gap-1.5"><span class="material-symbols-outlined text-[15px] text-gold">verified</span> Best Rate Guarantee</span>
                    <span class="flex items-center gap-1.5"><span class="material-symbols-outlined text-[15px] text-gold">shield</span> 256-Bit SSL Encrypted</span>
                </div>
            </div>
        </div>
    </footer>

    <!-- Floating WhatsApp Concierge Button (Positioned above mobile dock) -->
    <a href="https://wa.me/254742380183?text=Hello%20SkopeStay%2C%20I%20would%20like%20to%20inquire%20about%20a%20reservation." target="_blank" rel="noopener" class="fixed bottom-20 md:bottom-8 right-4 md:right-8 z-40 w-12 sm:w-14 h-12 sm:h-14 bg-[#25D366] text-white rounded-full flex items-center justify-center shadow-2xl hover:scale-110 active:scale-95 transition-all duration-300 group" title="Chat with 24/7 Concierge">
        <svg class="w-6 sm:w-7 h-6 sm:h-7 fill-current" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"></path></svg>
        <span class="hidden md:inline-block absolute right-full mr-3 bg-navy text-white text-xs font-semibold px-3 py-1.5 rounded-lg whitespace-nowrap shadow-lg opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none">
            24/7 Concierge Desk
        </span>
    </a>

    <!-- Global Scripts: AOS & Swiper -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof AOS !== 'undefined') {
                AOS.init({
                    duration: 750,
                    once: true,
                    offset: 40,
                    easing: 'ease-out-cubic'
                });
            }
        });
    </script>
</body>
</html>
