<?php
$page_title = 'User Management - Library Management System';
require_once 'includes/helpers.php';
include 'header.php';

// Prevent non-admin access if relevant, or keep it open as per original
// (Original did not check role, but we can verify role is Administrator or Librarian)

/* DELETE USER ACCOUNT */
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $deleteId = (int)$_GET['id'];
    // Avoid self-deletion if logged in
    if ($deleteId === (int)$_SESSION['user_id']) {
        echo "<script>alert('You cannot delete your own account!'); window.location.href='search_user.php';</script>";
        exit();
    }
    
    $sql = "DELETE FROM users WHERE UserID = :id";
    $stmt = $conn->prepare($sql);
    $stmt->execute([':id' => $deleteId]);
    redirectTo('search_user.php');
}

/* ADD ACCOUNT */
if (isset($_POST['create_account'])) {
    $firstname = trim($_POST['firstname']);
    $lastname = trim($_POST['lastname']);
    $fullname = $firstname . " " . $lastname;
    $email = trim($_POST['email']);
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);
    $role = $_POST['role'];
    $status = $_POST['status'];

    if ($password !== $confirm_password) {
        echo "<script>alert('Passwords do not match!');</script>";
    } else {
        // Check if username or email already exists
        $checkSql = "SELECT COUNT(*) FROM users WHERE Username = :username OR Email = :email";
        $checkStmt = $conn->prepare($checkSql);
        $checkStmt->execute([':username' => $username, ':email' => $email]);
        if ($checkStmt->fetchColumn() > 0) {
            echo "<script>alert('Username or Email already exists!');</script>";
        } else {
            // Hash password securely
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $sql = "INSERT INTO users (FullName, Username, Email, Password, Role, Status) VALUES (:fullname, :username, :email, :password, :role, :status)";
            $stmt = $conn->prepare($sql);
            $stmt->execute([
                ':fullname' => $fullname,
                ':username' => $username,
                ':email' => $email,
                ':password' => $hashedPassword,
                ':role' => $role,
                ':status' => $status
            ]);
            redirectTo('search_user.php');
        }
    }
}

/* UPDATE ACCOUNT */
if (isset($_POST['update_account']) && isset($_POST['user_id'])) {
    $userId = (int)$_POST['user_id'];
    $fullname = trim($_POST['fullname']);
    $email = trim($_POST['email']);
    $username = trim($_POST['username']);
    $role = $_POST['role'];
    $status = $_POST['status'];

    $sql = "UPDATE users SET FullName = :fullname, Email = :email, Username = :username, Role = :role, Status = :status WHERE UserID = :id";
    $stmt = $conn->prepare($sql);
    $stmt->execute([
        ':fullname' => $fullname,
        ':email' => $email,
        ':username' => $username,
        ':role' => $role,
        ':status' => $status,
        ':id' => $userId
    ]);
    
    // Update session username if updating self
    if ($userId === (int)$_SESSION['user_id']) {
        $_SESSION['username'] = $username;
        $_SESSION['role'] = $role;
    }
    
    redirectTo('search_user.php');
}

/* SEARCH + FILTER */
$search = $_GET['search'] ?? '';
$filterRole = $_GET['role'] ?? '';
$filterStatus = $_GET['status'] ?? '';

$sql = "SELECT * FROM users WHERE 1=1";
if (!empty($search)) {
    $sql .= " AND (FullName LIKE :search OR Username LIKE :search OR Email LIKE :search)";
}
if (!empty($filterRole)) {
    $sql .= " AND Role = :role";
}
if (!empty($filterStatus)) {
    $sql .= " AND Status = :status";
}
$sql .= " ORDER BY UserID DESC";

$stmt = $conn->prepare($sql);
if (!empty($search)) {
    $stmt->bindValue(':search', "%$search%");
}
if (!empty($filterRole)) {
    $stmt->bindValue(':role', $filterRole);
}
if (!empty($filterStatus)) {
    $stmt->bindValue(':status', $filterStatus);
}
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch user for editing if edit action is active
$editUser = null;
if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['id'])) {
    $editId = (int)$_GET['id'];
    $editStmt = $conn->prepare("SELECT * FROM users WHERE UserID = :id");
    $editStmt->execute([':id' => $editId]);
    $editUser = $editStmt->fetch(PDO::FETCH_ASSOC);
}
?>

<div style="margin-bottom: 30px;">
    <h1 style="font-size: 26px; font-weight: 700; color: #111827; margin: 0;">User Management</h1>
    <p style="color: #4b5563; font-size: 16px; margin-top: 8px; font-weight: 500;">Manage system administrators, librarians, and staff accounts.</p>
</div>

<div class="action-row">
    <form method="GET" class="filter-group">
        <input type="text" name="search" placeholder="Search by name, user..." value="<?php echo htmlspecialchars($search); ?>" class="search-input">
        
        <select name="role" class="filter-select" onchange="this.form.submit()">
            <option value="">All Roles</option>
            <option value="Administrator" <?php echo $filterRole === 'Administrator' ? 'selected' : ''; ?>>Administrator</option>
            <option value="Librarian" <?php echo $filterRole === 'Librarian' ? 'selected' : ''; ?>>Librarian</option>
            <option value="Staff" <?php echo $filterRole === 'Staff' ? 'selected' : ''; ?>>Staff</option>
        </select>

        <select name="status" class="filter-select" onchange="this.form.submit()">
            <option value="">All Status</option>
            <option value="Active" <?php echo $filterStatus === 'Active' ? 'selected' : ''; ?>>Active</option>
            <option value="Inactive" <?php echo $filterStatus === 'Inactive' ? 'selected' : ''; ?>>Inactive</option>
        </select>
        
        <?php if (!empty($search) || !empty($filterRole) || !empty($filterStatus)): ?>
            <a href="search_user.php" style="color: #6b7280; font-size: 14px; text-decoration: none;">Clear Filters</a>
        <?php endif; ?>
    </form>

    <button class="add-btn" onclick="openAddModal()">
        <i class="fa-solid fa-plus"></i> Add User
    </button>
</div>

<div class="table-container user-table">
    <table class="styled-table">
        <thead>
            <tr>
                <th>USER ID</th>
                <th>FULL NAME</th>
                <th>USERNAME</th>
                <th>EMAIL</th>
                <th>ROLE</th>
                <th>STATUS</th>
                <th style="width: 110px;">ACTIONS</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $row): ?>
            <tr>
                <td>USR-<?php echo str_pad($row['UserID'], 3, '0', STR_PAD_LEFT); ?></td>
                <td><?php echo htmlspecialchars($row['FullName']); ?></td>
                <td><?php echo htmlspecialchars($row['Username']); ?></td>
                <td><?php echo htmlspecialchars($row['Email']); ?></td>
                <td>
                    <span class="role-badge"><?php echo htmlspecialchars($row['Role']); ?></span>
                </td>
                <td>
                    <?php if ($row['Status'] === 'Active'): ?>
                        <span class="status-active">Active</span>
                    <?php else: ?>
                        <span class="status-inactive">Inactive</span>
                    <?php endif; ?>
                </td>
                <td>
                    <div class="action-buttons">
                        <a href="search_user.php?action=edit&id=<?php echo $row['UserID']; ?>" class="action-btn edit-btn" title="Edit User">
                            <i class="fa-regular fa-pen-to-square"></i>
                        </a>
                        <a href="javascript:void(0)" class="action-btn delete-btn" title="Delete User" onclick="confirmDelete(<?php echo $row['UserID']; ?>)">
                            <i class="fa-regular fa-trash-can"></i>
                        </a>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($users)): ?>
            <tr>
                <td colspan="7" style="text-align: center; color: #9ca3af; padding: 30px;">No users found matching your filters.</td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- ADD USER MODAL -->
<div class="modal-overlay-custom" id="addModal" style="display: none;">
    <div class="modal-card-custom">
        <div class="modal-header">
            <div class="modal-title">Add New User</div>
            <button class="modal-close" onclick="closeAddModal()">&times;</button>
        </div>
        <form method="POST">
            <div style="display: flex; gap: 12px;">
                <div class="form-group-custom" style="flex: 1;">
                    <label>First Name</label>
                    <input type="text" name="firstname" required>
                </div>
                <div class="form-group-custom" style="flex: 1;">
                    <label>Last Name</label>
                    <input type="text" name="lastname" required>
                </div>
            </div>
            <div class="form-group-custom">
                <label>Email</label>
                <input type="email" name="email" required>
            </div>
            <div class="form-group-custom">
                <label>Username</label>
                <input type="text" name="username" required>
            </div>
            <div style="display: flex; gap: 12px;">
                <div class="form-group-custom" style="flex: 1;">
                    <label>Password</label>
                    <input type="password" name="password" required>
                </div>
                <div class="form-group-custom" style="flex: 1;">
                    <label>Confirm Password</label>
                    <input type="password" name="confirm_password" required>
                </div>
            </div>
            <div style="display: flex; gap: 12px;">
                <div class="form-group-custom" style="flex: 1;">
                    <label>Role</label>
                    <select name="role">
                        <option value="Administrator">Administrator</option>
                        <option value="Librarian">Librarian</option>
                        <option value="Staff">Staff</option>
                    </select>
                </div>
                <div class="form-group-custom" style="flex: 1;">
                    <label>Status</label>
                    <select name="status">
                        <option value="Active">Active</option>
                        <option value="Inactive">Inactive</option>
                    </select>
                </div>
            </div>
            <button type="submit" name="create_account" class="add-btn" style="width: 100%; margin-top: 10px; justify-content: center;">Create Account</button>
        </form>
    </div>
</div>

<!-- EDIT USER MODAL -->
<?php if ($editUser): ?>
<div class="modal-overlay-custom" id="editModal">
    <div class="modal-card-custom">
        <div class="modal-header">
            <div class="modal-title">Update Account</div>
            <a href="search_user.php" class="modal-close" style="text-decoration: none;">&times;</a>
        </div>
        <form method="POST">
            <input type="hidden" name="user_id" value="<?php echo $editUser['UserID']; ?>">
            
            <div class="form-group-custom">
                <label>Full Name</label>
                <input type="text" name="fullname" value="<?php echo htmlspecialchars($editUser['FullName']); ?>" required>
            </div>
            
            <div class="form-group-custom">
                <label>Email Address</label>
                <input type="email" name="email" value="<?php echo htmlspecialchars($editUser['Email']); ?>" required>
            </div>
            
            <div style="display: flex; gap: 12px;">
                <div class="form-group-custom" style="flex: 1;">
                    <label>Username</label>
                    <input type="text" name="username" value="<?php echo htmlspecialchars($editUser['Username']); ?>" required>
                </div>
                <div class="form-group-custom" style="flex: 1;">
                    <label>Role</label>
                    <select name="role">
                        <option value="Administrator" <?php echo $editUser['Role'] === 'Administrator' ? 'selected' : ''; ?>>Administrator</option>
                        <option value="Librarian" <?php echo $editUser['Role'] === 'Librarian' ? 'selected' : ''; ?>>Librarian</option>
                        <option value="Staff" <?php echo $editUser['Role'] === 'Staff' ? 'selected' : ''; ?>>Staff</option>
                    </select>
                </div>
            </div>
            
            <div class="form-group-custom">
                <label>Status</label>
                <select name="status">
                    <option value="Active" <?php echo $editUser['Status'] === 'Active' ? 'selected' : ''; ?>>Active</option>
                    <option value="Inactive" <?php echo $editUser['Status'] === 'Inactive' ? 'selected' : ''; ?>>Inactive</option>
                </select>
            </div>
            
            <div style="display: flex; gap: 12px; margin-top: 15px;">
                <a href="search_user.php" class="add-btn" style="background: #e5e7eb; color: #374151; flex: 1; justify-content: center; text-decoration: none; font-weight: 600;">Cancel</a>
                <button type="submit" name="update_account" class="add-btn" style="flex: 1; justify-content: center;">Save Changes</button>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>

<script>
    function openAddModal() {
        document.getElementById('addModal').style.display = 'flex';
    }
    function closeAddModal() {
        document.getElementById('addModal').style.display = 'none';
    }
    function confirmDelete(id) {
        if (confirm("Are you sure you want to delete this user? This action cannot be undone.")) {
            window.location.href = "search_user.php?action=delete&id=" + id;
        }
    }
</script>

<?php include 'footer.php'; ?>