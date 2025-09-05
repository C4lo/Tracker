<?php
header("Content-Type: application/json; charset=UTF-8");
error_reporting(E_ALL);
ini_set('display_errors', 1);

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST");
header("Access-Control-Allow-Headers: Content-Type");

// Datenbankverbindung
$host = getenv('DB_HOST') ?: 'localhost';
$user = getenv('DB_USER') ?: 'root';
$pass = getenv('DB_PASS') ?: '';
$dbname = getenv('DB_NAME') ?: 'initiative_tracker';

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die(json_encode(["success" => false, "message" => "Datenbankverbindung fehlgeschlagen: " . $conn->connect_error]));
}

$action = $_GET["action"] ?? "";

// 🏗 **1. Neue Party erstellen**
if ($action == "create_party") {
    $data = json_decode(file_get_contents("php://input"), true);

    if (!isset($data["name"], $data["dm_id"])) {
        echo json_encode(["success" => false, "message" => "Fehlende Daten"]);
        exit;
    }

    $stmt = $conn->prepare("INSERT INTO parties (name) VALUES (?)");
    $stmt->bind_param("s", $data["name"]);

    if ($stmt->execute()) {
        $party_id = $stmt->insert_id;

        $stmt2 = $conn->prepare("INSERT INTO party_members (user_id, party_id, role) VALUES (?, ?, 'DM')");
        $stmt2->bind_param("ii", $data["dm_id"], $party_id);
        $stmt2->execute();
        $stmt2->close();

        echo json_encode(["success" => true, "party_id" => $party_id]);
    } else {
        echo json_encode(["success" => false, "message" => "Fehler beim Erstellen der Party: " . $stmt->error]);
    }
    $stmt->close();
}

// 👤 **2. Spieler einer Party hinzufügen**
elseif ($action == "add_user_to_party") {
    $data = json_decode(file_get_contents("php://input"), true);

    if (!isset($data["user_id"], $data["party_id"], $data["role"])) {
        echo json_encode(["success" => false, "message" => "Fehlende Daten"]);
        exit;
    }

    $stmt = $conn->prepare("INSERT INTO party_members (user_id, party_id, role) VALUES (?,?,?)");
    $stmt->bind_param("iis", $data["user_id"], $data["party_id"], $data["role"]);

    if ($stmt->execute()) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false, "message" => "Fehler beim Hinzufügen des Benutzers: " . $stmt->error]);
    }
    $stmt->close();
}

// 🎲 **3. Alle Parties eines Users abrufen**
elseif ($action == "get_party_members") {
    $user_id = $_GET["user_id"] ?? 0;

    if (!$user_id) {
        echo json_encode(["success" => false, "message" => "Fehlende User-ID"]);
        exit;
    }

    $stmt = $conn->prepare("SELECT p.id, p.name, pm.role FROM parties p JOIN party_members pm ON p.id = pm.party_id WHERE pm.user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $parties = [];
    while ($row = $result->fetch_assoc()) {
        $parties[] = $row;
    }
    echo json_encode(["success" => true, "parties" => $parties]);
}

$conn->close();
?>
