<?php

include 'database.php';

$database = new Database();
$conn = $database->connect();

if($_SERVER["REQUEST_METHOD"] == "POST"){

    // GET FORM DATA
    $fullname = trim($_POST['fullname']);
    $email = trim($_POST['email']);
    $username = trim($_POST['username']);

    $password = trim($_POST['password']);
    $confirmPassword = trim($_POST['confirm_password']);

    // CHECK PASSWORD MATCH
    if($password != $confirmPassword){

        echo "
        <script>
            alert('Passwords do not match!');
            window.location.href='register.php';
        </script>
        ";

        exit();
    }

    // CHECK EMAIL IF EXISTS
    $checkEmail = "SELECT * FROM users WHERE email = ?";
    $stmtEmail = $conn->prepare($checkEmail);
    $stmtEmail->execute([$email]);

    if($stmtEmail->rowCount() > 0){

        echo "
        <script>
            alert('Email already exists!');
            window.location.href='register.php';
        </script>
        ";

        exit();
    }

    // CHECK USERNAME IF EXISTS
    $checkUsername = "SELECT * FROM users WHERE username = ?";
    $stmtUsername = $conn->prepare($checkUsername);
    $stmtUsername->execute([$username]);

    if($stmtUsername->rowCount() > 0){

        echo "
        <script>
            alert('Username already exists!');
            window.location.href='register.php';
        </script>
        ";

        exit();
    }

    // HASH PASSWORD
    $hashedPassword = password_hash(
        $password,
        PASSWORD_DEFAULT
    );

    // INSERT USER
    $sql = "INSERT INTO users
            (
                fullname,
                email,
                username,
                password,
                status
            )

            VALUES
            (
                ?,
                ?,
                ?,
                ?,
                'Active'
            )";

    $stmt = $conn->prepare($sql);

    $result = $stmt->execute([
        $fullname,
        $email,
        $username,
        $hashedPassword
    ]);

    // SUCCESS
    if($result){

        echo "
        <script>
            alert('Account Created Successfully!');
            window.location.href='login.php';
        </script>
        ";

    } else {

        echo "
        <script>
            alert('Registration Failed!');
            window.location.href='register.php';
        </script>
        ";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Create Account</title>

    <link rel="stylesheet" href="style.css">

    <link rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

</head>

<body class="login-body">

    <div class="container">

        <!-- LOGO -->
        <div class="logo-circle">
            <i class="fa-solid fa-user-plus"></i>
        </div>

        <!-- TITLE -->
        <h1 class="title">
            Create Account
        </h1>

        <!-- REGISTER FORM -->
        <form method="POST">

            <!-- FULL NAME -->
            <div class="form-group">

                <label>Full Name</label>

                <input
                    type="text"
                    name="fullname"
                    class="form-control"
                    placeholder="Enter full name"
                    required>

            </div>

            <!-- EMAIL -->
            <div class="form-group">

                <label>Email Address</label>

                <input
                    type="email"
                    name="email"
                    class="form-control"
                    placeholder="Enter email"
                    required>

            </div>

            <!-- USERNAME -->
            <div class="form-group">

                <label>Username</label>

                <input
                    type="text"
                    name="username"
                    class="form-control"
                    placeholder="Enter username"
                    required>

            </div>

            <!-- PASSWORD -->
            <div class="form-group">

                <label>Password</label>

                <input
                    type="password"
                    name="password"
                    class="form-control"
                    placeholder="Enter password"
                    required>

            </div>

            <!-- CONFIRM PASSWORD -->
            <div class="form-group">

                <label>Confirm Password</label>

                <input
                    type="password"
                    name="confirm_password"
                    class="form-control"
                    placeholder="Confirm password"
                    required>

            </div>

            <!-- REGISTER BUTTON -->
            <button type="submit" class="btn">
                Create Account
            </button>

        </form>

        <!-- LOGIN LINK -->
        <div class="center-text">

            Already have an account?

            <a href="login.php">
                Login Here
            </a>

        </div>

    </div>

</body>
</html>