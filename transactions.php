<?php
$page_title = 'Transactions - Library Management System';
require_once 'includes/helpers.php';
require_once 'includes/transaction_service.php';
include 'header.php';

if (isset($_POST['issue_book'])) {
    issueBook($conn, (int) $_POST['book_id'], (int) $_POST['borrower_id'], true);
    redirectTo('transactions.php');
}

if (isset($_POST['process_return'])) {
    returnBook($conn, (int) $_POST['transaction_id']);
    redirectTo('transactions.php');
}

$transactions = $conn->query('SELECT * FROM transactions ORDER BY id DESC')->fetchAll(PDO::FETCH_ASSOC);
$available_books = $conn->query("SELECT * FROM books WHERE status = 'Available'")->fetchAll(PDO::FETCH_ASSOC);
$all_borrowers = $conn->query('SELECT * FROM borrowers')->fetchAll(PDO::FETCH_ASSOC);
$borrowed_txns = $conn->query("SELECT * FROM transactions WHERE status = 'Borrowed'")->fetchAll(PDO::FETCH_ASSOC);
?>

<style>
    .btn-group { display: flex; gap: 10px; }
    .btn-issue {
        background: #059669; color: #ffffff; border: none; padding: 10px 20px;
        border-radius: 8px; font-weight: 600; cursor: pointer;
        display: flex; align-items: center; gap: 8px;
    }
    .btn-return {
        background: #4b5563; color: #ffffff; border: none; padding: 10px 20px;
        border-radius: 8px; font-weight: 600; cursor: pointer;
    }
    .txn-text { font-weight: 500; color: #1f2937; }
</style>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
    <div>
        <h1 style="font-size: 26px; font-weight: 700; color: #111827; margin: 0;">Transactions</h1>
        <p style="color: #4b5563; font-size: 16px; margin-top: 8px; font-weight: 500;">Issue books to members, track return deadlines, and process return actions.</p>
    </div>
    <div class="btn-group">
        <button class="btn-issue" onclick="openModal('issueModal')">
            <i class="fa-solid fa-plus"></i> Issue Book
        </button>
        <button class="btn-return" onclick="openModal('returnModal')">Return Book</button>
    </div>
</div>

<div class="table-container">
    <table class="styled-table">
        <thead>
            <tr>
                <th style="width: 15%;">TRANSACTION ID</th>
                <th style="width: 20%;">BORROWER</th>
                <th style="width: 25%;">BOOK</th>
                <th style="width: 15%;">DATE BORROWED</th>
                <th style="width: 15%;">DUE DATE</th>
                <th style="width: 10%;">STATUS</th>
                <th style="width: 10%;">ACTIONS</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($transactions as $row):
                $dueDate = resolveDueDate($row['due_date'] ?? null, $row['borrow_date'], 14);
            ?>
            <tr>
                <td><span class="txn-text"><?php echo formatTransactionId((int) $row['id']); ?></span></td>
                <td style="text-align: left;"><?php echo htmlspecialchars($row['borrower_name'] ?? ''); ?></td>
                <td style="text-align: left;"><?php echo htmlspecialchars($row['book_title'] ?? ''); ?></td>
                <td><?php echo htmlspecialchars($row['borrow_date'] ?? ''); ?></td>
                <td><?php echo htmlspecialchars($dueDate); ?></td>
                <td>
                    <?php if ($row['status'] === 'Borrowed'): ?>
                        <span class="status-pill status-borrowed">Borrowed</span>
                    <?php else: ?>
                        <span class="status-pill status-returned">Returned</span>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if ($row['status'] === 'Borrowed'): ?>
                        <form method="POST" action="transactions.php" style="display:inline;">
                            <input type="hidden" name="transaction_id" value="<?php echo $row['id']; ?>">
                            <button type="submit" name="process_return" class="action-link" style="border:none; background:none; color:#dc2626; cursor:pointer;" title="Return Book" onclick="return confirm('Are you sure you want to mark this book as returned?')">
                                <i class="fa-solid fa-rotate-left"></i>
                            </button>
                        </form>
                    <?php else: ?>
                        <span style="color: #9ca3af; font-size: 12px;">Completed</span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($transactions)): ?>
            <tr>
                <td colspan="7" style="text-align: center; color: #9ca3af; padding: 30px;">No transaction records found.</td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<div class="modal-overlay" id="issueModal">
    <div class="popup-card">
        <div class="popup-header">
            <div class="popup-title"><i class="fa-solid fa-plus"></i> Issue New Book</div>
            <button class="close-btn" onclick="closeModal('issueModal')"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form method="POST" action="transactions.php">
            <div class="form-group">
                <label>Select Borrower</label>
                <select name="borrower_id" required>
                    <option value="">-- Select Member --</option>
                    <?php foreach ($all_borrowers as $b): ?>
                        <option value="<?php echo $b['id']; ?>"><?php echo htmlspecialchars($b['fullname']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Select Book</label>
                <select name="book_id" required>
                    <option value="">-- Select Available Book --</option>
                    <?php foreach ($available_books as $bk): ?>
                        <option value="<?php echo $bk['BookID']; ?>"><?php echo htmlspecialchars($bk['Title']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" name="issue_book" class="save-btn">Confirm Issue</button>
        </form>
    </div>
</div>

<div class="modal-overlay" id="returnModal">
    <div class="popup-card">
        <div class="popup-header">
            <div class="popup-title"><i class="fa-solid fa-rotate-left"></i> Return Book</div>
            <button class="close-btn" onclick="closeModal('returnModal')"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form method="POST" action="transactions.php">
            <div class="form-group">
                <label>Select Borrowed Item</label>
                <select name="transaction_id" required>
                    <option value="">-- Select Item --</option>
                    <?php foreach ($borrowed_txns as $t): ?>
                        <option value="<?php echo $t['id']; ?>"><?php echo htmlspecialchars($t['borrower_name']); ?> - <?php echo htmlspecialchars($t['book_title']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" name="process_return" class="save-btn" style="background: #4b5563;">Process Return</button>
        </form>
    </div>
</div>

<?php include 'footer.php'; ?>
