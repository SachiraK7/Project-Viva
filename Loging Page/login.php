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
// 1. Fixed the action to check for 'normal_login'
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'normal_login') {

    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // SERVER-SIDE VALIDATION
    if (empty($email) || empty($password)) {
        $message = "<div class='error-msg'>All fields are required!</div>";
    }
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "<div class='error-msg'>Invalid email format!</div>";
    }
    else {
        // CHECK IF USER EXISTS
        $stmt = $conn->prepare("SELECT id, full_name, password FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        
        // Check if a row was returned before binding results
        if ($stmt->num_rows == 1) {
            if ($stmt->fetch() && password_verify($password, $hashed_password)) {
                // LOGIN SUCCESS
                $_SESSION['user_id'] = $id;
                
                // 2. Fixed the session variable to match expense.php exactly
                $_SESSION['userName'] = $full_name; 
                
                $_SESSION['user_email'] = $email;

                // 3. Fixed the redirect to match your actual dashboard file
                header("Location: dashboard.php"); 
                exit();
            } else {
                $message = "<div class='error-msg'>Incorrect password!</div>";
            }
        } else {
            $message = "<div class='error-msg'>Email not registered!</div>";
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
                        <span>Or Sign up</span>
                    </div>

                
                    <p class="signup-link">Don't have an account? <a href="/Registerpage/register.php">Sign Up</a></p>
                </form>
            </div>
        </div>
    </div>

    <div id="g_id_onload"
         data-client_id="YOUR_GOOGLE_CLIENT_ID"
         data-callback="handleCredentialResponse"
         data-auto_prompt="false">
    </div>

    <script src="login.js"></script>
</body>
</html>
