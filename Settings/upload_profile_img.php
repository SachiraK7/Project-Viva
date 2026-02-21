<?php
$host = "localhost";
$db_name = "spendify";
$username = "root";
$password = "";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db_name", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if (isset($_FILES['profile_pic'])) {
        $file = $_FILES['profile_pic'];
        $fileName = time() . '_' . $file['name']; // Unique name to prevent overwriting
        $fileTmpName = $file['tmp_name'];
        $uploadDirectory = 'uploads/' . $fileName;

        // Move the file to  uploads folder
        if (move_uploaded_file($fileTmpName, $uploadDirectory)) {
            
            // Update the database (assuming LIMIT 1 for current setup)
            $stmt = $pdo->prepare("UPDATE users SET profile_img = ? LIMIT 1");
            if ($stmt->execute([$uploadDirectory])) {
                echo "success|" . $uploadDirectory;
            } else {
                echo "error|Database update failed.";
            }
        } else {
            echo "error|Failed to move uploaded file.";
        }
    }
} catch(PDOException $e) {
    echo "error|" . $e->getMessage();
}
?>