<?php
require_once "../config/db.php";
header("Content-Type: application/json");

$id = $_GET["id"] ?? null;

if (!$id) {
    http_response_code(400);
    echo json_encode(["error" => "Missing user ID"]);
    exit;
}

$stmt = $pdo->prepare("
    SELECT id, username, name, email, role, status
    FROM users
    WHERE id = ?
");
$stmt->execute([$id]);

$user = $stmt->fetch();

echo json_encode($user);
