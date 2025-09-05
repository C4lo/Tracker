<?php
ini_set('session.cookie_path', '/');
session_start();

require_once __DIR__ . '/db_connect.php';

$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

header('Content-Type: application/json');

if ($username === '' || $password === '') {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Username and password are required']);
    exit;
}

$stmt = $pdo->prepare('SELECT id, username, password_hash, role FROM users WHERE username = ?');
$stmt->execute([$username]);
$user = $stmt->fetch();

if ($user && password_verify($password, $user['password_hash'])) {
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['role'] = $user['role'] ?? 'player';
    echo json_encode(['status' => 'ok']);
} else {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'Invalid username or password']);
}
?>