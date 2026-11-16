<?php
// send_notifications.php - DEBUG MODE
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'db.php';

if (!isset($_GET['request_id']) || empty($_GET['request_id'])) {
    die("<h3>Error: No Request ID provided in URL!</h3>");
}

$request_id = intval($_GET['request_id']);

// 1. Fetch Request Details
$sql = "SELECT * FROM blood_requests WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $request_id);
mysqli_stmt_execute($stmt);
$request = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
mysqli_stmt_close($stmt);

if (!$request) {
    die("<h3>Error: Request ID #{$request_id} not found in database!</h3>");
}

echo "<h4>1. Emergency Request Loaded:</h4>";
echo "Blood Group: <strong>" . htmlspecialchars($request['blood_group']) . "</strong> | City: <strong>" . htmlspecialchars($request['city']) . "</strong><br><hr>";

// 2. Query Donors Matching Blood Group and City
$donor_sql = "SELECT name, email FROM donors WHERE blood_group = ? AND city = ?";
$d_stmt = mysqli_prepare($conn, $donor_sql);
mysqli_stmt_bind_param($d_stmt, "ss", $request['blood_group'], $request['city']);
mysqli_stmt_execute($d_stmt);
$donors = mysqli_stmt_get_result($d_stmt);

$matching_donors_count = mysqli_num_rows($donors);
echo "<h4>2. Donor Matching Status:</h4>";
echo "Found <strong>{$matching_donors_count}</strong> donor(s) matching blood group '{$request['blood_group']}' in city '{$request['city']}'.<br><hr>";

if ($matching_donors_count == 0) {
    echo "<p style='color:red;'><strong>Reason for no email:</strong> There are no donors in your database table 'donors' with blood_group='{$request['blood_group']}' and city='{$request['city']}'. Add a test donor row with these exact values in phpMyAdmin!</p>";
    exit();
}

// Function to send email via Brevo REST API with debugging
function send_email_brevo_debug($to_email, $to_name, $subject, $html_content) {
    // -------------------------------------------------------------
    // PUT YOUR BREVO DETAILS HERE AGAIN:
    $api_key = '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'; 
    $sender_email = 'subba4216@gmail.com'; 
    // -------------------------------------------------------------

    $data = [
        'sender' => [
            'name' => 'TriSeva Emergency Support',
            'email' => $sender_email
        ],
        'to' => [
            ['email' => $to_email, 'name' => $to_name]
        ],
        'subject' => $subject,
        'htmlContent' => $html_content
    ];

    $ch = curl_init('https://api.brevo.com/v3/smtp/email');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    
    // FIX FOR HOSTING SSL ISSUES
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 

    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'accept: application/json',
        'api-key: ' . $api_key,
        'content-type: application/json'
    ]);

    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curl_error = curl_error($ch);
    curl_close($ch);

    echo "Attempting to send email to: <strong>{$to_email}</strong>...<br>";
    if (!empty($curl_error)) {
        echo "<span style='color:red;'>cURL Error: " . htmlspecialchars($curl_error) . "</span><br>";
    }
    echo "Brevo Response Code: <strong>{$http_code}</strong><br>";
    echo "Brevo Response Body: <code>" . htmlspecialchars($response) . "</code><br><br>";

    return ($http_code == 201 || $http_code == 200);
}

// 3. Loop through matching donors and send email
$emails_sent = 0;
while ($donor = mysqli_fetch_assoc($donors)) {
    if (!empty($donor['email'])) {
        $subject = "URGENT: Blood Needed ({$request['blood_group']}) in {$request['city']}";
        $html_content = "<p>Emergency blood request for {$request['patient_name']}. Phone: {$request['phone']}</p>";

        if (send_email_brevo_debug($donor['email'], $donor['name'], $subject, $html_content)) {
            $emails_sent++;
        }
    }
}

mysqli_stmt_close($d_stmt);
?>