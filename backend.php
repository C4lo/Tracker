<?php
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, DELETE");
header("Access-Control-Allow-Headers: Content-Type");

// Datenbankverbindung
$host = "ht7m.your-database.de";  
$user = "jerome_tracker_w";  
$pass = "KTMf57rhQ17d9zjd";  
$dbname = "initiative_tracker"; 

$pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
]);

$method = $_SERVER["REQUEST_METHOD"];

if ($method === "GET") {
    // Einträge abrufen
    $mode = $_GET["mode"] ?? "session";
    $fight_name = $_GET["fight_name"] ?? null;

    $query = "SELECT * FROM initiative";
    $params = [];

    if ($mode === "db" && $fight_name) {
        $query .= " WHERE fight_name = ?";
        $params[] = $fight_name;
    }
    
    $stmt = $pdo->prepare($query);
    $stmt = $pdo->prepare("SELECT * FROM initiative ORDER BY initiative DESC");
    $stmt->execute($params);
    $entries = $stmt->fetchAll();

    echo json_encode(["success" => true, "entries" => $entries]);
    exit;
}

if ($method === "POST") {
    // Eingabedaten validieren
    $input = json_decode(file_get_contents("php://input"), true);

    if (!isset($input["name"], $input["initiative"], $input["hp"], $input["type"])) {
        echo json_encode(["success" => false, "message" => "Ungültige Daten"]);
        exit;
    }

    // Einfügen in die Datenbank
    try {
        $stmt = $pdo->prepare("INSERT INTO initiative (name, initiative, hp, type, fight_mode, fight_name,party_id,user_id) 
                              VALUES (?, ?, ?, ?, ?, ?,?,?)");
        $stmt->execute([
            $input["name"],
            $input["initiative"],
            $input["hp"],
            $input["type"],
            $input["fight_mode"],
            $input["fight_name"]?? null,
            $input["party_id"] ?? null,
            $input["user_id"]??null
        ]);

        echo json_encode(["success" => true]);
    } catch (Exception $e) {
        echo json_encode(["success" => false, "message" => $e->getMessage()]);
    }
    exit;
}

if ($method === "DELETE") {
    $input = json_decode(file_get_contents("php://input"), true);

    if (!isset($input["id"])) {
        echo json_encode(["success" => false, "message" => "Kein ID angegeben"]);
        exit;
    }

    try {
        $stmt = $pdo->prepare("DELETE FROM initiative WHERE id = ?");
        $stmt->execute([$input["id"]]);

        echo json_encode(["success" => true]);
    } catch (Exception $e) {
        echo json_encode(["success" => false, "message" => $e->getMessage()]);
    }
    exit;
}
?>
