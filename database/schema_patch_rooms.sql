ALTER TABLE `rooms` 
  ADD COLUMN `room_name` VARCHAR(100) DEFAULT NULL AFTER `room_number`,
  ADD COLUMN `max_occupancy` INT DEFAULT 2 AFTER `status`,
  ADD COLUMN `adults_allowed` INT DEFAULT 2 AFTER `max_occupancy`,
  ADD COLUMN `children_allowed` INT DEFAULT 0 AFTER `adults_allowed`,
  ADD COLUMN `bed_type` VARCHAR(50) DEFAULT 'Queen Bed' AFTER `children_allowed`,
  ADD COLUMN `num_beds` INT DEFAULT 1 AFTER `bed_type`,
  ADD COLUMN `weekend_rate` DECIMAL(10,2) DEFAULT NULL AFTER `price`,
  ADD COLUMN `holiday_rate` DECIMAL(10,2) DEFAULT NULL AFTER `weekend_rate`,
  ADD COLUMN `discount_percentage` INT DEFAULT 0 AFTER `holiday_rate`,
  ADD COLUMN `features` TEXT DEFAULT NULL AFTER `discount_percentage`,
  ADD COLUMN `description` TEXT DEFAULT NULL AFTER `features`,
  ADD COLUMN `additional_services` TEXT DEFAULT NULL AFTER `description`,
  ADD COLUMN `is_featured` BOOLEAN DEFAULT FALSE AFTER `additional_services`,
  ADD COLUMN `display_on_website` BOOLEAN DEFAULT TRUE AFTER `is_featured`,
  ADD COLUMN `available_online` BOOLEAN DEFAULT TRUE AFTER `display_on_website`,
  ADD COLUMN `seo_slug` VARCHAR(255) DEFAULT NULL AFTER `available_online`,
  ADD COLUMN `meta_title` VARCHAR(255) DEFAULT NULL AFTER `seo_slug`,
  ADD COLUMN `meta_description` TEXT DEFAULT NULL AFTER `meta_title`;

CREATE TABLE IF NOT EXISTS `room_images` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `room_id` int(11) NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `is_primary` tinyint(1) DEFAULT 0,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`room_id`) REFERENCES `rooms`(`id`) ON DELETE CASCADE
);
