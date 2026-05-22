<?php
session_start();
session_destroy();
header("Location: dashboard.php"); // Or login.php if it exists
exit;
?>