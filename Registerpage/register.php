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
                header("Location: login.php");
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

<div class="container">

    <div class="left-side">
        <img src="dashboardlogo (1).png" alt="Spendify Logo" class="logo">
    </div>

    <div class="right-side"> 
        <div class="form-container">  

            <h2 class="welcome-text">Welcome</h2>
            <p class="brand-text">Create an Account</p>

            <form action="register.php" method="POST" onsubmit="return validateForm()">

               
                <?php if (!empty($message)) { ?>
                    <p style="color:red;"><?php echo $message; ?></p>
                <?php } ?>

                <label for="name">Name</label>
                <input type="text" id="name" name="username" placeholder="Simon Riley" required>

                <label for="email">Email</label>
                <input type="email" id="email" name="email" placeholder="simon@company.com" required>

                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Dh36#&58" required minlength="8">

                <label for="confirmPassword">Confirm Password</label>
                <input type="password" id="confirmPassword" name="confirm_password" placeholder="Dh36#&!*" required>

                <button type="submit">Sign Up</button>

                <div class="divider">
                    <span>Or Sign Up With</span>
                </div>

                <div class="google-btn">
                    <button type="button">
                        <img src="google.png" alt="Google Icon" class="google-icon">
                        Sign Up with Google
                    </button>
                </div>

                <p class="signin-text">
                    Already have an account? <a href="login.php">Sign in</a>
                </p>

            </form>
        </div>
    </div>

</div>

</body>
</html>
