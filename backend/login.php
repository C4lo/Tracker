<?php
ini_set('session.cookie_path', '/'); // hilft, falls Pfad-Probleme auftreten
session_start();
// backend/login.php

// session_start() MUST be the very first thing on the page
// It tells the server to either start a new session or resume an existing one.
session_start(); 

// 1. Include the database connection
include 'db_connect.php';

// 2. Get the data from the POST request
$username = $_POST['username'];
$password = $_POST['password'];

// 3. Basic Validation
if (empty($username) || empty($password)) {
    die("Error: Username and password are required.");
}

// 4. Prepare a statement to get the user's info from the database
$stmt = $conn->prepare("SELECT id, username, password_hash FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

// 5. Check if a user with that username was found
if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();

    // 6. Verify the submitted password against the stored hash
    // password_verify() is the secure counterpart to password_hash()
    if (password_verify($password, $user['password_hash'])) {
        
        // Password is correct!
        // 7. Store user data in the session.
        // This is how the server "remembers" who is logged in across different pages.
        $_SESSION['user_id'] = $row['id'];
        $_SESSION['username'] = $row['username'];
        $_SESSION['role']=$row['role'];
        echo "Login successful!";

    } else {
        // Password was incorrect
        echo "Error: Invalid username or password.";
    }
} else {
    // No user found with that username
    echo "Error: Invalid username or password.";
}

$stmt->close();
$conn->close();
?>