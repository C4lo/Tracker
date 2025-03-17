<?php
ob_start(); // Fängt unerwartete Ausgaben ab
header("Content-Type: application/json; charset=UTF-8");
error_reporting(0); // Verhindert PHP-Warnungen
ini_set('display_errors', 0);

// Header für CORS (wenn du API-Aufrufe von anderen Domains zulassen willst)
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, DELETE");
header("Access-Control-Allow-Headers: Content-Type");

// Datenbankverbindung
$host = "ht7m.your-database.de";  
$user = "jerome_tracker_w";  
$pass = "KTMf57rhQ17d9zjd";  
$dbname = "initiative_tracker";  

$conn = new mysqli($host, $user, $pass, $dbname);

// Prüfe die Verbindung
if ($conn->connect_error) {
    die(json_encode(["success" => false, "message" => "Datenbankverbindung fehlgeschlagen: " . $conn->connect_error]));
}

// GET: Alle Einträge abrufen
if ($_SERVER["REQUEST_METHOD"] === "GET") {
    $result = $conn->query("SELECT * FROM initiative ORDER BY initiative DESC");
    $entries = [];

    while ($row = $result->fetch_assoc()) {
        $entries[] = $row;
    }

    // Stelle sicher, dass keine zusätzlichen Zeichen ausgegeben werden
    ob_end_clean();
    echo json_encode(["success" => true, "entries" => $entries]);
    exit;
}

// POST: Neuen Eintrag hinzufügen
elseif ($_SERVER["REQUEST_METHOD"] === "POST") {
    $data = json_decode(file_get_contents("php://input"), true);

    if (!isset($data["name"], $data["initiative"], $data["hp"], $data["type"])) {
        ob_end_clean();
        echo json_encode(["success" => false, "message" => "Ungültige Daten"]);
        exit;
    }

    $stmt = $conn->prepare("INSERT INTO initiative (name, initiative, hp, type) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("siss", $data["name"], $data["initiative"], $data["hp"], $data["type"]);

    ob_end_clean();
    if ($stmt->execute()) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false, "message" => "Fehler beim Speichern: " . $stmt->error]);
    }
    $stmt->close();
    exit;
}

// DELETE: Eintrag entfernen
elseif ($_SERVER["REQUEST_METHOD"] === "DELETE") {
    $data = json_decode(file_get_contents("php://input"), true);

    if (!isset($data["id"])) {
        ob_end_clean();
        echo json_encode(["success" => false, "message" => "Kein ID angegeben"]);
        exit;
    }

    $stmt = $conn->prepare("DELETE FROM initiative WHERE id = ?");
    $stmt->bind_param("i", $data["id"]);

    ob_end_clean();
    if ($stmt->execute()) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false, "message" => "Fehler beim Löschen: " . $stmt->error]);
    }
    $stmt->close();
    exit;
}

$conn->close();
?>
