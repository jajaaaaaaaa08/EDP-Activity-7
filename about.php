<?php
$page_title = 'About - Library Management System';
include 'header.php';
?>

<div class="about-page">
    <header class="about-page-header">
        <h1>About the System</h1>
        <p>Information about the Library Management System</p>
    </header>

    <section class="about-panel">
        <div class="about-panel-head">
            <div class="about-icon-box" aria-hidden="true">
                <i class="fa-solid fa-circle-info"></i>
            </div>
            <h2>System Overview</h2>
        </div>
        <div class="about-panel-body">
            <p>
                This Library Management System is designed to efficiently manage books, borrowers, and borrowing
                transactions within a library environment. It provides a centralized platform for organizing book
                records, maintaining borrower information, and handling the issuing and returning of books.
            </p>
            <p>
                The system improves accuracy and organization by keeping all data structured and easily accessible.
                It supports librarians in managing daily operations such as monitoring book availability, tracking due
                dates, and maintaining transaction records. Additionally, the system includes reporting features that
                help generate organized summaries and insights for better decision-making and record-keeping.
            </p>
        </div>
    </section>

    <section class="about-panel">
        <h2 class="about-panel-title">Key Features</h2>
        <div class="about-features-grid">
            <article class="about-feature">
                <div class="about-icon-box" aria-hidden="true">
                    <i class="fa-solid fa-book"></i>
                </div>
                <div>
                    <h3>Book Management</h3>
                    <p>Add, edit, and track books</p>
                </div>
            </article>
            <article class="about-feature">
                <div class="about-icon-box" aria-hidden="true">
                    <i class="fa-solid fa-users"></i>
                </div>
                <div>
                    <h3>Borrower Management</h3>
                    <p>Manage member information</p>
                </div>
            </article>
            <article class="about-feature">
                <div class="about-icon-box" aria-hidden="true">
                    <i class="fa-solid fa-right-left"></i>
                </div>
                <div>
                    <h3>Transaction Tracking</h3>
                    <p>Issue and return books</p>
                </div>
            </article>
            <article class="about-feature">
                <div class="about-icon-box" aria-hidden="true">
                    <i class="fa-regular fa-file-lines"></i>
                </div>
                <div>
                    <h3>Report Generation</h3>
                    <p>Generate detailed reports</p>
                </div>
            </article>
        </div>
    </section>

    <footer class="about-footer">
        <span>Library Management System v1.0</span>
        <span class="about-footer-divider">·</span>
        <span>&copy; <?php echo date('Y'); ?> Summit Knowledge Library</span>
    </footer>
</div>

<?php include 'footer.php'; ?>
