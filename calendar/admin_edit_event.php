<?php
require_once 'db_connection.php';
session_start();

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit;
}

if (!isset($_GET['id'])) {
    die("❌ Kein Termin gewählt.");
}

$id = (int) $_GET['id'];

$check = $pdo->prepare("SELECT id FROM calendar_events WHERE id = ?");
$check->execute([$id]);

if ($check->rowCount() === 0) {
    die("❌ Termin nicht gefunden oder ungültige ID.");
}

// Event laden
$stmt = $pdo->prepare("SELECT * FROM calendar_events WHERE id = ?");
$stmt->execute([$id]);
$event = $stmt->fetch();

if (!$event) {
    die("❌ Termin nicht gefunden.");
}

// Wenn gespeichert wird
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $date = DateTime::createFromFormat('d.m.Y', $_POST['date'])?->format('Y-m-d');
$time_start = DateTime::createFromFormat('H:i', $_POST['time_start'])?->format('H:i:s');
$time_end = DateTime::createFromFormat('H:i', $_POST['time_end'])?->format('H:i:s');
    $max = max(1, (int) $_POST['max_participants']);
    $desc = $_POST['setting_description'];
    $vote = isset($_POST['is_votable']) ? 1 : 0;

    $update = $pdo->prepare("UPDATE calendar_events SET title=?, date=?, time_start=?, time_end=?, max_participants=?, setting_description=?, is_votable=? WHERE id=?");
    $update->execute([$title, $date, $time_start, $time_end, $max, $desc, $vote, $id]);

    $_SESSION['admin_message'] = "✏️ Event bearbeitet!";
header("Location: admin_dashboard.php");
exit;

}
?>
<?php include 'flatpickr_include.php'; ?>

<h2>✏️ Termin bearbeiten</h2>
<form method="POST">
  <label>Titel:</label><br>
  <input type="text" name="title" value="<?= htmlspecialchars($event['title']) ?>"><br><br>

  <label>Datum:</label><br>
  <input type="text" name="date" id="date" value="<?= $event['date'] ?>"><br><br>

  <label>Startzeit:</label><br>
  <input type="text" name="time_start" id="time_start" value="<?= $event['time_start'] ?>"><br><br>

  <label>Endzeit:</label><br>
  <input type="text" name="time_end" id="time_end" value="<?= $event['time_end'] ?>"><br><br>

  <label>Max. Teilnehmer:</label><br>
  <input type="number" name="max_participants" value="<?= $event['max_participants'] ?>"><br><br>

  <label>Setting-Beschreibung:</label><br>
  <textarea name="setting_description" rows="4"><?= htmlspecialchars($event['setting_description']) ?></textarea><br><br>

  <label>
    <input type="checkbox" name="is_votable" value="1" <?= $event['is_votable'] ? "checked" : "" ?>> Setting-Wahl aktiv
  </label><br><br>

  <input type="submit" value="Speichern">
</form>
