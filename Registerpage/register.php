<?php
$servername = "localhost";
$dbusername = "root";
$dbpassword = "";
$dbname = "spendify";

$conn = new mysqli($servername, $dbusername, $dbpassword, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

   
    if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
        $message = "All fields are required.";
    } 
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Invalid email format.";
    } 
    elseif ($password !== $confirm_password) {
        $message = "Passwords do not match.";
    } 
    else {

        $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $check->bind_param("s", $email);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $message = "Email already registered.";
        } 
        else {

            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");

            if (!$stmt) {
                die("Prepare failed: " . $conn->error);
            }

            $stmt->bind_param("sss", $username, $email, $hashed_password);

            if ($stmt->execute()) {
                header("Location:/Webproject/Project-Viva/Loginpage/login.php");
                exit();
            } else {
                $message = "Something went wrong. Please try again.";
            }

            $stmt->close();
        }

        $check->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <title>Spendify - Register</title>
   <link rel="stylesheet" href="register.css"> 
   <link href="https://fonts.googleapis.com/css2?family=Kalam&family=Manual&display=swap" rel="stylesheet">
   <script src="register.js"></script>
</head>

<body>

<body>

<div class="page-wrapper">

    <div class="top-logo">
   <img src="dashboardlogo (1).png">
</div>

    

    <div class="container">

        <div class="left-side">
            <img src="bg pic.png" alt="Money Illustration">
        </div>

        <div class="right-side">
    <div class="form-card">

       
        <div class="card-top">
            <h1 class="welcome">Welcome</h1>
        </div>

        
        <div class="card-bottom">

            <p class="create-text">CREATE AN ACCOUNT</p>

            <form action="register.php" method="POST">

                <?php if (!empty($message)) { ?>
                    <p class="error-msg"><?php echo $message; ?></p>
                <?php } ?>

                <label>Name</label>
                <input type="text" name="username" placeholder='eg: "Simon Riley"' required>

                <label>Email</label>
                <input type="email" name="email" placeholder='eg: "simon@company.com"' required>

                <label>Password</label>
                <input type="password" name="password" placeholder='eg: "Dh36#&58"' required>

                <label>Confirm Password</label>
                <input type="password" name="confirm_password" placeholder='eg: "Dh36#&!*"' required>

                <button type="submit" class="signup-btn">Sign Up</button>

                <div class="divider">
                    <span>Or Sign Up With</span>
                </div>

                <div class="google-circle">
                    <img src="google.png">
                </div>

                <p class="signin">
                    Already have an account? <a href="login.php">Sign in</a>
                </p>

            </form>

        </div>
    </div>
</div>


</body>
</html>