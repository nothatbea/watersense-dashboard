<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once '../config/db.php';

try {
    // Get filter parameters
    $location = isset($_GET['location']) ? trim($_GET['location']) : '';
    $is_active = isset($_GET['is_active']) ? (int)$_GET['is_active'] : null;
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 100;
    $offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;
    
    // Build query
    $query = "SELECT * FROM sms_subscribers WHERE 1=1";
    $params = [];
    
    if (!empty($location)) {
        $query .= " AND location LIKE ?";
        $params[] = "%$location%";
    }
    
    if ($is_active !== null) {
        $query .= " AND is_active = ?";
        $params[] = $is_active;
    }
    
    $query .= " ORDER BY created_at DESC LIMIT ? OFFSET ?";
    $params[] = $limit;
    $params[] = $offset;
    
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $subscribers = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get total count
    $countQuery = "SELECT COUNT(*) as total FROM sms_subscribers WHERE 1=1";
    $countParams = [];
    
    if (!empty($location)) {
        $countQuery .= " AND location LIKE ?";
        $countParams[] = "%$location%";
    }
    
    if ($is_active !== null) {
        $countQuery .= " AND is_active = ?";
        $countParams[] = $is_active;
    }
    
    $countStmt = $pdo->prepare($countQuery);
    $countStmt->execute($countParams);
    $total = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    echo json_encode([
        'success' => true,
        'subscribers' => $subscribers,
        'total' => (int)$total,
        'limit' => $limit,
        'offset' => $offset
    ]);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database error occurred',
        'error' => $e->getMessage()
    ]);
}
?>