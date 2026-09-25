<?php
// Luxury 5-Star Maintenance Screen Template
$hotel_name = $m_hotel ?? 'SkopeStay Resort & Suites';
$logo_path = 'assets/images/SkopeStay logo.png';
if (file_exists(__DIR__ . '/../' . $logo_path)) {
    $logo_url = (strpos($_SERVER['REQUEST_URI'] ?? '', '/modules/') !== false || strpos($_SERVER['REQUEST_URI'] ?? '', '/auth/') !== false) 
        ? '../' . $logo_path : $logo_path;
} else {
    $logo_url = '';
}
$login_url = (strpos($_SERVER['REQUEST_URI'] ?? '', '/modules/') !== false) ? '../auth/login.php' : 'auth/login.php';
?>
<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Scheduled Maintenance | <?= htmlspecialchars($hotel_name) ?></title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:ital,wght@0,600;0,700;1,400&family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        navy: '#0B132B',
                        gold: '#D4AF37'
                    },
                    fontFamily: {
                        display: ['Playfair Display', 'serif'],
                        brand: ['Montserrat', 'sans-serif'],
                        sans: ['Inter', 'sans-serif']
                    }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #0B132B; }
        .gold-gradient-text {
            background: linear-gradient(135deg, #F5E6AB 0%, #D4AF37 50%, #A6821C 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .gold-gradient-btn {
            background: linear-gradient(135deg, #D4AF37 0%, #C49826 60%, #B0851A 100%);
        }
    </style>
</head>
<body class="h-full min-h-screen flex items-center justify-center p-4 sm:p-6 text-white relative overflow-hidden select-none">

    <!-- Background Cinematic Image with Luxury Vignette -->
    <div class="absolute inset-0 z-0">
        <img src="https://images.unsplash.com/photo-1542314831-c53cd3816002?q=80&w=2070&auto=format&fit=crop" 
             class="w-full h-full object-cover opacity-25" alt="Resort Background">
        <div class="absolute inset-0 bg-gradient-to-t from-navy via-navy/90 to-navy/70"></div>
    </div>

    <!-- Centered Maintenance Card -->
    <div class="relative z-10 max-w-xl w-full mx-auto text-center p-8 sm:p-12 rounded-3xl bg-white/5 backdrop-blur-2xl border border-white/10 shadow-2xl">
        
        <!-- Logo Badge -->
        <div class="w-20 h-20 rounded-2xl bg-white p-2.5 mx-auto mb-6 shadow-2xl border border-gold/40 flex items-center justify-center">
            <img src="<?= htmlspecialchars($logo_url) ?>" alt="Brand Logo" class="w-full h-full object-contain">
        </div>

        <div class="inline-flex items-center gap-2 px-3.5 py-1 rounded-full bg-amber-500/10 border border-amber-400/30 text-amber-300 text-xs font-semibold uppercase tracking-wider mb-4">
            <span class="w-2 h-2 rounded-full bg-amber-400 animate-ping"></span>
            System Upgrades in Progress
        </div>

        <h1 class="text-3xl sm:text-4xl font-display font-bold text-white mb-3">
            <?= htmlspecialchars($m_title) ?>
        </h1>

        <p class="text-gray-300 text-sm sm:text-base leading-relaxed mb-6 font-light">
            <?= htmlspecialchars($m_message) ?>
        </p>

        <!-- Status & Estimate Box -->
        <div class="p-4 rounded-2xl bg-white/5 border border-white/10 mb-8 max-w-md mx-auto text-xs space-y-2">
            <div class="flex items-center justify-between text-gray-400">
                <span>Estimated Restoration:</span>
                <span class="text-gold font-bold"><?= htmlspecialchars($m_end) ?></span>
            </div>
            <div class="flex items-center justify-between text-gray-400">
                <span>Emergency Front Desk:</span>
                <span class="text-white font-medium"><?= htmlspecialchars($m_phone) ?></span>
            </div>
            <div class="flex items-center justify-between text-gray-400">
                <span>Guest Concierge:</span>
                <span class="text-white font-medium"><?= htmlspecialchars($m_email) ?></span>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
            <a href="tel:<?= preg_replace('/[^0-9+]/', '', $m_phone) ?>" 
               class="w-full sm:w-auto px-6 py-3 rounded-xl bg-white/10 hover:bg-white/20 text-white font-semibold text-xs sm:text-sm border border-white/20 transition-all flex items-center justify-center gap-2">
                <span class="material-symbols-outlined text-[18px] text-gold">call</span>
                <span>Call Front Desk</span>
            </a>

            <a href="<?= htmlspecialchars($login_url) ?>" 
               class="w-full sm:w-auto px-6 py-3 rounded-xl gold-gradient-btn text-navy font-bold text-xs sm:text-sm hover:scale-[1.02] transition-all shadow-md flex items-center justify-center gap-2">
                <span class="material-symbols-outlined text-[18px]">admin_panel_settings</span>
                <span>Staff Portal Access</span>
            </a>
        </div>

        <div class="mt-8 pt-6 border-t border-white/10 text-[11px] text-gray-500">
            &copy; <?= date('Y') ?> <?= htmlspecialchars($hotel_name) ?> • All Rights Reserved.
        </div>
    </div>

</body>
</html>
