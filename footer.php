    </div>
</div>

<div class="modal-overlay" id="logoutModal">
    <div class="modal-card">
        <div class="modal-icon">
            <i class="fa-solid fa-arrow-right-from-bracket"></i>
        </div>
        <div class="modal-title">Confirm Logout</div>
        <div class="modal-text">Are you sure you want to log out of the system? Any unsaved changes may be lost.</div>
        <div class="modal-btns">
            <button class="btn-cancel" onclick="hideLogoutModal()">Cancel</button>
            <button class="btn-confirm" onclick="confirmLogout()">Yes, Logout</button>
        </div>
    </div>
</div>

<script src="assets/app.js"></script>
<script>
    const trigger = document.getElementById('profileDropdownTrigger');
    const dropdown = document.getElementById('profileDropdown');

    if (trigger && dropdown) {
        trigger.addEventListener('click', (e) => {
            e.stopPropagation();
            dropdown.classList.toggle('show');
        });

        document.addEventListener('click', () => {
            dropdown.classList.remove('show');
        });
    }

    function showLogoutModal() {
        document.getElementById('logoutModal').style.display = 'flex';
    }
    function hideLogoutModal() {
        document.getElementById('logoutModal').style.display = 'none';
    }
    function confirmLogout() {
        window.location.href = 'logout.php';
    }
</script>
</body>
</html>
