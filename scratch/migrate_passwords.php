<?php
require_once __DIR__ . '/../database.php';

$database = new Database();
$conn = $database->connect();

// Fetch all users
$stmt = $conn->query("SELECT UserID, Username, Password FROM users");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "Starting Password Migration...\n";
$migratedCount = 0;

foreach ($users as $user) {
    $password = $user['Password'];
    // Check if already hashed with Bcrypt (starts with $2y$)
    if (strpos($password, '$2y$') === 0) {
        echo "User '{$user['Username']}' already has a hashed password. Skipping.\n";
        continue;
    }

    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    
    $updateStmt = $conn->prepare("UPDATE users SET Password = :password WHERE UserID = :id");
    $updateStmt->execute([
        ':password' => $hashedPassword,
        ':id' => $user['UserID']
    ]);
    
    echo "Migrated user '{$user['Username']}' password to secure hash.\n";
    $migratedCount++;
}

echo "Password Migration completed. Migrated {$migratedCount} users.\n";
