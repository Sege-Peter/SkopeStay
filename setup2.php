<?php
require_once 'includes/config.php';

$sqls = [
    // Add total column to restaurant_orders if it's missing
    "ALTER TABLE `restaurant_orders` ADD COLUMN `total` DECIMAL(10,2) NOT NULL DEFAULT 0.00 AFTER `guest_name`"
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

// Now insert the sample data again
$inserts = [
    "INSERT IGNORE INTO `restaurant_orders` (`id`,`table_number`,`guest_name`,`total`,`status`) VALUES
    (1,'Table 3','James Otieno',3480.00,'Served'),
    (2,'Table 7','Walk-in Guest',1566.00,'Pending'),
    (3,'Table 1','Mary Wanjiku',2900.00,'Preparing')",
];

foreach ($inserts as $sql) {
    try {
        $pdo->exec($sql);
        echo "✅ OK Insert\n";
    } catch (Exception $e) {
        echo "❌ " . $e->getMessage() . "\n";
    }
}
echo "Done.\n";
