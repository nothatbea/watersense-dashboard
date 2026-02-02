<?php
session_start();
require_once "../config/db.php";
header("Content-Type: application/json");

$username = trim($_POST["username"] ?? "");
$password = $_POST["password"] ?? "";

/* Validation */
if ($username === "" || $password === "") {
    echo json_encode([
        "success" => false,
        "message" => "All fields are required"
    ]);
    exit;
}

$stmt = $pdo->prepare("
    SELECT id, username, password_hash, role
    FROM users
    WHERE username = ?
    LIMIT 1
");
$stmt->execute([$username]);
$user = $stmt->fetch();

/* User not found */
if (!$user) {
    echo json_encode([
        "success" => false,
        "message" => "Invalid username or password"
    ]);
    exit;
}

/* ❌ NO HASHING – PLAIN TEXT CHECK */
if ($password !== $user["password_hash"]) {
    echo json_encode([
        "success" => false,
        "message" => "Invalid username or password"
    ]);
    exit;
}

/* ✅ Login success */
$_SESSION["user_id"] = $user["id"];
$_SESSION["username"] = $user["username"];
$_SESSION["role"] = $user["role"];

echo json_encode([
    "success" => true,
    "role" => $user["role"]
]);
