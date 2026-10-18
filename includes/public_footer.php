    <?php
    $ft_phone = get_setting($pdo, 'contact_phone', '');
    $ft_tel = preg_replace('/[^0-9+]/', '', $ft_phone);
    $ft_email = get_setting($pdo, 'support_email', get_setting($pdo, 'contact_email', ''));
    $ft_wa = preg_replace('/\D/', '', get_setting($pdo, 'whatsapp_number', '')) ?: ltrim($ft_tel, '+');
    $ft_socials = array_filter([
        'Facebook'  => [get_setting($pdo, 'social_facebook', ''), 'M22 12a10 10 0 1 0-11.56 9.88v-6.99H7.9V12h2.54V9.8c0-2.5 1.49-3.89 3.78-3.89 1.09 0 2.24.2 2.24.2v2.46h-1.26c-1.24 0-1.63.77-1.63 1.56V12h2.78l-.44 2.89h-2.34v6.99A10 10 0 0 0 22 12Z'],
        'Instagram' => [get_setting($pdo, 'social_instagram', ''), 'M12 2.2c3.2 0 3.6 0 4.8.07 3.25.15 4.77 1.69 4.92 4.92.06 1.27.07 1.65.07 4.85s0 3.58-.07 4.85c-.15 3.23-1.66 4.77-4.92 4.92-1.27.06-1.64.07-4.8.07s-3.58 0-4.85-.07c-3.26-.15-4.77-1.7-4.92-4.92C2.17 15.58 2.2 15.2 2.2 12s0-3.58.07-4.85C2.42 3.92 3.94 2.37 7.15 2.22 8.42 2.2 8.8 2.2 12 2.2Zm0 4.66a5.14 5.14 0 1 0 0 10.28 5.14 5.14 0 0 0 0-10.28Zm0 8.48a3.34 3.34 0 1 1 0-6.68 3.34 3.34 0 0 1 0 6.68Zm5.34-9.89a1.2 1.2 0 1 0 0 2.4 1.2 1.2 0 0 0 0-2.4Z'],
        'X'         => [get_setting($pdo, 'social_x', ''), 'M18.24 2.25h3.31l-7.23 8.26 8.5 11.24h-6.66l-5.21-6.82-5.97 6.82H1.67l7.73-8.84L1.25 2.25h6.83l4.71 6.23 5.45-6.23Zm-1.16 17.52h1.83L7.08 4.13H5.12l11.96 15.64Z'],
        'TikTok'    => [get_setting($pdo, 'social_tiktok', ''), 'M19.6 6.7a4.8 4.8 0 0 1-3.8-4.2V2h-3.4v13.7a2.9 2.9 0 1 1-2-2.7V9.5a6.3 6.3 0 1 0 5.4 6.2V8.8a8.2 8.2 0 0 0 4.8 1.5V6.9l-1-.2Z'],
        'LinkedIn'  => [get_setting($pdo, 'social_linkedin', ''), 'M20.45 20.45h-3.56v-5.57c0-1.33-.02-3.04-1.85-3.04-1.85 0-2.14 1.45-2.14 2.94v5.67H9.35V9h3.41v1.56h.05c.48-.9 1.64-1.85 3.37-1.85 3.6 0 4.27 2.37 4.27 5.46v6.28ZM5.34 7.43a2.06 2.06 0 1 1 0-4.13 2.06 2.06 0 0 1 0 4.13ZM7.12 20.45H3.56V9h3.56v11.45ZM22.22 0H1.77C.8 0 0 .77 0 1.73v20.54C0 23.23.8 24 1.77 24h20.45c.98 0 1.78-.77 1.78-1.73V1.73C24 .77 23.2 0 22.22 0Z'],
        'YouTube'   => [get_setting($pdo, 'social_youtube', ''), 'M23.5 6.2a3 3 0 0 0-2.1-2.1C19.5 3.6 12 3.6 12 3.6s-7.5 0-9.4.5A3 3 0 0 0 .5 6.2 31 31 0 0 0 0 12a31 31 0 0 0 .5 5.8 3 3 0 0 0 2.1 2.1c1.9.5 9.4.5 9.4.5s7.5 0 9.4-.5a3 3 0 0 0 2.1-2.1A31 31 0 0 0 24 12a31 31 0 0 0-.5-5.8ZM9.6 15.6V8.4l6.2 3.6-6.2 3.6Z'],
    ], fn($v) => $v[0] !== '');
    ?>
    <!-- Luxury Footer -->
    <footer id="contact" class="bg-navy text-white pt-20 pb-12 border-t border-white/10 mt-auto w-full relative z-10">
        <!-- Subtle Top Glow -->
        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-3/4 max-w-4xl h-px bg-gradient-to-r from-transparent via-gold/40 to-transparent"></div>
        
        <div class="max-w-7xl mx-auto px-4 sm:px-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-10 md:gap-12 mb-16">
                <!-- Brand Info -->
                <div class="space-y-5">
                    <div class="flex items-center gap-3">
                        <?= brand_mark('w-11 h-11 rounded-xl') ?>
                        <div class="min-w-0">
                            <span class="font-display font-bold text-2xl text-white tracking-tight block truncate"><?= brand_wordmark() ?></span>
                            <span class="text-[10px] uppercase font-brand tracking-[0.25em] text-gray-400 font-bold block truncate"><?= e(brand('brand_subtitle')) ?></span>
                        </div>
                    </div>
                    <p class="text-gray-400 text-sm leading-relaxed max-w-sm">
                        <?= e(get_setting($pdo, 'footer_about', '')) ?>
                    </p>
                    <ul class="space-y-1.5 text-sm text-gray-400">
                        <li class="flex items-start gap-2"><span class="material-symbols-outlined text-[16px] text-gold mt-0.5">location_on</span><span><?= e(get_setting($pdo, 'contact_address', '')) ?></span></li>
                        <?php if ($ft_phone): ?><li class="flex items-center gap-2"><span class="material-symbols-outlined text-[16px] text-gold">call</span><a href="tel:<?= e($ft_tel) ?>" class="hover:text-gold"><?= e($ft_phone) ?></a></li><?php endif; ?>
                        <?php if ($ft_email): ?><li class="flex items-center gap-2"><span class="material-symbols-outlined text-[16px] text-gold">mail</span><a href="mailto:<?= e($ft_email) ?>" class="hover:text-gold break-all"><?= e($ft_email) ?></a></li><?php endif; ?>
                    </ul>
                    <div class="flex flex-wrap gap-2.5 pt-1">
                        <?php if ($ft_wa): ?>
                        <a href="https://wa.me/<?= e($ft_wa) ?>" target="_blank" rel="noopener" class="w-9 h-9 rounded-xl bg-white/5 border border-white/10 flex items-center justify-center hover:bg-gold hover:text-navy hover:border-gold transition-all" title="WhatsApp" aria-label="WhatsApp">
                            <span class="material-symbols-outlined text-[18px]">chat</span>
                        </a>
                        <?php endif; ?>
                        <?php foreach ($ft_socials as $label => [$url, $path]): ?>
                        <a href="<?= e($url) ?>" target="_blank" rel="noopener" class="w-9 h-9 rounded-xl bg-white/5 border border-white/10 flex items-center justify-center hover:bg-gold hover:text-navy hover:border-gold transition-all" title="<?= $label ?>" aria-label="<?= $label ?>">
                            <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24" aria-hidden="true"><path d="<?= $path ?>"/></svg>
                        </a>
                        <?php endforeach; ?>
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
                        <li><a href="about.php" class="hover:text-gold transition-colors flex items-center gap-1.5"><span class="material-symbols-outlined text-xs text-gray-500">chevron_right</span> About <?= e(brand_short_name()) ?></a></li>
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
                    <form class="space-y-3" onsubmit="return subscribeNewsletter(event, this)">
                        <div class="relative">
                            <input type="email" name="email" placeholder="Your email address" required aria-label="Email address" class="bg-white/5 border border-white/10 rounded-xl px-4 py-3 w-full text-white text-sm focus:outline-none focus:border-gold transition-colors pr-12">
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
                <p>© <?= date('Y') ?> <?= e(brand('hotel_name')) ?>. All Rights Reserved.</p>
                <div class="flex items-center gap-6">
                    <span class="flex items-center gap-1.5"><span class="material-symbols-outlined text-[15px] text-gold">verified</span> Best Rate Guarantee</span>
                    <span class="flex items-center gap-1.5"><span class="material-symbols-outlined text-[15px] text-gold">shield</span> 256-Bit SSL Encrypted</span>
                </div>
            </div>
        </div>
    </footer>

    <!-- Floating WhatsApp Concierge Button (Positioned above mobile dock) -->
    <?php if ($ft_wa): ?>
    <a href="https://wa.me/<?= e($ft_wa) ?>?text=<?= rawurlencode('Hello ' . brand_short_name() . ', I would like to inquire about a reservation.') ?>" target="_blank" rel="noopener" class="fixed bottom-20 md:bottom-8 right-4 md:right-8 z-40 w-12 sm:w-14 h-12 sm:h-14 bg-[#25D366] text-white rounded-full flex items-center justify-center shadow-2xl hover:scale-110 active:scale-95 transition-all duration-300 group" title="Chat with 24/7 Concierge">
        <svg class="w-6 sm:w-7 h-6 sm:h-7 fill-current" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"></path></svg>
        <span class="hidden md:inline-block absolute right-full mr-3 bg-navy text-white text-xs font-semibold px-3 py-1.5 rounded-lg whitespace-nowrap shadow-lg opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none">
            24/7 Concierge Desk
        </span>
    </a>
    <?php endif; ?>

    <!-- Global Scripts: AOS & Swiper -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js"></script>
    <script>
        async function subscribeNewsletter(e, form) {
            e.preventDefault();
            const btn = form.querySelector('button');
            btn.disabled = true;
            try {
                const res = await fetch('api/public.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ action: 'subscribe', email: form.email.value })
                });
                const d = await res.json();
                Swal.fire({ toast: true, position: 'top-end', icon: d.success ? 'success' : 'error', title: d.message, showConfirmButton: false, timer: 3500 });
                if (d.success) form.reset();
            } catch (err) {
                Swal.fire({ toast: true, position: 'top-end', icon: 'error', title: 'Could not subscribe right now.', showConfirmButton: false, timer: 3000 });
            } finally {
                btn.disabled = false;
            }
            return false;
        }

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
