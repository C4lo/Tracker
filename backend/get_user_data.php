<?php
ini_set('session.cookie_path', '/');
session_start();

require_once __DIR__ . '/db_connect.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'User not logged in']);
    exit;
}

$stmt = $pdo->prepare('SELECT p.id, p.name FROM parties p JOIN party_members pm ON p.id = pm.party_id WHERE pm.user_id = ?');
$stmt->execute([$_SESSION['user_id']]);
$parties = $stmt->fetchAll();

echo json_encode(['status' => 'success', 'parties' => $parties]);
?>
