<?php
include '../database.php';
$db = new Database();
$conn = $db->connect();
$cols = $conn->query("DESCRIBE books")->fetchAll(PDO::FETCH_ASSOC);
echo "<pre>";
print_r($cols);
echo "</pre>";
?>
