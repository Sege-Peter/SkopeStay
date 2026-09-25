<?php
$page_title = "System Settings & Theme Configuration | SkopeStay";
require_once __DIR__ . '/../includes/config.php';

// Authentication & Permission Check
if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$current_username = $_SESSION['username'] ?? 'Administrator';
$current_role = get_user_primary_role($pdo, $user_id);

// Load all live settings from database
$settings = get_all_settings($pdo);

// Staff Users for User Management tab
$staff = $pdo->query("SELECT u.id, u.username, u.email, u.created_at, COALESCE(r.name, u.role, 'Staff') as primary_role 
                      FROM users u 
                      LEFT JOIN user_roles ur ON u.id = ur.user_id 
                      LEFT JOIN roles r ON ur.role_id = r.id 
                      GROUP BY u.id 
                      ORDER BY u.id ASC")->fetchAll(PDO::FETCH_ASSOC);

include __DIR__ . '/../includes/head.php';
?>
<!DOCTYPE html>
<html class="light" lang="en">
<head>
    <style>
        .settings-tab.active {
            background-color: white;
            border-color: var(--theme-secondary, #D4AF37) !important;
            box-shadow: 0 4px 14px -2px rgba(212, 175, 55, 0.2);
        }
        .dark .settings-tab.active {
            background-color: #1f2937;
            border-color: var(--theme-secondary, #D4AF37) !important;
        }
        .palette-card.active {
            border-color: var(--theme-secondary, #D4AF37) !important;
            transform: scale(1.02);
            box-shadow: 0 0 15px rgba(212, 175, 55, 0.35);
        }
    </style>
</head>
<body class="bg-[#F8FAFC] dark:bg-gray-950 text-gray-800 dark:text-gray-100 font-sans-ui min-h-screen selection:bg-gold/30 selection:text-navy">

<?php include __DIR__ . '/../includes/sidebar.php'; ?>

<main class="md:ml-64 min-h-screen flex flex-col transition-all duration-300">

<?php include __DIR__ . '/../includes/header.php'; ?>

<!-- Content Container -->
<div class="flex-1 p-4 sm:p-6 lg:p-8 space-y-6 max-w-[1600px] mx-auto w-full">

    <!-- Header & Breadcrumbs -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 pb-2 border-b border-gray-200 dark:border-gray-800">
        <div>
            <div class="flex items-center gap-2 text-xs font-semibold text-gray-400 mb-1">
                <span>Enterprise Suite</span>
                <span>•</span>
                <span class="text-navy dark:text-gold">Configuration & System Control</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-luxury font-bold text-gray-900 dark:text-white tracking-tight">
                Global System <span class="text-navy dark:text-gold">Settings</span>
            </h1>
            <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 mt-0.5">
                Customize property branding, live theme color palettes, contact coordinates, and public maintenance guards.
            </p>
        </div>

        <div class="flex items-center gap-2.5">
            <?php if (($settings['maintenance_mode'] ?? '0') === '1'): ?>
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-rose-100 dark:bg-rose-950 text-rose-700 dark:text-rose-300 text-xs font-bold border border-rose-300 animate-pulse">
                    <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                    Maintenance Mode Active
                </span>
            <?php else: ?>
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-emerald-100 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-300 text-xs font-bold border border-emerald-300">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    Public Site Live
                </span>
            <?php endif; ?>
        </div>
    </div>

    <!-- Layout Grid: Left Tabs / Right Panels -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

        <!-- =================================================================
             NAVIGATION TABS (Left Column)
             ================================================================= -->
        <div class="lg:col-span-3 space-y-2">
            <?php
            $tabs = [
                ['id' => 'theme',        'icon' => 'palette',         'label' => 'Theme & Color Styling', 'desc' => 'Site-wide brand palette'],
                ['id' => 'general',      'icon' => 'apartment',       'label' => 'Property & Contacts',   'desc' => 'Name, phones, address, tax'],
                ['id' => 'maintenance',  'icon' => 'construction',    'label' => 'Maintenance Guard',    'desc' => 'Downtime screen & VIP bypass'],
                ['id' => 'operations',   'icon' => 'room_service',    'label' => 'Hospitality Rules',     'desc' => 'Check-in, POS, bookings'],
                ['id' => 'users',        'icon' => 'manage_accounts', 'label' => 'Staff Directory',       'desc' => 'RBAC & user accounts'],
                ['id' => 'notifications','icon' => 'notifications',   'label' => 'Alerts & Email',        'desc' => 'Automated event triggers']
            ];
            foreach ($tabs as $i => $tab): ?>
            <button type="button" 
                    onclick="switchSettingsTab('<?= $tab['id'] ?>')" 
                    id="tab-btn-<?= $tab['id'] ?>"
                    class="settings-tab w-full flex items-center justify-between p-3.5 sm:p-4 rounded-2xl border text-left transition-all duration-200 group <?= $i === 0 ? 'active' : 'bg-white dark:bg-gray-900 border-gray-200 dark:border-gray-800 hover:border-gold/50' ?>">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-9 h-9 rounded-xl bg-gray-100 dark:bg-gray-800 flex items-center justify-center text-gray-600 dark:text-gray-300 group-hover:bg-gold/20 group-hover:text-navy transition-colors shrink-0">
                        <span class="material-symbols-outlined text-[20px]"><?= $tab['icon'] ?></span>
                    </div>
                    <div class="min-w-0">
                        <div class="text-xs sm:text-sm font-bold text-gray-900 dark:text-white truncate">
                            <?= $tab['label'] ?>
                        </div>
                        <div class="text-[11px] text-gray-400 truncate">
                            <?= $tab['desc'] ?>
                        </div>
                    </div>
                </div>
                <span class="material-symbols-outlined text-[18px] text-gray-400 group-hover:translate-x-0.5 transition-transform shrink-0">chevron_right</span>
            </button>
            <?php endforeach; ?>
        </div>

        <!-- =================================================================
             SETTINGS PANELS (Right Column)
             ================================================================= -->
        <div class="lg:col-span-9 bg-white dark:bg-gray-900 rounded-3xl border border-gray-200/80 dark:border-gray-800 shadow-sm overflow-hidden">

            <!-- -------------------------------------------------------------
                 PANEL 1: THEME & COLOR STYLING ACROSS ALL SITE
                 ------------------------------------------------------------- -->
            <div id="panel-theme" class="settings-panel p-6 sm:p-8 space-y-6">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 pb-5 border-b border-gray-100 dark:border-gray-800">
                    <div>
                        <h2 class="text-xl font-luxury font-bold text-gray-900 dark:text-white flex items-center gap-2">
                            <span class="material-symbols-outlined text-gold">palette</span>
                            Theme & Color Settings Across All Site
                        </h2>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                            Colors selected here automatically propagate across the public landing page, booking engine, login portal, and ERP management modules.
                        </p>
                    </div>
                    <button type="button" onclick="submitSettingsForm('theme-form', 'theme')" 
                            class="px-5 py-2.5 rounded-xl gold-gradient-bg text-navy font-bold text-xs sm:text-sm shadow-md hover:scale-[1.02] active:scale-95 transition-all flex items-center gap-2">
                        <span class="material-symbols-outlined text-[18px]">check</span>
                        Save Theme Colors
                    </button>
                </div>

                <form id="theme-form" class="space-y-6">
                    <input type="hidden" name="group" value="theme">

                    <!-- Theme Preset Cards Grid -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-3">
                            Luxury Color Presets (1-Click Switch)
                        </label>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3.5">
                            
                            <!-- Preset 1: Luxury Navy & Gold (Default) -->
                            <div onclick="selectPreset('navy_gold', '#0B132B', '#D4AF37', '#10B981')" 
                                 class="palette-card p-3.5 rounded-2xl border-2 cursor-pointer transition-all bg-gray-50 dark:bg-gray-800 <?= ($settings['theme_palette'] ?? 'navy_gold') === 'navy_gold' ? 'active' : 'border-gray-200 dark:border-gray-700' ?>" data-preset="navy_gold">
                                <div class="flex items-center gap-2 mb-2">
                                    <span class="w-5 h-5 rounded-full shadow-sm" style="background:#0B132B"></span>
                                    <span class="w-5 h-5 rounded-full shadow-sm" style="background:#D4AF37"></span>
                                    <span class="w-5 h-5 rounded-full shadow-sm" style="background:#10B981"></span>
                                </div>
                                <div class="text-xs font-bold text-gray-900 dark:text-white">Luxury Navy & Gold</div>
                                <div class="text-[10px] text-gray-400">Default 5-Star Hotel Resort</div>
                            </div>

                            <!-- Preset 2: Royal Emerald & Gold -->
                            <div onclick="selectPreset('emerald_gold', '#064E3B', '#D4AF37', '#059669')" 
                                 class="palette-card p-3.5 rounded-2xl border-2 cursor-pointer transition-all bg-gray-50 dark:bg-gray-800 <?= ($settings['theme_palette'] ?? '') === 'emerald_gold' ? 'active' : 'border-gray-200 dark:border-gray-700' ?>" data-preset="emerald_gold">
                                <div class="flex items-center gap-2 mb-2">
                                    <span class="w-5 h-5 rounded-full shadow-sm" style="background:#064E3B"></span>
                                    <span class="w-5 h-5 rounded-full shadow-sm" style="background:#D4AF37"></span>
                                    <span class="w-5 h-5 rounded-full shadow-sm" style="background:#059669"></span>
                                </div>
                                <div class="text-xs font-bold text-gray-900 dark:text-white">Royal Emerald & Gold</div>
                                <div class="text-[10px] text-gray-400">Botanical Garden & Safari Retreat</div>
                            </div>

                            <!-- Preset 3: Imperial Burgundy & Bronze -->
                            <div onclick="selectPreset('burgundy_bronze', '#4A0E17', '#D97706', '#B45309')" 
                                 class="palette-card p-3.5 rounded-2xl border-2 cursor-pointer transition-all bg-gray-50 dark:bg-gray-800 <?= ($settings['theme_palette'] ?? '') === 'burgundy_bronze' ? 'active' : 'border-gray-200 dark:border-gray-700' ?>" data-preset="burgundy_bronze">
                                <div class="flex items-center gap-2 mb-2">
                                    <span class="w-5 h-5 rounded-full shadow-sm" style="background:#4A0E17"></span>
                                    <span class="w-5 h-5 rounded-full shadow-sm" style="background:#D97706"></span>
                                    <span class="w-5 h-5 rounded-full shadow-sm" style="background:#B45309"></span>
                                </div>
                                <div class="text-xs font-bold text-gray-900 dark:text-white">Imperial Burgundy & Bronze</div>
                                <div class="text-[10px] text-gray-400">Vineyard Estate & Private Club</div>
                            </div>

                            <!-- Preset 4: Ocean Azure & Gold -->
                            <div onclick="selectPreset('azure_gold', '#0F172A', '#38BDF8', '#F59E0B')" 
                                 class="palette-card p-3.5 rounded-2xl border-2 cursor-pointer transition-all bg-gray-50 dark:bg-gray-800 <?= ($settings['theme_palette'] ?? '') === 'azure_gold' ? 'active' : 'border-gray-200 dark:border-gray-700' ?>" data-preset="azure_gold">
                                <div class="flex items-center gap-2 mb-2">
                                    <span class="w-5 h-5 rounded-full shadow-sm" style="background:#0F172A"></span>
                                    <span class="w-5 h-5 rounded-full shadow-sm" style="background:#38BDF8"></span>
                                    <span class="w-5 h-5 rounded-full shadow-sm" style="background:#F59E0B"></span>
                                </div>
                                <div class="text-xs font-bold text-gray-900 dark:text-white">Ocean Azure & Sky</div>
                                <div class="text-[10px] text-gray-400">Coastal Beachfront & Yacht Club</div>
                            </div>

                            <!-- Preset 5: Obsidian & Platinum -->
                            <div onclick="selectPreset('obsidian_platinum', '#18181B', '#A1A1AA', '#6366F1')" 
                                 class="palette-card p-3.5 rounded-2xl border-2 cursor-pointer transition-all bg-gray-50 dark:bg-gray-800 <?= ($settings['theme_palette'] ?? '') === 'obsidian_platinum' ? 'active' : 'border-gray-200 dark:border-gray-700' ?>" data-preset="obsidian_platinum">
                                <div class="flex items-center gap-2 mb-2">
                                    <span class="w-5 h-5 rounded-full shadow-sm" style="background:#18181B"></span>
                                    <span class="w-5 h-5 rounded-full shadow-sm" style="background:#A1A1AA"></span>
                                    <span class="w-5 h-5 rounded-full shadow-sm" style="background:#6366F1"></span>
                                </div>
                                <div class="text-xs font-bold text-gray-900 dark:text-white">Obsidian & Platinum</div>
                                <div class="text-[10px] text-gray-400">Ultra-Modern Minimalist City Hotel</div>
                            </div>

                            <!-- Preset 6: Custom Hex Wheels -->
                            <div onclick="selectPreset('custom', null, null, null)" 
                                 class="palette-card p-3.5 rounded-2xl border-2 cursor-pointer transition-all bg-gray-50 dark:bg-gray-800 <?= ($settings['theme_palette'] ?? '') === 'custom' ? 'active' : 'border-gray-200 dark:border-gray-700' ?>" data-preset="custom">
                                <div class="flex items-center gap-2 mb-2">
                                    <span class="material-symbols-outlined text-gold">colorize</span>
                                </div>
                                <div class="text-xs font-bold text-gray-900 dark:text-white">Custom Color Wheel</div>
                                <div class="text-[10px] text-gray-400">Choose exact HEX codes below</div>
                            </div>
                        </div>
                        <input type="hidden" name="theme_palette" id="theme_palette_input" value="<?= htmlspecialchars($settings['theme_palette'] ?? 'navy_gold') ?>">
                    </div>

                    <!-- Custom Hex Color Pickers -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-4 border-t border-gray-100 dark:border-gray-800">
                        <!-- Primary Color -->
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">
                                Primary Brand Color
                            </label>
                            <div class="flex items-center gap-2">
                                <input type="color" id="primary_picker" value="<?= htmlspecialchars($settings['primary_color'] ?? '#0B132B') ?>" 
                                       class="w-10 h-10 rounded-xl cursor-pointer border border-gray-300 dark:border-gray-700 p-0.5 bg-white">
                                <input type="text" name="primary_color" id="primary_text" value="<?= htmlspecialchars($settings['primary_color'] ?? '#0B132B') ?>" 
                                       class="w-full px-3 py-2 rounded-xl border border-gray-200 dark:border-gray-700 dark:bg-gray-800 font-mono text-xs uppercase font-bold text-gray-800 dark:text-gray-200 outline-none focus:border-gold">
                            </div>
                            <p class="text-[10px] text-gray-400 mt-1">Used for navbars, primary hero sections, sidebar</p>
                        </div>

                        <!-- Secondary / Gold Color -->
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">
                                Secondary / Accent Gold
                            </label>
                            <div class="flex items-center gap-2">
                                <input type="color" id="secondary_picker" value="<?= htmlspecialchars($settings['secondary_color'] ?? '#D4AF37') ?>" 
                                       class="w-10 h-10 rounded-xl cursor-pointer border border-gray-300 dark:border-gray-700 p-0.5 bg-white">
                                <input type="text" name="secondary_color" id="secondary_text" value="<?= htmlspecialchars($settings['secondary_color'] ?? '#D4AF37') ?>" 
                                       class="w-full px-3 py-2 rounded-xl border border-gray-200 dark:border-gray-700 dark:bg-gray-800 font-mono text-xs uppercase font-bold text-gray-800 dark:text-gray-200 outline-none focus:border-gold">
                            </div>
                            <p class="text-[10px] text-gray-400 mt-1">Used for CTAs, gold luxury badges, accents</p>
                        </div>

                        <!-- Action / Success Accent -->
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">
                                Highlights & Metric Accent
                            </label>
                            <div class="flex items-center gap-2">
                                <input type="color" id="accent_picker" value="<?= htmlspecialchars($settings['accent_color'] ?? '#10B981') ?>" 
                                       class="w-10 h-10 rounded-xl cursor-pointer border border-gray-300 dark:border-gray-700 p-0.5 bg-white">
                                <input type="text" name="accent_color" id="accent_text" value="<?= htmlspecialchars($settings['accent_color'] ?? '#10B981') ?>" 
                                       class="w-full px-3 py-2 rounded-xl border border-gray-200 dark:border-gray-700 dark:bg-gray-800 font-mono text-xs uppercase font-bold text-gray-800 dark:text-gray-200 outline-none focus:border-gold">
                            </div>
                            <p class="text-[10px] text-gray-400 mt-1">Used for verified pills and success indicators</p>
                        </div>
                    </div>

                    <!-- Live Component Preview Canvas -->
                    <div class="p-5 rounded-2xl border border-gray-200 dark:border-gray-800 bg-gray-50 dark:bg-gray-850 space-y-3">
                        <div class="flex justify-between items-center text-xs font-bold text-gray-500">
                            <span>LIVE COMPONENT PREVIEW CANVAS</span>
                            <span class="text-emerald-600 font-semibold flex items-center gap-1">
                                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                Real-Time Reactive Preview
                            </span>
                        </div>
                        
                        <div id="preview-box" class="p-5 rounded-xl border shadow-sm transition-all" style="background:#0B132B; color:white;">
                            <div class="flex items-center justify-between pb-3 border-b border-white/10">
                                <div class="font-luxury font-bold text-lg text-white">
                                    Skope<span id="preview-brand-accent" style="color:#D4AF37">Stay</span> Luxury Resort
                                </div>
                                <span id="preview-badge" class="px-2.5 py-0.5 rounded-full text-xs font-bold uppercase tracking-wider" style="background:#10B981; color:white;">
                                    Verified 5-Star
                                </span>
                            </div>
                            <div class="pt-4 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
                                <div>
                                    <div class="text-xs font-semibold text-gray-300">Presidential Beachfront Suite</div>
                                    <div id="preview-price" class="text-xl font-bold font-luxury" style="color:#D4AF37">KSh 58,000 / Night</div>
                                </div>
                                <button type="button" id="preview-cta" class="px-4 py-2 rounded-lg font-bold text-xs shadow" style="background:#D4AF37; color:#0B132B;">
                                    Instant Reservation
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <!-- -------------------------------------------------------------
                 PANEL 2: PROPERTY IDENTITY & CONTACT DETAILS
                 ------------------------------------------------------------- -->
            <div id="panel-general" class="settings-panel hidden p-6 sm:p-8 space-y-6">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 pb-5 border-b border-gray-100 dark:border-gray-800">
                    <div>
                        <h2 class="text-xl font-luxury font-bold text-gray-900 dark:text-white flex items-center gap-2">
                            <span class="material-symbols-outlined text-gold">apartment</span>
                            Property Name & Contact Details
                        </h2>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                            Configure legal company name, guest inquiry telephones, official address, and tax information.
                        </p>
                    </div>
                    <button type="button" onclick="submitSettingsForm('general-form', 'general')" 
                            class="px-5 py-2.5 rounded-xl gold-gradient-bg text-navy font-bold text-xs sm:text-sm shadow-md hover:scale-[1.02] active:scale-95 transition-all flex items-center gap-2">
                        <span class="material-symbols-outlined text-[18px]">check</span>
                        Save Property Details
                    </button>
                </div>

                <form id="general-form" class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <input type="hidden" name="group" value="general">

                    <div class="sm:col-span-2">
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">
                            Official Hotel / Resort Name *
                        </label>
                        <input type="text" name="hotel_name" value="<?= htmlspecialchars($settings['hotel_name'] ?? 'SkopeStay Resort & Suites') ?>" required 
                               class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 dark:bg-gray-800 font-semibold text-sm text-gray-900 dark:text-white outline-none focus:border-gold">
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">
                            Brand Tagline / Slogan
                        </label>
                        <input type="text" name="hotel_tagline" value="<?= htmlspecialchars($settings['hotel_tagline'] ?? 'Where Pure Luxury Meets Intelligent Operations') ?>" 
                               class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 dark:bg-gray-800 text-sm text-gray-900 dark:text-white outline-none focus:border-gold">
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">
                            Primary Reception Phone *
                        </label>
                        <input type="text" name="contact_phone" value="<?= htmlspecialchars($settings['contact_phone'] ?? '+254 742 380 183') ?>" required 
                               class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 dark:bg-gray-800 text-sm text-gray-900 dark:text-white outline-none focus:border-gold">
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">
                            Concierge & Emergency Phone
                        </label>
                        <input type="text" name="contact_phone_alt" value="<?= htmlspecialchars($settings['contact_phone_alt'] ?? '+254 712 111 222') ?>" 
                               class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 dark:bg-gray-800 text-sm text-gray-900 dark:text-white outline-none focus:border-gold">
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">
                            Administrative Email *
                        </label>
                        <input type="email" name="contact_email" value="<?= htmlspecialchars($settings['contact_email'] ?? 'admin@skopestay.com') ?>" required 
                               class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 dark:bg-gray-800 text-sm text-gray-900 dark:text-white outline-none focus:border-gold">
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">
                            Guest Concierge & Support Email
                        </label>
                        <input type="email" name="support_email" value="<?= htmlspecialchars($settings['support_email'] ?? 'support@skopestay.com') ?>" 
                               class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 dark:bg-gray-800 text-sm text-gray-900 dark:text-white outline-none focus:border-gold">
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">
                            Physical Property Address *
                        </label>
                        <input type="text" name="contact_address" value="<?= htmlspecialchars($settings['contact_address'] ?? 'Mombasa Coastal Highway, Beachfront, Mombasa, Kenya') ?>" required 
                               class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 dark:bg-gray-800 text-sm text-gray-900 dark:text-white outline-none focus:border-gold">
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">
                            City & Country
                        </label>
                        <div class="grid grid-cols-2 gap-2">
                            <input type="text" name="contact_city" value="<?= htmlspecialchars($settings['contact_city'] ?? 'Mombasa') ?>" placeholder="City" 
                                   class="w-full px-3 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 dark:bg-gray-800 text-sm text-gray-900 dark:text-white outline-none focus:border-gold">
                            <input type="text" name="contact_country" value="<?= htmlspecialchars($settings['contact_country'] ?? 'Kenya') ?>" placeholder="Country" 
                                   class="w-full px-3 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 dark:bg-gray-800 text-sm text-gray-900 dark:text-white outline-none focus:border-gold">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">
                            Tax PIN / Registration Number
                        </label>
                        <input type="text" name="tax_pin" value="<?= htmlspecialchars($settings['tax_pin'] ?? 'P051283928X') ?>" 
                               class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 dark:bg-gray-800 font-mono text-sm text-gray-900 dark:text-white outline-none focus:border-gold">
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">
                            Operating Currency
                        </label>
                        <div class="grid grid-cols-2 gap-2">
                            <select name="currency_code" class="w-full px-3 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 dark:bg-gray-800 text-xs font-bold text-gray-900 dark:text-white outline-none focus:border-gold">
                                <option value="KES" <?= ($settings['currency_code'] ?? 'KES') === 'KES' ? 'selected' : '' ?>>KES (Kenya Shilling)</option>
                                <option value="USD" <?= ($settings['currency_code'] ?? '') === 'USD' ? 'selected' : '' ?>>USD (US Dollar)</option>
                                <option value="EUR" <?= ($settings['currency_code'] ?? '') === 'EUR' ? 'selected' : '' ?>>EUR (Euro)</option>
                                <option value="GBP" <?= ($settings['currency_code'] ?? '') === 'GBP' ? 'selected' : '' ?>>GBP (British Pound)</option>
                            </select>
                            <input type="text" name="currency_symbol" value="<?= htmlspecialchars($settings['currency_symbol'] ?? 'KSh') ?>" placeholder="Symbol" 
                                   class="w-full px-3 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 dark:bg-gray-800 text-sm font-bold text-gray-900 dark:text-white outline-none focus:border-gold">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">
                            Taxes & Service Charge (%)
                        </label>
                        <div class="grid grid-cols-2 gap-2">
                            <input type="number" step="0.1" name="tax_rate" value="<?= htmlspecialchars($settings['tax_rate'] ?? '16') ?>" placeholder="VAT %" 
                                   class="w-full px-3 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 dark:bg-gray-800 text-sm text-gray-900 dark:text-white outline-none focus:border-gold">
                            <input type="number" step="0.1" name="service_charge" value="<?= htmlspecialchars($settings['service_charge'] ?? '5') ?>" placeholder="Service %" 
                                   class="w-full px-3 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 dark:bg-gray-800 text-sm text-gray-900 dark:text-white outline-none focus:border-gold">
                        </div>
                    </div>
                </form>
            </div>

            <!-- -------------------------------------------------------------
                 PANEL 3: MAINTENANCE MODE GUARD & DOWNTIME SCREEN
                 ------------------------------------------------------------- -->
            <div id="panel-maintenance" class="settings-panel hidden p-6 sm:p-8 space-y-6">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 pb-5 border-b border-gray-100 dark:border-gray-800">
                    <div>
                        <h2 class="text-xl font-luxury font-bold text-gray-900 dark:text-white flex items-center gap-2">
                            <span class="material-symbols-outlined text-rose-500">construction</span>
                            Maintenance Mode Guard
                        </h2>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                            Safely restrict public website access during upgrades while granting staff and VIPs bypass access.
                        </p>
                    </div>
                    <button type="button" onclick="submitSettingsForm('maintenance-form', 'maintenance')" 
                            class="px-5 py-2.5 rounded-xl gold-gradient-bg text-navy font-bold text-xs sm:text-sm shadow-md hover:scale-[1.02] active:scale-95 transition-all flex items-center gap-2">
                        <span class="material-symbols-outlined text-[18px]">check</span>
                        Save Maintenance Settings
                    </button>
                </div>

                <form id="maintenance-form" class="space-y-6">
                    <input type="hidden" name="group" value="maintenance">

                    <!-- Big Interactive Master Switch -->
                    <div class="p-5 rounded-2xl border-2 <?= ($settings['maintenance_mode'] ?? '0') === '1' ? 'border-rose-400 bg-rose-50/50 dark:bg-rose-950/20' : 'border-gray-200 dark:border-gray-800 bg-gray-50 dark:bg-gray-850' ?> flex items-center justify-between gap-4">
                        <div class="flex items-center gap-3.5">
                            <div class="w-12 h-12 rounded-xl <?= ($settings['maintenance_mode'] ?? '0') === '1' ? 'bg-rose-100 text-rose-600' : 'bg-gray-200 dark:bg-gray-700 text-gray-600' ?> flex items-center justify-center shrink-0">
                                <span class="material-symbols-outlined text-2xl">shield_lock</span>
                            </div>
                            <div>
                                <h3 class="text-sm sm:text-base font-bold text-gray-900 dark:text-white">
                                    Global Maintenance Guard State
                                </h3>
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    When active, unauthenticated public visitors are immediately redirected to your luxury maintenance screen.
                                </p>
                            </div>
                        </div>

                        <!-- Toggle Switch -->
                        <label class="relative inline-flex items-center cursor-pointer shrink-0">
                            <input type="checkbox" name="maintenance_mode" value="1" <?= ($settings['maintenance_mode'] ?? '0') === '1' ? 'checked' : '' ?> class="sr-only peer">
                            <div class="w-14 h-7 bg-gray-300 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[4px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-rose-600"></div>
                        </label>
                    </div>

                    <!-- Notice Texts -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">
                                Public Downtime Heading
                            </label>
                            <input type="text" name="maintenance_title" value="<?= htmlspecialchars($settings['maintenance_title'] ?? 'Enhancing Your 5-Star Experience') ?>" required 
                                   class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 dark:bg-gray-800 text-sm text-gray-900 dark:text-white outline-none focus:border-gold">
                        </div>

                        <div class="sm:col-span-2">
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">
                                Detailed Visitor Notice
                            </label>
                            <textarea name="maintenance_message" rows="3" required 
                                      class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 dark:bg-gray-800 text-sm text-gray-900 dark:text-white outline-none focus:border-gold"><?= htmlspecialchars($settings['maintenance_message'] ?? 'We are currently performing scheduled maintenance to upgrade our digital concierge and reservation ecosystem.') ?></textarea>
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">
                                Estimated Downtime Duration
                            </label>
                            <input type="text" name="maintenance_estimated_end" value="<?= htmlspecialchars($settings['maintenance_estimated_end'] ?? 'Scheduled duration: approx. 45 minutes') ?>" 
                                   class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 dark:bg-gray-800 text-sm text-gray-900 dark:text-white outline-none focus:border-gold">
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">
                                Secret VIP / Staff Bypass Key
                            </label>
                            <div class="flex items-center gap-2">
                                <input type="text" name="maintenance_bypass_key" id="bypass_key_input" value="<?= htmlspecialchars($settings['maintenance_bypass_key'] ?? 'skope_vip_bypass_2026') ?>" 
                                       class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 dark:bg-gray-800 font-mono text-xs text-gray-900 dark:text-white outline-none focus:border-gold">
                                <button type="button" onclick="copyBypassLink()" class="px-3 py-2.5 rounded-xl bg-gray-100 dark:bg-gray-800 hover:bg-gold hover:text-navy text-gray-600 dark:text-gray-300 text-xs font-bold transition-colors shrink-0" title="Copy Bypass URL">
                                    <span class="material-symbols-outlined text-[18px]">content_copy</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <!-- -------------------------------------------------------------
                 PANEL 4: HOSPITALITY RULES & RESERVATION POLICIES
                 ------------------------------------------------------------- -->
            <div id="panel-operations" class="settings-panel hidden p-6 sm:p-8 space-y-6">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 pb-5 border-b border-gray-100 dark:border-gray-800">
                    <div>
                        <h2 class="text-xl font-luxury font-bold text-gray-900 dark:text-white flex items-center gap-2">
                            <span class="material-symbols-outlined text-gold">room_service</span>
                            Hospitality Operations & Reservation Rules
                        </h2>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                            Set check-in hours, cancellation windows, and enable or disable public booking engines.
                        </p>
                    </div>
                    <button type="button" onclick="submitSettingsForm('operations-form', 'operations')" 
                            class="px-5 py-2.5 rounded-xl gold-gradient-bg text-navy font-bold text-xs sm:text-sm shadow-md hover:scale-[1.02] active:scale-95 transition-all flex items-center gap-2">
                        <span class="material-symbols-outlined text-[18px]">check</span>
                        Save Rules
                    </button>
                </div>

                <form id="operations-form" class="space-y-6">
                    <input type="hidden" name="group" value="operations">

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">
                                Standard Check-In Time
                            </label>
                            <input type="time" name="checkin_time" value="<?= htmlspecialchars($settings['checkin_time'] ?? '14:00') ?>" 
                                   class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 dark:bg-gray-800 text-sm text-gray-900 dark:text-white outline-none focus:border-gold">
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">
                                Standard Check-Out Time
                            </label>
                            <input type="time" name="checkout_time" value="<?= htmlspecialchars($settings['checkout_time'] ?? '11:00') ?>" 
                                   class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 dark:bg-gray-800 text-sm text-gray-900 dark:text-white outline-none focus:border-gold">
                        </div>
                    </div>

                    <!-- Operational Service Toggles -->
                    <div class="pt-4 border-t border-gray-100 dark:border-gray-800 space-y-3">
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-2">
                            Public Modules Availability
                        </label>

                        <label class="flex items-center justify-between p-3.5 rounded-xl bg-gray-50 dark:bg-gray-800/60 border border-gray-200 dark:border-gray-700 cursor-pointer">
                            <div>
                                <div class="text-xs font-bold text-gray-900 dark:text-white">Enable Online Suite Bookings</div>
                                <div class="text-[11px] text-gray-400">Allows public guests to book rooms from the homepage.</div>
                            </div>
                            <input type="checkbox" name="enable_online_booking" value="1" <?= ($settings['enable_online_booking'] ?? '1') === '1' ? 'checked' : '' ?> class="w-5 h-5 rounded text-navy accent-navy cursor-pointer">
                        </label>

                        <label class="flex items-center justify-between p-3.5 rounded-xl bg-gray-50 dark:bg-gray-800/60 border border-gray-200 dark:border-gray-700 cursor-pointer">
                            <div>
                                <div class="text-xs font-bold text-gray-900 dark:text-white">Enable Dining & Restaurant POS</div>
                                <div class="text-[11px] text-gray-400">Accept table orders, room service, and kitchen tickets.</div>
                            </div>
                            <input type="checkbox" name="enable_restaurant_pos" value="1" <?= ($settings['enable_restaurant_pos'] ?? '1') === '1' ? 'checked' : '' ?> class="w-5 h-5 rounded text-navy accent-navy cursor-pointer">
                        </label>

                        <label class="flex items-center justify-between p-3.5 rounded-xl bg-gray-50 dark:bg-gray-800/60 border border-gray-200 dark:border-gray-700 cursor-pointer">
                            <div>
                                <div class="text-xs font-bold text-gray-900 dark:text-white">Enable Infinity Pool Day Passes</div>
                                <div class="text-[11px] text-gray-400">Allow visitor pass issuance and leisure club tracking.</div>
                            </div>
                            <input type="checkbox" name="enable_pool_passes" value="1" <?= ($settings['enable_pool_passes'] ?? '1') === '1' ? 'checked' : '' ?> class="w-5 h-5 rounded text-navy accent-navy cursor-pointer">
                        </label>

                        <label class="flex items-center justify-between p-3.5 rounded-xl bg-gray-50 dark:bg-gray-800/60 border border-gray-200 dark:border-gray-700 cursor-pointer">
                            <div>
                                <div class="text-xs font-bold text-gray-900 dark:text-white">Enable Banquet & Conference Inquiries</div>
                                <div class="text-[11px] text-gray-400">Receive MICE conference space and ballroom booking requests.</div>
                            </div>
                            <input type="checkbox" name="enable_hall_booking" value="1" <?= ($settings['enable_hall_booking'] ?? '1') === '1' ? 'checked' : '' ?> class="w-5 h-5 rounded text-navy accent-navy cursor-pointer">
                        </label>
                    </div>
                </form>
            </div>

            <!-- -------------------------------------------------------------
                 PANEL 5: STAFF RBAC & USER DIRECTORY
                 ------------------------------------------------------------- -->
            <div id="panel-users" class="settings-panel hidden p-6 sm:p-8 space-y-6">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 pb-5 border-b border-gray-100 dark:border-gray-800">
                    <div>
                        <h2 class="text-xl font-luxury font-bold text-gray-900 dark:text-white flex items-center gap-2">
                            <span class="material-symbols-outlined text-gold">manage_accounts</span>
                            Staff & User Directory
                        </h2>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                            Manage user logins and assign permissions via the Enterprise RBAC Matrix.
                        </p>
                    </div>
                    <a href="user_management.php" 
                       class="px-5 py-2.5 rounded-xl bg-navy text-white font-bold text-xs sm:text-sm hover:bg-navy-light transition-all shadow-md flex items-center gap-2">
                        <span class="material-symbols-outlined text-[18px] text-gold">admin_panel_settings</span>
                        Manage RBAC Matrix
                    </a>
                </div>

                <div class="overflow-x-auto rounded-2xl border border-gray-200 dark:border-gray-800">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-gray-50 dark:bg-gray-800/60 border-b border-gray-200 dark:border-gray-700 font-bold text-gray-600 dark:text-gray-300 uppercase">
                            <tr>
                                <th class="p-3.5">User Identity</th>
                                <th class="p-3.5">Assigned Role</th>
                                <th class="p-3.5">Created Date</th>
                                <th class="p-3.5 text-right">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                            <?php foreach ($staff as $u): ?>
                            <tr class="hover:bg-gray-50/50 dark:hover:bg-gray-800/40 transition-colors">
                                <td class="p-3.5">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-gold/20 text-navy dark:text-gold flex items-center justify-center font-bold text-xs">
                                            <?= strtoupper(substr($u['username'], 0, 1)) ?>
                                        </div>
                                        <div>
                                            <div class="font-bold text-gray-900 dark:text-white"><?= htmlspecialchars($u['username']) ?></div>
                                            <div class="text-[11px] text-gray-400"><?= htmlspecialchars($u['email']) ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="p-3.5">
                                    <span class="px-2.5 py-1 rounded-full bg-navy/10 dark:bg-white/10 text-navy dark:text-gold font-bold text-[11px]">
                                        <?= htmlspecialchars($u['primary_role']) ?>
                                    </span>
                                </td>
                                <td class="p-3.5 text-gray-500">
                                    <?= date('M j, Y', strtotime($u['created_at'])) ?>
                                </td>
                                <td class="p-3.5 text-right">
                                    <span class="px-2 py-0.5 rounded-full bg-emerald-100 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-300 font-bold text-[10px]">
                                        ACTIVE
                                    </span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- -------------------------------------------------------------
                 PANEL 6: NOTIFICATIONS & AUTOMATED EMAIL ALERTS
                 ------------------------------------------------------------- -->
            <div id="panel-notifications" class="settings-panel hidden p-6 sm:p-8 space-y-6">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 pb-5 border-b border-gray-100 dark:border-gray-800">
                    <div>
                        <h2 class="text-xl font-luxury font-bold text-gray-900 dark:text-white flex items-center gap-2">
                            <span class="material-symbols-outlined text-gold">notifications</span>
                            Automated Alerts & Notifications
                        </h2>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                            Determine which operational events trigger alerts and emails to the front desk.
                        </p>
                    </div>
                    <button type="button" onclick="submitSettingsForm('notifications-form', 'notifications')" 
                            class="px-5 py-2.5 rounded-xl gold-gradient-bg text-navy font-bold text-xs sm:text-sm shadow-md hover:scale-[1.02] active:scale-95 transition-all flex items-center gap-2">
                        <span class="material-symbols-outlined text-[18px]">check</span>
                        Save Notification Rules
                    </button>
                </div>

                <form id="notifications-form" class="space-y-5">
                    <input type="hidden" name="group" value="notifications">

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 dark:text-gray-300 mb-1.5">
                            Notification Recipient Email *
                        </label>
                        <input type="email" name="notify_email_recipient" value="<?= htmlspecialchars($settings['notify_email_recipient'] ?? 'admin@skopestay.com') ?>" required 
                               class="w-full px-4 py-2.5 rounded-xl border border-gray-200 dark:border-gray-700 dark:bg-gray-800 text-sm text-gray-900 dark:text-white outline-none focus:border-gold">
                    </div>

                    <div class="space-y-3 pt-2">
                        <label class="flex items-center justify-between p-3.5 rounded-xl bg-gray-50 dark:bg-gray-800/60 border border-gray-200 dark:border-gray-700 cursor-pointer">
                            <div>
                                <div class="text-xs font-bold text-gray-900 dark:text-white">New Room Booking Alerts</div>
                                <div class="text-[11px] text-gray-400">Trigger instant alert upon guest online reservation.</div>
                            </div>
                            <input type="checkbox" name="notify_new_booking" value="1" <?= ($settings['notify_new_booking'] ?? '1') === '1' ? 'checked' : '' ?> class="w-5 h-5 rounded text-navy accent-navy cursor-pointer">
                        </label>

                        <label class="flex items-center justify-between p-3.5 rounded-xl bg-gray-50 dark:bg-gray-800/60 border border-gray-200 dark:border-gray-700 cursor-pointer">
                            <div>
                                <div class="text-xs font-bold text-gray-900 dark:text-white">Maintenance Work Order Dispatches</div>
                                <div class="text-[11px] text-gray-400">Alert engineering staff when a new ticket is logged.</div>
                            </div>
                            <input type="checkbox" name="notify_maintenance" value="1" <?= ($settings['notify_maintenance'] ?? '1') === '1' ? 'checked' : '' ?> class="w-5 h-5 rounded text-navy accent-navy cursor-pointer">
                        </label>

                        <label class="flex items-center justify-between p-3.5 rounded-xl bg-gray-50 dark:bg-gray-800/60 border border-gray-200 dark:border-gray-700 cursor-pointer">
                            <div>
                                <div class="text-xs font-bold text-gray-900 dark:text-white">Low Inventory Stock Warning</div>
                                <div class="text-[11px] text-gray-400">Notify SCM team when stock levels breach minimum thresholds.</div>
                            </div>
                            <input type="checkbox" name="notify_low_stock" value="1" <?= ($settings['notify_low_stock'] ?? '1') === '1' ? 'checked' : '' ?> class="w-5 h-5 rounded text-navy accent-navy cursor-pointer">
                        </label>
                    </div>
                </form>
            </div>

        </div><!-- end panels container -->

    </div>

</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
</main>

<script>
// ---- Tab Navigation ----
function switchSettingsTab(tabId) {
    document.querySelectorAll('.settings-panel').forEach(p => p.classList.add('hidden'));
    document.querySelectorAll('.settings-tab').forEach(t => t.classList.remove('active'));

    const targetPanel = document.getElementById('panel-' + tabId);
    const targetTabBtn = document.getElementById('tab-btn-' + tabId);
    
    if (targetPanel) targetPanel.classList.remove('hidden');
    if (targetTabBtn) targetTabBtn.classList.add('active');
}

// ---- Live Color Picker Synchronizers ----
const primaryPicker = document.getElementById('primary_picker');
const primaryText   = document.getElementById('primary_text');
const secondaryPicker = document.getElementById('secondary_picker');
const secondaryText   = document.getElementById('secondary_text');
const accentPicker  = document.getElementById('accent_picker');
const accentText    = document.getElementById('accent_text');

function updateLivePreview() {
    const p = primaryText.value;
    const s = secondaryText.value;
    const a = accentText.value;

    const box = document.getElementById('preview-box');
    const brand = document.getElementById('preview-brand-accent');
    const badge = document.getElementById('preview-badge');
    const price = document.getElementById('preview-price');
    const cta   = document.getElementById('preview-cta');

    if (box) box.style.backgroundColor = p;
    if (brand) brand.style.color = s;
    if (badge) badge.style.backgroundColor = a;
    if (price) price.style.color = s;
    if (cta) {
        cta.style.backgroundColor = s;
        cta.style.color = p;
    }
}

// Bind Color inputs
if (primaryPicker && primaryText) {
    primaryPicker.addEventListener('input', e => { primaryText.value = e.target.value.toUpperCase(); updateLivePreview(); });
    primaryText.addEventListener('input', e => { primaryPicker.value = e.target.value; updateLivePreview(); });
}
if (secondaryPicker && secondaryText) {
    secondaryPicker.addEventListener('input', e => { secondaryText.value = e.target.value.toUpperCase(); updateLivePreview(); });
    secondaryText.addEventListener('input', e => { secondaryPicker.value = e.target.value; updateLivePreview(); });
}
if (accentPicker && accentText) {
    accentPicker.addEventListener('input', e => { accentText.value = e.target.value.toUpperCase(); updateLivePreview(); });
    accentText.addEventListener('input', e => { accentPicker.value = e.target.value; updateLivePreview(); });
}

// Preset Selector
function selectPreset(presetKey, p, s, a) {
    document.querySelectorAll('.palette-card').forEach(c => c.classList.remove('active'));
    const clicked = document.querySelector(`.palette-card[data-preset="${presetKey}"]`);
    if (clicked) clicked.classList.add('active');

    document.getElementById('theme_palette_input').value = presetKey;

    if (p && s && a) {
        primaryPicker.value = p;
        primaryText.value = p;
        secondaryPicker.value = s;
        secondaryText.value = s;
        accentPicker.value = a;
        accentText.value = a;
        updateLivePreview();
    }
}

// Copy Maintenance Bypass URL
function copyBypassLink() {
    const key = document.getElementById('bypass_key_input')?.value || 'skope_vip_bypass_2026';
    const origin = window.location.origin;
    const url = `${origin}/skopestay/?bypass=${key}`;
    navigator.clipboard.writeText(url).then(() => {
        Swal.fire({
            icon: 'success',
            title: 'Bypass URL Copied!',
            text: url,
            timer: 2200,
            showConfirmButton: false,
            toast: true,
            position: 'top-end'
        });
    });
}

// Submit Any Form via Async API
async function submitSettingsForm(formId, groupName) {
    const form = document.getElementById(formId);
    if (!form) return;

    const formData = new FormData(form);
    const data = {};

    // Gather text and check inputs
    form.querySelectorAll('input, select, textarea').forEach(el => {
        if (!el.name) return;
        if (el.type === 'checkbox') {
            data[el.name] = el.checked ? '1' : '0';
        } else {
            data[el.name] = el.value;
        }
    });

    data.group = groupName;

    Swal.fire({
        title: 'Applying Configuration...',
        text: 'Saving changes to persistent database and updating site-wide themes.',
        allowOutsideClick: false,
        didOpen: () => Swal.showLoading()
    });

    try {
        const res = await fetch('../api/settings.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        });
        const json = await res.json();

        if (json.success) {
            Swal.fire({
                icon: 'success',
                title: 'Settings Saved & Applied!',
                text: json.message || 'Configuration updated successfully.',
                timer: 1600,
                showConfirmButton: false
            }).then(() => {
                location.reload();
            });
        } else {
            Swal.fire('Configuration Error', json.message || 'Failed to save settings.', 'error');
        }
    } catch (err) {
        Swal.fire('Network Error', 'Unable to reach the server. Please check your connection.', 'error');
    }
}

// Initial preview setup on page load
updateLivePreview();
</script>

</body>
</html>
