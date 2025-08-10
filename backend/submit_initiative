<?php
// ------> neue Datei anlegen: backend/submit_initiative.php ------
ini_set('session.cookie_path', '/');
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
  http_response_code(401);
  echo json_encode(['ok'=>false,'error'=>'Not logged in']);
  exit;
}

require_once __DIR__ . '/../backend/db_connect.php'; // Pfad anpassen, falls anders

$name = trim($_POST['name'] ?? '');
$initiative = isset($_POST['initiative']) ? (int)$_POST['initiative'] : null;

if ($name === '' || $initiative === null) {
  http_response_code(400);
  echo json_encode(['ok'=>false,'error'=>'Missing fields']);
  exit;
}

$stmt = $pdo->prepare("INSERT INTO initiative_submissions (user_id, name, initiative) VALUES (?, ?, ?)");
$stmt->execute([$_SESSION['user_id'], $name, $initiative]);

echo json_encode(['ok'=>true]);
