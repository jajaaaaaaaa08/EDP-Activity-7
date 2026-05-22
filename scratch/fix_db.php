<?php
include 'database.php';
$db = new Database();
$c = $db->connect();
try {
    $c->query("ALTER TABLE transactions ADD COLUMN due_date DATE AFTER borrow_date");
} catch(Exception $e) {}
try {
    $c->query("ALTER TABLE transactions ADD COLUMN return_date DATE AFTER status");
} catch(Exception $e) {}
echo "Done";
