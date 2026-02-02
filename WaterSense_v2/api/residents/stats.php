<?php
require_once "../config/db.php";
header("Content-Type: application/json");

$stats = [];

/* Total residents */
$stats['total'] = $pdo->query("SELECT COUNT(*) FROM residents")->fetchColumn();

/* By barangay */
$stats['palingon'] = $pdo->query("SELECT COUNT(*) FROM residents WHERE barangay='palingon'")->fetchColumn();
$stats['lingga']   = $pdo->query("SELECT COUNT(*) FROM residents WHERE barangay='lingga'")->fetchColumn();

/* Alert stats */
$stats['alerts_enabled'] = $pdo->query("
    SELECT COUNT(*) FROM residents 
    WHERE email_alerts = 1 OR sms_alerts = 1
")->fetchColumn();

$stats['alerts_disabled'] = $pdo->query("
    SELECT COUNT(*) FROM residents 
    WHERE email_alerts = 0 AND sms_alerts = 0
")->fetchColumn();

$stats['urgent'] = $pdo->query("
    SELECT COUNT(*) FROM residents 
    WHERE urgent_contact = 1
")->fetchColumn();

/* Recent registrations */
$stmt = $pdo->query("
    SELECT full_name, barangay, registration_date
    FROM residents
    ORDER BY registration_date DESC
    LIMIT 5
");
$stats['recent'] = $stmt->fetchAll();

echo json_encode($stats);
