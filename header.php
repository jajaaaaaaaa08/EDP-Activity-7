<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

require_once 'database.php';
$database = new Database();
$conn = $database->connect();

$current_page = basename($_SERVER['PHP_SELF'], '.php');
?>
<!DOCTYPE html>
<html lang="en" class="app-layout">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? htmlspecialchars($page_title) : 'Library Management System'; ?></title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>

<div class="top-navbar">
    <div class="navbar-left">
        <div class="top-logo"><i class="fa-solid fa-book-open"></i></div>
        <div class="system-title">Library Management System</div>
    </div>
    <div class="navbar-right" id="profileDropdownTrigger">
        <div class="admin-icon"><i class="fa-regular fa-user"></i></div>
        <span class="admin-text"><?php echo htmlspecialchars($_SESSION['username'] ?? 'Admin'); ?></span>
        <i class="fa-solid fa-chevron-down"></i>

        <div class="profile-dropdown" id="profileDropdown">
            <a href="profile.php" class="dropdown-item">
                <i class="fa-regular fa-user"></i> View Profile
            </a>
            <a href="settings.php" class="dropdown-item">
                <i class="fa-solid fa-gear"></i> Settings
            </a>
            <div class="dropdown-divider"></div>
            <a href="javascript:void(0)" class="dropdown-item logout" onclick="showLogoutModal()">
                <i class="fa-solid fa-arrow-right-from-bracket"></i> Logout
            </a>
        </div>
    </div>
</div>

<div class="main-container">
    <div class="sidebar">
        <a href="dashboard.php" class="sidebar-link <?php echo $current_page === 'dashboard' ? 'active' : ''; ?>"><i class="fa-solid fa-table-cells-large"></i> Dashboard</a>
        <a href="books.php" class="sidebar-link <?php echo $current_page === 'books' ? 'active' : ''; ?>"><i class="fa-solid fa-book"></i> Books</a>
        <a href="borrowers.php" class="sidebar-link <?php echo $current_page === 'borrowers' ? 'active' : ''; ?>"><i class="fa-solid fa-users"></i> Borrowers</a>
        <a href="transactions.php" class="sidebar-link <?php echo $current_page === 'transactions' ? 'active' : ''; ?>"><i class="fa-solid fa-right-left"></i> Transactions</a>
        <a href="reports.php" class="sidebar-link <?php echo $current_page === 'reports' ? 'active' : ''; ?>"><i class="fa-regular fa-file-lines"></i> Reports</a>
        <div class="admin-wrapper">
            <div class="admin-divider"></div>
            <div class="admin-section-label">ADMINISTRATION</div>
            <a href="search_user.php" class="sidebar-link <?php echo $current_page === 'search_user' ? 'active' : ''; ?>"><i class="fa-solid fa-shield"></i> User Management</a>
        </div>
        <a href="about.php" class="sidebar-link <?php echo $current_page === 'about' ? 'active' : ''; ?>"><i class="fa-solid fa-circle-info"></i> About</a>
    </div>

    <div class="content-area">
