<?php
include '../database.php';
$db = new Database();
$conn = $db->connect();

try {
    // 1. Create categories table
    $conn->exec("CREATE TABLE IF NOT EXISTS categories (
        CategoryID INT AUTO_INCREMENT PRIMARY KEY,
        CategoryName VARCHAR(100) NOT NULL
    )");
    echo "Categories table created.\n";

    // 2. Insert categories
    $categories = ['Adventure', 'Mystery', 'Biography', 'Programming', 'Philosophy'];
    $stmt = $conn->prepare("INSERT IGNORE INTO categories (CategoryName) VALUES (?)");
    foreach ($categories as $cat) {
        $stmt->execute([$cat]);
    }
    echo "Categories inserted.\n";

    // 3. Add CategoryID to books table if it doesn't exist
    $cols = $conn->query("DESCRIBE books")->fetchAll(PDO::FETCH_COLUMN);
    if (!in_array('CategoryID', $cols)) {
        $conn->exec("ALTER TABLE books ADD COLUMN CategoryID INT AFTER author");
        echo "CategoryID column added to books table.\n";
    }

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
