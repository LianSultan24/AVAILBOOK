<?php
session_start();

// Destroy all session data
session_unset();
session_destroy();

// Return to login page
header('Location: login.php');
exit();
?>