<?php
session_start();
require_once 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $event_id = (int) $_POST['event_id'];
    $nickname = trim($_POST['nickname']);
    $setting_option = $_POST['setting_option'] ?? null;
    $note = $_POST['note'] ?? null;

    // Check: existiert das Event überhaupt?
    $check = $pdo->prepare("SELECT max_participants FROM calendar_events WHERE id = ?");
    $check->execute([$event_id]);
    $event = $check->fetch();

    if (!$event) {
        $_SESSION['registration_error'] = "❌ Ungültiger Termin.";
        header("Location: calendar_public.php");
        exit;
    }

    // Ist das Event schon voll?
    $count = $pdo->prepare("SELECT COUNT(*) FROM calendar_registrations WHERE event_id = ?");
    $count->execute([$event_id]);
    $current = $count->fetchColumn();

    if ($current >= $event['max_participants']) {
        $_SESSION['registration_error'] = "❌ Dieser Termin ist leider schon voll.";
        header("Location: calendar_public.php");
        exit;
    }

    // Doppelte Anmeldung verhindern
    $dupe = $pdo->prepare("SELECT COUNT(*) FROM calendar_registrations WHERE event_id = ? AND LOWER(TRIM(nickname)) = LOWER(TRIM(?))");
    $dupe->execute([$event_id, $nickname]);
    if ($dupe->fetchColumn() > 0) {
        $_SESSION['registration_error'] = "❌ Du bist schon angemeldet.";
        header("Location: calendar_public.php");
        exit;
    }

    // Anmeldung speichern
    $stmt = $pdo->prepare("INSERT INTO calendar_registrations (event_id, nickname, note) VALUES (?, ?, ?)");
    $stmt->execute([$event_id, $nickname, $note]);
    $registration_id = $pdo->lastInsertId();

    if (!empty($setting_option)) {
        $vote = $pdo->prepare("INSERT INTO calendar_setting_votes (registration_id, setting_option) VALUES (?, ?)");
        $vote->execute([$registration_id, $setting_option]);
    }

    $_SESSION['last_registered_name'] = $nickname;
    $_SESSION['last_registered_vote'] = $setting_option ?? '';

    header("Location: calendar_public.php");
    exit;
}
?>

