<?php
// Enable full error output for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
include("db.php");

// ------------------------------------------
// CONFIGURATION & CONSTANTS
// ------------------------------------------
define('BREVO_API_KEY', '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'); 
define('SENDER_NAME', 'TriSeva');
define('SENDER_EMAIL', 'subba4216@gmail.com'); 
define('ADMIN_EMAIL', 'subba4216@gmail.com');     

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    die("Unauthorized access: User session not found. Please log in again.");
}

if (!isset($conn) || $conn->connect_error) {
    die("Database Connection Error: " . ($conn->connect_error ?? "DB connection object missing."));
}

$user_id       = $_SESSION['user_id'];
$reporter_name = $_SESSION['fullname'] ?? $_SESSION['user_name'] ?? 'Registered User';
$location      = trim($_POST['location'] ?? 'N/A');
$landmark      = trim($_POST['landmark'] ?? 'N/A');
$waste_type    = trim($_POST['wasteType'] ?? 'N/A');
$severity      = trim($_POST['severity'] ?? 'N/A');
$description   = trim($_POST['description'] ?? 'None');

// ------------------------------------------
// 1. SAVE TO DATABASE
// ------------------------------------------
$sql = "INSERT INTO waste_reports (user_id, location, landmark, waste_type, severity, description, status, reported_date, reporter_name) VALUES (?, ?, ?, ?, ?, ?, 'pending', NOW(), ?)";
$stmt = $conn->prepare($sql);

if ($stmt) {
    $stmt->bind_param("isssssss", $user_id, $location, $landmark, $waste_type, $severity, $description, $reporter_name);
    
    if ($stmt->execute()) {
        $stmt->close();

        // ------------------------------------------
        // 2. SEND BREVO EMAIL NOTIFICATION
        // ------------------------------------------
        $emailSubject = "🚨 New Waste Report Submitted: {$location}";
        $emailBody = "
            <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; border: 1px solid #10b981; border-radius: 10px; padding: 20px;'>
                <h2 style='color: #047857; margin-top: 0;'>♻️ New Garbage Dump Reported</h2>
                <p>A new waste report has been submitted on the <strong>TriSeva Portal</strong>.</p>
                <hr style='border: 0; border-top: 1px solid #eee;' />
                <table style='width: 100%; font-size: 14px; line-height: 1.8;'>
                    <tr><td style='width:35%;'><strong>Reporter Name:</strong></td><td>{$reporter_name}</td></tr>
                    <tr><td><strong>User ID:</strong></td><td>{$user_id}</td></tr>
                    <tr><td><strong>Location Address:</strong></td><td>{$location}</td></tr>
                    <tr><td><strong>Nearby Landmark:</strong></td><td>{$landmark}</td></tr>
                    <tr><td><strong>Waste Category:</strong></td><td><strong style='text-transform: capitalize;'>{$waste_type}</strong></td></tr>
                    <tr><td><strong>Severity Level:</strong></td><td><strong style='color: #d32f2f; text-transform: uppercase;'>{$severity}</strong></td></tr>
                    <tr><td><strong>Description:</strong></td><td>{$description}</td></tr>
                    <tr><td><strong>Date Reported:</strong></td><td>" . date('Y-m-d H:i:s') . "</td></tr>
                </table>
            </div>
        ";

        sendBrevoEmail(ADMIN_EMAIL, "Admin", $emailSubject, $emailBody);

        echo "success";
    } else {
        echo "Execute Error: " . $stmt->error;
    }
} else {
    echo "Prepare Error: " . $conn->error;
}

$conn->close();

// ------------------------------------------
// 3. BREVO REST API HELPER FUNCTION
// ------------------------------------------
function sendBrevoEmail($toEmail, $toName, $subject, $htmlBody) {
    $url = 'https://api.brevo.com/v3/smtp/email';

    $payload = [
        "sender" => [
            "name"  => SENDER_NAME,
            "email" => SENDER_EMAIL
        ],
        "to" => [
            [
                "email" => $toEmail,
                "name"  => $toName
            ]
        ],
        "subject"     => $subject,
        "htmlContent" => $htmlBody
    ];

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_HTTPHEADER     => [
            'accept: application/json',
            'api-key: ' . BREVO_API_KEY,
            'content-type: application/json'
        ],
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => json_encode($payload),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYPEER => false
    ]);

    $response = curl_exec($ch);
    curl_close($ch);
    return $response;
}
?>