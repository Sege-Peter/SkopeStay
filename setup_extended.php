<?php
require_once 'includes/config.php';

$sqls = [
    "CREATE TABLE IF NOT EXISTS `customers` (
      `id` INT AUTO_INCREMENT PRIMARY KEY,
      `full_name` VARCHAR(150) NOT NULL,
      `phone` VARCHAR(50) DEFAULT NULL,
      `email` VARCHAR(150) DEFAULT NULL,
      `national_id` VARCHAR(100) DEFAULT NULL,
      `address` TEXT DEFAULT NULL,
      `loyalty_tier` VARCHAR(50) DEFAULT 'Standard',
      `reward_points` INT DEFAULT 0,
      `customer_category` VARCHAR(50) DEFAULT 'Hotel Guest',
      `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

    "CREATE TABLE IF NOT EXISTS `halls` (
      `id` INT AUTO_INCREMENT PRIMARY KEY,
      `name` VARCHAR(150) NOT NULL,
      `type` VARCHAR(100) NOT NULL,
      `capacity` INT NOT NULL,
      `hourly_rate` DECIMAL(10,2) DEFAULT 0.00,
      `daily_rate` DECIMAL(10,2) DEFAULT 0.00,
      `description` TEXT,
      `amenities` TEXT,
      `status` ENUM('Available','Reserved','Maintenance') DEFAULT 'Available',
      `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

    "CREATE TABLE IF NOT EXISTS `hall_bookings` (
      `id` INT AUTO_INCREMENT PRIMARY KEY,
      `hall_id` INT NOT NULL,
      `customer_id` INT NOT NULL,
      `event_date` DATE NOT NULL,
      `start_time` TIME NOT NULL,
      `end_time` TIME NOT NULL,
      `total_amount` DECIMAL(10,2) DEFAULT 0.00,
      `deposit_paid` DECIMAL(10,2) DEFAULT 0.00,
      `services` TEXT,
      `status` ENUM('Pending','Confirmed','Cancelled','Completed') DEFAULT 'Pending',
      `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
      FOREIGN KEY (`hall_id`) REFERENCES `halls`(`id`) ON DELETE CASCADE,
      FOREIGN KEY (`customer_id`) REFERENCES `customers`(`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",

    "CREATE TABLE IF NOT EXISTS `pool_passes` (
      `id` INT AUTO_INCREMENT PRIMARY KEY,
      `customer_id` INT DEFAULT NULL,
      `guest_name` VARCHAR(150) DEFAULT NULL,
      `phone` VARCHAR(50) DEFAULT NULL,
      `pass_type` VARCHAR(50) NOT NULL,
      `ticket_type` VARCHAR(50) DEFAULT 'Walk-in',
      `visit_date` DATE NOT NULL,
      `entry_time` TIME DEFAULT NULL,
      `number_of_guests` INT DEFAULT 1,
      `amount` DECIMAL(10,2) DEFAULT 0.00,
      `status` ENUM('Active','Expired','Cancelled') DEFAULT 'Active',
      `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
      FOREIGN KEY (`customer_id`) REFERENCES `customers`(`id`) ON DELETE SET NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
];

foreach ($sqls as $sql) {
    try {
        $pdo->exec($sql);
        echo "✅ Created table\n";
    } catch (Exception $e) {
        echo "❌ " . $e->getMessage() . "\n";
    }
}

$inserts = [
    "INSERT IGNORE INTO `customers` (`id`, `full_name`, `phone`, `email`, `loyalty_tier`, `customer_category`) VALUES
    (1, 'John Doe', '+254711223344', 'john@example.com', 'Gold', 'VIP Customer'),
    (2, 'Jane Kamau', '+254722334455', 'jane.k@example.com', 'Standard', 'Hotel Guest')",

    "INSERT IGNORE INTO `halls` (`id`, `name`, `type`, `capacity`, `hourly_rate`, `daily_rate`, `status`, `description`, `amenities`) VALUES
    (1, 'Grand Ballroom', 'Conference Hall', 500, 15000.00, 100000.00, 'Available', 'Luxurious hall for large conferences and weddings.', 'Projector,Sound System,AC,WiFi'),
    (2, 'Oasis Garden', 'Outdoor Event Ground', 1000, 10000.00, 80000.00, 'Available', 'Beautiful garden for outdoor weddings and parties.', 'Tents,Outdoor Seating'),
    (3, 'Executive Boardroom', 'Meeting Room', 20, 2000.00, 15000.00, 'Available', 'Intimate room for high-level corporate meetings.', 'Smart TV,Whiteboard,Video Conferencing')",

    "INSERT IGNORE INTO `pool_passes` (`id`, `guest_name`, `pass_type`, `visit_date`, `amount`) VALUES
    (1, 'Mike Wanjala', 'Daily Adult', CURDATE(), 1000.00),
    (2, 'Sarah Ochieng', 'Daily Child', CURDATE(), 500.00)"
];

foreach ($inserts as $sql) {
    try {
        $pdo->exec($sql);
        echo "✅ Inserted data\n";
    } catch (Exception $e) {
        echo "❌ " . $e->getMessage() . "\n";
    }
}
echo "Done.\n";
