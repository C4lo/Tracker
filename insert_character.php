<?php
// Verbindung zur Datenbank herstellen
$servername = "localhost";
$username = "root";
$password = ""; // Passwort für deinen MySQL-Benutzer (leer für XAMPP/WAMP)
$dbname = "dnd_database";

$conn = new mysqli($servername, $username, $password, $dbname);

// Verbindung prüfen
if ($conn->connect_error) {
    die("Verbindung fehlgeschlagen: " . $conn->connect_error);
}

// Eingabewerte aus dem Formular abrufen
$name = $_POST['name'];
$class = $_POST['class'];
$race = $_POST['race'];
$level = (int)$_POST['level'];
$strength = (int)$_POST['strength'];
$dexterity = (int)$_POST['dexterity'];
$constitution = (int)$_POST['constitution'];
$intelligence = (int)$_POST['intelligence'];
$wisdom = (int)$_POST['wisdom'];
$charisma = (int)$_POST['charisma'];
$background = $_POST['background'];
$alignment = $_POST['alignment'];

// Prepared Statement erstellen
$stmt = $conn->prepare("INSERT INTO characters 
    (name, class, race, level, strength, dexterity, constitution, intelligence, wisdom, charisma, background, alignment) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

// Werte an das Prepared Statement binden
$stmt->bind_param("sssiiiiiiiss", 
    $name, $class, $race, $level, $strength, $dexterity, 
    $constitution, $intelligence, $wisdom, $charisma, $background, $alignment
);

// Prepared Statement ausführen
if ($stmt->execute()) {
    echo "Charakter erfolgreich hinzugefügt!";
} else {
    echo "Fehler beim Hinzufügen: " . $stmt->error;
}

// Prepared Statement und Verbindung schließen
$stmt->close();
$conn->close();
?>