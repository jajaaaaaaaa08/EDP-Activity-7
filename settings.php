<?php
$page_title = 'Settings - Library Management System';
include 'header.php';
?>

<div style="margin-bottom: 30px;">
    <h1 style="font-size: 26px; font-weight: 700; color: #111827; margin: 0;">Settings</h1>
</div>

<div class="settings-wrapper">
    <div class="settings-nav">
        <div class="settings-tab active" data-tab="general">
            <i class="fa-solid fa-sliders"></i> General
        </div>
        <div class="settings-tab" data-tab="library">
            <i class="fa-solid fa-book"></i> Library Config
        </div>
        <div class="settings-tab" data-tab="security">
            <i class="fa-solid fa-shield-halved"></i> Security
        </div>
        <div class="settings-tab" data-tab="notifications">
            <i class="fa-regular fa-bell"></i> Notifications
        </div>
    </div>

    <div class="settings-content">
        <div class="settings-section active" id="general">
            <div class="section-title">General Settings</div>
            <div class="form-group">
                <label class="form-label">Library Name</label>
                <input type="text" class="form-input" value="City Public Library">
                <p class="form-hint">This name will appear on all reports and system headers.</p>
            </div>
            <div class="form-group">
                <label class="form-label">System Email</label>
                <input type="email" class="form-input" value="admin@library.com">
            </div>
            <div class="form-group">
                <label class="form-label">System Language</label>
                <select class="form-input">
                    <option>English (US)</option>
                    <option>Tagalog</option>
                </select>
            </div>
            <button class="save-btn">Save Changes</button>
        </div>

        <div class="settings-section" id="library">
            <div class="section-title">Library Configuration</div>
            <div class="form-group">
                <label class="form-label">Borrowing Period (Days)</label>
                <input type="number" class="form-input" value="7">
                <p class="form-hint">Default number of days a book can be borrowed.</p>
            </div>
            <div class="form-group">
                <label class="form-label">Max Books per Member</label>
                <input type="number" class="form-input" value="5">
            </div>
            <button class="save-btn">Save Config</button>
        </div>

        <div class="settings-section" id="security">
            <div class="section-title">Security & Access</div>
            <div class="form-group">
                <label class="form-label">Two-Factor Authentication</label>
                <select class="form-input">
                    <option>Disabled</option>
                    <option>Email OTP</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Password Expiry</label>
                <select class="form-input">
                    <option>Never</option>
                    <option>Every 90 Days</option>
                </select>
            </div>
            <button class="save-btn">Update Security</button>
        </div>

        <div class="settings-section" id="notifications">
            <div class="section-title">Notification Settings</div>
            <div class="form-group">
                <label class="form-label">Overdue Alerts</label>
                <select class="form-input">
                    <option>Send Daily</option>
                    <option>Send Weekly</option>
                    <option>Disabled</option>
                </select>
            </div>
            <button class="save-btn">Save Preferences</button>
        </div>
    </div>
</div>

<script>
    const tabs = document.querySelectorAll('.settings-tab');
    const sections = document.querySelectorAll('.settings-section');

    tabs.forEach(tab => {
        tab.addEventListener('click', () => {
            const target = tab.getAttribute('data-tab');
            tabs.forEach(t => t.classList.remove('active'));
            tab.classList.add('active');
            sections.forEach(s => s.classList.remove('active'));
            document.getElementById(target).classList.add('active');
        });
    });
</script>

<?php include 'footer.php'; ?>
