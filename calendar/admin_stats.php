<?php
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.php");
    exit;
}

require_once 'db_connection.php';
require_once 'admin_header.php';

// 1. Gesamtzahl Events
$total_events = $pdo->query("SELECT COUNT(*) FROM calendar_events")->fetchColumn();

// 2. Gesamtanzahl Registrierungen
$total_registrations = $pdo->query("SELECT COUNT(*) FROM calendar_registrations")->fetchColumn();

// 3. Top 10 Spielernamen (auch anonym möglich)
$top_players_stmt = $pdo->query("
    SELECT 
        CASE WHEN nickname IS NULL OR nickname = '' THEN 'Anonymer Spieler' ELSE nickname END AS nickname, 
        COUNT(*) AS count
    FROM calendar_registrations
    GROUP BY nickname
    ORDER BY count DESC
    LIMIT 10
");
$top_players = $top_players_stmt->fetchAll(PDO::FETCH_ASSOC);

// 4. Verteilung der Stimmen
$votes_stmt = $pdo->query("
    SELECT setting_option, COUNT(*) AS count
    FROM calendar_setting_votes
    GROUP BY setting_option
    ORDER BY count DESC
");
$votes = $votes_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<h2>📊 Statistikübersicht</h2>

<p><strong>Gesamtzahl an Events:</strong> <?= $total_events ?></p>
<p><strong>Gesamtzahl an Anmeldungen:</strong> <?= $total_registrations ?></p>

<h3>👤 Top 10 Spieler (nach Anmeldungen):</h3>
<ul>
<?php foreach ($top_players as $player): ?>
  <li><?= htmlspecialchars($player['nickname']) ?> – <?= $player['count'] ?> Anmeldungen</li>
<?php endforeach; ?>
</ul>

<h3>🗳️ Verteilung der Stimmen:</h3>
<ul>
<?php if (count($votes) === 0): ?>
  <li><em>Noch keine Stimmen abgegeben.</em></li>
<?php else: ?>
  <?php foreach ($votes as $vote): ?>
    <li><?= htmlspecialchars($vote['setting_option']) ?> – <?= $vote['count'] ?> Stimmen</li>
  <?php endforeach; ?>
<?php endif; ?>
</ul>

<!-- Chart.js einbinden -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<h3>📊 Diagramme</h3>
<div style="max-width: 600px; margin: 0 auto;">
  <canvas id="playerChart"></canvas>
</div>

<div style="max-width: 400px; margin: 3em auto 0;">
  <canvas id="voteChart"></canvas>
</div>


<script>
// Spieler-Daten aus PHP holen
const playerLabels = <?= json_encode(array_column($top_players, 'nickname')) ?>;
const playerData = <?= json_encode(array_column($top_players, 'count')) ?>;

// Stimmen-Daten aus PHP holen
const voteLabels = <?= json_encode(array_column($votes, 'setting_option')) ?>;
const voteData = <?= json_encode(array_column($votes, 'count')) ?>;

// 📊 Balkendiagramm: Top Spieler
new Chart(document.getElementById('playerChart'), {
  type: 'bar',
  data: {
    labels: playerLabels,
    datasets: [{
      label: 'Anmeldungen',
      data: playerData,
      backgroundColor: 'rgba(54, 162, 235, 0.6)',
      borderColor: 'rgba(54, 162, 235, 1)',
      borderWidth: 1
    }]
  },
  options: {
    scales: {
      y: { beginAtZero: true }
    }
  }
});

// 🥧 Tortendiagramm: Stimmenverteilung
new Chart(document.getElementById('voteChart'), {
  type: 'pie',
  data: {
    labels: voteLabels,
    datasets: [{
      label: 'Stimmen',
      data: voteData,
      backgroundColor: [
        'rgba(255, 99, 132, 0.6)',
        'rgba(255, 205, 86, 0.6)',
        'rgba(75, 192, 192, 0.6)',
        'rgba(153, 102, 255, 0.6)'
      ]
    }]
  }
});
</script>

