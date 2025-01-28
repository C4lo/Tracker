<?php
require_once '../config/config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $stmt = $conn->prepare("INSERT INTO characters (name, class, race, level, strength, dexterity, constitution, intelligence, wisdom, charisma, background, alignment) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssiiiiiiiss", 
        $_POST['name'], $_POST['class'], $_POST['race'], (int)$_POST['level'], (int)$_POST['strength'], 
        (int)$_POST['dexterity'], (int)$_POST['constitution'], (int)$_POST['intelligence'], 
        (int)$_POST['wisdom'], (int)$_POST['charisma'], $_POST['background'], $_POST['alignment']
    );

    if ($stmt->execute()) {
        echo "Charakter erfolgreich hinzugefügt!";
    } else {
        echo "Fehler: " . $stmt->error;
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Charakter erstellen</title>
</head>
<body>
    <h1>Neuen Charakter erstellen</h1>
    <form method="POST">
        <!-- Felder wie Name, Klasse, Rasse, etc. -->
        <label>Name:</label> <input type="text" name="name" required><br>
        <label>Klasse:</label> <input type="text" name="class" required><br>
        <label>Rasse:</label> <input type="text" name="race" required><br>
        <label>Level:</label> <input type="number" name="level" min="1" max="20" required><br>
        <label>Stärke:</label> <input type="number" name="strength" required><br>
        <label>Geschicklichkeit:</label> <input type="number" name="dexterity" required><br>
        <label>Konstitution:</label> <input type="number" name="constitution" required><br>
        <label>Intelligenz:</label> <input type="number" name="intelligence" required><br>
        <label>Weisheit:</label> <input type="number" name="wisdom" required><br>
        <label>Charisma:</label> <input type="number" name="charisma" required><br>
        <label>Hintergrund:</label> <input type="text" name="background"><br>
        <label>Ausrichtung:</label> <input type="text" name="alignment"><br>
        <button type="submit">Speichern</button>
    </form>
</body>
</html>
