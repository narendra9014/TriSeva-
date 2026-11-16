<?php
session_start();
header('Content-Type: application/json');

require_once 'db.php';

$rawInput = file_get_contents("php://input");
$data = json_decode($rawInput, true);

if (!$data || empty($data['email'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid email or missing payload']);
    exit();
}

$fullname = trim($data['name'] ?? '');
$email    = filter_var(trim($data['email']), FILTER_SANITIZE_EMAIL);
$avatar   = $data['photo'] ?? null;
$phone    = "";
$password = password_hash(bin2hex(random_bytes(16)), PASSWORD_DEFAULT);

try {
    // 1. Check if user already exists
    $stmt = $pdo->prepare("SELECT id, fullname, email, avatar FROM users WHERE email = :email LIMIT 1");
    $stmt->execute([':email' => $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        // 2. Register new user
        $stmt = $pdo->prepare("
            INSERT INTO users (fullname, email, phone, password, avatar)
            VALUES (:fullname, :email, :phone, :password, :avatar)
        ");

        $stmt->execute([
            ':fullname' => $fullname,
            ':email'    => $email,
            ':phone'    => $phone,
            ':password' => $password,
            ':avatar'   => $avatar
        ]);

        $userId = $pdo->lastInsertId();
    } else {
        $userId   = $user['id'];
        $fullname = $user['fullname'];
    }

    // 3. Set Session securely
    session_regenerate_id(true);

    $_SESSION['user_id']  = $userId;
    $_SESSION['fullname'] = $fullname;
    $_SESSION['email']    = $email;

    echo json_encode(['success' => true, 'user_id' => $userId]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>