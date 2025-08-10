<?php
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true): ?>
  <div style="background:#f0f0f0; border-bottom:1px solid #ccc; padding:0.5em 1em; font-family:sans-serif; display:flex; justify-content:space-between; align-items:center;">
    
    <div>
      <span style="font-weight:bold; color:#333;">✅ Eingeloggt als Admin</span>
    </div>

    <div style="display:flex; gap:1em;">
      <a href="admin_dashboard.php" style="text-decoration:none;">📋 Dashboard</a>
      <a href="admin_create_event.php" style="text-decoration:none;">➕ Neuer Termin</a>
      <a href="admin_stats.php">📈 Statistik</a>
      <a href="admin_logout.php" style="text-decoration:none; color:#c00;">🚪 Logout</a>
    </div>

  </div>
<?php endif; ?>
