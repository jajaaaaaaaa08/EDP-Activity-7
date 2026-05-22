<?php
include 'database.php';
$database = new Database();
$conn = $database->connect();
$res = $conn->query("SELECT id, book_id, book_title, status FROM transactions WHERE id IN (3, 13)")->fetchAll(PDO::FETCH_ASSOC);
print_r($res);
