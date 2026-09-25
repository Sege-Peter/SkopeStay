    <!-- Footer -->
    <footer class="bg-navy text-white pt-24 pb-12 border-t border-white/10 mt-auto w-full">
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
                        <li><a href="index.php#rooms" class="hover:text-gold transition-colors">Rooms & Suites</a></li>
                        <li><a href="index.php#halls" class="hover:text-gold transition-colors">Meetings & Events</a></li>
                        <li><a href="index.php#pool" class="hover:text-gold transition-colors">Pool & Wellness</a></li>
                        <li><a href="index.php#restaurant" class="hover:text-gold transition-colors">Fine Dining</a></li>
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
                    <form class="flex" onsubmit="event.preventDefault(); Swal.fire({toast:true, position:'top-end', icon:'success', title:'Subscribed!', showConfirmButton:false, timer:3000});">
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

    <!-- AOS Initialization -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if(typeof AOS !== 'undefined') {
                AOS.init({
                    duration: 800,
                    once: true,
                    offset: 50
                });
            }
        });
    </script>
</body>
</html>
