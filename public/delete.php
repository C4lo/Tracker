<?php
require_once '../config/config.php';

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $stmt = $conn->prepare("DELETE FROM characters WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: read.php");
}
?>
