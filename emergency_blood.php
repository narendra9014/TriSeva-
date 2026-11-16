<?php
// emergency_request.php
require_once 'db.php';

$message = "";
$message_class = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_request'])) {
    // Collect and sanitize inputs
    $patient_name    = trim($_POST['patient_name'] ?? '');
    $blood_group     = trim($_POST['blood_group'] ?? '');
    $units_needed    = intval($_POST['units_needed'] ?? 1);
    $hospital_name   = trim($_POST['hospital_name'] ?? '');
    $city            = trim($_POST['city'] ?? '');
    $contact_person  = trim($_POST['contact_person'] ?? '');
    $phone_number    = trim($_POST['phone_number'] ?? '');
    $urgency         = trim($_POST['urgency'] ?? 'Normal');
    $additional_info = trim($_POST['additional_info'] ?? '');

    if (!empty($patient_name) && !empty($blood_group) && !empty($hospital_name) && !empty($city) && !empty($phone_number)) {
        
        // Prepared Statement to prevent SQL Injection
        $sql = "INSERT INTO blood_requests (patient_name, blood_group, units_needed, hospital_name, city, contact_person, phone, urgency, additional_info) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        if ($stmt = mysqli_prepare($conn, $sql)) {
            mysqli_stmt_bind_param($stmt, "ssissssss", $patient_name, $blood_group, $units_needed, $hospital_name, $city, $contact_person, $phone_number, $urgency, $additional_info);
            
            if (mysqli_stmt_execute($stmt)) {
                $request_id = mysqli_insert_id($conn);
                mysqli_stmt_close($stmt);
                
                // Redirect directly to notification handler
                header("Location: send_notifications.php?request_id=" . $request_id);
                exit();
            } else {
                $message = "Error saving request. Please try again.";
                $message_class = "alert-danger";
                mysqli_stmt_close($stmt);
            }
        }
    } else {
        $message = "Please fill in all mandatory fields.";
        $message_class = "alert-warning";
    }
}
?>