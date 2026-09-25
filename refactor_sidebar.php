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
        
        $replacement = "<?php include '../includes/sidebar.php'; ?>";
        
        // Find the first <aside> and its closing </aside> tag.
        // We assume there's only one <aside> which is the sidebar.
        $new_content = preg_replace('/<aside[\s\S]*?<\/aside>/i', $replacement, $content, 1);
        
        if ($new_content && $new_content !== $content) {
            file_put_contents($file, $new_content);
            echo "Refactored sidebar in $file\n";
        } else {
            echo "Failed or no change in $file\n";
        }
    } else {
        echo "File not found: $file\n";
    }
}
?>
