<?php
session_start(); // Start the session to identify the logged-in user

$host = "localhost";
$db_name = "spendify_db";
$username = "root";
$password = "";

// Default fallback values in case something goes wrong
$userName = "Simon Riley";
$userEmail = "simon@company.com";
$profileImg = "image 10.png"; // Default fallback image

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db_name", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // --- NEW: HANDLE PROFILE PICTURE DELETION ---
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete_profile_pic' && isset($_SESSION['user_email'])) {
        // Update the database to remove the profile image
        $stmt = $pdo->prepare("UPDATE users SET profile_img = NULL WHERE email = :email");
        $stmt->bindParam(':email', $_SESSION['user_email']);
        
        if ($stmt->execute()) {
            // Send success response back to JavaScript with the default fallback image
            echo "success|image 10.png";
            exit(); 
        } else {
            echo "error|Database update failed.";
            exit();
        }
    }
    // ---------------------------------------------------------------------

    // --- HANDLE PROFILE PICTURE UPLOAD ---
    // This intercepts the image sent by your JavaScript fetch()
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['profile_pic']) && isset($_SESSION['user_email'])) {
        $uploadDir = "uploads/";
        
        // Create the uploads directory if it doesn't exist
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $fileName = basename($_FILES["profile_pic"]["name"]);
        $fileType = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        // Create a unique file name to prevent overriding images
        $newFileName = $uploadDir . uniqid('img_') . '.' . $fileType; 

        // Validate image format
        $allowedTypes = array('jpg', 'png', 'jpeg', 'gif', 'webp');
        if (in_array($fileType, $allowedTypes)) {
            if (move_uploaded_file($_FILES["profile_pic"]["tmp_name"], $newFileName)) {
                
                // Update the database with the new file path
                $stmt = $pdo->prepare("UPDATE users SET profile_img = :profile_img WHERE email = :email");
                $stmt->bindParam(':profile_img', $newFileName);
                $stmt->bindParam(':email', $_SESSION['user_email']);
                
                if ($stmt->execute()) {
                    // Send success response back to JavaScript with the new image path
                    echo "success|" . $newFileName;
                    exit(); // Stop rendering the rest of the HTML
                } else {
                    echo "error|Database update failed.";
                    exit();
                }
            } else {
                echo "error|Failed to save image to server.";
                exit();
            }
        } else {
            echo "error|Invalid file type. Only JPG, PNG, GIF, and WEBP are allowed.";
            exit();
        }
    }
    // ---------------------------------------------------------------------

    // --- HANDLE ALL MODAL SUBMISSIONS (NAME, EMAIL, PASSWORD, OR DELETE) ---
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_email'])) {
        
        // 1. Check if it's a Password Update
        if (!empty($_POST['new_password']) && !empty($_POST['confirm_password'])) {
            $newPassword = $_POST['new_password'];
            $confirmPassword = $_POST['confirm_password'];
            
            if ($newPassword === $confirmPassword) {
                $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
                
                $updateStmt = $pdo->prepare("UPDATE users SET password = :password WHERE email = :email");
                $updateStmt->bindParam(':password', $hashedPassword);
                $updateStmt->bindParam(':email', $_SESSION['user_email']);
                
                if ($updateStmt->execute()) {
                    header("Location: settings.php"); 
                    exit();
                }
            }
        }
        // 2. Check if it's a Name or Email Update
        elseif (!empty($_POST['modal_input'])) {
            $newValue = trim($_POST['modal_input']);
            
            // Check if the user entered an email address format
            if (filter_var($newValue, FILTER_VALIDATE_EMAIL)) {
                $updateStmt = $pdo->prepare("UPDATE users SET email = :new_email WHERE email = :current_email");
                $updateStmt->bindParam(':new_email', $newValue);
                $updateStmt->bindParam(':current_email', $_SESSION['user_email']);
                
                if ($updateStmt->execute()) {
                    $_SESSION['user_email'] = $newValue; // Update active session
                    header("Location: settings.php"); 
                    exit();
                }
            } else {
                $updateStmt = $pdo->prepare("UPDATE users SET full_name = :full_name WHERE email = :email");
                $updateStmt->bindParam(':full_name', $newValue);
                $updateStmt->bindParam(':email', $_SESSION['user_email']);
                
                if ($updateStmt->execute()) {
                    header("Location: settings.php"); 
                    exit();
                }
            }
        }
        // 3. Check if it's a Delete Account request
        elseif (isset($_POST['delete_account']) && $_POST['delete_account'] === 'yes') {
            $deleteStmt = $pdo->prepare("DELETE FROM users WHERE email = :email");
            $deleteStmt->bindParam(':email', $_SESSION['user_email']);
            
            if ($deleteStmt->execute()) {
                session_unset(); 
                session_destroy(); 
                header("Location: /Loging Page/login.php"); 
                exit();
            }
        }
    }
    // ---------------------------------------------------------------------

    // Check if the user is logged in and their email is in the session
    if (isset($_SESSION['user_email'])) {
        $loggedInEmail = $_SESSION['user_email'];

        // Fetching the specific logged-in user's data
        $stmt = $pdo->prepare("SELECT full_name, email, profile_img FROM users WHERE email = :email");
        $stmt->bindParam(':email', $loggedInEmail);
        $stmt->execute();
        $db_user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($db_user) {
            $userName = $db_user['full_name']; 
            $userEmail = $db_user['email'];
            
            // If a profile image exists in DB, use it. Otherwise, stay with fallback.
            if (!empty($db_user['profile_img'])) {
                $profileImg = $db_user['profile_img'];
            }
        }
    }
} catch(PDOException $e) {
    // Silently handle error to keep the UI clean
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Spendify Settings</title>
    <link rel="stylesheet" href="settings.css">
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;600;800&family=Poppins:wght@400;600;700&family=Roboto+Slab:wght@400;700&display=swap" rel="stylesheet">
</head>
<body>
<div class="app-container">
    <aside class="sidebar">
   
<a href="/Dashboard/dash.php" style="text-decoration: none; color: inherit;">
 <div class="logo-section">
    <img src="logo 1.png" alt="Spendify" class="logo-img" >
    <span class="logo-text">Spendify</span>
                
</div> </a>

<nav class="nav-links">
    <a href="/Dashboard/dash.php" class="nav-link-wrapper">
        <div class="nav-item">
            <img src="image 1.png" class="nav-icon"> Dashboard
        </div>
    </a>

    <a href="/Expenses/expense.php" class="nav-link-wrapper">
        <div class="nav-item">
            <img src="image 2.png" class="nav-icon"> Expense
        </div>
    </a>

    <a href="/Overview/overview.php" class="nav-link-wrapper">
        <div class="nav-item">
            <img src="image 18.png" class="nav-icon"> Overview
        </div>
    </a>
</nav>

<a href="/Settings/settings.php" class="nav-link-wrapper">
    <div class="nav-item active settings-bottom">
        <img src="image 4.png" class="nav-icon" > Settings
    </div>
</a>
</aside>

<main class="main-content">
<header class="top-header">
<div class="menu-label"><img src="Margin.png" alt="Menu">Settings</div>
<div class="profile-top">
<img src="<?php echo $profileImg; ?>" class="mini-avatar">

</div>
</header>

<div class="settings-white-card">
<section class="left-profile">
<div class="profile-hero">
 <img src="<?php echo $profileImg; ?>" class="large-avatar" onclick="handleProfilePicChange()" style="cursor: pointer;"> 
 
<h1 class="display-name"><?php echo htmlspecialchars($userName); ?></h1>
</div>
                    
<div class="link-buttons">
      <a href="privacy.php" style="text-decoration: none;">
        <button class="btn-box" style="width: 100%;">Privacy policy</button> </a>

      <a href="terms.php" style="text-decoration: none;">
          <button class="btn-box" style="width: 100%;">Terms & conditions</button> </a>
      <a href="/Loging Page/login.php" style="text-decoration: none;">
        <button class="btn-box" style="width: 100%;">Logout</button>
    </a>
       <button class="btn-red" onclick="handleDeleteAccount()">Delete account</button>
   
    
</div>
</section>

<div class="v-line"></div>

<section class="right-form">
<h3 class="group-title">Profile information</h3>
                    
<div class="form-group">
<label>Full name</label>
<div class="custom-input">
    <input type="text" id="name" value="<?php echo htmlspecialchars($userName); ?>" readonly>
    <span class="edit-link" onclick="handleEdit('name')">Change name ></span>
</div>
</div>

<div class="form-group">
    <label>Email address</label>
<div class="custom-input">
    <input type="email" id="email" value="<?php echo htmlspecialchars($userEmail); ?>" readonly>
    <span class="edit-link" onclick="handleEdit('email')">Change email address ></span>
</div>
</div>

<h3 class="group-title security-gap">Security</h3>
<div class="form-group">
    <label>Password</label>
<div class="custom-input">
<input type="password" id="password" value="********" readonly>
<span class="edit-link" onclick="handleEdit('password')">Change password ></span>
</div>
</div>
</section>
</div>
</main>
</div>

<div id="modal-overlay" class="modal-overlay" style="display: none;">
<div class="modal-box">
<div class="modal-header">
   <h3 id="modal-title">Change Name</h3>
   <span class="close-x" onclick="closeModal()">&times;</span>
</div>
        
<form method="POST" action="">
    <div class="modal-body" id="modal-body-content">
        <div id="single-field-container">
            <input type="text" id="modal-input" name="modal_input" class="modal-field">
        </div>

        <div id="password-fields-container" style="display: none;">
            <label class="modal-label">New Password</label>
            <input type="password" id="new-password" name="new_password" class="modal-field">
            <label class="modal-label">Confirm Password</label>
            <input type="password" id="confirm-password" name="confirm_password" class="modal-field">
        </div>
    </div>

    <div class="modal-footer" id="modal-footer-buttons">
        <button type="button" class="btn-cancel" id="cancel-btn" onclick="closeModal()">Cancel</button>
        <button type="submit" class="btn-confirm" id="confirm-btn">Confirm</button>
    </div>
</form>

</div>
</div>

<div id="delete-modal-overlay" class="modal-overlay" style="display: none;">
    <div class="modal-box" style="padding-bottom: 20px;">
        <div class="modal-header">
           <h3 style="font-size: 1.2rem; margin: 0;">Delete Account</h3>
           <span class="close-x" onclick="document.getElementById('delete-modal-overlay').style.display='none'">&times;</span>
        </div>
        
        <form method="POST" action="">
            <input type="hidden" name="delete_account" value="yes">
            
            <div class="modal-body" style="padding: 25px 20px; text-align: left;">
                <div style="display: flex; align-items: center; gap: 15px;">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="#ff4d4d" width="40px" height="40px" style="flex-shrink: 0;">
                        <path d="M12 2L1 21h22L12 2zm1 14h-2v-2h2v2zm0-4h-2V8h2v4z"/>
                    </svg>
                    <p style="margin: 0; font-size: 14px; color: #555; line-height: 1.5;">
                        Are you sure you want to delete this item?<br>This action cannot be undone.
                    </p>
                </div>
            </div>

            <div class="modal-footer" style="justify-content: center; gap: 15px;">
                <button type="button" class="btn-cancel" style="background-color: #000; color: #fff; width: 80px;" onclick="document.getElementById('delete-modal-overlay').style.display='none'">No</button>
                <button type="submit" class="btn-confirm" style="background-color: #ff4d4d; color: #fff; border: none; width: 80px;">Yes</button>
            </div>
        </form>
    </div>
</div>

<script src="settings.js"></script>
</body>
</html>