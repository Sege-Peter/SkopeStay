<?php
/**
 * Lightweight, idempotent schema upgrades.
 * Each step runs once; progress is stored in system_settings.schema_version.
 */

const SKOPESTAY_SCHEMA_VERSION = 5;

function run_migrations($pdo) {
    try {
        $current = (int)$pdo->query("SELECT setting_value FROM system_settings WHERE setting_key = 'schema_version'")->fetchColumn();
    } catch (Exception $e) {
        return; // system_settings not installed yet; setup scripts handle that
    }
    if ($current >= SKOPESTAY_SCHEMA_VERSION) return;

    $column_exists = function ($table, $col) use ($pdo) {
        $st = $pdo->prepare("SHOW COLUMNS FROM `$table` LIKE ?");
        $st->execute([$col]);
        return (bool)$st->fetch();
    };

    if ($current < 1) {
        $seed = $pdo->prepare("INSERT IGNORE INTO system_settings (setting_key, setting_value, setting_group, description) VALUES (?, ?, ?, ?)");
        $rows = [
            ['brand_word_1', 'Skope', 'branding', 'First part of the two-tone wordmark'],
            ['brand_word_2', 'Stay', 'branding', 'Accent-coloured second part of the wordmark'],
            ['brand_subtitle', 'Resort & Suites', 'branding', 'Small caption under the public wordmark'],
            ['erp_subtitle', 'Hospitality ERP', 'branding', 'Caption under the wordmark in the admin sidebar'],
            ['favicon_url', '', 'branding', 'Browser tab icon (falls back to the logo)'],
            ['logo_display', 'logo', 'branding', 'Show uploaded logo image or a Material icon'],
            ['brand_icon', 'apartment', 'branding', 'Material Symbols icon used when logo_display = icon'],
            ['sidebar_color', '', 'theme', 'Admin sidebar background (blank = derived from primary)'],
            ['whatsapp_number', '254742380183', 'social', 'WhatsApp number in international format, digits only'],
            ['social_facebook', '', 'social', 'Facebook page URL'],
            ['social_instagram', '', 'social', 'Instagram profile URL'],
            ['social_x', '', 'social', 'X / Twitter profile URL'],
            ['social_tiktok', '', 'social', 'TikTok profile URL'],
            ['social_linkedin', '', 'social', 'LinkedIn page URL'],
            ['social_youtube', '', 'social', 'YouTube channel URL'],
            ['footer_about', 'An architectural masterpiece blending coastal tranquility with smart luxury hospitality. Experience bespoke living, culinary brilliance, and effortless reservations.', 'website', 'Short about text in the public footer'],
            ['meta_description', 'Luxury resort suites, fine dining, events and leisure with instant online booking.', 'website', 'Search engine description for the public site'],
            ['google_maps_embed', '', 'website', 'Google Maps embed URL for the contact section'],
            ['receipt_footer', 'Thank you for staying with us. We look forward to welcoming you back.', 'billing', 'Closing note printed on receipts'],
            ['booking_prefix', 'BK', 'billing', 'Prefix for booking reference numbers'],
            ['allow_public_signup', '0', 'security', 'Allow visitors to create portal accounts'],
            ['session_timeout', '120', 'security', 'Minutes of inactivity before staff are signed out'],
        ];
        foreach ($rows as $r) $seed->execute($r);
    }

    if ($current < 2) {
        // Public bookings need a Pending state before staff confirm them
        $pdo->exec("ALTER TABLE bookings MODIFY status ENUM('Pending','Active','CheckedOut','Cancelled') DEFAULT 'Active'");

        if (!$column_exists('users', 'full_name'))  $pdo->exec("ALTER TABLE users ADD full_name VARCHAR(150) NULL AFTER username");
        if (!$column_exists('users', 'phone'))      $pdo->exec("ALTER TABLE users ADD phone VARCHAR(50) NULL AFTER email");
        if (!$column_exists('users', 'is_active'))  $pdo->exec("ALTER TABLE users ADD is_active TINYINT(1) NOT NULL DEFAULT 1");
        if (!$column_exists('users', 'last_login')) $pdo->exec("ALTER TABLE users ADD last_login DATETIME NULL");

        if (!$column_exists('inquiries', 'staff_notes')) $pdo->exec("ALTER TABLE inquiries ADD staff_notes TEXT NULL");

        $pdo->exec("CREATE TABLE IF NOT EXISTS newsletter_subscribers (
            id INT AUTO_INCREMENT PRIMARY KEY,
            email VARCHAR(150) NOT NULL UNIQUE,
            source VARCHAR(50) DEFAULT 'footer',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    }

    if ($current < 3) {
        $pdo->exec("INSERT IGNORE INTO permissions (name, module, description) VALUES ('inquiries_manage', 'Inquiries', 'View and respond to guest inquiries')");
    }

    if ($current < 4) {
        // Front-of-house roles can work the guest inquiry inbox
        $pdo->exec("INSERT IGNORE INTO role_permissions (role_id, permission_id)
                    SELECT r.id, p.id FROM roles r JOIN permissions p ON p.name = 'inquiries_manage'
                    WHERE r.name IN ('Super Admin', 'Hotel Manager', 'Receptionist')");
    }

    if ($current < 5) {
        $seed = $pdo->prepare("INSERT IGNORE INTO system_settings (setting_key, setting_value, setting_group, description) VALUES (?, ?, ?, ?)");
        $seed->execute(['show_demo_login', '1', 'security', 'Show the demo credentials autofill on the sign-in page']);
        $seed->execute(['login_background', '', 'branding', 'Custom background image for the sign-in page']);
    }

    $pdo->prepare("INSERT INTO system_settings (setting_key, setting_value, setting_group, description)
                   VALUES ('schema_version', ?, 'system', 'Internal schema version')
                   ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)")
        ->execute([(string)SKOPESTAY_SCHEMA_VERSION]);
}
