<?php
$files = [
    'modules/dashboard.php',
    'modules/inventory.php',
    'modules/restaurant.php',
    'modules/rooms.php'
];

foreach ($files as $file) {
    if (file_exists($file)) {
        $content = file_get_contents($file);
        
        $replacement = "<?php include '../includes/header.php'; ?>";
        
        // Find the first <header> and its closing </header> tag.
        $new_content = preg_replace('/<header[\s\S]*?<\/header>/i', $replacement, $content, 1);
        
        if ($new_content && $new_content !== $content) {
            file_put_contents($file, $new_content);
            echo "Refactored header in $file\n";
        } else {
            echo "Failed or no change in $file\n";
        }
    } else {
        echo "File not found: $file\n";
    }
}
?>
