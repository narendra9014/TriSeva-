<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized session access']);
    exit();
}

require_once 'db.php';

$rawInput = file_get_contents('php://input');
$data     = json_decode($rawInput, true);

if (!$data) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid JSON payload']);
    exit();
}

$userId      = $_SESSION['user_id'];
$fullname    = filter_var($data['fullname'] ?? $data['name'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
$username    = filter_var($data['username'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);
$bloodGroup  = $data['bloodGroup'] ?? 'O+';
$isDonor     = !empty($data['isDonor']) ? 1 : 0;
$isAvailable = !empty($data['isAvailable']) ? 1 : 0;
$avatar      = $data['avatar'] ?? null;

try {
    $sql = "UPDATE users 
            SET fullname = ?, username = ?, blood_group = ?, is_donor = ?, is_available = ?, avatar = ? 
            WHERE id = ?";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$fullname, $username, $bloodGroup, $isDonor, $isAvailable, $avatar, $userId]);

    // Keep active session in sync
    $_SESSION['fullname'] = $fullname;

    echo json_encode(['success' => true, 'message' => 'Profile updated successfully']);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>