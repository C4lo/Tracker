<?php
// ------> hier einfügen ------
ini_set('session.cookie_path', '/');
session_start();

header('Content-Type: application/json');

if (isset($_SESSION['user_id'])) {
    echo json_encode([
        'status' => 'success',
        'username' => $_SESSION['username'] ?? null,
        'role' => $_SESSION['role'] ?? 'player'
    ]);
} else {
    echo json_encode(['status' => 'error']);
}
