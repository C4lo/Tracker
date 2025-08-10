<?php
// ------> neue Datei anlegen: backend/list_my_submissions.php ------
ini_set('session.cookie_path', '/');
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
  http_response_code(401);
  echo json_encode(['ok'=>false,'error'=>'Not logged in']);
  exit;
}

require_once __DIR__ . '/../backend/db_connect.php';

$stmt = $pdo->prepare("SELECT id, name, initiative, processed, created_at
                       FROM initiative_submissions
                       WHERE user_id = ?
                       ORDER BY created_at DESC LIMIT 10");
$stmt->execute([$_SESSION['user_id']]);
echo json_encode(['ok'=>true, 'items'=>$stmt->fetchAll(PDO::FETCH_ASSOC)]);
