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
        
        $replacement = "<?php include '../includes/footer.php'; ?>";
        
        // Find the first <footer> and its closing </footer> tag.
        $new_content = preg_replace('/<footer[\s\S]*?<\/footer>/i', $replacement, $content, 1);
        
        if ($new_content && $new_content !== $content) {
            file_put_contents($file, $new_content);
            echo "Refactored footer in $file\n";
        } else {
            echo "Failed or no change in $file\n";
        }
    } else {
        echo "File not found: $file\n";
    }
}
?>
