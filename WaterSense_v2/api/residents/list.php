<?php
require_once "../config/db.php";
header("Content-Type: application/json");

$search = $_GET['search'] ?? '';
$location = $_GET['location'] ?? 'all';
$alert = $_GET['alert'] ?? 'all';

$sql = "SELECT * FROM residents WHERE 1";
$params = [];

if ($search !== '') {
    $sql .= " AND (full_name LIKE ? OR email LIKE ? OR barangay LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if ($location !== 'all') {
    $sql .= " AND barangay = ?";
    $params[] = $location;
}

if ($alert === 'enabled') {
    $sql .= " AND (email_alerts = 1 OR sms_alerts = 1)";
} elseif ($alert === 'disabled') {
    $sql .= " AND email_alerts = 0 AND sms_alerts = 0";
} elseif ($alert === 'urgent') {
    $sql .= " AND urgent_contact = 1";
}

$sql .= " ORDER BY created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);

echo json_encode($stmt->fetchAll());
