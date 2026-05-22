<?php
$page_title = 'Reports - Library Management System';
require_once 'includes/helpers.php';
require_once 'includes/stats.php';
require_once 'includes/reports.php';
include 'header.php';

$stats = getLibraryStats($conn);
$report_type = $_POST['report_type'] ?? 'Inventory Report';
$report_data = [];

if (isset($_POST['generate_preview'])) {
    $report_data = fetchReportPreview($conn, $report_type);
}
?>

<style>
    .stats-card { padding: 24px; gap: 20px; }
    .stats-icon { width: 56px; height: 56px; font-size: 24px; }
    .icon-total { background: #eff6ff; color: #2563eb; }
    .icon-active { background: #f0fdf4; color: #166534; }
    .icon-inactive { background: #fef2f2; color: #dc2626; }
    .stats-label { font-size: 14px; margin-bottom: 4px; }
    .stats-number { font-size: 24px; }
    .styled-table th, .styled-table td { text-align: left; }
    .styled-table td { padding: 15px 25px; border-bottom: 1px solid #f3f4f6; border-left: none; border-right: none; }
</style>

<div style="margin-bottom: 30px;">
    <h1 style="font-size: 26px; font-weight: 700; color: #111827; margin: 0;">Report Generator</h1>
    <p style="color: #4b5563; font-size: 16px; margin-top: 8px; font-weight: 500;">Generate and download library status and history reports.</p>
</div>

<div class="stats-grid">
    <div class="stats-card">
        <div class="stats-icon icon-total"><i class="fa-solid fa-book"></i></div>
        <div class="stats-info">
            <div class="stats-label">Total Inventory</div>
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
</div>

<div class="filter-section">
    <form method="POST" action="reports.php">
        <div style="display: flex; gap: 20px; align-items: end;">
            <div style="flex: 1;">
                <label style="display: block; font-size: 16px; font-weight: 700; color: #111827; margin-bottom: 10px;">Report Type</label>
                <select name="report_type" style="width: 100%; padding: 12px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 15px; outline: none;">
                    <option value="Inventory Report" <?php echo $report_type === 'Inventory Report' ? 'selected' : ''; ?>>Inventory Report</option>
                    <option value="Borrowed Books Report" <?php echo $report_type === 'Borrowed Books Report' ? 'selected' : ''; ?>>Borrowed Books Report</option>
                    <option value="Returned Books Report" <?php echo $report_type === 'Returned Books Report' ? 'selected' : ''; ?>>Returned Books Report</option>
                </select>
            </div>
            <div style="display: flex; gap: 12px;">
                <button type="submit" name="generate_preview" style="background: #059669; color: white; border: none; padding: 12px 25px; border-radius: 8px; font-weight: 600; font-size: 14px; cursor: pointer; display: flex; align-items: center; gap: 8px;">
                    <i class="fa-solid fa-eye"></i> Preview
                </button>
                <button type="submit" formaction="export.php" style="background: #1d6f42; color: white; border: none; padding: 12px 25px; border-radius: 8px; font-weight: 600; font-size: 14px; cursor: pointer; display: flex; align-items: center; gap: 8px;">
                    <i class="fa-solid fa-file-excel"></i> Export Excel
                </button>
            </div>
        </div>
    </form>
</div>

<?php if (isset($_POST['generate_preview'])): ?>
<div class="table-section">
    <div class="table-header-box">
        <h2 style="margin: 0; font-size: 17px; color: #374151; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em;">Preview: <?php echo htmlspecialchars($report_type); ?></h2>
    </div>
    <table class="styled-table">
        <thead>
            <tr>
                <?php if ($report_type === 'Inventory Report'): ?>
                    <th>BOOK ID</th>
                    <th>TITLE</th>
                    <th>AUTHOR</th>
                    <th>CATEGORY</th>
                    <th>STATUS</th>
                <?php else: ?>
                    <th>TRANSACTION ID</th>
                    <th>BORROWER</th>
                    <th>BOOK TITLE</th>
                    <th><?php echo $report_type === 'Borrowed Books Report' ? 'DUE DATE' : 'RETURN DATE'; ?></th>
                    <th>STATUS</th>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($report_data)): ?>
                <?php foreach ($report_data as $row): ?>
                    <tr>
                        <?php if ($report_type === 'Inventory Report'): ?>
                            <td><?php echo formatBookId((int) $row['BookID']); ?></td>
                            <td style="font-weight: 500; color: #111827;"><?php echo htmlspecialchars($row['Title']); ?></td>
                            <td><?php echo htmlspecialchars($row['Author']); ?></td>
                            <td><span style="background: #f3f4f6; padding: 4px 10px; border-radius: 6px; font-size: 12px; font-weight: 500;"><?php echo htmlspecialchars($row['CategoryName'] ?? 'General'); ?></span></td>
                            <td>
                                <span class="status-pill <?php echo $row['status'] === 'Available' ? 'status-returned' : 'status-borrowed'; ?>">
                                    <?php echo $row['status']; ?>
                                </span>
                            </td>
                        <?php else: ?>
                            <td><?php echo formatTransactionId((int) $row['id']); ?></td>
                            <td style="font-weight: 500; color: #111827;"><?php echo htmlspecialchars($row['borrower_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['book_title']); ?></td>
                            <td><?php echo $row['display_date']; ?></td>
                            <td>
                                <span class="status-pill <?php echo $row['status'] === 'Borrowed' ? 'status-borrowed' : 'status-returned'; ?>">
                                    <?php echo $row['status']; ?>
                                </span>
                            </td>
                        <?php endif; ?>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" style="text-align: center; padding: 60px; color: #9ca3af; font-size: 15px;">No records found for the selected criteria.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>

<?php include 'footer.php'; ?>
