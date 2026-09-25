<?php
require_once 'includes/config.php';

$images = [
    'Grilled Tilapia' => 'https://images.unsplash.com/photo-1580476262798-bddd9f4b7369?q=80&w=2070&auto=format&fit=crop',
    'Beef Steak' => 'https://images.unsplash.com/photo-1600891964092-4316c288032e?q=80&w=2070&auto=format&fit=crop',
    'Fresh Juice' => 'https://images.unsplash.com/photo-1622597467836-f38ec2ee5e1f?q=80&w=1932&auto=format&fit=crop',
    'Choco Lava' => 'https://images.unsplash.com/photo-1606313564200-e75d5e30476c?q=80&w=1974&auto=format&fit=crop',
    'Caesar Salad' => 'https://images.unsplash.com/photo-1550304943-4f24f54ddde9?q=80&w=2070&auto=format&fit=crop',
    'Skope Burger' => 'https://images.unsplash.com/photo-1568901346375-23c9450c58cd?q=80&w=1899&auto=format&fit=crop',
    'Chicken Wings' => 'https://images.unsplash.com/photo-1524114664604-cd8133cd67ad?q=80&w=1984&auto=format&fit=crop',
    'Cappuccino' => 'https://images.unsplash.com/photo-1534040385115-33dcb3acba5b?q=80&w=1974&auto=format&fit=crop',
    'Fruit Platter' => 'https://images.unsplash.com/photo-1490474418585-ba9eb8fd36ea?q=80&w=2070&auto=format&fit=crop'
];

try {
    $stmt = $pdo->prepare("UPDATE restaurant_items SET image_url = ? WHERE name = ?");
    $count = 0;
    foreach ($images as $name => $url) {
        $stmt->execute([$url, $name]);
        $count += $stmt->rowCount();
    }
    echo "SUCCESS: Updated $count menu items with real food images!";
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage();
}
