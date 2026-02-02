<?php
require_once "../config/db.php";
header("Content-Type: application/json");

$id = $_POST["id"] ?? null;

if (!$id) {
    http_response_code(400);
    echo json_encode(["error" => "Missing user ID"]);
    exit;
}

$stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
$stmt->execute([$id]);

echo json_encode(["success" => true]);
