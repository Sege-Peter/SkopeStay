<?php
/**
 * SkopeStay Branding & Security Helpers
 * Central source for the property name, logo, favicon and theme colours so
 * every page (public site, login, ERP modules, receipts) follows the admin
 * settings instead of hardcoded values.
 */

const BRAND_DEFAULTS = [
    'hotel_name'       => 'SkopeStay Resort & Suites',
    'brand_word_1'     => 'Skope',
    'brand_word_2'     => 'Stay',
    'brand_subtitle'   => 'Resort & Suites',
    'erp_subtitle'     => 'Hospitality ERP',
    'logo_url'         => 'assets/images/SkopeStay logo.png',
    'favicon_url'      => '',
    'logo_display'     => 'logo',      // logo | icon
    'brand_icon'       => 'apartment',
    'primary_color'    => '#0B132B',
    'secondary_color'  => '#D4AF37',
    'accent_color'     => '#10B981',
    'sidebar_color'    => '',
    'font_heading'     => 'Playfair Display',
    'font_body'        => 'Inter',
];

function brand($key) {
    global $pdo;
    $default = BRAND_DEFAULTS[$key] ?? '';
    $val = $pdo ? get_setting($pdo, $key, $default) : $default;
    return ($val === '' || $val === null) ? $default : $val;
}

function e($s) {
    return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
}

/** Relative prefix from the current script back to the project root ('' or '../'). */
function base_prefix() {
    $dir = basename(dirname($_SERVER['SCRIPT_FILENAME'] ?? ''));
    return in_array($dir, ['modules', 'auth', 'api'], true) ? '../' : '';
}

function asset_url($path) {
    if ($path === '' || preg_match('#^(https?:)?//#', $path)) return $path;
    return base_prefix() . str_replace(' ', '%20', ltrim($path, '/'));
}

function brand_logo_url() {
    $logo = brand('logo_url');
    if (!preg_match('#^(https?:)?//#', $logo) && !file_exists(__DIR__ . '/../' . $logo)) {
        $logo = BRAND_DEFAULTS['logo_url'];
    }
    return asset_url($logo);
}

function brand_favicon_url() {
    $fav = brand('favicon_url');
    return $fav ? asset_url($fav) : brand_logo_url();
}

/** Two-tone wordmark, e.g. Skope<span class="text-gold">Stay</span>. */
function brand_wordmark($accent_class = 'text-gold') {
    $w1 = brand('brand_word_1');
    $w2 = brand('brand_word_2');
    return e($w1) . ($w2 !== '' ? '<span class="' . e($accent_class) . '">' . e($w2) . '</span>' : '');
}

function brand_short_name() {
    return trim(brand('brand_word_1') . brand('brand_word_2'));
}

/** Logo badge: uploaded image or a Material icon, depending on logo_display. */
function brand_mark($box_class = 'w-10 h-10 rounded-xl', $icon_class = 'text-2xl') {
    if (brand('logo_display') === 'icon') {
        return '<div class="' . e($box_class) . ' bg-navy text-gold flex items-center justify-center shrink-0 shadow-md">'
             . '<span class="material-symbols-outlined ' . e($icon_class) . '">' . e(brand('brand_icon')) . '</span></div>';
    }
    return '<div class="' . e($box_class) . ' bg-white flex items-center justify-center shrink-0 shadow-md overflow-hidden p-1">'
         . '<img src="' . e(brand_logo_url()) . '" alt="' . e(brand('hotel_name')) . ' logo" class="w-full h-full object-contain"></div>';
}

function brand_favicon_tags() {
    $url = e(brand_favicon_url());
    return '<link rel="icon" href="' . $url . '"><link rel="apple-touch-icon" href="' . $url . '">'
         . '<meta name="theme-color" content="' . e(brand('primary_color')) . '">';
}

// ---- Colour maths -------------------------------------------------------

function valid_hex($hex) {
    return is_string($hex) && preg_match('/^#([0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/', $hex);
}

function hex_rgb_array($hex) {
    $hex = ltrim($hex, '#');
    if (strlen($hex) === 3) $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
    return [hexdec(substr($hex, 0, 2)), hexdec(substr($hex, 2, 2)), hexdec(substr($hex, 4, 2))];
}

/** Mix a colour toward white (amount > 0) or black (amount < 0). */
function shade_hex($hex, $amount) {
    if (!valid_hex($hex)) return $hex;
    $target = $amount > 0 ? 255 : 0;
    $a = abs($amount);
    $out = array_map(fn($c) => (int)round($c + ($target - $c) * $a), hex_rgb_array($hex));
    return sprintf('#%02X%02X%02X', ...$out);
}

/** Readable text colour (navy-ish dark or white) for a given background. */
function contrast_text($hex) {
    // Pick whichever of dark/white gives the higher WCAG contrast ratio
    $lin = array_map(function ($c) { $c /= 255; return $c <= 0.03928 ? $c / 12.92 : pow(($c + 0.055) / 1.055, 2.4); }, hex_rgb_array($hex));
    $L = 0.2126 * $lin[0] + 0.7152 * $lin[1] + 0.0722 * $lin[2];
    $vs_white = 1.05 / ($L + 0.05);
    $vs_dark = ($L + 0.05) / 0.0607; // #0B132B luminance ~0.0107
    return $vs_dark >= $vs_white ? '#0B132B' : '#FFFFFF';
}

/** Full palette derived from the three admin colours. */
function brand_palette() {
    $p = brand('primary_color');   if (!valid_hex($p)) $p = BRAND_DEFAULTS['primary_color'];
    $s = brand('secondary_color'); if (!valid_hex($s)) $s = BRAND_DEFAULTS['secondary_color'];
    $a = brand('accent_color');    if (!valid_hex($a)) $a = BRAND_DEFAULTS['accent_color'];
    $sb = brand('sidebar_color');  if (!valid_hex($sb)) $sb = shade_hex($p, -0.15);
    return [
        'primary'        => $p,
        'primary_dark'   => shade_hex($p, -0.35),
        'primary_light'  => shade_hex($p, 0.12),
        'primary_subtle' => shade_hex($p, 0.22),
        'secondary'      => $s,
        'secondary_light'=> shade_hex($s, 0.55),
        'secondary_dark' => shade_hex($s, -0.25),
        'accent'         => $a,
        'sidebar'        => $sb,
        'sidebar_end'    => shade_hex($sb, 0.12),
        'on_secondary'   => contrast_text($s),
    ];
}

/** CSS variables + brand utility overrides shared by every layout. */
function brand_css_vars() {
    $c = brand_palette();
    [$pr, $pg, $pb] = hex_rgb_array($c['primary']);
    [$sr, $sg, $sbb] = hex_rgb_array($c['secondary']);
    return ":root{--theme-primary:{$c['primary']};--theme-primary-dark:{$c['primary_dark']};--theme-primary-light:{$c['primary_light']};"
         . "--theme-secondary:{$c['secondary']};--theme-secondary-light:{$c['secondary_light']};--theme-secondary-dark:{$c['secondary_dark']};"
         . "--theme-accent:{$c['accent']};--theme-sidebar:{$c['sidebar']};--theme-sidebar-end:{$c['sidebar_end']};"
         . "--theme-on-secondary:{$c['on_secondary']};--theme-primary-rgb:$pr,$pg,$pb;--theme-secondary-rgb:$sr,$sg,$sbb;}";
}

/** JS-side colour constants so SweetAlert buttons etc. follow the theme. */
function brand_js() {
    $c = brand_palette();
    return '<script>window.BRAND=' . json_encode([
        'name' => brand('hotel_name'), 'short' => brand_short_name(),
        'primary' => $c['primary'], 'secondary' => $c['secondary'], 'accent' => $c['accent'],
        'currency' => get_setting($GLOBALS['pdo'], 'currency_symbol', 'KSh'),
    ]) . ';</script>';
}

function money($amount) {
    global $pdo;
    return get_setting($pdo, 'currency_symbol', 'KSh') . ' ' . number_format((float)$amount, 2);
}

// ---- CSRF ---------------------------------------------------------------

function csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_meta() {
    return '<meta name="csrf-token" content="' . e(csrf_token()) . '">';
}

function verify_csrf() {
    $sent = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? ($_POST['csrf_token'] ?? '');
    if (!$sent || empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $sent)) {
        http_response_code(419);
        echo json_encode(['success' => false, 'message' => 'Security token expired. Please refresh the page and try again.']);
        exit;
    }
}

/** True for roles allowed to change system configuration. */
function is_admin_role($role) {
    return in_array($role, ['Super Admin', 'Hotel Manager', 'Manager'], true);
}
