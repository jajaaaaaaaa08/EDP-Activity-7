<?php

include 'database.php';

$database = new Database();
$conn = $database->connect();

$email = $_GET['email'] ?? '';

$success = false;
$error = "";

if(isset($_POST['reset'])){

    $newPassword = trim($_POST['new_password']);
    $confirmPassword = trim($_POST['confirm_password']);

    if(strlen($newPassword) < 6){

        $error = "Password must be at least 6 characters.";

    } elseif($newPassword != $confirmPassword){

        $error = "Passwords do not match.";

    } else {

        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

        $query = $conn->prepare(
            "UPDATE users
             SET Password = :password
             WHERE Email = :email"
        );

        $query->bindParam(":password", $hashedPassword);
        $query->bindParam(":email", $email);

        if($query->execute()){

            $success = true;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Reset Password</title>

<link rel="stylesheet" href="style.css">

<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

</head>

<body class="login-body">

<div class="recovery-container">

<?php if(!$success){ ?>

    <!-- RESET FORM -->

    <h2 class="recovery-title">
        Reset Password
    </h2>

    <p class="recovery-subtitle">
        Create a secure new password for your account.
    </p>

    <?php if($error != ""){ ?>

        <div class="error-box">
            <i class="fa-solid fa-circle-exclamation"></i>
            <?php echo $error; ?>
        </div>

    <?php } ?>

    <form method="POST">

        <div class="form-group">

            <label>New Password</label>

            <input
                type="password"
                name="new_password"
                class="form-control"
                placeholder="Enter new password"
                required>

        </div>

        <div class="form-group">

            <label>Confirm Password</label>

            <input
                type="password"
                name="confirm_password"
                class="form-control"
                placeholder="Confirm password"
                required>

        </div>

        <button
            type="submit"
            name="reset"
            class="btn">

            <i class="fa-solid fa-key"></i>
            Update Password

        </button>

    </form>

<?php } else { ?>

    <!-- SUCCESS STATE -->

    <div class="success-check">
        <i class="fa-solid fa-check"></i>
    </div>

    <h2 class="success-main-title">
        Password Updated
    </h2>

    <div class="success-box">

        <h3>Password Successfully Updated</h3>

        <p>
            Your password has been changed successfully.
            You may now log in using your new credentials.
        </p>

    </div>

    <button
        class="btn"
        onclick="window.location.href='login.php'">

        <i class="fa-solid fa-right-to-bracket"></i>
        Back to Login

    </button>

<?php } ?>

</div>

</body>
</html>