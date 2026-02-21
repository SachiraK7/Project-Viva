<?php
$host = "localhost";
$db_name = "spendify";
$username = "root";
$password = "";

// Default fallback values
$userName = "Simon Riley";
$userEmail = "simon@company.com";
$profileImg = "image 10.png"; // Default fallback image



try {
    $pdo = new PDO("mysql:host=$host;dbname=$db_name", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Fetching user 
    $stmt = $pdo->prepare("SELECT name, email,profile_img FROM users LIMIT 1");
    $stmt->execute();
    $db_user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($db_user) {
        $userName = $db_user['name']; // Your specific column name
        $userEmail = $db_user['email'];
    }
    // NEW LOGIC: If a profile image exists in DB, use it. Otherwise, stay with fallback.
        if (!empty($db_user['profile_img'])) {
            $profileImg = $db_user['profile_img'];
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
   

<a href="dash.php" style="text-decoration: none; color: inherit;">
 <div class="logo-section">
    <img src="logo 1.png" alt="Spendify" class="logo-img" >
    <span class="logo-text">Spendify</span>
                
</div> </a>


<nav class="nav-links">
    <a href="dash.php" class="nav-link-wrapper">
        <div class="nav-item">
            <img src="image 1.png" class="nav-icon"> Dashboard
        </div>
    </a>

    <a href="expense.php" class="nav-link-wrapper">
        <div class="nav-item">
            <img src="image 2.png" class="nav-icon"> Expense
        </div>
    </a>

    <a href="overview.php" class="nav-link-wrapper">
        <div class="nav-item">
            <img src="image 18.png" class="nav-icon"> Overview
        </div>
    </a>
</nav>

<a href="settings.php" class="nav-link-wrapper">
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
        
<div class="modal-body" id="modal-body-content">
<div id="single-field-container">
<input type="text" id="modal-input" class="modal-field">
</div>

<div id="password-fields-container" style="display: none;">
<label class="modal-label">New Password</label>
<input type="password" id="new-password" class="modal-field">
<label class="modal-label">Confirm Password</label>
<input type="password" id="confirm-password" class="modal-field">
</div>
</div>

<div class="modal-footer" id="modal-footer-buttons">
<button class="btn-cancel" id="cancel-btn" onclick="closeModal()">Cancel</button>
<button class="btn-confirm" id="confirm-btn" onclick="saveModalData()">Confirm</button>
</div>
</div>
</div>


<script src="settings.js"></script>
</body>
</html>

