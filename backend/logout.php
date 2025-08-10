<?php
ini_set('session.cookie_path', '/'); // hilft, falls Pfad-Probleme auftreten
session_start();
// backend/logout.php
session_start(); // Resume the session

// Unset all session variables
$_SESSION = array();

// Destroy the session
session_destroy();

// Send a success message back
echo "Logout successful.";
?>