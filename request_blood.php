<?php
// ==========================================
// 1. CONFIGURATION & BREVO CREDENTIALS
// ==========================================
define('BREVO_API_KEY', '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'); // Paste your Brevo xkeysib-... key here
define('SENDER_EMAIL', 'subba4216@gmail.com');     // Must match your verified sender in Brevo
define('SENDER_NAME', 'triseva');

// Database Configuration (InfinityFree Credentials)
$db_host = "sql211.infinityfree.com"; 
$db_user = "if0_42196384";             
$db_pass = "trisevacse";                           // Replace with your InfinityFree account password
$db_name = "if0_42196384_triseva_db"; 

// ==========================================
// 2. PROCESS EMERGENCY REQUEST
// ==========================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Capture form values submitted from bloodsupport.php
    $patient_name    = $_POST['patient_name'] ?? 'N/A';
    $blood_group     = $_POST['blood_group'] ?? '';
    $units_needed    = $_POST['units_needed'] ?? '1';
    $hospital_name  = $_POST['hospital_name'] ?? 'N/A';
    $city            = trim($_POST['city'] ?? '');
    $contact_person  = $_POST['contact_person'] ?? 'N/A';
    $phone           = $_POST['phone'] ?? '';
    $urgency         = $_POST['urgency'] ?? 'Normal';
    $additional_info = $_POST['additional_info'] ?? 'None';

    // Connect to InfinityFree MySQL Database
    $conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

    if ($conn->connect_error) {
        die("Database Connection Failed: " . $conn->connect_error);
    }

    // --- FETCH MATCHING DONORS FROM DATABASE ---
    // Match blood group and city (ignoring letter casing & surrounding spaces)
    $query = "SELECT fullname, email, phone FROM donors WHERE bloodgroup = ? AND LOWER(TRIM(city)) = LOWER(TRIM(?))";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $blood_group, $city);
    $stmt->execute();
    $result = $stmt->get_result();

    $matching_donors = [];

    while ($row = $result->fetch_assoc()) {
        // Format phone number for SMS (Remove non-digits, strip leading 0, prepend 91 country code)
        $clean_phone = preg_replace('/[^0-9]/', '', $row['phone']);
        $clean_phone = ltrim($clean_phone, '0');
        if (strlen($clean_phone) === 10) {
            $clean_phone = '91' . $clean_phone;
        }

        $matching_donors[] = [
            'name'  => $row['fullname'],
            'email' => $row['email'],
            'phone' => $clean_phone
        ];
    }
    $stmt->close();

    // --- FALLBACK: Search for all donors with that blood group across ALL cities if none found in target city ---
    if (empty($matching_donors)) {
        $fallback_query = "SELECT fullname, email, phone FROM donors WHERE bloodgroup = ?";
        $stmt_fb = $conn->prepare($fallback_query);
        $stmt_fb->bind_param("s", $blood_group);
        $stmt_fb->execute();
        $fb_result = $stmt_fb->get_result();

        while ($row = $fb_result->fetch_assoc()) {
            $clean_phone = preg_replace('/[^0-9]/', '', $row['phone']);
            $clean_phone = ltrim($clean_phone, '0');
            if (strlen($clean_phone) === 10) {
                $clean_phone = '91' . $clean_phone;
            }

            $matching_donors[] = [
                'name'  => $row['fullname'],
                'email' => $row['email'],
                'phone' => $clean_phone
            ];
        }
        $stmt_fb->close();
    }

    $conn->close();

    // --- DISPATCH BREVO ALERTS TO EVERY MATCHED DONOR ---
    foreach ($matching_donors as $donor) {
        
        // Send Brevo Email Alert
        if (!empty($donor['email']) && filter_var($donor['email'], FILTER_VALIDATE_EMAIL)) {
            sendBrevoEmail(
                $donor['email'],
                $donor['name'],
                $patient_name,
                $blood_group,
                $units_needed,
                $hospital_name,
                $city,
                $contact_person,
                $phone,
                $urgency,
                $additional_info
            );
        }

        // Send Brevo SMS Alert (Requires Brevo SMS credits)
        if (!empty($donor['phone'])) {
            $sms_msg = "EMERGENCY: Urgent {$blood_group} blood ({$units_needed} units) needed at {$hospital_name}, {$city}. Contact: {$phone} ({$contact_person}). - TriSeva";
            sendBrevoSMS($donor['phone'], $sms_msg);
        }
    }

    // Respond back to JavaScript frontend
    echo "success";
    exit;
}

// ==========================================
// 3. BREVO API HELPER FUNCTIONS
// ==========================================

/**
 * Sends Transactional Email via Brevo REST API v3
 */
function sendBrevoEmail($toEmail, $toName, $patient, $bloodGroup, $units, $hospital, $city, $contact, $phone, $urgency, $notes) {
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
        "subject" => "🚨 CRITICAL ALERT: {$bloodGroup} Blood Needed in {$city}",
        "htmlContent" => "
            <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; border: 1px solid #e53935; border-radius: 10px; padding: 20px; background-color: #ffffff;'>
                <h2 style='color: #d32f2f; margin-top: 0;'>🚨 Emergency Blood Request</h2>
                <p>Hello <strong>{$toName}</strong>,</p>
                <p>An urgent blood request has been posted matching your blood group profile.</p>
                <hr style='border: 0; border-top: 1px solid #eee;' />
                <table style='width: 100%; font-size: 14px; line-height: 1.8;'>
                    <tr><td style='width:35%;'><strong>Patient Name:</strong></td><td>{$patient}</td></tr>
                    <tr><td><strong>Blood Group Needed:</strong></td><td><span style='color:#d32f2f; font-weight:bold; font-size: 16px;'>{$bloodGroup}</span> ({$units} Units)</td></tr>
                    <tr><td><strong>Urgency Level:</strong></td><td><strong style='color: #e53935;'>{$urgency}</strong></td></tr>
                    <tr><td><strong>Hospital:</strong></td><td>{$hospital}, {$city}</td></tr>
                    <tr><td><strong>Contact Person:</strong></td><td>{$contact}</td></tr>
                    <tr><td><strong>Phone Number:</strong></td><td><a href='tel:{$phone}' style='color: #059669; font-weight: bold;'>{$phone}</a></td></tr>
                    <tr><td><strong>Additional Info:</strong></td><td>{$notes}</td></tr>
                </table>
                <hr style='border: 0; border-top: 1px solid #eee;' />
                <p style='font-size: 13px; color: #666;'>If you are available to donate, please reach out to the contact phone number above immediately.</p>
            </div>
        "
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

/**
 * Sends Transactional SMS via Brevo REST API v3
 */
function sendBrevoSMS($recipientPhone, $messageText) {
    $url = 'https://api.brevo.com/v3/transactionalSMS/send';

    $payload = [
        "sender"    => "TriSeva",
        "recipient" => $recipientPhone,
        "content"   => $messageText,
        "type"      => "transactional"
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