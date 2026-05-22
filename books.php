<?php
$page_title = 'Book Management - Library Management System';
require_once 'includes/helpers.php';
require_once 'includes/transaction_service.php';
include 'header.php';

$categories = $conn->query('SELECT * FROM categories')->fetchAll(PDO::FETCH_ASSOC);
$borrowers = $conn->query('SELECT * FROM borrowers')->fetchAll(PDO::FETCH_ASSOC);

if (isset($_POST['add_book'])) {
    $stmt = $conn->prepare("INSERT INTO books (Title, Author, CategoryID, status) VALUES (:title, :author, :cat_id, 'Available')");
    $stmt->execute([
        'title' => $_POST['title'],
        'author' => $_POST['author'],
        'cat_id' => $_POST['category_id'],
    ]);
    redirectTo('books.php');
}

if (isset($_POST['edit_book'])) {
    $stmt = $conn->prepare('UPDATE books SET Title = :title, Author = :author, CategoryID = :cat_id WHERE BookID = :id');
    $stmt->execute([
        'title' => $_POST['title'],
        'author' => $_POST['author'],
        'cat_id' => $_POST['category_id'],
        'id' => $_POST['id'],
    ]);
    redirectTo('books.php');
}

if (isset($_GET['delete_id'])) {
    $stmt = $conn->prepare('DELETE FROM books WHERE BookID = ?');
    $stmt->execute([(int) $_GET['delete_id']]);
    redirectTo('books.php');
}

if (isset($_POST['borrow_book'])) {
    issueBook($conn, (int) $_POST['book_id'], (int) $_POST['borrower_id'], false);
    redirectTo('books.php');
}

$books = $conn->query("
    SELECT books.*, categories.CategoryName
    FROM books
    LEFT JOIN categories ON books.CategoryID = categories.CategoryID
    ORDER BY BookID DESC
")->fetchAll(PDO::FETCH_ASSOC);
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
    <div>
        <h1 style="font-size: 26px; font-weight: 700; color: #111827; margin: 0;">Book Management</h1>
        <p style="color: #4b5563; font-size: 16px; margin-top: 8px; font-weight: 500;">Manage library book inventory, status, and issue books to members.</p>
    </div>
    <button class="add-account-btn" onclick="openModal('addModal')">
        <i class="fa-solid fa-plus"></i> Add Book
    </button>
</div>

<div class="table-container">
    <table class="styled-table">
        <thead>
            <tr>
                <th>BOOK ID</th>
                <th>TITLE</th>
                <th>AUTHOR</th>
                <th>CATEGORY</th>
                <th>STATUS</th>
                <th>ACTIONS</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($books as $row): ?>
            <tr>
                <td><span style="color: #6b7280; font-weight: 500;"><?php echo formatBookId((int) $row['BookID']); ?></span></td>
                <td style="text-align: left;"><?php echo htmlspecialchars($row['Title']); ?></td>
                <td style="text-align: left;"><?php echo htmlspecialchars($row['Author']); ?></td>
                <td><span style="background: #f3f4f6; padding: 4px 10px; border-radius: 6px; font-size: 12px; font-weight: 500;"><?php echo htmlspecialchars($row['CategoryName'] ?? 'Uncategorized'); ?></span></td>
                <td>
                    <?php if ($row['status'] === 'Available'): ?>
                        <span class="status-pill status-available">Available</span>
                    <?php else: ?>
                        <span class="status-pill status-borrowed">Borrowed</span>
                    <?php endif; ?>
                </td>
                <td>
                    <a href="javascript:void(0)" class="action-link edit-icon" title="Edit Book"
                       onclick="openEditModal('<?php echo $row['BookID']; ?>', '<?php echo addslashes($row['Title']); ?>', '<?php echo addslashes($row['Author']); ?>', '<?php echo $row['CategoryID']; ?>')">
                        <i class="fa-regular fa-pen-to-square"></i>
                    </a>
                    <a href="books.php?delete_id=<?php echo $row['BookID']; ?>" class="action-link delete-icon" title="Delete Book"
                       onclick="return confirm('Are you sure you want to delete this book?')">
                        <i class="fa-regular fa-trash-can"></i>
                    </a>
                    <?php if ($row['status'] === 'Available'): ?>
                    <a href="javascript:void(0)" class="action-link" style="color: #059669;" title="Issue/Borrow Book"
                       onclick="openBorrowModal('<?php echo $row['BookID']; ?>', '<?php echo addslashes($row['Title']); ?>')">
                        <i class="fa-solid fa-hand-holding-hand"></i>
                    </a>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($books)): ?>
            <tr>
                <td colspan="6" style="text-align: center; color: #9ca3af; padding: 30px;">No books available in the inventory.</td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<div class="modal-overlay" id="addModal">
    <div class="popup-card">
        <div class="popup-header">
            <div class="popup-title"><i class="fa-solid fa-book"></i> Add New Book</div>
            <button class="close-btn" onclick="closeModal('addModal')"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form method="POST" action="books.php">
            <div class="form-group">
                <label>Book Title</label>
                <input type="text" name="title" required>
            </div>
            <div class="form-group">
                <label>Author</label>
                <input type="text" name="author" required>
            </div>
            <div class="form-group">
                <label>Category</label>
                <select name="category_id" required>
                    <option value="">Select Category...</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?php echo $cat['CategoryID']; ?>"><?php echo htmlspecialchars($cat['CategoryName']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" name="add_book" class="save-btn">Save Book</button>
        </form>
    </div>
</div>

<div class="modal-overlay" id="editModal">
    <div class="popup-card">
        <div class="popup-header">
            <div class="popup-title"><i class="fa-solid fa-pen-to-square"></i> Edit Book</div>
            <button class="close-btn" onclick="closeModal('editModal')"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form method="POST" action="books.php">
            <input type="hidden" name="id" id="edit_id">
            <div class="form-group">
                <label>Book Title</label>
                <input type="text" name="title" id="edit_title" required>
            </div>
            <div class="form-group">
                <label>Author</label>
                <input type="text" name="author" id="edit_author" required>
            </div>
            <div class="form-group">
                <label>Category</label>
                <select name="category_id" id="edit_category_id" required>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?php echo $cat['CategoryID']; ?>"><?php echo htmlspecialchars($cat['CategoryName']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" name="edit_book" class="save-btn">Update Book</button>
        </form>
    </div>
</div>

<div class="modal-overlay" id="borrowModal">
    <div class="popup-card">
        <div class="popup-header">
            <h2 class="popup-title">Borrow Book</h2>
            <button class="close-btn" onclick="closeModal('borrowModal')">&times;</button>
        </div>
        <div style="padding: 20px;">
            <p style="margin-bottom: 20px; color: #4b5563;">You are issuing: <strong id="borrow_book_title" style="color: #111827;"></strong></p>
            <form method="POST" action="books.php">
                <input type="hidden" name="book_id" id="borrow_book_id">
                <div class="form-group">
                    <label>Select Borrower</label>
                    <select name="borrower_id" required>
                        <option value="">-- Select Member --</option>
                        <?php foreach ($borrowers as $b): ?>
                            <option value="<?php echo $b['id']; ?>"><?php echo htmlspecialchars($b['fullname']); ?> (<?php echo htmlspecialchars($b['email']); ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" name="borrow_book" class="save-btn" style="background: #059669;">Issue Book</button>
            </form>
        </div>
    </div>
</div>

<script>
    function openBorrowModal(id, title) {
        document.getElementById('borrow_book_id').value = id;
        document.getElementById('borrow_book_title').innerText = title;
        openModal('borrowModal');
    }

    function openEditModal(id, title, author, catId) {
        document.getElementById('edit_id').value = id;
        document.getElementById('edit_title').value = title;
        document.getElementById('edit_author').value = author;
        document.getElementById('edit_category_id').value = catId;
        openModal('editModal');
    }
</script>

<?php include 'footer.php'; ?>
