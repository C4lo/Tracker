<?php
ini_set('session.cookie_path', '/'); // hilft, falls Pfad-Probleme auftreten
session_start();
// backend/get_user_data.php

// Start the session to get the logged-in user's ID
session_start();
include 'db_connect.php';

// Check if the user is actually logged in
if (!isset($_SESSION['user_id'])) {
    header('Content-Type: application/json');
    http_response_code(401); // Unauthorized
    echo json_encode(['status' => 'error', 'message' => 'User not logged in']);
    exit(); // Stop the script
}

$user_id = $_SESSION['user_id'];

// Prepare a SQL statement to get all parties the user is a member of
// We use a JOIN to link the party_members table with the parties table
$stmt = $conn->prepare("
    SELECT p.id, p.name 
    FROM parties p
    JOIN party_members pm ON p.id = pm.party_id
    WHERE pm.user_id = ?
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$parties = [];
while ($row = $result->fetch_assoc()) {
    $parties[] = $row;
}

// Send the data back as a JSON response
header('Content-Type: application/json');
echo json_encode([
    'status' => 'success',
    'parties' => $parties
]);

$stmt->close();
$conn->close();
?>