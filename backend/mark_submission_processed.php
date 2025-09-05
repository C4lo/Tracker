<?php
// ------> neue Datei anlegen: backend/mark_submission_processed.php ------
ini_set('session.cookie_path', '/');
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) { http_response_code(401); exit; }
$role = $_SESSION['role'] ?? 'player';
if (!in_array($role, ['gm','admin'])) { http_response_code(403); exit; }

require_once __DIR__ . '/../backend/db_connect.php';

$data = json_decode(file_get_contents('php://input'), true);
$id = (int)($data['id'] ?? 0);
if ($id <= 0) { http_response_code(400); echo json_encode(['ok'=>false]); exit; }

$stmt = $pdo->prepare("UPDATE initiative_submissions SET processed = 1 WHERE id = ?");
$stmt->execute([$id]);

echo json_encode(['ok'=>true]);
