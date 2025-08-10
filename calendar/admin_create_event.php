<?php
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.php");
    exit;
}

require_once 'db_connection.php'; // Hier baust du deine DB-Verbindung auf
require_once 'admin_header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Daten aus dem Formular holen
    $title = $_POST['title'];
    $date = DateTime::createFromFormat('d.m.Y', $_POST['date'])?->format('Y-m-d');
$time_start = DateTime::createFromFormat('H:i', $_POST['time_start'])?->format('H:i:s');
$time_end = DateTime::createFromFormat('H:i', $_POST['time_end'])?->format('H:i:s');
    $max_participants = (int) $_POST['max_participants'];
    $setting_description = $_POST['setting_description'];
    $is_votable = isset($_POST['is_votable']) ? 1 : 0;

    // Vorbereitung des SQL-Befehls
    $sql = "INSERT INTO calendar_events
            (title, date, time_start, time_end, max_participants, setting_description, is_votable)
            VALUES (?, ?, ?, ?, ?, ?, ?)";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        $title,
        $date,
        $time_start,
        $time_end,
        $max_participants,
        $setting_description,
        $is_votable
    ]);

    $_SESSION['admin_message'] = "✅ Termin erfolgreich gespeichert!";
header("Location: admin_dashboard.php");
exit;

}
?>
<?php include 'flatpickr_include.php'; ?>
<form method="POST" action="admin_create_event.php">
  <label>Titel:</label><br>
  <input type="text" name="title"><br><br>

  <label>Datum:</label><br>
  <input type="text" name="date" id="date"><br><br>

  <label>Startzeit:</label><br>
  <input type="text" name="time_start" id="time_start"><br><br>

  <label>Endzeit:</label><br>
  <input type="text" name="time_end" id="time_end"><br><br>

  <label>Maximale Teilnehmer:</label><br>
  <input type="number" name="max_participants" min="1" value="5"><br><br>

  <label>Setting-Beschreibung:</label><br>
  <textarea name="setting_description" rows="4" cols="40"></textarea><br><br>

  <label>
    <input type="checkbox" name="is_votable" value="1"> Setting-Wahl durch Spieler aktivieren
  </label><br><br>

  <input type="submit" value="Termin speichern">
</form>
