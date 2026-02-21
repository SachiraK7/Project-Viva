<?php
$host = "localhost";
$db_name = "spendify";
$username = "root";
$password = "";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db_name", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // If you use sessions, you should use $_SESSION['user_id'] here.
    // Based on your settings.php logic (LIMIT 1), we target the first user.
    $stmt = $pdo->prepare("DELETE FROM users LIMIT 1");
    $stmt->execute();

    // After deletion, redirect to register page
    header("Location: register.php");
    exit();

} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>



































