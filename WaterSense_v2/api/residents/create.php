<?php
require_once "../config/db.php";
header("Content-Type: application/json");

$data = $_POST;

if (
    empty($data["full_name"]) ||
    empty($data["phone"]) ||
    empty($data["barangay"]) ||
    empty($data["registration_date"])
) {
    echo json_encode([
        "success" => false,
        "message" => "Required fields are missing"
    ]);
    exit;
}

$stmt = $pdo->query("SELECT COUNT(*) FROM residents");
$count = (int)$stmt->fetchColumn() + 1;
$residentId = "RES-" . str_pad($count, 3, "0", STR_PAD_LEFT);

$stmt = $pdo->prepare("
    INSERT INTO residents (
        resident_id,
        full_name,
        email,
        phone,
        barangay,
        address,
        email_alerts,
        sms_alerts,
        urgent_contact,
        household_size,
        registration_date,
        notes
    ) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)
");

$stmt->execute([
    $residentId,
    $data["full_name"],
    $data["email"] ?: null,
    $data["phone"],
    $data["barangay"],
    $data["address"] ?: null,
    isset($data["email_alerts"]) ? 1 : 0,
    isset($data["sms_alerts"]) ? 1 : 0,
    isset($data["urgent_contact"]) ? 1 : 0,
    $data["household_size"] ?: null,
    $data["registration_date"],
    $data["notes"] ?: null
]);

echo json_encode([
    "success" => true,
    "message" => "Resident added successfully"
]);
