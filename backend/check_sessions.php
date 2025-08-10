<?php
ini_set('session.cookie_path', '/'); // hilft, falls Pfad-Probleme auftreten
session_start();
// backend/check_session.php

// Set the header BEFORE any other output
header('Content-Type: application/json');

// Check if a user ID exists in the session
if (isset($_SESSION['user_id']) && isset($_SESSION['username'])) {
    // If logged in, send a success status and the user's data
    echo json_encode([
        'status' => 'success',
        'user_id' => $_SESSION['user_id'],
        'username' => $_SESSION['username']
    ]);
} else {
    // If not logged in, send an error status
    echo json_encode([
        'status' => 'error',
        'message' => 'User is not logged in.'
    ]);
}
?>