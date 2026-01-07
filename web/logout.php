<?php
// Start session safely
if (session_id() == '') {
    session_start();
}

// Clear session data
$_SESSION = array();  // safe for all PHP versions
session_unset();
session_destroy();

// Redirect to homepage
header("Location: index.php");
exit;
