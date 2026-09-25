<?php
require_once 'includes/config.php';

$sqls = [
    // Add item_name column to restaurant_order_items if it's missing or alter it
    "ALTER TABLE `restaurant_order_items` ADD COLUMN `item_name` VARCHAR(100) NOT NULL AFTER `order_id`"
];

foreach ($sqls as $sql) {
    try {
        $pdo->exec($sql);
        echo "✅ OK: $sql\n";
    } catch (Exception $e) {
        // Might fail if column already exists, which is fine
        echo "ℹ️ Note: " . $e->getMessage() . "\n";
    }
}
echo "Done.\n";
