<?php
$page_title = 'Dashboard - Library Management System';
require_once 'includes/helpers.php';
require_once 'includes/stats.php';
include 'header.php';

$stats = getLibraryStats($conn);

$transactions = $conn->query("
    SELECT borrowers.fullname, books.Title, books.status
    FROM books
    INNER JOIN borrowers ON borrowers.id = books.borrower_id
    ORDER BY books.BookID DESC
    LIMIT 5
")->fetchAll(PDO::FETCH_ASSOC);
?>

<style>
    .stats-grid { max-width: 1400px; }
    .table-section { max-width: 1400px; }
    .table-header-box h2 { margin: 0; font-size: 19px; color: #111827; font-weight: 700; }
    .styled-table th, .styled-table td { text-align: left; padding: 15px 25px; border-bottom: 1px solid #f3f4f6; border-left: none; border-right: none; }
</style>

<div style="margin-bottom: 30px;">
    <h1 style="font-size: 26px; font-weight: 700; color: #111827; margin: 0;">Dashboard</h1>
    <p style="color: #4b5563; font-size: 16px; margin-top: 8px; font-weight: 500;">Welcome back to the Library Management System!</p>
</div>

<div class="stats-grid">
    <div class="stats-card">
        <div class="stats-icon icon-total"><i class="fa-solid fa-book"></i></div>
        <div class="stats-info">
            <div class="stats-label">Total Books</div>
            <div class="stats-number"><?php echo $stats['total_books']; ?></div>
        </div>
    </div>
    <div class="stats-card">
        <div class="stats-icon icon-active"><i class="fa-solid fa-square-check"></i></div>
        <div class="stats-info">
            <div class="stats-label">Available Books</div>
            <div class="stats-number"><?php echo $stats['available_books']; ?></div>
        </div>
    </div>
    <div class="stats-card">
        <div class="stats-icon icon-inactive"><i class="fa-solid fa-rectangle-xmark"></i></div>
        <div class="stats-info">
            <div class="stats-label">Borrowed Books</div>
            <div class="stats-number"><?php echo $stats['borrowed_books']; ?></div>
        </div>
    </div>
    <div class="stats-card">
        <div class="stats-icon icon-admins"><i class="fa-solid fa-users"></i></div>
        <div class="stats-info">
            <div class="stats-label">Total Members</div>
            <div class="stats-number"><?php echo $stats['total_members']; ?></div>
        </div>
    </div>
</div>

<div class="table-section">
    <div class="table-header-box">
        <h2>Recent Transactions</h2>
    </div>
    <table class="styled-table">
        <thead>
            <tr>
                <th>BORROWER</th>
                <th>BOOK</th>
                <th>DATE</th>
                <th>STATUS</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($transactions as $row): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['fullname']); ?></td>
                <td><?php echo htmlspecialchars($row['Title']); ?></td>
                <td><?php echo date('Y-m-d'); ?></td>
                <td>
                    <?php if ($row['status'] === 'Available'): ?>
                        <span class="status-pill status-returned">Returned</span>
                    <?php else: ?>
                        <span class="status-pill status-borrowed">Borrowed</span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($transactions)): ?>
            <tr>
                <td colspan="4" style="text-align: center; color: #9ca3af;">No recent transactions.</td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include 'footer.php'; ?>
