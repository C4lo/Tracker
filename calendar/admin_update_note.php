<?php
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.php");
    exit;
}

require_once 'db_connection.php';

$id = (int) ($_POST['id'] ?? 0);
$note = trim($_POST['note'] ?? '');

if ($id > 0) {
    $stmt = $pdo->prepare("UPDATE calendar_registrations SET note = ? WHERE id = ?");
    $stmt->execute([$note, $id]);
}

header("Location: admin_dashboard.php");
exit;
