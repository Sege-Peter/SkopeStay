<?php
require_once 'includes/config.php';

$updates = [
    'Deluxe King' => [
        'occupancy' => 2,
        'features' => 'King Bed, City Skyline View, Rainforest Shower, Free High-Speed Wi-Fi, 55" 4K Smart TV',
        'desc' => 'Spacious luxury sanctuary with bespoke furnishings and private skyline balcony.'
    ],
    'Executive Suite' => [
        'occupancy' => 2,
        'features' => 'King Bed, Executive Lounge Area, Jacuzzi Tub, Nespresso Bar, 24/7 Butler Service',
        'desc' => 'Expansive suite featuring dedicated lounge salon and marbled bathroom with Jacuzzi.'
    ],
    'Single Standard' => [
        'occupancy' => 1,
        'features' => 'Premium Single Bed, Ergonomic Workstation, High-Speed Wi-Fi, Rain Shower',
        'desc' => 'Efficient and stylish retreat crafted for corporate and solo travelers.'
    ],
    'Family Suite' => [
        'occupancy' => 4,
        'features' => '2 Queen Beds, Living Room, Kitchenette, 2 Smart TVs, Complimentary Breakfast for 4',
        'desc' => 'Dual-bedroom family retreat with connecting living room and dining kitchenette.'
    ],
    'Honeymoon Suite' => [
        'occupancy' => 2,
        'features' => 'Four-Poster King Bed, Private Sunset Terrace, Hydrotherapy Tub, Champagne Welcome',
        'desc' => 'Romantic haven designed with candlelit ambiance, hydrotherapy tub, and panoramic views.'
    ],
    'Presidential Suite' => [
        'occupancy' => 4,
        'features' => 'Master King Suite, Boardroom, Private Gym, Panoramic Penthouse Terrace, Private Chef',
        'desc' => 'The pinnacle of opulence featuring dedicated private elevator, boardroom, and chef pantry.'
    ]
];

$stmt = $pdo->prepare("UPDATE rooms SET max_occupancy = ?, features = ?, description = ? WHERE type = ?");
foreach ($updates as $type => $data) {
    $stmt->execute([$data['occupancy'], $data['features'], $data['desc'], $type]);
}

echo "Rooms details updated.\n";
