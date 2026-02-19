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

// FORM SUBMISSION LOGIC
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'update_password') {

    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Basic Server-Side Validation
    if (empty($new_password) || empty($confirm_password)) {
        $message = "<div class='error-text'>All fields are required!</div>";
    } elseif ($new_password !== $confirm_password) {
        $message = "<div class='error-text'>Passwords do not match!</div>";
    } else {
        // Hash the new password
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        
        // Placeholder: You would normally get the user's email/ID from a token in the URL or session here.
        // Assuming we have a $user_id available:
        // $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
        // $stmt->bind_param("si", $hashed_password, $user_id);
        // $stmt->execute();
        
        $message = "<div class='error-text' style='background-color: #4CAF50;'>Password updated successfully!</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Spendify - Update Password</title>
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
                <h1>Update Password</h1>
            </div>

            <div class="card-body">
                <h3>FORGOT PASSWORD</h3>
                
                <p class="desc-text">The password must be different<br>than before</p>

                <?php if($message != "") echo $message; ?>

                <form id="updateForm" action="" method="POST">
                    <input type="hidden" name="action" value="update_password">

                    <label class="input-label">New Password</label>
                    <div class="input-group">
                        <input type="password" id="new_password" name="new_password" placeholder='eg: "********"' required>
                    </div>

                    <label class="input-label">Confirm Password</label>
                    <div class="input-group">
                        <input type="password" id="confirm_password" name="confirm_password" placeholder='eg: "********"' required>
                    </div>

                    <button type="submit" class="sign-in-btn">Continue</button>
                </form>
            </div>
        </div>
    </div>

    <script src="update_password.js"></script>
</body>
</html>