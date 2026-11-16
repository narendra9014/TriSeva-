<?php
// accept_request.php
header('Content-Type: application/json');
require_once 'db.php';

session_start();
$donor_id = isset($_SESSION['donor_id']) ? intval($_SESSION['donor_id']) : 0;

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['notification_id']) && $donor_id > 0) {
    $notification_id = intval($_POST['notification_id']);
    
    // 1. Fetch targeted notification tracking validation indexes
    $query = "SELECT request_id FROM notifications WHERE id = ? AND donor_id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "ii", $notification_id, $donor_id);
    mysqli_stmt_execute($stmt);
    $res = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    mysqli_stmt_close($stmt);
    
    if($res) {
        $request_id = $res['request_id'];
        
        // Start isolation transaction structure processing block
        mysqli_begin_transaction($conn);
        
        try {
            // A. Update central notification row records
            $up_sql = "UPDATE notifications SET status = 'accepted' WHERE id = ?";
            $st1 = mysqli_prepare($conn, $up_sql);
            mysqli_stmt_bind_param($st1, "i", $notification_id);
            mysqli_stmt_execute($st1);
            mysqli_stmt_close($st1);
            
            // B. Register the confirmation inside the donor_responses dashboard
            $ins_sql = "INSERT INTO donor_responses (request_id, donor_id, response_status) VALUES (?, ?, 'accepted')";
            $st2 = mysqli_prepare($conn, $ins_sql);
            mysqli_stmt_bind_param($st2, "ii", $request_id, $donor_id);
            mysqli_stmt_execute($st2);
            mysqli_stmt_close($st2);
            
            mysqli_commit($conn);
            echo json_encode(["success" => true, "message" => "Thank you! Your acceptance has been logged, and the requester is notified."]);
            
        } catch (Exception $e) {
            mysqli_rollback($conn);
            echo json_encode(["success" => false, "error" => "Transactional update workflow failed execution tracking."]);
        }
    } else {
        echo json_encode(["success" => false, "error" => "Notification configuration targeting index mismatch."]);
    }
} else {
    echo json_encode(["success" => false, "error" => "Bad request or missing session authentication."]);
}
?>