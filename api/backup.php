<?php
/**
 * Streams a full SQL dump of the database (structure + data) as a download.
 * Pure PDO so it works on shared hosting without mysqldump.
 */
require_once __DIR__ . '/../includes/config.php';

$uid = $_SESSION['user_id'];
if (!has_permission($pdo, $uid, 'settings_manage')) {
    http_response_code(403);
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Forbidden: administrative privileges required.']);
    exit;
}

set_time_limit(300);
$db = $pdo->query("SELECT DATABASE()")->fetchColumn();
$file = preg_replace('/[^a-z0-9_]/i', '', $db) . '_backup_' . date('Y-m-d_His') . '.sql';

header('Content-Type: application/sql; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $file . '"');
header('Cache-Control: no-store');

log_action($pdo, $uid, 'database_backup', 'Settings', 'Downloaded a full database backup');

echo "-- " . brand('hotel_name') . " database backup\n";
echo "-- Database: `$db`  Generated: " . date('Y-m-d H:i:s T') . "\n\n";
echo "SET NAMES utf8mb4;\nSET FOREIGN_KEY_CHECKS = 0;\nSET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';\n\n";

$tables = $pdo->query("SHOW FULL TABLES WHERE Table_type = 'BASE TABLE'")->fetchAll(PDO::FETCH_NUM);
foreach ($tables as [$table]) {
    $create = $pdo->query("SHOW CREATE TABLE `$table`")->fetch(PDO::FETCH_NUM)[1];
    echo "-- --------------------------------------------------------\n-- Table `$table`\n";
    echo "DROP TABLE IF EXISTS `$table`;\n$create;\n\n";

    $rows = $pdo->query("SELECT * FROM `$table`", PDO::FETCH_NUM);
    $batch = [];
    foreach ($rows as $row) {
        $batch[] = '(' . implode(',', array_map(fn($v) => $v === null ? 'NULL' : $pdo->quote((string)$v), $row)) . ')';
        if (count($batch) >= 200) {
            echo "INSERT INTO `$table` VALUES\n" . implode(",\n", $batch) . ";\n";
            $batch = [];
        }
    }
    if ($batch) echo "INSERT INTO `$table` VALUES\n" . implode(",\n", $batch) . ";\n";
    echo "\n";
    flush();
}
echo "SET FOREIGN_KEY_CHECKS = 1;\n";
