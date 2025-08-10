<?php
ini_set('session.cookie_path', '/'); // hilft, falls Pfad-Probleme auftreten
session_start();
// backend/db_connect.php

// --- Database Credentials ---
$db_host = 'ht7m.your-database.de';
$db_user = 'jerome_tracker'; // Or your specific database username
$db_pass = 'h6akkvAgUC33Px3q';     // Or your specific database password
$db_name = 'initiative_tracker'; // Your database name

// --- Establish Connection ---
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

// --- Check for Connection Errors ---
// This check is crucial. If it fails, the script will stop here.
if ($conn->connect_error) {
    // Stop everything and report the error.
    die("Database Connection Failed: " . $conn->connect_error);
}

?>