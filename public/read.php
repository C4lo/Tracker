<?php
require_once '../config/config.php';

$result = $conn->query("SELECT * FROM characters");
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Charaktere anzeigen</title>
</head>
<body>
    <h1>Charaktere</h1>
    <table border="1">
        <tr>
            <th>Name</th>
            <th>Klasse</th>
            <th>Rasse</th>
            <th>Level</th>
            <th>Aktionen</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?php echo $row['name']; ?></td>
            <td><?php echo $row['class']; ?></td>
            <td><?php echo $row['race']; ?></td>
            <td><?php echo $row['level']; ?></td>
            <td>
                <a href="update.php?id=<?php echo $row['id']; ?>">Bearbeiten</a> |
                <a href="delete.php?id=<?php echo $row['id']; ?>">Löschen</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>
