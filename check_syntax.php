<?php
$files = ['includes/sidebar.php','modules/rooms.php','modules/dashboard.php','modules/inventory.php','modules/finance.php','modules/settings.php','modules/bookings.php','modules/bookings_calendar.php'];
$all_ok = true;
foreach ($files as $f) {
    $output = shell_exec("C:\\xampp\\php\\php.exe -l \"$f\" 2>&1");
    $ok = strpos($output, 'No syntax errors') !== false;
    echo ($ok ? '✅' : '❌') . " $f: " . trim($output) . "\n";
    if (!$ok) $all_ok = false;
}
echo $all_ok ? "\n✅ All files OK!\n" : "\n❌ Some files have errors.\n";
?>
