<?php

include 'database.php';

$database = new Database();
$conn = $database->connect();

$error = "";

if(isset($_POST['recover'])){

    $email = trim($_POST['email']);

    $query = $conn->prepare(
        "SELECT * FROM users WHERE Email = :email"
    );

    $query->bindParam(":email", $email);
    $query->execute();

    if($query->rowCount() > 0){

        header("Location: reset_password.php?email=" . urlencode($email));
        exit();

    } else {

        $error = "Email address not found.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport"
content="width=device-width, initial-scale=1.0">

<title>Password Recovery</title>

<link rel="stylesheet" href="style.css">

<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

</head>

<body class="login-body">

<div class="container recovery-container">

    <!-- LOGO -->
    <div class="logo-circle">
        <i class="fa-solid fa-book-open"></i>
    </div>

    <!-- TITLE -->
    <h1 class="title">
        Password Recovery
    </h1>

    <!-- DESCRIPTION -->
    <p class="recovery-text">
        Enter your registered email address to recover your account password.
    </p>

    <?php if($error != ""){ ?>

        <div class="error-box">
            <i class="fa-solid fa-circle-exclamation"></i>
            <?php echo $error; ?>
        </div>

    <?php } ?>

    <!-- FORM -->
    <form method="POST">

        <div class="form-group">

            <label>Email Address</label>

            <input
                type="email"
                name="email"
                class="form-control"
                placeholder="Enter your email"
                required>

        </div>

        <!-- BUTTON -->
        <button
            type="submit"
            name="recover"
            class="btn">

            <i class="fa-solid fa-paper-plane"></i>
            Send Recovery Email

        </button>

    </form>

    <!-- BACK -->
    <div class="center-text">

        <a href="login.php">
            Back to Login
        </a>

    </div>

</div>

</body>
</html>