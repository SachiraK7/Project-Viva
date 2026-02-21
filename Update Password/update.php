<?php
// Start the session to retrieve the email from the previous page
session_start();

// --- Database Configuration ---
$host = 'localhost';
$dbname = 'spendify_db'; // Replace with your DB name
$username = 'root';             // Replace with your DB username
$dbpass = '';                   // Replace with your DB password

$message = '';

// Check if the form was submitted
if (isset($_POST['submit'])) {
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Check if the session variable from the previous page exists
    // (e.g., $_SESSION['reset_email'] = 'user@example.com'; should be set on the previous page)
    if (isset($_SESSION['reset_email'])) {
        $email = $_SESSION['reset_email'];

        if ($new_password === $confirm_password) {
            try {
                // Connect to the database using PDO
                $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $dbpass);
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                // Hash the new password for security (Highly Recommended)
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

                // FIXED: Changed table name from 'user' to 'users' to match your database
                $stmt = $pdo->prepare("UPDATE users SET password = :password WHERE email = :email");
                $stmt->bindParam(':password', $hashed_password);
                $stmt->bindParam(':email', $email);

                if ($stmt->execute()) {
                    // FIXED: Clear the session variable for security
                    unset($_SESSION['reset_email']); 
                    
                    // FIXED: Redirect to login page upon successful update
                    header("Location: /Loging Page/login.php");
                    exit();
                } else {
                    $message = "<div class='alert error'>Failed to update password.</div>";
                }
            } catch(PDOException $e) {
                $message = "<div class='alert error'>Database error: " . $e->getMessage() . "</div>";
            }
        } else {
            $message = "<div class='alert error'>Passwords do not match.</div>";
        }
    } else {
        $message = "<div class='alert error'>Session expired. Please start the password reset process again.</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Password - Spendify</title>
    <link href="https://fonts.googleapis.com/css2?family=Caveat:wght@600&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="update.css">
</head>
<body>
    <div class="page-container">
        <header class="header">
            <img src="dashboardlogo.png" alt="Spendify Logo" class="logo">
        </header>

        <main class="main-content">
            <div class="form-container">
                <div class="card">
                    <div class="card-header">
                        <h1>Update Password</h1>
                    </div>
                    <div class="card-body">
                        <h2>FORGOT PASSWORD</h2>
                        <p class="subtitle">The password must be different<br>than before</p>

                        <?php echo $message; ?> 

                        <form action="" method="POST" id="updatePasswordForm">
                            <div class="input-group">
                                <label for="new_password">New Password</label>
                                <input type="password" name="new_password" id="new_password" placeholder='eg: "d$S%si#"' required>
                            </div>
                            
                            <div class="input-group">
                                <label for="confirm_password">Confirm Password</label>
                                <input type="password" name="confirm_password" id="confirm_password" placeholder='eg: "d$S%si#"' required>
                            </div>

                            <button type="submit" name="submit" class="btn-continue">Continue</button>
                        </form>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="update.js"></script>
</body>
</html>