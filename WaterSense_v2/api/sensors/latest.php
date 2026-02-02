<?php
header('Content-Type: application/json');

require_once __DIR__ . '/../config/db.php';

try {
    $stmt = $pdo->query("
        SELECT 
            l.id AS location_id,
            l.name AS location_name,
            wl.water_level,
            wl.status,
            wl.created_at,
            wl.battery
        FROM water_levels wl
        JOIN locations l ON wl.location_id = l.id
        WHERE wl.id IN (
            SELECT MAX(id)
            FROM water_levels
            GROUP BY location_id
        )
        ORDER BY wl.created_at DESC
    ");

    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($data);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "error" => "Database query failed",
        "details" => $e->getMessage()
    ]);
}
