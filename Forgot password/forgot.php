<?php
session_start();

// DATABASE CONNECTION
$servername = "localhost";
$username_db = "root";
$password_db = "";
$dbname = "spendify_db";

$conn = new mysqli($servername, $username_db, $password_db, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = "";

// FORM SUBMISSION LOGIC (Adapted for Password Reset)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'reset') {

    $email = trim($_POST['email']);

    // SERVER-SIDE VALIDATION
    if (empty($email)) {
        $message = "<div class='error-text'>Email address is required!</div>";
    }
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "<div class='error-text'>Invalid email format!</div>";
    }
    else {
        // CHECK IF USER EXISTS
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows == 1) {
            // Placeholder for your actual email sending logic
            $message = "<div class='error-text' style='background-color: #4CAF50;'>Password reset link sent to your email!</div>";
        } else {
            $message = "<div class='error-text'>Email not registered!</div>";
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Spendify - Reset Password</title>
    <link href="https://fonts.googleapis.com/css2?family=Kalam:wght@400;700&family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="login.css">
</head>
<body>

    <div class="logo-container">
        <img src="dashboardlogo.png" alt="Spendify Logo" class="brand-logo">
    </div>

    <div class="main-wrapper">
        <div class="login-card">
            
            <div class="card-header">
                <h1>Reset Password</h1>
            </div>

            <div class="card-body">
                <h3>FORGOT PASSWORD</h3>
                
                <p class="desc-text">Enter your email address to reset<br>your password</p>

                <?php if($message != "") echo $message; ?>

                <form id="resetForm" action="" method="POST">
                    <input type="hidden" name="action" value="reset">

                    <div class="input-group">
                        <input type="email" name="email" placeholder='eg: "simon@company.com"' required>
                    </div>

                    <button type="submit" class="sign-in-btn">Reset Password</button>

                    <div class="divider">
                        <span>Or Go Back</span>
                    </div>

                    <a href="login.php" class="back-sign-in-btn">Sign In</a>
                </form>
            </div>
        </div>
    </div>

    <script src="login.js"></script>
</body>
</html>