<?php
session_start();

// --- DATABASE CONNECTION ---
$servername = "localhost";
$username_db = "root";
$password_db = "";
$dbname = "spendify_db";

$conn = new mysqli($servername, $username_db, $password_db, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$error_msg = "";

// --- LOGIN LOGIC ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user = $_POST['username'];
    $pass = $_POST['password'];

    // Prevent SQL Injection
    $user = $conn->real_escape_string($user);
    $pass = $conn->real_escape_string($pass);

    $sql = "SELECT * FROM users WHERE username = '$user' AND password = '$pass'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Login Success
        $_SESSION['username'] = $user;
        echo "<script>alert('Login Successful! Welcome " . $user . "');</script>";
        // header("Location: dashboard.php"); // Redirect user here
    } else {
        // Login Failed
        $error_msg = "Invalid username or password!";
    }
}
$conn->close();
?> 


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Spendify - Login</title>
    <link rel="stylesheet" href="login.css">
</head>
<body>

    <div class="logo-container">
        <img src="dashboardlogo.png" alt="Spendify Logo" class="brand-logo">
    </div>

    <div class="main-wrapper">
        <div class="login-card">
            
            <div class="card-header">
                <h1>Welcome</h1>
            </div>

            <div class="card-body">
                <h3>USER LOGIN</h3>

                <?php if($error_msg): ?>
                    <p class="error-text"><?php echo $error_msg; ?></p>
                <?php endif; ?>

                <form action="login.php" method="POST" id="loginForm">
                    
                    <div class="input-group">
                        <div class="icon-holder">
                            <img src="user.png" alt="User">
                        </div>
                        <input type="text" name="username" placeholder="Username" required>
                    </div>

                    <div class="input-group">
                        <div class="icon-holder">
                            <img src="padlock.png" alt="Lock">
                        </div>
                        <input type="password" name="password" placeholder="Password" required>
                    </div>

                    <div class="form-options">
                        <label class="remember-me">
                            <input type="checkbox" name="remember"> Remember me
                        </label>
                        <a href="#" class="forgot-pass">Forgot Password?</a>
                    </div>

                    <button type="submit" class="sign-in-btn">Sign In</button>

                    <div class="divider">
                        <span>Or Sign In With</span>
                    </div>

                    <button type="button" class="google-btn">
                        <img src="google.png" alt="Google">
                    </button>

                    <p class="signup-link">Don't have an account? <a href="C:\xampp\htdocs\Viva\Project-Viva\Register page\register.php">Sign Up</a></p>
                </form>
            </div>
        </div>
    </div>

    <script src="script.js"></script>
</body>
</html>