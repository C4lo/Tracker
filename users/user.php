<?php
header("Content-Type: application/json; charset=UTF-8");
error_reporting(E_ALL);
ini_set('display_errors', 1);

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST");
header("Access-Control-Allow-Headers: Content-Type");

$host = "ht7m.your-database.de";
$user = "jerome_tracker_w";
$pass = "KTMf57rhQ17d9zjd";
$dbname = "initiative_tracker";

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die(json_encode(["success" => false, "message" => "Datenbankverbindung fehlgeschlagen"]));
}

$action = $_GET["action"] ?? "";

// **1. Registrierung**
if ($action == "register") {
    $data = json_decode(file_get_contents("php://input"), true);
    $username = $data["username"];
    $password = password_hash($data["password"], PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO users (username, password_hash) VALUES (?, ?)");
    $stmt->bind_param("ss", $username, $password);
    
    if ($stmt->execute()) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false, "message" => "Fehler bei der Registrierung"]);
    }
    $stmt->close();
}

// **2. Login**
elseif ($action == "login") {
    $data = json_decode(file_get_contents("php://input"), true);
    $username = $data["username"];
    $password = $data["password"];

    $stmt = $conn->prepare("SELECT id, username, password_hash FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();

    if ($result && password_verify($password, $result["password_hash"])) {
        echo json_encode(["success" => true, "user" => ["id" => $result["id"], "username" => $result["username"]]]);
    } else {
        echo json_encode(["success" => false, "message" => "Login fehlgeschlagen"]);
    }
    $stmt->close();
}

$conn->close();
?>
