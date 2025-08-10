<?php
session_start();
require_once 'db_connection.php';

// Alle kommenden Events laden
$sql = "SELECT e.*, 
        (SELECT COUNT(*) FROM calendar_registrations r WHERE r.event_id = e.id) AS current_count
        FROM calendar_events e
        WHERE e.date >= CURDATE()
        ORDER BY e.date, e.time_start";

$stmt = $pdo->query($sql);
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h2>🗓️ Nächste Spieltermine</h2>
<?php if (isset($_SESSION['registration_error'])): ?>
  <div style="padding:1em; background:#ffe1e1; border:1px solid #c44; margin-bottom:1em;">
    ❌ <?= htmlspecialchars($_SESSION['registration_error']) ?>
  </div>
  <?php unset($_SESSION['registration_error']); ?>
<?php elseif (isset($_SESSION['last_registered_name'])): ?>
  <div style="padding:1em; background:#e1ffe1; border:1px solid #4c4; margin-bottom:1em;">
    ✅ Du hast dich erfolgreich angemeldet als: <?= htmlspecialchars($_SESSION['last_registered_name']) ?>
    <?php if (!empty($_SESSION['last_registered_vote'])): ?>
      – Wahl: <em><?= htmlspecialchars($_SESSION['last_registered_vote']) ?></em>
    <?php endif; ?>
  </div>
  <?php unset($_SESSION['last_registered_name'], $_SESSION['last_registered_vote']); ?>
<?php endif; ?>

<?php foreach ($events as $event): ?>
<?php
$settingClass = '';
if (!empty($event['setting_option'])) {
    $key = strtolower(str_replace([' ', '&', 'ä', 'ö', 'ü', 'ß'], ['-', 'und', 'ae', 'oe', 'ue', 'ss'], $event['setting_option']));
    $settingClass = 'event-' . $key;
}
?>
  <div class="event-card <?= $settingClass ?>">
    <strong><?= htmlspecialchars($event['title']) ?></strong><br>
    📅 <?= date("d.m.Y", strtotime($event['date'])) ?> – 🕒 <?= substr($event['time_start'], 0, 5) ?> bis <?= substr($event['time_end'], 0, 5) ?>
<br>
    👥 <?= $event['current_count'] ?> / <?= $event['max_participants'] ?> Teilnehmer<br>

    <?php if ($event['setting_description']): ?>
      🧭 Setting: <?= nl2br(htmlspecialchars($event['setting_description'])) ?><br>
    <?php endif; ?>

    <?php if ((int)$event['current_count'] < (int)$event['max_participants']): ?>
      <form method="POST" action="calendar_register.php">
        <input type="hidden" name="event_id" value="<?= $event['id'] ?>">
        <label>Dein (Spitz)Name (optional):</label><br>
        <input type="text" name="nickname" maxlength="255"><br><br>

        <?php if ($event['is_votable']): ?>
  <label>Was möchtest du spielen?</label><br>
  <select name="setting_option" required>
    <option value="">Bitte wählen</option>
    <option value="D&D">D&D</option>
    <option value="Star Wars">Star Wars</option>
    <option value="Spieleabend">Spieleabend</option>
    <option value="egal">Ist mir egal</option>
  </select><br><br>
  
  <label>Hast du noch eine Info für mich?</label><br>
<textarea name="note" placeholder="z. B. Wunschcharakter, Snack, Bemerkung …" rows="2" cols="40"></textarea><br><br>

<?php endif; ?>

        <input type="submit" value="Anmelden">
      </form>
    <?php else: ?>
      <strong style="color:red;">Dieser Slot ist voll.</strong>
    <?php endif; ?>
  </div>

<?php endforeach; ?>

<link rel="stylesheet" href="style.css">
