<?php
include 'database.php';
$db = new Database();
$conn = $db->connect();
if ($conn) {
    echo "Tables in library_db:\n";
    $tables = $conn->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    foreach ($tables as $table) {
        echo "- $table\n";
        $columns = $conn->query("DESCRIBE `$table`")->fetchAll(PDO::FETCH_ASSOC);
        foreach ($columns as $col) {
            echo "  * {$col['Field']} ({$col['Type']}) - Null: {$col['Null']}, Key: {$col['Key']}, Default: {$col['Default']}\n";
        }
    }
}
