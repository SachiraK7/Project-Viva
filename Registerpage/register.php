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

// FORM SUBMIT LOGIC
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'register') {

    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $pass = $_POST['password'];
    $confirm = $_POST['confirm_password'];

    // SERVER VALIDATION
    if (empty($name) || empty($email) || empty($pass) || empty($confirm)) {
        $message = "<div class='error-msg'>All fields are required!</div>";
    }
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "<div class='error-msg'>Invalid email format!</div>";
    }
    elseif ($pass !== $confirm) {
        $message = "<div class='error-msg'>Passwords do not match!</div>";
    }
    elseif (strlen($pass) < 6) {
        $message = "<div class='error-msg'>Password must be at least 6 characters!</div>";
    }
    else {

        // CHECK IF EMAIL EXISTS
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $message = "<div class='error-msg'>Email already registered!</div>";
        } else {

            $hashed_password = password_hash($pass, PASSWORD_DEFAULT);

            $insert = $conn->prepare("INSERT INTO users (full_name, email, password) VALUES (?, ?, ?)");
            $insert->bind_param("sss", $name, $email, $hashed_password);

            if ($insert->execute()) {
                $message = "<div class='success-msg'>Account Created Successfully! <a href='/Dashboard/dash.php'>Go to the Dashboard</a></div>";
            } else {
                $message = "<div class='error-msg'>Database error. Please try again.</div>";
            }

            $insert->close();
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
    <title>Spendify - Sign Up</title>
    <link href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@700&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="register.css">
    <script src="https://accounts.google.com/gsi/client" async defer></script>
</head>
<body>

    <div class="logo-container">
        <img src="dashboardlogo.png" alt="Spendify" class="brand-logo">
    </div>

    <div class="main-wrapper">
        <div class="signup-card">
            
            <div class="card-header">
                <h1>Hi There!</h1>
            </div>

            <div class="card-body">
                <h3>CREATE AN ACCOUNT</h3>
                
                <?php if($message != "") echo $message; ?>

                <form action="register.php" method="POST" id="signupForm">
                    <input type="hidden" name="action" value="register">
                    
                    <div class="form-group">
                        <label>Name</label>
                        <input type="text" name="name" placeholder='eg: "Simon Riley"' required>
                    </div>

                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" placeholder='eg: "simon@company.com"' required>
                    </div>

                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" name="password" id="password" placeholder='eg: "Dh36#&58"' required>
                    </div>

                    <div class="form-group">
                        <label>Confirm Password</label>
                        <input type="password" name="confirm_password" id="confirm_password" placeholder='eg: "Dh36#&!*"' required>
                    </div>

                    <button type="submit" class="signup-btn">Sign Up<a href="/Dashboard/dash.php"></a></button>

                    <div class="divider">
                        <span>Or Sign In</span>
                    </div>

                    

                    <p class="login-link">Already have an account? <a href="/Loging Page/login.php">Sign In</a></p>
                </form>
            </div>
        </div>
    </div>

    <div id="g_id_onload"
         data-client_id="YOUR_GOOGLE_CLIENT_ID"
         data-callback="handleCredentialResponse"
         data-auto_prompt="false">
    </div>

    <script src="register.js"></script>
</body>
</html>