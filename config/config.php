<?php
$servername = "localhost";
$username = "root";
$password = ""; // XAMPP/WAMP: Standard ist leer
$dbname = "dnd_database";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Verbindung fehlgeschlagen: " . $conn->connect_error);
}
?>
