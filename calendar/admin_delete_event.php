<?php
require_once 'db_connection.php';
session_start();

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit;
}

$id = (int) $_GET['id'];

$stmt = $pdo->prepare("DELETE FROM calendar_events WHERE id = ?");
$stmt->execute([$id]);

$_SESSION['admin_message'] = "🗑️ Event gelöscht!";

header("Location: admin_dashboard.php");
exit;
