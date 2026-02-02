<?php
require_once "../config/db.php";
header("Content-Type: application/json");

$stmt = $pdo->prepare("
    SELECT
        full_name,
        barangay,
        registration_date
    FROM residents
    ORDER BY registration_date DESC
    LIMIT 5
");

$stmt->execute();
$rows = $stmt->fetchAll();

echo json_encode($rows);
