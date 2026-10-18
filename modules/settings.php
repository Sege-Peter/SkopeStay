<?php
$page_title = "System Settings | SkopeStay";
require_once __DIR__ . '/../includes/config.php';

$user_id = $_SESSION['user_id'];
$settings = get_all_settings($pdo);
$s = fn($k, $d = '') => $settings[$k] ?? $d;
$pal = brand_palette();

// Staff Users for the directory tab
$staff = $pdo->query("SELECT u.id, u.username, u.full_name, u.email, u.created_at, u.last_login, u.is_active,
                             COALESCE(r.name, u.role, 'Staff') AS primary_role
                      FROM users u
                      LEFT JOIN user_roles ur ON u.id = ur.user_id
                      LEFT JOIN roles r ON ur.role_id = r.id
                      GROUP BY u.id
                      ORDER BY u.id ASC")->fetchAll(PDO::FETCH_ASSOC);

// System facts for the System tab
$db_size = $pdo->query("SELECT ROUND(SUM(data_length + index_length) / 1048576, 2) FROM information_schema.tables WHERE table_schema = DATABASE()")->fetchColumn();
$table_count = $pdo->query("SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = DATABASE()")->fetchColumn();
$last_changes = $pdo->query("SELECT a.details, a.created_at, u.username FROM audit_logs a LEFT JOIN users u ON u.id = a.user_id
                             WHERE a.module = 'Settings' ORDER BY a.id DESC LIMIT 6")->fetchAll();

$presets = [
    'navy_gold'         => ['Luxury Navy & Gold', 'Classic 5-star resort', '#0B132B', '#D4AF37', '#10B981'],
    'emerald_gold'      => ['Royal Emerald & Gold', 'Garden & safari retreat', '#064E3B', '#D4AF37', '#059669'],
    'burgundy_bronze'   => ['Burgundy & Bronze', 'Vineyard & private club', '#4A0E17', '#D97706', '#B45309'],
    'azure_gold'        => ['Ocean Azure & Sky', 'Beachfront & yacht club', '#0F172A', '#38BDF8', '#F59E0B'],
    'obsidian_platinum' => ['Obsidian & Platinum', 'Minimalist city hotel', '#18181B', '#A1A1AA', '#6366F1'],
    'safari_sand'       => ['Safari Sand', 'Lodge & bush camp', '#3F2E1E', '#E0B872', '#7C9A3D'],
    'lagoon_coral'      => ['Lagoon & Coral', 'Tropical island resort', '#0E3B43', '#FF7F50', '#2DD4BF'],
    'royal_plum'        => ['Royal Plum', 'Boutique & spa', '#2E1065', '#F0ABFC', '#A855F7'],
];

$tabs = [
    ['branding',      'badge',            'Branding & Logo',     'Name, logo, favicon'],
    ['theme',         'palette',          'Theme Colours',       'Presets & custom colours'],
    ['general',       'apartment',        'Property & Contacts', 'Phones, address, tax'],
    ['website',       'public',           'Website & Social',    'Footer, SEO, social links'],
    ['operations',    'room_service',     'Hospitality Rules',   'Check-in, modules'],
    ['billing',       'receipt_long',     'Billing & Receipts',  'Currency, tax, receipts'],
    ['maintenance',   'construction',     'Maintenance Mode',    'Downtime screen'],
    ['notifications', 'notifications',    'Alerts & Email',      'Event triggers'],
    ['security',      'shield_lock',      'Security & Access',   'Sign-in, sessions'],
    ['users',         'manage_accounts',  'Staff Directory',     'Accounts & roles'],
    ['system',        'dns',              'System & Backup',     'Database backup, info'],
];

$icons = ['apartment', 'hotel', 'villa', 'cottage', 'house', 'beach_access', 'pool', 'spa', 'forest', 'landscape', 'castle', 'diamond', 'star', 'king_bed', 'luggage', 'restaurant'];

// Small field helpers keep the markup consistent
function field_text($name, $label, $value, $opts = []) {
    $type = $opts['type'] ?? 'text';
    $req = !empty($opts['required']) ? 'required' : '';
    $span = !empty($opts['wide']) ? 'sm:col-span-2' : '';
    $extra = $opts['attrs'] ?? '';
    $help = !empty($opts['help']) ? '<p class="text-[11px] text-gray-400 mt-1">' . e($opts['help']) . '</p>' : '';
    $ph = isset($opts['placeholder']) ? 'placeholder="' . e($opts['placeholder']) . '"' : '';
    echo "<div class=\"$span\"><label class=\"set-label\" for=\"f-$name\">" . e($label) . ($req ? ' <span class="text-rose-500">*</span>' : '') . "</label>"
       . "<input id=\"f-$name\" type=\"$type\" name=\"$name\" value=\"" . e($value) . "\" $req $ph $extra class=\"set-input\">$help</div>";
}
function field_toggle($name, $title, $desc, $on) {
    echo '<label class="set-toggle-row"><div class="min-w-0"><div class="text-sm font-bold text-gray-900 dark:text-white">' . e($title) . '</div>'
       . '<div class="text-xs text-gray-500 dark:text-gray-400">' . e($desc) . '</div></div>'
       . '<span class="relative inline-flex items-center shrink-0"><input type="checkbox" name="' . e($name) . '" value="1" ' . ($on ? 'checked' : '') . ' class="sr-only peer">'
       . '<span class="set-switch"></span></span></label>';
}
function panel_head($icon, $title, $desc, $form = null, $group = null, $btn = 'Save changes') {
    echo '<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 pb-5 border-b border-gray-100 dark:border-gray-800">'
       . '<div><h2 class="text-xl font-luxury font-bold text-gray-900 dark:text-white flex items-center gap-2"><span class="material-symbols-outlined text-gold">' . $icon . '</span>' . e($title) . '</h2>'
       . '<p class="text-xs text-gray-500 dark:text-gray-400 mt-1 max-w-xl">' . e($desc) . '</p></div>';
    if ($form) {
        echo '<button type="button" onclick="submitSettingsForm(\'' . $form . '\', \'' . $group . '\')" class="save-btn px-5 py-2.5 rounded-xl gold-gradient-bg font-bold text-sm shadow-md hover:scale-[1.02] active:scale-95 transition-all flex items-center gap-2 shrink-0" data-form="' . $form . '">'
           . '<span class="material-symbols-outlined text-[18px]">check</span>' . e($btn) . '</button>';
    }
    echo '</div>';
}

include __DIR__ . '/../includes/head.php';
?>
<style>
    .set-label { display:block; font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.06em; color:rgb(55 65 81); margin-bottom:6px; }
    .dark .set-label { color:rgb(209 213 219); }
    .set-input { width:100%; padding:10px 14px; border-radius:12px; border:1px solid rgb(229 231 235); background:#fff; font-size:14px; color:rgb(17 24 39); outline:none; transition:border-color .15s, box-shadow .15s; }
    .set-input:focus { border-color:var(--theme-secondary); box-shadow:0 0 0 3px rgba(var(--theme-secondary-rgb), .18); }
    .dark .set-input { background:rgb(31 41 55); border-color:rgb(55 65 81); color:#fff; }
    .set-toggle-row { display:flex; align-items:center; justify-content:space-between; gap:16px; padding:14px 16px; border-radius:14px; border:1px solid rgb(229 231 235); background:rgb(249 250 251); cursor:pointer; }
    .dark .set-toggle-row { background:rgba(31,41,55,.6); border-color:rgb(55 65 81); }
    .set-switch { width:44px; height:24px; border-radius:999px; background:rgb(209 213 219); position:relative; transition:background .2s; }
    .set-switch::after { content:''; position:absolute; top:3px; left:3px; width:18px; height:18px; border-radius:50%; background:#fff; box-shadow:0 1px 3px rgba(0,0,0,.25); transition:transform .2s; }
    .peer:checked + .set-switch { background:var(--theme-primary); }
    .dark .peer:checked + .set-switch { background:var(--theme-secondary); }
    .peer:checked + .set-switch::after { transform:translateX(20px); }
    .peer:focus-visible + .set-switch { box-shadow:0 0 0 3px rgba(var(--theme-secondary-rgb), .35); }
    .settings-tab { border-color: rgb(229 231 235); background:#fff; }
    .dark .settings-tab { border-color: rgb(31 41 55); background: rgb(17 24 39); }
    .settings-tab.active { border-color: var(--theme-secondary) !important; box-shadow: 0 4px 14px -2px rgba(var(--theme-secondary-rgb), .25); }
    .settings-tab.active .tab-icon { background: var(--theme-primary); color: var(--theme-secondary); }
    .palette-card.active { border-color: var(--theme-secondary) !important; box-shadow: 0 0 0 3px rgba(var(--theme-secondary-rgb), .25); }
    .dropzone.drag { border-color: var(--theme-secondary); background: rgba(var(--theme-secondary-rgb), .08); }
    .save-btn.dirty { animation: pulseSave 1.6s ease-in-out infinite; }
    @keyframes pulseSave { 0%,100% { box-shadow: 0 0 0 0 rgba(var(--theme-secondary-rgb), .5); } 50% { box-shadow: 0 0 0 8px rgba(var(--theme-secondary-rgb), 0); } }
</style>
<body class="bg-[#F8FAFC] dark:bg-gray-950 text-gray-800 dark:text-gray-100 min-h-screen">

<?php include __DIR__ . '/../includes/sidebar.php'; ?>

<main class="md:ml-64 min-h-screen flex flex-col transition-all duration-300">

<?php include __DIR__ . '/../includes/header.php'; ?>

<div class="flex-1 p-4 sm:p-6 lg:p-8 space-y-6 max-w-[1600px] mx-auto w-full">

    <!-- Page header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 pb-2">
        <div>
            <div class="flex items-center gap-2 text-xs font-semibold text-gray-400 mb-1">
                <span>System</span><span class="material-symbols-outlined text-[14px]">chevron_right</span>
                <span class="text-navy dark:text-gold">Settings</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-luxury font-bold text-gray-900 dark:text-white tracking-tight">System Settings</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Your property's name, logo, colours and rules. Changes apply to the website, sign-in page, admin panel and receipts.</p>
        </div>
        <div class="flex items-center gap-2.5">
            <a href="../index.php" target="_blank" class="px-3.5 py-2 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 text-xs font-bold text-gray-700 dark:text-gray-200 hover:border-gold flex items-center gap-1.5">
                <span class="material-symbols-outlined text-[16px]">open_in_new</span> Preview website
            </a>
            <?php if ($s('maintenance_mode', '0') === '1'): ?>
                <span class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl bg-rose-100 dark:bg-rose-950 text-rose-700 dark:text-rose-300 text-xs font-bold border border-rose-300">
                    <span class="w-2 h-2 rounded-full bg-rose-500 animate-pulse"></span> Maintenance on
                </span>
            <?php else: ?>
                <span class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl bg-emerald-50 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-300 text-xs font-bold border border-emerald-200 dark:border-emerald-800">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span> Website live
                </span>
            <?php endif; ?>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

        <!-- Tabs: horizontal scroller on mobile, list on desktop -->
        <nav class="lg:col-span-3 lg:sticky lg:top-20 flex lg:flex-col gap-2 overflow-x-auto lg:overflow-visible pb-2 lg:pb-0 -mx-4 px-4 lg:mx-0 lg:px-0 scrollbar-hide" aria-label="Settings sections">
            <?php foreach ($tabs as [$id, $icon, $label, $desc]): ?>
            <button type="button" onclick="switchSettingsTab('<?= $id ?>')" id="tab-btn-<?= $id ?>"
                    class="settings-tab shrink-0 lg:w-full flex items-center gap-3 p-2.5 lg:p-3 rounded-2xl border text-left transition-all duration-200 hover:border-gold/60">
                <span class="tab-icon w-9 h-9 rounded-xl bg-gray-100 dark:bg-gray-800 flex items-center justify-center text-gray-600 dark:text-gray-300 transition-colors shrink-0">
                    <span class="material-symbols-outlined text-[20px]"><?= $icon ?></span>
                </span>
                <span class="min-w-0 pr-2">
                    <span class="block text-sm font-bold text-gray-900 dark:text-white whitespace-nowrap lg:truncate"><?= e($label) ?></span>
                    <span class="hidden lg:block text-[11px] text-gray-400 truncate"><?= e($desc) ?></span>
                </span>
            </button>
            <?php endforeach; ?>
        </nav>

        <div class="lg:col-span-9 bg-white dark:bg-gray-900 rounded-3xl border border-gray-200/80 dark:border-gray-800 shadow-sm overflow-hidden">

            <!-- ============================ BRANDING ============================ -->
            <section id="panel-branding" class="settings-panel hidden p-5 sm:p-8 space-y-7">
                <?php panel_head('badge', 'Branding & Logo', 'Set the property name, the two-part wordmark and the logo shown on every page. Logos and favicons upload instantly; text changes apply when you save.', 'branding-form', 'branding', 'Save branding'); ?>

                <!-- Live identity preview -->
                <div class="rounded-2xl overflow-hidden border border-gray-200 dark:border-gray-800">
                    <div class="px-4 py-2 text-[10px] font-extrabold uppercase tracking-wider text-gray-400 bg-gray-50 dark:bg-gray-800/60 flex justify-between"><span>Live preview</span><span>Website header · Admin sidebar</span></div>
                    <div class="grid sm:grid-cols-2">
                        <div class="p-5 bg-[#F8FAFC] flex items-center gap-3">
                            <div id="pv-mark-public" class="w-11 h-11 rounded-xl flex items-center justify-center shrink-0 shadow-md overflow-hidden"></div>
                            <div class="min-w-0">
                                <div class="font-luxury font-bold text-2xl leading-tight truncate" style="color:var(--theme-primary)"><span class="pv-w1"></span><span class="pv-w2" style="color:var(--theme-secondary)"></span></div>
                                <div class="pv-sub text-[10px] uppercase tracking-[0.25em] text-gray-400 font-bold truncate"></div>
                            </div>
                        </div>
                        <div class="p-5 flex items-center gap-3" style="background:linear-gradient(180deg,var(--theme-sidebar),var(--theme-primary))">
                            <div id="pv-mark-admin" class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 overflow-hidden"></div>
                            <div class="min-w-0">
                                <div class="font-extrabold text-white text-base leading-none truncate"><span class="pv-w1"></span><span class="pv-w2" style="color:var(--theme-secondary)"></span></div>
                                <div class="pv-erp text-[9px] uppercase tracking-[0.2em] mt-1 font-bold text-white/50 truncate"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <form id="branding-form" class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <?php
                    field_text('hotel_name', 'Official property name', $s('hotel_name', 'SkopeStay Resort & Suites'), ['required' => true, 'wide' => true, 'help' => 'Used in page titles, receipts, emails and the footer.']);
                    field_text('brand_word_1', 'Wordmark: first part', $s('brand_word_1', 'Skope'), ['required' => true, 'help' => 'Shown in the main brand colour.']);
                    field_text('brand_word_2', 'Wordmark: accent part', $s('brand_word_2', 'Stay'), ['help' => 'Shown in the accent colour. Leave blank for a single-colour name.']);
                    field_text('brand_subtitle', 'Website caption', $s('brand_subtitle', 'Resort & Suites'), ['help' => 'Small text under the name on the website.']);
                    field_text('erp_subtitle', 'Admin panel caption', $s('erp_subtitle', 'Hospitality ERP'), ['help' => 'Small text under the name in the admin sidebar.']);
                    field_text('hotel_tagline', 'Tagline / slogan', $s('hotel_tagline'), ['wide' => true, 'help' => 'Shown on the sign-in page and website hero.']);
                    ?>

                    <div class="sm:col-span-2">
                        <span class="set-label">Brand mark style</span>
                        <div class="grid grid-cols-2 gap-3 max-w-md">
                            <?php foreach (['logo' => ['image', 'Uploaded logo'], 'icon' => ['interests', 'Icon badge']] as $val => [$ic, $lbl]): ?>
                            <label class="cursor-pointer">
                                <input type="radio" name="logo_display" value="<?= $val ?>" class="sr-only peer" <?= $s('logo_display', 'logo') === $val ? 'checked' : '' ?>>
                                <span class="flex items-center gap-2 p-3 rounded-xl border-2 border-gray-200 dark:border-gray-700 peer-checked:border-gold peer-checked:bg-gold/5 text-sm font-bold">
                                    <span class="material-symbols-outlined text-[20px]"><?= $ic ?></span><?= $lbl ?>
                                </span>
                            </label>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="sm:col-span-2" id="icon-picker-wrap">
                        <span class="set-label">Badge icon (used when "Icon badge" is selected)</span>
                        <input type="hidden" name="brand_icon" id="brand_icon" value="<?= e($s('brand_icon', 'apartment')) ?>">
                        <div class="flex flex-wrap gap-2">
                            <?php foreach ($icons as $ic): ?>
                            <button type="button" data-icon="<?= $ic ?>" class="icon-opt w-11 h-11 rounded-xl border-2 border-gray-200 dark:border-gray-700 flex items-center justify-center hover:border-gold transition-colors <?= $s('brand_icon', 'apartment') === $ic ? '!border-gold bg-gold/10' : '' ?>" title="<?= $ic ?>">
                                <span class="material-symbols-outlined"><?= $ic ?></span>
                            </button>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </form>

                <!-- Image uploads (instant) -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 pt-6 border-t border-gray-100 dark:border-gray-800">
                    <?php
                    $uploads = [
                        ['logo', 'Logo', 'PNG/WEBP with transparent background, square, at least 256px. Max 2 MB.', brand_logo_url()],
                        ['favicon', 'Favicon', 'Browser tab icon. Square PNG or ICO, 64px or larger. Max 512 KB.', $s('favicon_url') ? asset_url($s('favicon_url')) : ''],
                        ['login_bg', 'Sign-in background', 'Wide landscape photo for the sign-in page. Max 4 MB.', $s('login_background') ? asset_url($s('login_background')) : ''],
                    ];
                    foreach ($uploads as [$kind, $label, $hint, $url]): ?>
                    <div class="rounded-2xl border border-gray-200 dark:border-gray-800 p-4 flex flex-col">
                        <div class="flex items-center justify-between mb-3">
                            <span class="set-label !mb-0"><?= $label ?></span>
                            <button type="button" onclick="resetImage('<?= $kind ?>')" class="text-[11px] font-bold text-gray-400 hover:text-rose-500">Reset</button>
                        </div>
                        <label class="dropzone flex-1 min-h-[150px] rounded-xl border-2 border-dashed border-gray-300 dark:border-gray-700 flex flex-col items-center justify-center gap-2 cursor-pointer transition-colors p-3 text-center" data-kind="<?= $kind ?>">
                            <div class="w-full h-24 flex items-center justify-center rounded-lg <?= $kind === 'login_bg' ? '' : 'checker' ?>">
                                <img id="img-<?= $kind ?>" src="<?= e($url) ?>" alt="" class="max-h-24 max-w-full object-contain rounded <?= $url ? '' : 'hidden' ?>">
                                <span id="ph-<?= $kind ?>" class="material-symbols-outlined text-4xl text-gray-300 <?= $url ? 'hidden' : '' ?>">add_photo_alternate</span>
                            </div>
                            <span class="text-xs font-bold text-navy dark:text-gold">Click or drop an image</span>
                            <input type="file" accept="image/png,image/jpeg,image/webp,image/gif,image/x-icon,.ico" class="hidden" onchange="uploadImage('<?= $kind ?>', this.files[0])">
                        </label>
                        <p class="text-[11px] text-gray-400 mt-2"><?= $hint ?></p>
                    </div>
                    <?php endforeach; ?>
                </div>
            </section>

            <!-- ============================ THEME ============================ -->
            <section id="panel-theme" class="settings-panel hidden p-5 sm:p-8 space-y-7">
                <?php panel_head('palette', 'Theme Colours', 'Pick a preset or set your own colours. They apply to the website, booking flow, sign-in page, admin panel and receipts.', 'theme-form', 'theme', 'Save colours'); ?>

                <form id="theme-form" class="space-y-7">
                    <div>
                        <span class="set-label">Presets</span>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                            <?php foreach ($presets as $key => [$name, $desc, $p, $sc, $a]): ?>
                            <button type="button" onclick="selectPreset('<?= $key ?>', '<?= $p ?>', '<?= $sc ?>', '<?= $a ?>')" data-preset="<?= $key ?>"
                                    class="palette-card text-left rounded-2xl border-2 border-gray-200 dark:border-gray-700 overflow-hidden transition-all hover:-translate-y-0.5 <?= $s('theme_palette', 'navy_gold') === $key ? 'active' : '' ?>">
                                <span class="flex h-12"><span class="flex-[3]" style="background:<?= $p ?>"></span><span class="flex-[2]" style="background:<?= $sc ?>"></span><span class="flex-1" style="background:<?= $a ?>"></span></span>
                                <span class="block p-2.5"><span class="block text-xs font-bold text-gray-900 dark:text-white"><?= $name ?></span><span class="block text-[10px] text-gray-400"><?= $desc ?></span></span>
                            </button>
                            <?php endforeach; ?>
                        </div>
                        <input type="hidden" name="theme_palette" id="theme_palette_input" value="<?= e($s('theme_palette', 'navy_gold')) ?>">
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 pt-6 border-t border-gray-100 dark:border-gray-800">
                        <?php
                        $pickers = [
                            ['primary_color', 'Primary', 'Navigation, headers, dark sections', $pal['primary']],
                            ['secondary_color', 'Accent', 'Buttons, highlights, wordmark', $pal['secondary']],
                            ['accent_color', 'Success / badges', 'Status pills and positive figures', $pal['accent']],
                            ['sidebar_color', 'Admin sidebar', 'Leave blank to derive from primary', $s('sidebar_color')],
                        ];
                        foreach ($pickers as [$name, $label, $help, $val]): ?>
                        <div>
                            <label class="set-label" for="txt-<?= $name ?>"><?= $label ?></label>
                            <div class="flex items-center gap-2">
                                <input type="color" id="pick-<?= $name ?>" value="<?= e($val ?: $pal['sidebar']) ?>" class="w-11 h-11 rounded-xl cursor-pointer border border-gray-300 dark:border-gray-700 p-0.5 bg-white shrink-0" aria-label="<?= $label ?> colour picker">
                                <input type="text" name="<?= $name ?>" id="txt-<?= $name ?>" value="<?= e($val) ?>" maxlength="7" placeholder="#RRGGBB" class="set-input font-mono uppercase !text-xs font-bold">
                            </div>
                            <p class="text-[11px] text-gray-400 mt-1"><?= $help ?></p>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <p id="contrast-warn" class="hidden text-xs font-semibold text-amber-700 bg-amber-50 border border-amber-200 rounded-xl px-3 py-2 items-center gap-2">
                        <span class="material-symbols-outlined text-[18px]">warning</span><span>Low contrast between primary and accent: text on buttons may be hard to read.</span>
                    </p>
                </form>

                <!-- Preview -->
                <div>
                    <span class="set-label">Live preview</span>
                    <div class="rounded-2xl overflow-hidden border border-gray-200 dark:border-gray-800 grid md:grid-cols-[180px_1fr]">
                        <div id="tp-sidebar" class="p-4 space-y-1.5 hidden md:block">
                            <div class="text-white font-extrabold text-sm mb-3"><?= e(brand('brand_word_1')) ?><span class="tp-acc-text"><?= e(brand('brand_word_2')) ?></span></div>
                            <div class="tp-active text-xs font-semibold px-2.5 py-1.5 rounded-lg">Dashboard</div>
                            <div class="text-xs font-semibold px-2.5 py-1.5 text-white/60">Reservations</div>
                            <div class="text-xs font-semibold px-2.5 py-1.5 text-white/60">Rooms</div>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-950">
                            <div id="tp-hero" class="p-5 text-white">
                                <div class="text-[10px] uppercase tracking-[0.25em] font-bold tp-acc-text">Direct booking</div>
                                <div class="font-luxury text-xl font-bold mt-1">Presidential Ocean Suite</div>
                                <div class="flex flex-wrap items-center gap-3 mt-3">
                                    <span class="font-luxury text-lg font-bold tp-acc-text"><?= e(get_setting($pdo, 'currency_symbol', 'KSh')) ?> 58,000 <span class="text-xs text-white/60 font-sans">/ night</span></span>
                                    <span id="tp-badge" class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase text-white">Available</span>
                                </div>
                            </div>
                            <div class="p-5 flex flex-wrap gap-3 items-center">
                                <button type="button" id="tp-btn" class="px-4 py-2 rounded-xl font-bold text-xs shadow">Reserve now</button>
                                <button type="button" id="tp-btn2" class="px-4 py-2 rounded-xl font-bold text-xs border-2 bg-white">View details</button>
                                <span id="tp-link" class="text-xs font-bold underline">Read more</span>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- ============================ GENERAL ============================ -->
            <section id="panel-general" class="settings-panel hidden p-5 sm:p-8 space-y-6">
                <?php panel_head('apartment', 'Property & Contacts', 'Contact details shown to guests on the website, maintenance page and receipts.', 'general-form', 'general', 'Save details'); ?>
                <form id="general-form" class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <?php
                    field_text('contact_phone', 'Reception phone', $s('contact_phone'), ['required' => true]);
                    field_text('contact_phone_alt', 'Alternative phone', $s('contact_phone_alt'));
                    field_text('contact_email', 'Administrative email', $s('contact_email'), ['type' => 'email', 'required' => true]);
                    field_text('support_email', 'Guest support email', $s('support_email'), ['type' => 'email']);
                    field_text('contact_address', 'Physical address', $s('contact_address'), ['required' => true, 'wide' => true]);
                    field_text('contact_city', 'City', $s('contact_city'));
                    field_text('contact_country', 'Country', $s('contact_country'));
                    ?>
                    <div>
                        <label class="set-label" for="f-star">Star rating</label>
                        <select id="f-star" name="hotel_star_rating" class="set-input">
                            <?php for ($i = 1; $i <= 7; $i++): ?><option value="<?= $i ?>" <?= (int)$s('hotel_star_rating', 5) === $i ? 'selected' : '' ?>><?= $i ?> star<?= $i > 1 ? 's' : '' ?></option><?php endfor; ?>
                        </select>
                    </div>
                    <div>
                        <label class="set-label" for="f-tz">Timezone</label>
                        <select id="f-tz" name="timezone" class="set-input">
                            <?php foreach (timezone_identifiers_list() as $tz): ?>
                            <option value="<?= e($tz) ?>" <?= $s('timezone', 'Africa/Nairobi') === $tz ? 'selected' : '' ?>><?= e($tz) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </form>
            </section>

            <!-- ============================ WEBSITE ============================ -->
            <section id="panel-website" class="settings-panel hidden p-5 sm:p-8 space-y-6">
                <?php panel_head('public', 'Website & Social', 'Text and links shown on the public website footer, search results and WhatsApp button.', 'website-form', 'website', 'Save website'); ?>
                <form id="website-form" class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div class="sm:col-span-2">
                        <label class="set-label" for="f-footer_about">Footer about text</label>
                        <textarea id="f-footer_about" name="footer_about" rows="3" maxlength="500" class="set-input"><?= e($s('footer_about')) ?></textarea>
                    </div>
                    <div class="sm:col-span-2">
                        <label class="set-label" for="f-meta">Search engine description</label>
                        <textarea id="f-meta" name="meta_description" rows="2" maxlength="300" class="set-input"><?= e($s('meta_description')) ?></textarea>
                        <p class="text-[11px] text-gray-400 mt-1">Shown under your site name in Google results. Aim for 120–160 characters.</p>
                    </div>
                    <?php
                    field_text('whatsapp_number', 'WhatsApp number', $s('whatsapp_number'), ['placeholder' => '254712345678', 'help' => 'International format, digits only. Powers the floating chat button.']);
                    field_text('google_maps_embed', 'Google Maps embed link', $s('google_maps_embed'), ['type' => 'url', 'placeholder' => 'https://www.google.com/maps/embed?...']);
                    field_text('social_facebook', 'Facebook', $s('social_facebook'), ['type' => 'url', 'placeholder' => 'https://facebook.com/yourpage']);
                    field_text('social_instagram', 'Instagram', $s('social_instagram'), ['type' => 'url', 'placeholder' => 'https://instagram.com/yourpage']);
                    field_text('social_x', 'X (Twitter)', $s('social_x'), ['type' => 'url', 'placeholder' => 'https://x.com/yourpage']);
                    field_text('social_tiktok', 'TikTok', $s('social_tiktok'), ['type' => 'url', 'placeholder' => 'https://tiktok.com/@yourpage']);
                    field_text('social_linkedin', 'LinkedIn', $s('social_linkedin'), ['type' => 'url', 'placeholder' => 'https://linkedin.com/company/...']);
                    field_text('social_youtube', 'YouTube', $s('social_youtube'), ['type' => 'url', 'placeholder' => 'https://youtube.com/@...']);
                    ?>
                </form>
            </section>

            <!-- ============================ OPERATIONS ============================ -->
            <section id="panel-operations" class="settings-panel hidden p-5 sm:p-8 space-y-6">
                <?php panel_head('room_service', 'Hospitality Rules', 'Check-in hours and which public booking features are available.', 'operations-form', 'operations', 'Save rules'); ?>
                <form id="operations-form" class="space-y-6">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <?php
                        field_text('checkin_time', 'Standard check-in time', $s('checkin_time', '14:00'), ['type' => 'time']);
                        field_text('checkout_time', 'Standard check-out time', $s('checkout_time', '11:00'), ['type' => 'time']);
                        ?>
                    </div>
                    <div class="space-y-3 pt-5 border-t border-gray-100 dark:border-gray-800">
                        <span class="set-label">Public modules</span>
                        <?php
                        field_toggle('enable_online_booking', 'Online room bookings', 'Guests can reserve rooms from the website.', $s('enable_online_booking', '1') === '1');
                        field_toggle('enable_restaurant_pos', 'Dining & restaurant', 'Accept table reservations and room-service orders.', $s('enable_restaurant_pos', '1') === '1');
                        field_toggle('enable_pool_passes', 'Pool day passes', 'Sell pool and leisure passes online.', $s('enable_pool_passes', '1') === '1');
                        field_toggle('enable_hall_booking', 'Events & conference halls', 'Receive hall and banquet booking requests.', $s('enable_hall_booking', '1') === '1');
                        ?>
                    </div>
                </form>
            </section>

            <!-- ============================ BILLING ============================ -->
            <section id="panel-billing" class="settings-panel hidden p-5 sm:p-8 space-y-6">
                <?php panel_head('receipt_long', 'Billing & Receipts', 'Currency, taxes and what appears on printed receipts.', 'billing-form', 'billing', 'Save billing'); ?>
                <form id="billing-form" class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label class="set-label" for="f-cur">Currency</label>
                        <select id="f-cur" name="currency_code" class="set-input" onchange="const m={KES:'KSh',USD:'$',EUR:'€',GBP:'£',UGX:'USh',TZS:'TSh',RWF:'FRw',ZAR:'R',NGN:'₦',AED:'AED'};document.getElementById('f-currency_symbol').value=m[this.value]||this.value">
                            <?php foreach (['KES' => 'Kenya Shilling', 'UGX' => 'Uganda Shilling', 'TZS' => 'Tanzania Shilling', 'RWF' => 'Rwanda Franc', 'USD' => 'US Dollar', 'EUR' => 'Euro', 'GBP' => 'British Pound', 'ZAR' => 'South African Rand', 'NGN' => 'Nigerian Naira', 'AED' => 'UAE Dirham'] as $code => $nm): ?>
                            <option value="<?= $code ?>" <?= $s('currency_code', 'KES') === $code ? 'selected' : '' ?>><?= $code ?> · <?= $nm ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <?php
                    field_text('currency_symbol', 'Currency symbol', $s('currency_symbol', 'KSh'), ['required' => true]);
                    field_text('tax_rate', 'VAT (%)', $s('tax_rate', '16'), ['type' => 'number', 'attrs' => 'step="0.1" min="0" max="100"']);
                    field_text('service_charge', 'Service charge (%)', $s('service_charge', '5'), ['type' => 'number', 'attrs' => 'step="0.1" min="0" max="100"']);
                    field_text('tax_pin', 'Tax PIN / registration no.', $s('tax_pin'), ['help' => 'Printed on receipts.']);
                    field_text('booking_prefix', 'Booking reference prefix', $s('booking_prefix', 'BK'), ['help' => 'e.g. BK gives references like #BK-00042.']);
                    ?>
                    <div class="sm:col-span-2">
                        <label class="set-label" for="f-rf">Receipt footer note</label>
                        <textarea id="f-rf" name="receipt_footer" rows="2" maxlength="300" class="set-input"><?= e($s('receipt_footer')) ?></textarea>
                    </div>
                </form>
            </section>

            <!-- ============================ MAINTENANCE ============================ -->
            <section id="panel-maintenance" class="settings-panel hidden p-5 sm:p-8 space-y-6">
                <?php panel_head('construction', 'Maintenance Mode', 'Temporarily show visitors a maintenance screen. Signed-in staff and anyone with the bypass link can still use the site.', 'maintenance-form', 'maintenance', 'Save maintenance'); ?>
                <form id="maintenance-form" class="space-y-5">
                    <?php field_toggle('maintenance_mode', 'Maintenance mode', 'When on, public visitors see the maintenance screen instead of the website.', $s('maintenance_mode', '0') === '1'); ?>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <?php field_text('maintenance_title', 'Heading', $s('maintenance_title'), ['required' => true, 'wide' => true]); ?>
                        <div class="sm:col-span-2">
                            <label class="set-label" for="f-mm">Message to visitors <span class="text-rose-500">*</span></label>
                            <textarea id="f-mm" name="maintenance_message" rows="3" required class="set-input"><?= e($s('maintenance_message')) ?></textarea>
                        </div>
                        <?php field_text('maintenance_estimated_end', 'Expected duration', $s('maintenance_estimated_end')); ?>
                        <div>
                            <label class="set-label" for="bypass_key_input">Staff bypass key</label>
                            <div class="flex items-center gap-2">
                                <input type="text" name="maintenance_bypass_key" id="bypass_key_input" value="<?= e($s('maintenance_bypass_key', 'skope_vip_bypass_2026')) ?>" class="set-input font-mono !text-xs">
                                <button type="button" onclick="randomKey()" class="px-3 py-2.5 rounded-xl bg-gray-100 dark:bg-gray-800 hover:bg-gold/20 text-gray-600 dark:text-gray-300 shrink-0" title="Generate new key"><span class="material-symbols-outlined text-[18px]">casino</span></button>
                                <button type="button" onclick="copyBypassLink()" class="px-3 py-2.5 rounded-xl bg-gray-100 dark:bg-gray-800 hover:bg-gold/20 text-gray-600 dark:text-gray-300 shrink-0" title="Copy bypass link"><span class="material-symbols-outlined text-[18px]">content_copy</span></button>
                            </div>
                        </div>
                    </div>
                </form>
            </section>

            <!-- ============================ NOTIFICATIONS ============================ -->
            <section id="panel-notifications" class="settings-panel hidden p-5 sm:p-8 space-y-6">
                <?php panel_head('notifications', 'Alerts & Email', 'Which events raise alerts in the admin bell and who receives them.', 'notifications-form', 'notifications', 'Save alerts'); ?>
                <form id="notifications-form" class="space-y-5">
                    <div class="max-w-md"><?php field_text('notify_email_recipient', 'Alert recipient email', $s('notify_email_recipient'), ['type' => 'email', 'required' => true]); ?></div>
                    <div class="space-y-3">
                        <?php
                        field_toggle('notify_new_booking', 'New bookings', 'Alert when a guest books online.', $s('notify_new_booking', '1') === '1');
                        field_toggle('notify_maintenance', 'Urgent work orders', 'Alert when a high-priority maintenance ticket is opened.', $s('notify_maintenance', '1') === '1');
                        field_toggle('notify_low_stock', 'Low stock', 'Alert when inventory falls below its minimum level.', $s('notify_low_stock', '1') === '1');
                        ?>
                    </div>
                </form>
            </section>

            <!-- ============================ SECURITY ============================ -->
            <section id="panel-security" class="settings-panel hidden p-5 sm:p-8 space-y-6">
                <?php panel_head('shield_lock', 'Security & Access', 'Control who can create accounts and how long idle sessions stay signed in.', 'security-form', 'security', 'Save security'); ?>
                <form id="security-form" class="space-y-5">
                    <div class="max-w-xs"><?php field_text('session_timeout', 'Idle sign-out (minutes)', $s('session_timeout', '120'), ['type' => 'number', 'attrs' => 'min="5" max="1440"', 'help' => 'Staff are signed out after this much inactivity.']); ?></div>
                    <div class="space-y-3">
                        <?php
                        field_toggle('allow_public_signup', 'Public account sign-up', 'Let visitors create portal accounts. New accounts get no staff access until an admin assigns a role.', $s('allow_public_signup', '0') === '1');
                        field_toggle('show_demo_login', 'Show demo credentials on sign-in page', 'Turn this off before going live. Anyone can see the demo username and password.', $s('show_demo_login', '1') === '1');
                        ?>
                    </div>
                    <div class="rounded-2xl bg-gray-50 dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 p-4 text-xs text-gray-600 dark:text-gray-300 space-y-1.5">
                        <p class="font-bold text-gray-900 dark:text-white flex items-center gap-1.5"><span class="material-symbols-outlined text-[18px] text-emerald-500">verified_user</span>Always on</p>
                        <p>• Every admin page and API requires sign-in and the right role permission.</p>
                        <p>• Five wrong passwords lock sign-in for five minutes.</p>
                        <p>• Settings and uploads are protected against cross-site requests, and uploaded files are checked by content.</p>
                    </div>
                </form>
            </section>

            <!-- ============================ USERS ============================ -->
            <section id="panel-users" class="settings-panel hidden p-5 sm:p-8 space-y-6">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 pb-5 border-b border-gray-100 dark:border-gray-800">
                    <div>
                        <h2 class="text-xl font-luxury font-bold text-gray-900 dark:text-white flex items-center gap-2"><span class="material-symbols-outlined text-gold">manage_accounts</span>Staff Directory</h2>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Accounts that can sign in. Manage roles and permissions in the RBAC matrix.</p>
                    </div>
                    <a href="user_management.php" class="px-5 py-2.5 rounded-xl bg-navy text-white font-bold text-sm shadow-md flex items-center gap-2">
                        <span class="material-symbols-outlined text-[18px] text-gold">admin_panel_settings</span> Manage roles
                    </a>
                </div>
                <div class="overflow-x-auto rounded-2xl border border-gray-200 dark:border-gray-800">
                    <table class="w-full text-left text-sm min-w-[560px]">
                        <thead class="bg-gray-50 dark:bg-gray-800/60 text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            <tr><th class="p-3.5">User</th><th class="p-3.5">Role</th><th class="p-3.5">Last sign-in</th><th class="p-3.5 text-right">Status</th></tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                            <?php foreach ($staff as $u): ?>
                            <tr class="hover:bg-gray-50/60 dark:hover:bg-gray-800/40">
                                <td class="p-3.5">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-full bg-navy text-white flex items-center justify-center font-bold text-xs"><?= e(strtoupper(substr($u['username'], 0, 2))) ?></div>
                                        <div class="min-w-0"><div class="font-bold text-gray-900 dark:text-white truncate"><?= e($u['full_name'] ?: $u['username']) ?></div><div class="text-[11px] text-gray-400 truncate"><?= e($u['username']) ?> · <?= e($u['email']) ?></div></div>
                                    </div>
                                </td>
                                <td class="p-3.5"><span class="px-2.5 py-1 rounded-full bg-navy/10 dark:bg-white/10 text-navy dark:text-gold font-bold text-[11px]"><?= e($u['primary_role']) ?></span></td>
                                <td class="p-3.5 text-gray-500 text-xs"><?= $u['last_login'] ? date('M j, Y H:i', strtotime($u['last_login'])) : 'Never' ?></td>
                                <td class="p-3.5 text-right">
                                    <?php if ((int)$u['is_active'] === 1): ?>
                                    <span class="px-2 py-0.5 rounded-full bg-emerald-100 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-300 font-bold text-[10px]">ACTIVE</span>
                                    <?php else: ?>
                                    <span class="px-2 py-0.5 rounded-full bg-gray-200 dark:bg-gray-800 text-gray-600 dark:text-gray-400 font-bold text-[10px]">DISABLED</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </section>

            <!-- ============================ SYSTEM ============================ -->
            <section id="panel-system" class="settings-panel hidden p-5 sm:p-8 space-y-6">
                <?php panel_head('dns', 'System & Backup', 'Download a full database backup and check system health.'); ?>
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
                    <?php foreach ([['PHP', PHP_VERSION, 'code'], ['Database', $db_size . ' MB', 'database'], ['Tables', $table_count, 'table'], ['Schema', 'v' . $s('schema_version', '0'), 'schema']] as [$l, $v, $ic]): ?>
                    <div class="rounded-2xl border border-gray-200 dark:border-gray-800 p-4">
                        <span class="material-symbols-outlined text-gold"><?= $ic ?></span>
                        <div class="text-lg font-extrabold text-gray-900 dark:text-white mt-1"><?= e($v) ?></div>
                        <div class="text-[11px] uppercase tracking-wider font-bold text-gray-400"><?= $l ?></div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <div class="rounded-2xl border border-gray-200 dark:border-gray-800 p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <p class="font-bold text-gray-900 dark:text-white flex items-center gap-2"><span class="material-symbols-outlined text-gold">cloud_download</span>Database backup</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Downloads every table as a .sql file you can restore with phpMyAdmin or the mysql command line.</p>
                    </div>
                    <a href="../api/backup.php" class="px-5 py-2.5 rounded-xl bg-navy text-white font-bold text-sm shadow-md flex items-center gap-2 justify-center shrink-0"><span class="material-symbols-outlined text-[18px] text-gold">download</span>Download backup</a>
                </div>
                <div>
                    <span class="set-label">Recent settings changes</span>
                    <div class="rounded-2xl border border-gray-200 dark:border-gray-800 divide-y divide-gray-100 dark:divide-gray-800">
                        <?php if (!$last_changes): ?><p class="p-4 text-xs text-gray-400">No changes recorded yet.</p><?php endif; ?>
                        <?php foreach ($last_changes as $c): ?>
                        <div class="p-3.5 flex items-start justify-between gap-4 text-xs">
                            <span class="text-gray-700 dark:text-gray-300 min-w-0 break-words"><?= e($c['details']) ?></span>
                            <span class="text-gray-400 shrink-0 text-right"><?= e($c['username'] ?? 'system') ?><br><?= date('M j, H:i', strtotime($c['created_at'])) ?></span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </section>

        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
</main>

<style>
    .checker { background: repeating-conic-gradient(#f3f4f6 0% 25%, #ffffff 0% 50%) 50% / 16px 16px; }
</style>
<script>
const CSRF = document.querySelector('meta[name="csrf-token"]').content;
const LOGO_URL = <?= json_encode(brand_logo_url()) ?>;

// ---- Tabs (deep-linkable via #hash) ----
function switchSettingsTab(tabId) {
    if (!document.getElementById('panel-' + tabId)) tabId = 'branding';
    document.querySelectorAll('.settings-panel').forEach(p => p.classList.add('hidden'));
    document.querySelectorAll('.settings-tab').forEach(t => t.classList.remove('active'));
    document.getElementById('panel-' + tabId).classList.remove('hidden');
    const btn = document.getElementById('tab-btn-' + tabId);
    btn.classList.add('active');
    if (window.innerWidth < 1024) btn.scrollIntoView({ block: 'nearest', inline: 'center' });
    history.replaceState(null, '', '#' + tabId);
}
switchSettingsTab(location.hash.slice(1) || 'branding');
window.addEventListener('hashchange', () => switchSettingsTab(location.hash.slice(1)));

// ---- Unsaved-changes tracking ----
const dirtyForms = new Set();
document.querySelectorAll('.settings-panel form').forEach(f => {
    f.addEventListener('input', () => markDirty(f.id));
    f.addEventListener('change', () => markDirty(f.id));
});
function markDirty(id) {
    dirtyForms.add(id);
    document.querySelector(`.save-btn[data-form="${id}"]`)?.classList.add('dirty');
}
window.addEventListener('beforeunload', e => { if (dirtyForms.size) { e.preventDefault(); e.returnValue = ''; } });

// ---- Branding preview ----
function renderMark(el, forAdmin) {
    const mode = document.querySelector('input[name="logo_display"]:checked')?.value || 'logo';
    const icon = document.getElementById('brand_icon').value;
    const logo = document.getElementById('img-logo')?.getAttribute('src') || LOGO_URL;
    if (mode === 'icon') {
        el.style.background = forAdmin ? 'rgba(var(--theme-secondary-rgb),.15)' : 'var(--theme-primary)';
        el.style.padding = '0';
        el.innerHTML = `<span class="material-symbols-outlined text-2xl" style="color:var(--theme-secondary)">${icon}</span>`;
    } else {
        el.style.background = '#fff';
        el.style.padding = '4px';
        el.innerHTML = `<img src="${logo}" alt="" class="w-full h-full object-contain">`;
    }
}
function updateBrandPreview() {
    const v = n => document.querySelector(`#branding-form [name="${n}"]`).value;
    document.querySelectorAll('.pv-w1').forEach(e => e.textContent = v('brand_word_1'));
    document.querySelectorAll('.pv-w2').forEach(e => e.textContent = v('brand_word_2'));
    document.querySelector('.pv-sub').textContent = v('brand_subtitle');
    document.querySelector('.pv-erp').textContent = v('erp_subtitle');
    renderMark(document.getElementById('pv-mark-public'), false);
    renderMark(document.getElementById('pv-mark-admin'), true);
    document.getElementById('icon-picker-wrap').style.opacity = (document.querySelector('input[name="logo_display"]:checked')?.value === 'icon') ? '1' : '.5';
}
document.getElementById('branding-form').addEventListener('input', updateBrandPreview);
document.getElementById('branding-form').addEventListener('change', updateBrandPreview);
document.querySelectorAll('.icon-opt').forEach(b => b.addEventListener('click', () => {
    document.querySelectorAll('.icon-opt').forEach(x => x.classList.remove('!border-gold', 'bg-gold/10'));
    b.classList.add('!border-gold', 'bg-gold/10');
    document.getElementById('brand_icon').value = b.dataset.icon;
    document.querySelector('input[name="logo_display"][value="icon"]').checked = true;
    markDirty('branding-form');
    updateBrandPreview();
}));
updateBrandPreview();

// ---- Image uploads ----
document.querySelectorAll('.dropzone').forEach(z => {
    ['dragenter', 'dragover'].forEach(ev => z.addEventListener(ev, e => { e.preventDefault(); z.classList.add('drag'); }));
    ['dragleave', 'drop'].forEach(ev => z.addEventListener(ev, e => { e.preventDefault(); z.classList.remove('drag'); }));
    z.addEventListener('drop', e => { if (e.dataTransfer.files[0]) uploadImage(z.dataset.kind, e.dataTransfer.files[0]); });
});
function showImage(kind, url) {
    const img = document.getElementById('img-' + kind), ph = document.getElementById('ph-' + kind);
    img.src = url || ''; img.classList.toggle('hidden', !url); ph.classList.toggle('hidden', !!url);
    if (kind === 'logo') updateBrandPreview();
}
async function uploadImage(kind, file) {
    if (!file) return;
    const fd = new FormData();
    fd.append('kind', kind);
    fd.append('file', file);
    Swal.fire({ title: 'Uploading...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
    try {
        const res = await fetch('../api/branding_upload.php', { method: 'POST', headers: { 'X-CSRF-Token': CSRF }, body: fd });
        const d = await res.json();
        if (!d.success) return Swal.fire('Upload failed', d.message, 'error');
        if (kind === 'logo') document.querySelector('input[name="logo_display"][value="logo"]').checked = true;
        showImage(kind, d.url + '?v=' + Date.now());
        Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: d.message, showConfirmButton: false, timer: 2500 });
    } catch (e) {
        Swal.fire('Network error', 'Could not reach the server.', 'error');
    }
}
async function resetImage(kind) {
    const ok = await Swal.fire({ title: 'Restore default?', text: 'The current image will be replaced with the default.', icon: 'question', showCancelButton: true, confirmButtonText: 'Restore' });
    if (!ok.isConfirmed) return;
    const res = await fetch('../api/branding_upload.php', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': CSRF }, body: JSON.stringify({ action: 'reset', kind }) });
    const d = await res.json();
    if (d.success) { showImage(kind, d.url); Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: d.message, showConfirmButton: false, timer: 2000 }); }
    else Swal.fire('Error', d.message, 'error');
}

// ---- Theme colours ----
const HEX = /^#[0-9a-fA-F]{6}$/;
const colorKeys = ['primary_color', 'secondary_color', 'accent_color', 'sidebar_color'];
function hexToRgb(h) { const n = parseInt(h.slice(1), 16); return [n >> 16 & 255, n >> 8 & 255, n & 255]; }
function lum(h) { return hexToRgb(h).map(c => { c /= 255; return c <= .03928 ? c / 12.92 : Math.pow((c + .055) / 1.055, 2.4); }).reduce((a, c, i) => a + c * [.2126, .7152, .0722][i], 0); }
function contrast(a, b) { const [x, y] = [lum(a), lum(b)].sort((m, n) => n - m); return (x + .05) / (y + .05); }
function shade(h, amt) { return '#' + hexToRgb(h).map(c => Math.round(c + ((amt > 0 ? 255 : 0) - c) * Math.abs(amt)).toString(16).padStart(2, '0')).join(''); }
function val(k) { return document.getElementById('txt-' + k).value.trim(); }

function updateThemePreview() {
    const p = HEX.test(val('primary_color')) ? val('primary_color') : '#0B132B';
    const s = HEX.test(val('secondary_color')) ? val('secondary_color') : '#D4AF37';
    const a = HEX.test(val('accent_color')) ? val('accent_color') : '#10B981';
    const sb = HEX.test(val('sidebar_color')) ? val('sidebar_color') : shade(p, -0.15);
    const onS = lum(s) > 0.4 ? '#0B132B' : '#FFFFFF';
    document.getElementById('tp-sidebar').style.background = `linear-gradient(180deg, ${sb}, ${p})`;
    document.getElementById('tp-hero').style.background = p;
    document.querySelectorAll('.tp-acc-text').forEach(e => e.style.color = s);
    const act = document.querySelector('.tp-active');
    act.style.background = s + '30'; act.style.color = shade(s, .5); act.style.borderLeft = `3px solid ${s}`;
    document.getElementById('tp-badge').style.background = a;
    Object.assign(document.getElementById('tp-btn').style, { background: s, color: onS });
    Object.assign(document.getElementById('tp-btn2').style, { borderColor: p, color: p });
    document.getElementById('tp-link').style.color = p;
    const warn = document.getElementById('contrast-warn');
    const low = contrast(p, s) < 2.2;
    warn.classList.toggle('hidden', !low);
    warn.classList.toggle('flex', low);
}
colorKeys.forEach(k => {
    const pick = document.getElementById('pick-' + k), txt = document.getElementById('txt-' + k);
    pick.addEventListener('input', () => { txt.value = pick.value.toUpperCase(); setCustom(); updateThemePreview(); });
    txt.addEventListener('input', () => { if (HEX.test(txt.value)) pick.value = txt.value; setCustom(); updateThemePreview(); });
});
function setCustom() {
    document.getElementById('theme_palette_input').value = 'custom';
    document.querySelectorAll('.palette-card').forEach(c => c.classList.remove('active'));
}
function selectPreset(key, p, s, a) {
    document.querySelectorAll('.palette-card').forEach(c => c.classList.toggle('active', c.dataset.preset === key));
    document.getElementById('theme_palette_input').value = key;
    [['primary_color', p], ['secondary_color', s], ['accent_color', a]].forEach(([k, v]) => {
        document.getElementById('txt-' + k).value = v;
        document.getElementById('pick-' + k).value = v;
    });
    markDirty('theme-form');
    updateThemePreview();
}
updateThemePreview();

// ---- Maintenance helpers ----
function randomKey() {
    const a = new Uint8Array(12); crypto.getRandomValues(a);
    document.getElementById('bypass_key_input').value = 'staff_' + Array.from(a, b => b.toString(16).padStart(2, '0')).join('');
    markDirty('maintenance-form');
}
function copyBypassLink() {
    const key = document.getElementById('bypass_key_input').value;
    const url = new URL('../index.php?bypass=' + encodeURIComponent(key), location.href).href;
    navigator.clipboard.writeText(url).then(() => Swal.fire({ icon: 'success', title: 'Bypass link copied', text: url, timer: 2500, showConfirmButton: false, toast: true, position: 'top-end' }));
}

// ---- Save any form ----
async function submitSettingsForm(formId, groupName) {
    const form = document.getElementById(formId);
    if (!form.reportValidity()) return;
    const data = { group: groupName };
    form.querySelectorAll('input, select, textarea').forEach(el => {
        if (!el.name) return;
        if (el.type === 'checkbox') data[el.name] = el.checked ? '1' : '0';
        else if (el.type === 'radio') { if (el.checked) data[el.name] = el.value; }
        else data[el.name] = el.value;
    });

    const btn = document.querySelector(`.save-btn[data-form="${formId}"]`);
    const html = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<span class="material-symbols-outlined text-[18px] animate-spin">progress_activity</span>Saving...';
    try {
        const res = await fetch('../api/settings.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': CSRF },
            body: JSON.stringify(data)
        });
        const json = await res.json();
        if (json.success) {
            dirtyForms.delete(formId);
            btn.classList.remove('dirty');
            const reload = ['branding', 'theme'].includes(groupName) && json.updated?.length;
            Swal.fire({ icon: 'success', title: 'Saved', text: json.message, timer: 1400, showConfirmButton: false })
                .then(() => { if (reload) location.reload(); });
        } else {
            Swal.fire({ icon: 'error', title: 'Please check these fields', html: (json.errors || [json.message]).map(m => `<div class="text-sm">${String(m).replace(/</g, '&lt;')}</div>`).join('') });
        }
    } catch (err) {
        Swal.fire('Network error', 'Unable to reach the server. Please check your connection.', 'error');
    } finally {
        btn.disabled = false;
        btn.innerHTML = html;
    }
}
</script>

</body>
</html>
