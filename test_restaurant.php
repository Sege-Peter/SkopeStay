<?php
require_once 'includes/config.php';
try {
    $items = $pdo->query("SELECT * FROM restaurant_items")->fetchAll(PDO::FETCH_ASSOC);
    print_r($items);
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage();
}
