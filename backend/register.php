<?php
ini_set('session.cookie_path', '/'); // hilft, falls Pfad-Probleme auftreten
session_start();
// backend/register.php

// 1. Include the database connection
include 'db_connect.php';

// 2. Get the data from the POST request sent by the JavaScript
$username = $_POST['username'];
$password = $_POST['password'];

// 3. Basic Validation: Check if inputs are empty
if (empty($username) || empty($password)) {
    // Stop the script and send an error message back
    die("Error: Username and password are required.");
}

// 4. Securely hash the password
// password_hash() is the standard, secure way to store passwords.
$password_hash = password_hash($password, PASSWORD_DEFAULT);

// 5. Use a Prepared Statement to safely insert the new user into the database
// This prevents SQL injection attacks.
$stmt = $conn->prepare("INSERT INTO users (username, password_hash) VALUES (?, ?)");

// 'ss' means we are binding two strings to the query
$stmt->bind_param("ss", $username, $password_hash);

// 6. Execute the statement and provide feedback
if ($stmt->execute()) {
    echo "Registration successful!";
} else {
    // Check if the error is a 'duplicate entry' error (error number 1062)
    if ($conn->errno == 1062) {
        echo "Error: This username is already taken.";
    } else {
        // For other errors, show a generic message
        echo "Error: An error occurred during registration.";
    }
}

// 7. Close the statement and the connection
$stmt->close();
$conn->close();
?>