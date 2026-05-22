<?php
include 'database.php';
$database = new Database();
$conn = $database->connect();

echo "Transactions table sample:\n";
$rows = $conn->query("SELECT * FROM transactions LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
print_r($rows);

echo "\nBooks table sample:\n";
$rows = $conn->query("SELECT * FROM books LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
print_r($rows);

echo "\nBorrow table sample:\n";
$rows = $conn->query("SELECT * FROM borrow LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
print_r($rows);
?>
