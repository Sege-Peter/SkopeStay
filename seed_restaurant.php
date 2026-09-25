<?php
require_once 'includes/config.php';

$items = [
    ['name' => 'Signature Continental Breakfast', 'category' => 'Breakfast', 'price' => 1850, 'description' => 'Fresh pastries, artisanal preserves, farm eggs, cured meats, and fresh pressed juice', 'image_url' => 'https://images.unsplash.com/photo-1533089860892-a7c6f0a88666?q=80&w=800&auto=format&fit=crop'],
    ['name' => 'Eggs Royale with Smoked Salmon', 'category' => 'Breakfast', 'price' => 1750, 'description' => 'Poached organic eggs, Scottish smoked salmon, toasted brioche and velvety hollandaise', 'image_url' => 'https://images.unsplash.com/photo-1525351484163-7529414344d8?q=80&w=800&auto=format&fit=crop'],
    ['name' => 'Prime Aged Angus Ribeye (350g)', 'category' => 'Main Course', 'price' => 3800, 'description' => 'Charred over aromatic charcoal, accompanied by truffle potato purée and pepper reduction', 'image_url' => 'https://images.unsplash.com/photo-1544025162-d76694265947?q=80&w=800&auto=format&fit=crop'],
    ['name' => 'Grilled Swahili Coast Red Snapper', 'category' => 'Main Course', 'price' => 2900, 'description' => 'Fresh catch marinated in coconut ginger tamarind jus, served with saffron rice', 'image_url' => 'https://images.unsplash.com/photo-1519708227418-c8fd9a32b7a2?q=80&w=800&auto=format&fit=crop'],
    ['name' => 'Wild Truffle & Porcini Tagliatelle', 'category' => 'Main Course', 'price' => 2400, 'description' => 'Handmade pasta tossed with wild porcini, black truffle emulsion and 24-month parmesan', 'image_url' => 'https://images.unsplash.com/photo-1621996346565-e3d5d6281699?q=80&w=800&auto=format&fit=crop'],
    ['name' => 'Skope Gourmet Wagyu Burger', 'category' => 'Main Course', 'price' => 2200, 'description' => 'Wagyu beef patty, smoked provolone, caramelized shallots, brioche bun and rustic fries', 'image_url' => 'https://images.unsplash.com/photo-1568901346375-23c9450c58cd?q=80&w=800&auto=format&fit=crop'],
    ['name' => 'Valrhona Molten Chocolate Lava', 'category' => 'Desserts', 'price' => 1200, 'description' => 'Warm 70% dark chocolate cake with a molten center, served with pistachio gelato', 'image_url' => 'https://images.unsplash.com/photo-1606313564200-e75d5e30476c?q=80&w=800&auto=format&fit=crop'],
    ['name' => 'Gold Leaf Passion Mojito', 'category' => 'Beverages', 'price' => 950, 'description' => 'Fresh mint, coastal passionfruit, sparkling mineral water, crushed cane sugar & edible gold leaf', 'image_url' => 'https://images.unsplash.com/photo-1551024709-8f23befc6f87?q=80&w=800&auto=format&fit=crop']
];

$stmt = $pdo->prepare("INSERT INTO restaurant_items (name, category, price, description, image_url, status) VALUES (?,?,?,?,?, 'Available')");
foreach($items as $it) {
    $stmt->execute([$it['name'], $it['category'], $it['price'], $it['description'], $it['image_url']]);
}
echo "Inserted " . count($items) . " gourmet restaurant items successfully.\n";
