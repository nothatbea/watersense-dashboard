<?php
require_once "../config/db.php";
header("Content-Type: application/json");

$data = json_decode(file_get_contents("php://input"), true);

$stmt = $pdo->prepare("
    UPDATE users
    SET name = ?, username = ?, email = ?, role = ?, status = ?
    WHERE id = ?
");

$stmt->execute([
    $data["name"],
    $data["username"],
    $data["email"],
    $data["role"],
    $data["status"],
    $data["id"]
]);

echo json_encode(["success" => true]);
