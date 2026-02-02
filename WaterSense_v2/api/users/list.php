<?php
require_once "../config/db.php";
header("Content-Type: application/json");

$stmt = $pdo->prepare("
    SELECT id, name, email, role, status
    FROM users
    ORDER BY name ASC
");
$stmt->execute();

$users = $stmt->fetchAll();

echo json_encode($users);
