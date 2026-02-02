<?php
require_once "../config/db.php";
header("Content-Type: application/json");

$data = json_decode(file_get_contents("php://input"), true);

if (
    empty($data["name"]) ||
    empty($data["username"]) ||
    empty($data["email"]) ||
    empty($data["role"]) ||
    empty($data["status"])
) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Missing fields"]);
    exit;
}

$stmt = $pdo->prepare("
    INSERT INTO users (username, role, name, email, status)
    VALUES (?, ?, ?, ?, ?)
");

$stmt->execute([
    $data["username"],
    $data["role"],
    $data["name"],
    $data["email"],
    $data["status"]
]);

echo json_encode(["success" => true]);
