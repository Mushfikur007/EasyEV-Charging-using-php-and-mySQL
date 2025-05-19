<?php
session_start();

// Unset all session variables
$_SESSION = array();

// Destroy the session
session_destroy();

// Redirect to login page with message
session_start();
$_SESSION['message'] = "You have been logged out successfully.";
$_SESSION['message_type'] = "info";
header("Location: login.php");
exit();
?> 