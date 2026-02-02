<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once '../config/db.php';

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit();
}

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);

// Validate input
if (!isset($input['phone_number']) || empty($input['phone_number'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Phone number is required']);
    exit();
}

$phoneNumber = trim($input['phone_number']);

// Validate Philippine mobile number format (11 digits starting with 09)
if (!preg_match('/^09\d{9}$/', $phoneNumber)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid Philippine mobile number format. Must be 11 digits starting with 09.']);
    exit();
}

// Optional: Get location from input (defaults to Barangay Lingga)
$location = isset($input['location']) ? trim($input['location']) : 'Barangay Lingga';

try {
    // Check if phone number already exists
    $checkStmt = $pdo->prepare("SELECT id, is_active FROM sms_subscribers WHERE phone_number = ?");
    $checkStmt->execute([$phoneNumber]);
    $existing = $checkStmt->fetch(PDO::FETCH_ASSOC);
    
    if ($existing) {
        if ($existing['is_active']) {
            // Already subscribed and active
            echo json_encode([
                'success' => true,
                'message' => 'This number is already subscribed to alerts.',
                'already_subscribed' => true
            ]);
        } else {
            // Reactivate existing subscription
            $updateStmt = $pdo->prepare("UPDATE sms_subscribers SET is_active = 1, updated_at = NOW() WHERE id = ?");
            $updateStmt->execute([$existing['id']]);
            
            echo json_encode([
                'success' => true,
                'message' => 'Your subscription has been reactivated successfully!',
                'reactivated' => true
            ]);
        }
    } else {
        // Insert new subscriber
        $insertStmt = $pdo->prepare("
            INSERT INTO sms_subscribers (phone_number, location, is_active, created_at, updated_at) 
            VALUES (?, ?, 1, NOW(), NOW())
        ");
        $insertStmt->execute([$phoneNumber, $location]);
        
        echo json_encode([
            'success' => true,
            'message' => 'Successfully subscribed! You will receive SMS alerts when water levels reach warning thresholds.',
            'subscriber_id' => $pdo->lastInsertId()
        ]);
    }
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Database error occurred. Please try again later.',
        'error' => $e->getMessage()
    ]);
}
?>