<?php
require_once 'includes/config.php';

$rooms = $pdo->query("SELECT COUNT(*) as cnt, status FROM rooms GROUP BY status")->fetchAll();
$items = $pdo->query("SELECT COUNT(*) as cnt, status FROM inventory_items GROUP BY status")->fetchAll();

echo "=== ROOMS ===\n";
foreach($rooms as $r) echo "  {$r['status']}: {$r['cnt']}\n";

echo "\n=== INVENTORY ===\n";
foreach($items as $i) echo "  {$i['status']}: {$i['cnt']}\n";

echo "\nDatabase seeded successfully!\n";
?>
