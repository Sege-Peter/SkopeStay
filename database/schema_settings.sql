-- SkopeStay Enterprise System Settings Schema
CREATE TABLE IF NOT EXISTS `system_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `setting_key` varchar(100) NOT NULL UNIQUE,
  `setting_value` text,
  `setting_group` varchar(50) DEFAULT 'general',
  `description` varchar(255) DEFAULT NULL,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Seed Default Settings
INSERT INTO `system_settings` (`setting_key`, `setting_value`, `setting_group`, `description`) VALUES
-- 1. General Property Identity & Contacts
('hotel_name', 'SkopeStay Resort & Suites', 'general', 'Official resort and company brand name'),
('hotel_tagline', 'Where Pure Luxury Meets Intelligent Operations', 'general', 'Brand slogan and hero headline'),
('hotel_star_rating', '5', 'general', 'Accredited hotel star tier rating'),
('contact_email', 'admin@skopestay.com', 'general', 'Primary administrative contact email'),
('support_email', 'support@skopestay.com', 'general', 'Guest concierge & IT support email'),
('contact_phone', '+254 742 380 183', 'general', 'Primary front desk reception telephone'),
('contact_phone_alt', '+254 712 111 222', 'general', 'Secondary concierge & reservation line'),
('contact_address', 'Mombasa Coastal Highway, Beachfront, Mombasa, Kenya', 'general', 'Physical postal property address'),
('contact_city', 'Mombasa', 'general', 'Resort city location'),
('contact_country', 'Kenya', 'general', 'Country of operation'),
('tax_pin', 'P051283928X', 'general', 'National revenue authority tax PIN'),
('currency_code', 'KES', 'general', 'Operating financial currency ISO code'),
('currency_symbol', 'KSh', 'general', 'Display currency symbol'),
('tax_rate', '16', 'general', 'Default VAT tax percentage'),
('service_charge', '5', 'general', 'Default hospitality service charge percentage'),
('timezone', 'Africa/Nairobi', 'general', 'System time zone for audits and bookings'),

-- 2. Theme Colors & Visual Branding
('theme_palette', 'navy_gold', 'theme', 'Active theme color preset'),
('primary_color', '#0B132B', 'theme', 'Primary brand luxury color (hex)'),
('secondary_color', '#D4AF37', 'theme', 'Secondary luxury accent color (hex)'),
('accent_color', '#10B981', 'theme', 'Action & success accent color (hex)'),
('font_heading', 'Playfair Display', 'theme', 'Primary display serif font'),
('font_body', 'Inter', 'theme', 'Primary UI sans-serif font'),
('logo_url', 'assets/images/SkopeStay logo.png', 'theme', 'Path to official high-resolution logo'),

-- 3. Maintenance Mode
('maintenance_mode', '0', 'maintenance', 'Global maintenance mode toggle (1 = Enabled, 0 = Disabled)'),
('maintenance_title', 'Enhancing Your 5-Star Experience', 'maintenance', 'Heading displayed on public maintenance screen'),
('maintenance_message', 'We are currently performing scheduled maintenance to upgrade our digital concierge and reservation ecosystem. We apologize for the temporary interruption and will be back online shortly.', 'maintenance', 'Detailed notice for visitors'),
('maintenance_estimated_end', 'Scheduled duration: approx. 45 minutes', 'maintenance', 'Estimated downtime notice'),
('maintenance_bypass_key', 'skope_vip_bypass_2026', 'maintenance', 'Secret URL key allowing VIP/staff bypass (?bypass=KEY)'),

-- 4. Operations & Reservations
('checkin_time', '14:00', 'operations', 'Standard daily guest check-in time'),
('checkout_time', '11:00', 'operations', 'Standard daily guest check-out time'),
('enable_online_booking', '1', 'operations', 'Enable public direct suite booking engine'),
('enable_restaurant_pos', '1', 'operations', 'Enable dining restaurant POS & room service'),
('enable_pool_passes', '1', 'operations', 'Enable pool & leisure day pass booking'),
('enable_hall_booking', '1', 'operations', 'Enable banquet & conference hall inquiries'),

-- 5. Notifications
('notify_new_booking', '1', 'notifications', 'Send alert on new room reservation'),
('notify_maintenance', '1', 'notifications', 'Send alert on new CMMS work order'),
('notify_low_stock', '1', 'notifications', 'Send alert on low inventory threshold'),
('notify_email_recipient', 'admin@skopestay.com', 'notifications', 'Destination inbox for automated alerts')
ON DUPLICATE KEY UPDATE `description` = VALUES(`description`);
