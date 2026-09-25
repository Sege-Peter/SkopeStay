<?php
require_once 'includes/config.php';

try {
    // Drop the problematic foreign key
    $pdo->exec("ALTER TABLE `restaurant_order_items` DROP FOREIGN KEY `restaurant_order_items_ibfk_2`");
    echo "✅ Dropped foreign key restaurant_order_items_ibfk_2\n";
} catch (Exception $e) {
    echo "ℹ️ FK might already be dropped: " . $e->getMessage() . "\n";
}

try {
    // Also drop the item_id column if it exists since we don't use it anymore
    $pdo->exec("ALTER TABLE `restaurant_order_items` DROP COLUMN `item_id`");
    echo "✅ Dropped column item_id\n";
} catch (Exception $e) {
    echo "ℹ️ Column item_id might already be dropped: " . $e->getMessage() . "\n";
}
echo "Done.\n";
