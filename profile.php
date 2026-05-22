<?php
$page_title = 'My Profile - Library Management System';
include 'header.php';
?>

<style>
    .content-area { display: flex; flex-direction: column; align-items: center; }
    .upload-btn {
        color: #059669; font-size: 14px; font-weight: 600; cursor: pointer;
        display: flex; align-items: center; gap: 8px; text-decoration: none;
    }
    .btn-row { display: flex; justify-content: space-between; align-items: center; margin-top: 30px; }
    .btn-left { display: flex; gap: 12px; }
    .btn-save { background: #059669; color: white; border: none; padding: 12px 24px; border-radius: 8px; font-weight: 600; cursor: pointer; }
    .btn-cancel-link { background: #e5e7eb; color: #4b5563; border: none; padding: 12px 24px; border-radius: 8px; font-weight: 600; cursor: pointer; text-decoration: none; display: inline-block; }
    .btn-logout-red { background: #dc2626; color: white; border: none; padding: 12px 24px; border-radius: 8px; font-weight: 600; cursor: pointer; }
</style>

<div class="profile-wrapper">
    <div style="margin-bottom: 30px; width: 100%;">
        <h1 style="font-size: 26px; font-weight: 700; color: #111827; margin: 0;">My Profile</h1>
    </div>

    <div class="profile-card centered">
        <div class="avatar-section">
            <div class="avatar-circle">
                <i class="fa-regular fa-user"></i>
            </div>
            <label class="upload-btn">
                <i class="fa-solid fa-arrow-up-from-bracket"></i> Upload Profile Picture
            </label>
        </div>

        <div class="form-group">
            <label class="form-label">Full Name</label>
            <input type="text" class="form-input" value="Admin User">
        </div>

        <div class="form-group">
            <label class="form-label">Email Address</label>
            <input type="email" class="form-input" value="admin@library.com">
        </div>

        <div class="form-group">
            <label class="form-label">Username</label>
            <input type="text" class="form-input" value="<?php echo htmlspecialchars($_SESSION['username'] ?? 'admin'); ?>">
        </div>

        <div class="form-group">
            <label class="form-label">Password</label>
            <input type="password" class="form-input" placeholder="Enter new password">
        </div>

        <div class="btn-row">
            <div class="btn-left">
                <button class="btn-save">Save Changes</button>
                <a href="dashboard.php" class="btn-cancel-link">Cancel</a>
            </div>
            <button class="btn-logout-red" onclick="showLogoutModal()">Logout</button>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
