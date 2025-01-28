<?php
require_once '../config/config.php';

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $stmt = $conn->prepare("SELECT * FROM characters WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $character = $result->fetch_assoc();
    $stmt->close();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $stmt = $conn->prepare("UPDATE characters SET name = ?, class = ?, race = ?, level = ? WHERE id = ?");
    $stmt->bind_param("sssii", $_POST['name'], $_POST['class'], $_POST['race'], (int)$_POST['level'], (int)$_POST['id']);
    $stmt->execute();
    header("Location: read.php");
}
?>

<form method="POST">
    <input type="hidden" name="id" value="<?php echo $character['id']; ?>">
    <label>Name:</label> <input type="text" name="name" value="<?php echo $character['name']; ?>"><br>
    <label>Klasse:</label> <input type="text" name="class" value="<?php echo $character['class']; ?>"><br>
    <label>Rasse:</label> <input type="text" name="race" value="<?php echo $character['race']; ?>"><br>
    <label>Level:</label> <input type="number" name="level" value="<?php echo $character['level']; ?>"><br>
    <button type="submit">Speichern</button>
</form>
