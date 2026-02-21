<?php
session_start();
// Include your database connection info
$host = "localhost";
$db_name = "spendify";
$username = "root";
$password = "";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db_name", $username, $password);
    
    // Assuming you have a user ID stored in the session
    // If not, you can delete based on the email we fetched earlier
    if (isset($_SESSION['user_email'])) {
        $stmt = $pdo->prepare("DELETE FROM users WHERE email = ?");
        $stmt->execute([$_SESSION['user_email']]);
        
        // Clear session and redirect
        session_unset();
        session_destroy();
        header("Location: login.php?status=account_deleted");
        exit();
    }
} catch(PDOException $e) {
    die("Error deleting account: " . $e->getMessage());
}
?>