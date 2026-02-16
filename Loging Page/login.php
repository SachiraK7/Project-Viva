<?php
session_start();

// --- DATABASE CONNECTION ---
$servername = "localhost";
$username_db = "root";
$password_db = "";
$dbname = "spendify_db";

$conn = new mysqli($servername, $username_db, $password_db, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = "";

// --- 1. NORMAL LOGIN HANDLER ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'normal_login') {
    $email = $conn->real_escape_string($_POST['email']);
    $pass = $_POST['password'];

    $sql = "SELECT * FROM users WHERE email = '$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        // In a real app, use: if(password_verify($pass, $row['password']))
        if ($pass === $row['password']) {
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['email'] = $row['email'];
            $message = "<div class='success-msg'>Login Successful! Welcome back.</div>";
            // header("Location: dashboard.php"); // Redirect here
        } else {
            $message = "<div class='error-msg'>Incorrect password.</div>";
        }
    } else {
        $message = "<div class='error-msg'>User not found.</div>";
    }
}

// --- 2. GOOGLE LOGIN HANDLER (Backend) ---
// This block runs when JavaScript sends the Google Token
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['google_token'])) {
    $id_token = $_POST['google_token'];

    // Verify token with Google (Simple verification for this example)
    // In production, use the official Google Client Library for PHP
    $payload = json_decode(file_get_contents("https://oauth2.googleapis.com/tokeninfo?id_token=" . $id_token), true);

    if (isset($payload['email'])) {
        $g_email = $conn->real_escape_string($payload['email']);
        $g_name = $conn->real_escape_string($payload['name']);
        $g_id = $conn->real_escape_string($payload['sub']);

        // Check if user exists
        $check = $conn->query("SELECT * FROM users WHERE email='$g_email'");

        if ($check->num_rows > 0) {
            // User exists: Log them in
            $row = $check->fetch_assoc();
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['email'] = $row['email'];
            echo "success"; // Tell JS it worked
        } else {
            // New user: Create account
            $sql = "INSERT INTO users (full_name, email, google_id) VALUES ('$g_name', '$g_email', '$g_id')";
            if ($conn->query($sql) === TRUE) {
                $_SESSION['user_id'] = $conn->insert_id;
                $_SESSION['email'] = $g_email;
                echo "success";
            } else {
                echo "error";
            }
        }
    } else {
        echo "invalid_token";
    }
    exit(); // Stop script here so we don't return HTML to the AJAX call
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Spendify - Login</title>
    <link href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@700&family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="login.css">
    <script src="https://accounts.google.com/gsi/client" async defer></script>
</head>
<body>

    <div class="logo-container">
        <img src="dashboardlogo.png" alt="Spendify Logo" class="brand-logo">
    </div>

    <div class="main-wrapper">
        <div class="login-card">
            
            <div class="card-header">
                <h1>Welcome!</h1>
            </div>

            <div class="card-body">
                <h3>USER LOGIN</h3>

                <?php if($message != "") echo $message; ?>

                <form action="login.php" method="POST">
                    <input type="hidden" name="action" value="normal_login">

                    <div class="input-group">
                        <div class="icon-holder">
                            <img src="user.png" alt="User">
                        </div>
                        <input type="email" name="email" placeholder="Email" required>
                    </div>

                    <div class="input-group">
                        <div class="icon-holder">
                            <img src="padlock.png" alt="Lock">
                        </div>
                        <input type="password" name="password" placeholder="Password" required>
                    </div>

                    <div class="options">
                        <label class="remember-me">
                            <input type="checkbox" name="remember"> Remember me
                        </label>
                        <a href="#" class="forgot-pass">Forgot Password?</a>
                    </div>

                    <button type="submit" class="sign-in-btn">Sign In</button>

                    <div class="divider">
                        <span>Or Sign In With</span>
                    </div>

                    <div class="google-wrapper">
                        <div id="customGoogleBtn" class="google-btn">
                            <a href="https://accounts.google.com/signin/v2/identifier" target="_blank">
                            <img src="google.png" alt="Google">
                            </a>
                        </div>
                    </div>

                    <p class="signup-text">Don't have an account? <a href="signup.php">Sign Up</a></p>
                </form>
            </div>
        </div>
    </div>

    <div id="g_id_onload"
         data-client_id="YOUR_GOOGLE_CLIENT_ID"
         data-callback="handleCredentialResponse"
         data-auto_prompt="false">
    </div>

    <script src="script.js"></script>
</body>
</html>