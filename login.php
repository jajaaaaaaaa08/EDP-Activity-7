<?php

session_start();

include 'database.php';

$database = new Database();
$conn = $database->connect();

$error = "";

if(isset($_POST['login'])){

    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    /* CHECK USER */
    $sql = "SELECT * FROM users
            WHERE Username = :username
            LIMIT 1";

    $stmt = $conn->prepare($sql);
    $stmt->execute([
        ':username' => $username
    ]);

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    /* IF USER EXISTS */
    if($user){
        $password_correct = false;
        
        // Verify with hashed password
        if (password_verify($password, $user['Password'])) {
            $password_correct = true;
        } 
        // Self-healing fallback: Check plain-text password if stored plain
        elseif ($password === $user['Password']) {
            $password_correct = true;
            // Update to hashed password in database
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $updateStmt = $conn->prepare("UPDATE users SET Password = :password WHERE UserID = :id");
            $updateStmt->execute([
                ':password' => $hashedPassword,
                ':id' => $user['UserID']
            ]);
        }

        if ($password_correct) {
            /* CHECK STATUS */
            if($user['Status'] == 'Inactive'){
                $error = "Account is inactive.";
            }else{
                $_SESSION['user_id'] = $user['UserID'];
                $_SESSION['username'] = $user['Username'];
                $_SESSION['role'] = $user['Role'];

                header("Location: dashboard.php");
                exit();
            }
        } else {
            $error = "Invalid username or password.";
        }
    }else{
        $error = "Invalid username or password.";
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
    content="width=device-width, initial-scale=1.0">

    <title>Library Management System</title>

    <link rel="stylesheet" href="style.css">

    <link rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

</head>

<body class="login-body">

<div class="container">

    <!-- LOGO -->
    <div class="logo-circle">
        <i class="fa-solid fa-book-open"></i>
    </div>

    <!-- TITLE -->
    <h3 class="login-title">
        Library Management System!!
    </h3>

    <!-- ERROR -->
    <?php if(!empty($error)){ ?>

        <div style="
            background:#fee2e2;
            color:#dc2626;
            padding:12px;
            border-radius:10px;
            margin-bottom:18px;
            text-align:center;
            font-size:10px;
        ">
            <?= $error; ?>
        </div>

    <?php } ?>

    <!-- LOGIN FORM -->
    <form method="POST">

        <!-- USERNAME -->
        <div class="form-group">

            <label>Username</label>

            <input
                type="text"
                name="username"
                class="form-control"
                required>

        </div>

        <!-- PASSWORD -->
        <div class="form-group">

            <label>Password</label>

            <input
                type="password"
                name="password"
                class="form-control"
                required>

        </div>

        <!-- LOGIN BUTTON -->
        <button type="submit"
        name="login"
        class="btn">

            Login

        </button>

    </form>

    <!-- FORGOT PASSWORD -->
    <div class="center-text">

        <a href="forgot_password.php">
            Forgot Password?
        </a>

    </div>

    <!-- SIGN UP -->
    <div class="center-text">

        Don't have an account?

        <a href="register.php">
            Sign Up Now
        </a>

    </div>

</div>

</body>
</html>