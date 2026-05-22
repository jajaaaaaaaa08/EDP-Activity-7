<?php
$page_title = 'Borrower Management - Library Management System';
require_once 'includes/helpers.php';
include 'header.php';

if (isset($_POST['add_borrower'])) {
    $stmt = $conn->prepare('INSERT INTO borrowers (fullname, email, contact, membership_date) VALUES (:name, :email, :contact, :date)');
    $stmt->execute([
        'name' => $_POST['name'],
        'email' => $_POST['email'],
        'contact' => $_POST['contact'],
        'date' => date('Y-m-d'),
    ]);
    redirectTo('borrowers.php');
}

if (isset($_POST['edit_borrower'])) {
    $stmt = $conn->prepare('UPDATE borrowers SET fullname = :name, email = :email, contact = :contact WHERE id = :id');
    $stmt->execute([
        'name' => $_POST['name'],
        'email' => $_POST['email'],
        'contact' => $_POST['contact'],
        'id' => $_POST['id'],
    ]);
    redirectTo('borrowers.php');
}

if (isset($_GET['delete_id'])) {
    $stmt = $conn->prepare('DELETE FROM borrowers WHERE id = ?');
    $stmt->execute([(int) $_GET['delete_id']]);
    redirectTo('borrowers.php');
}

$borrowers = $conn->query('SELECT * FROM borrowers ORDER BY id DESC')->fetchAll(PDO::FETCH_ASSOC);
?>

<style>
    .brw-id { font-weight: 500; color: #6b7280; }
    .edit-icon:hover { color: #1d4ed8; }
    .delete-icon:hover { color: #b91c1c; }
</style>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
    <div>
        <h1 style="font-size: 26px; font-weight: 700; color: #111827; margin: 0;">Borrower Management</h1>
        <p style="color: #4b5563; font-size: 16px; margin-top: 8px; font-weight: 500;">Manage library members, their membership records, and contacts.</p>
    </div>
    <button class="add-account-btn" onclick="openModal('addModal')">
        <i class="fa-solid fa-plus"></i> Add Borrower
    </button>
</div>

<div class="table-container">
    <table class="styled-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>NAME</th>
                <th>EMAIL</th>
                <th>CONTACT</th>
                <th>MEMBERSHIP DATE</th>
                <th>ACTIONS</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($borrowers as $row): ?>
            <tr>
                <td><span class="brw-id"><?php echo formatBorrowerId((int) $row['id']); ?></span></td>
                <td style="text-align: left;"><?php echo htmlspecialchars($row['fullname'] ?? ''); ?></td>
                <td style="text-align: left;"><?php echo htmlspecialchars($row['email'] ?? ''); ?></td>
                <td><?php echo htmlspecialchars($row['contact'] ?? ''); ?></td>
                <td><?php echo htmlspecialchars($row['membership_date'] ?? '-'); ?></td>
                <td>
                    <a href="javascript:void(0)" class="action-link edit-icon" title="Edit Borrower"
                       onclick="openEditModal('<?php echo $row['id']; ?>', <?php echo json_encode($row['fullname'] ?? ''); ?>, <?php echo json_encode($row['email'] ?? ''); ?>, <?php echo json_encode($row['contact'] ?? ''); ?>)">
                        <i class="fa-regular fa-pen-to-square"></i>
                    </a>
                    <a href="borrowers.php?delete_id=<?php echo $row['id']; ?>" class="action-link delete-icon" title="Delete Borrower"
                       onclick="return confirm('Are you sure you want to delete this borrower?')">
                        <i class="fa-regular fa-trash-can"></i>
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($borrowers)): ?>
            <tr>
                <td colspan="6" style="text-align: center; color: #9ca3af; padding: 30px;">No registered borrowers found.</td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<div class="modal-overlay" id="addModal">
    <div class="popup-card">
        <div class="popup-header">
            <div class="popup-title"><i class="fa-solid fa-user-plus"></i> Add New Borrower</div>
            <button class="close-btn" onclick="closeModal('addModal')"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form method="POST" action="borrowers.php">
            <div class="form-group">
                <label>Full Name</label>
                <input type="text" name="name" required>
            </div>
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" required>
            </div>
            <div class="form-group">
                <label>Contact Number</label>
                <input type="text" name="contact">
            </div>
            <button type="submit" name="add_borrower" class="save-btn">Save Borrower</button>
        </form>
    </div>
</div>

<div class="modal-overlay" id="editModal">
    <div class="popup-card">
        <div class="popup-header">
            <div class="popup-title"><i class="fa-solid fa-user-pen"></i> Edit Borrower</div>
            <button class="close-btn" onclick="closeModal('editModal')"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form method="POST" action="borrowers.php">
            <input type="hidden" name="id" id="edit_id">
            <div class="form-group">
                <label>Full Name</label>
                <input type="text" name="name" id="edit_name" required>
            </div>
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" id="edit_email" required>
            </div>
            <div class="form-group">
                <label>Contact Number</label>
                <input type="text" name="contact" id="edit_contact">
            </div>
            <button type="submit" name="edit_borrower" class="save-btn">Update Borrower</button>
        </form>
    </div>
</div>

<script>
    function openEditModal(id, name, email, contact) {
        document.getElementById('edit_id').value = id;
        document.getElementById('edit_name').value = name;
        document.getElementById('edit_email').value = email;
        document.getElementById('edit_contact').value = contact;
        openModal('editModal');
    }
</script>

<?php include 'footer.php'; ?>
