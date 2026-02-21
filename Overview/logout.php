<?php
session_start();

// 1. Unset all session variables
$_SESSION = array();

// 2. Destroy the session
session_destroy();

// 3. Redirect to login page
header("Location: login.php");
exit();
?>