<?php
require_once 'includes/config.php';

$sqls = [
    "CREATE TABLE IF NOT EXISTS `inquiries` (
      `id` INT AUTO_INCREMENT PRIMARY KEY,
      `full_name` VARCHAR(150) NOT NULL,
      `email` VARCHAR(150) DEFAULT NULL,
      `phone` VARCHAR(50) DEFAULT NULL,
      `inquiry_type` VARCHAR(100) NOT NULL DEFAULT 'Contact',
      `message` TEXT,
      `status` ENUM('Unread','Read','Responded') DEFAULT 'Unread',
      `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
];

foreach ($sqls as $sql) {
    try {
        $pdo->exec($sql);
        echo "✅ OK: Table created.\n";
    } catch (Exception $e) {
        echo "❌ " . $e->getMessage() . "\n";
    }
}
echo "Done.\n";
