<?php
// ------> neue Datei anlegen: backend/list_pending_submissions.php ------
ini_set('session.cookie_path', '/');
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) { http_response_code(401); exit; }
$role = $_SESSION['role'] ?? 'player';
if (!in_array($role, ['gm','admin'])) { http_response_code(403); exit; }

require_once __DIR__ . '/../backend/db_connect.php';

$stmt = $pdo->query("SELECT s.id, s.name, s.initiative, u.username, s.created_at
                     FROM initiative_submissions s
                     JOIN users u ON u.id = s.user_id
                     WHERE s.processed = 0
                     ORDER BY s.created_at ASC");
echo json_encode(['ok'=>true, 'items'=>$stmt->fetchAll(PDO::FETCH_ASSOC)]);
