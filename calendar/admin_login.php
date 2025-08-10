<?php
session_start();

// === Zugangsdaten ===
$USERNAME = 'admin';
$PASSWORD = 'meinSuperPasswort'; // → kannst du später in Hash umwandeln

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $enteredUser = $_POST['username'];
    $enteredPass = $_POST['password'];

    if ($enteredUser === $USERNAME && $enteredPass === $PASSWORD) {
        $_SESSION['admin_logged_in'] = true;
        header("Location: admin_dashboard.php");
        exit;
    } else {
        $error = "❌ Falscher Benutzername oder Passwort.";
    }
}
echo "Login verarbeitet.";
?>

<h2>🔐 Admin-Login</h2>
<?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>

<form method="POST">
  <label>Benutzername:</label><br>
  <input type="text" name="username"><br><br>

  <label>Passwort:</label><br>
  <input type="password" name="password"><br><br>

  <input type="submit" value="Login">
</form>
