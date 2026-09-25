<?php
require_once 'includes/config.php';

$sqls = [
    "ALTER TABLE `restaurant_order_items` ADD COLUMN `price` DECIMAL(10,2) NOT NULL AFTER `item_name`",
    "ALTER TABLE `restaurant_order_items` ADD COLUMN `quantity` INT NOT NULL DEFAULT 1 AFTER `price`"
];

foreach ($sqls as $sql) {
    try {
        $pdo->exec($sql);
        echo "✅ OK: $sql\n";
    } catch (Exception $e) {
        echo "ℹ️ Note: " . $e->getMessage() . "\n";
    }
}
echo "Done.\n";
