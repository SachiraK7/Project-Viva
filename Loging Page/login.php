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
            $stmt->bind_result($id, $full_name, $hashed_password);
            $stmt->fetch();
            
            if (password_verify($password, $hashed_password)) {
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