<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.php");
    exit;
}

require_once 'db_connection.php';
require_once 'admin_header.php';
if (isset($_SESSION['admin_message'])) {
    echo "<div style='background:#e1ffe1; padding:1em; border:1px solid #4c4;'>"
       . htmlspecialchars($_SESSION['admin_message']) . "</div>";
    unset($_SESSION['admin_message']);
}
// Lade alle Events
$sql = "SELECT * FROM calendar_events ORDER BY date DESC, time_start";
$events_stmt = $pdo->query($sql);
$events = $events_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h2>🛠️ Adminübersicht – Geplante Termine & Anmeldungen</h2>

<?php foreach ($events as $event): ?>
  <div style="border:1px solid #aaa; padding:1em; margin-bottom:2em;">
    <strong><?= htmlspecialchars($event['title']) ?></strong><br>
    📅 <?= date("d.m.Y", strtotime($event['date'])) ?> – 🕒 <?= substr($event['time_start'], 0, 5) ?> bis <?= substr($event['time_end'], 0, 5) ?><br>
    👥 Max: <?= $event['max_participants'] ?><br>
    🧭 <?= nl2br(htmlspecialchars($event['setting_description'])) ?><br>
    📊 Abstimmung aktiv: <?= $event['is_votable'] ? "Ja" : "Nein" ?><br><br>

    🟩 <a href="admin_edit_event.php?id=<?= $event['id'] ?>">✏️ Bearbeiten</a> |
    🟩 <a href="admin_delete_event.php?id=<?= $event['id'] ?>" onclick="return confirm('Wirklich löschen?');">🗑️ Löschen</a><br><br>

    <strong>Teilnehmer:</strong><br>

    <?php
    $reg_stmt = $pdo->prepare("SELECT * FROM calendar_registrations WHERE event_id = ? ORDER BY timestamp");
    $reg_stmt->execute([$event['id']]);
    $registrations = $reg_stmt->fetchAll(PDO::FETCH_ASSOC);

    if (count($registrations) === 0): ?>
      <em>Noch keine Anmeldungen</em><br>
    <?php else: ?>
      <ul>
      <?php foreach ($registrations as $reg): ?>
        <li>
          <?= htmlspecialchars($reg['nickname'] ?: "Anonymer Spieler") ?> –
          <?= date("d.m.Y H:i", strtotime($reg['timestamp'])) ?>
<?php if (!empty($reg['note'])): ?>
  <br><em>💬 <?= nl2br(htmlspecialchars($reg['note'])) ?></em>
<?php endif; ?>
<form method="POST" action="admin_update_note.php" style="margin-top:0.5em;">
  <input type="hidden" name="id" value="<?= $reg['id'] ?>">
  <input type="text" name="note" value="<?= htmlspecialchars($reg['note']) ?>" placeholder="Anmerkung" style="width:80%;">
  <button type="submit" title="Speichern">💾</button>
</form>

          <?php if ($event['is_votable']): ?>
            <?php
            $vote_stmt = $pdo->prepare("SELECT setting_option FROM calendar_setting_votes WHERE registration_id = ?");
            $vote_stmt->execute([$reg['id']]);
            $vote = $vote_stmt->fetchColumn();
            if ($vote): ?>
              – 🗳️ <em><?= htmlspecialchars($vote) ?></em>
            <?php endif; ?>
          <?php endif; ?>
        </li>
      <?php endforeach; ?>
      </ul>
    <?php endif; ?>

    <?php if ($event['is_votable']): ?>
      <br><strong>Abstimmungsergebnisse:</strong><br>
      <?php
      $vote_sum_stmt = $pdo->prepare("
        SELECT setting_option, COUNT(*) as count 
        FROM calendar_setting_votes v
        JOIN calendar_registrations r ON r.id = v.registration_id
        WHERE r.event_id = ?
        GROUP BY setting_option
        ORDER BY count DESC
      ");
      $vote_sum_stmt->execute([$event['id']]);
      $votes = $vote_sum_stmt->fetchAll(PDO::FETCH_ASSOC);

      if ($votes):
        foreach ($votes as $vote): ?>
          <?= htmlspecialchars($vote['setting_option']) ?>: <?= $vote['count'] ?><br>
        <?php endforeach;
      else: ?>
        <em>Noch keine Stimmen</em>
      <?php endif; ?>
    <?php endif; ?>
  </div>
<?php endforeach; ?>

<a href="admin_logout.php">🚪 Logout</a>
