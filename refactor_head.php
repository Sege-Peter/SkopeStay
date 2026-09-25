<?php
$files = [
    'auth/login.php',
    'modules/dashboard.php',
    'modules/inventory.php',
    'modules/restaurant.php',
    'modules/rooms.php'
];

foreach ($files as $file) {
    if (file_exists($file)) {
        $content = file_get_contents($file);
        
        // Extract title
        $title = "SkopeStay Management";
        if (preg_match('/<title>(.*?)<\/title>/is', $content, $matches)) {
            $title = trim($matches[1]);
        }
        
        $depth = strpos($file, 'auth/') !== false || strpos($file, 'modules/') !== false ? '../' : '';
        
        $replacement = "<?php \$page_title = \"$title\"; include '{$depth}includes/head.php'; ?>";
        
        // Replace from <head to </head> (case insensitive, multiline)
        $new_content = preg_replace('/<head>[\s\S]*?<\/head>/i', $replacement, $content);
        
        if ($new_content && $new_content !== $content) {
            file_put_contents($file, $new_content);
            echo "Refactored head in $file\n";
        } else {
            echo "Failed or no change in $file\n";
        }
    } else {
        echo "File not found: $file\n";
    }
}
?>
