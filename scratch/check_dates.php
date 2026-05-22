<?php
include 'database.php';
$database = new Database();
$conn = $database->connect();
$res = $conn->query("SELECT id, book_title, borrow_date, due_date FROM transactions WHERE id IN (3, 6, 8, 10, 12)")->fetchAll(PDO::FETCH_ASSOC);
print_r($res);
