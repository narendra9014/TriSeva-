<?php
// decline_request.php
header('Content-Type: application/json');
require_once 'db.php';

session_start();
$donor_id = isset($_SESSION['donor_id']) ? intval($_SESSION['donor_id']) : 0;

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['notification_id']) && $donor_id > 0) {
    $notification_id = intval($_POST['notification_id']);
    
    $query = "SELECT request_id FROM notifications WHERE id = ? AND donor_id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "ii", $notification_id, $donor_id);
    mysqli_stmt_execute($stmt);
    $res = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    mysqli_stmt_close($stmt);
    
    if($res) {
        $request_id = $res['request_id'];
        mysqli_begin_transaction($conn);
        
        try {
            $up_sql = "UPDATE notifications SET status = 'declined' WHERE id = ?";
            $st1 = mysqli_prepare($conn, $up_sql);
            mysqli_stmt_bind_param($st1, "i", $notification_id);
            mysqli_stmt_execute($st1);
            mysqli_stmt_close($st1);
            
            $ins_sql = "INSERT INTO donor_responses (request_id, donor_id, response_status) VALUES (?, ?, 'declined')";
            $st2 = mysqli_prepare($conn, $ins_sql);
            mysqli_stmt_bind_param($st2, "ii", $request_id, $donor_id);
            mysqli_stmt_execute($st2);
            mysqli_stmt_close($st2);
            
            mysqli_commit($conn);
            echo json_encode(["success" => true, "message" => "Alert update complete. Request marked as declined."]);
        } catch (Exception $e) {
            mysqli_rollback($conn);
            echo json_encode(["success" => false, "error" => "Failed processing rollback metrics sequence."]);
        }
    } else {
        echo json_encode(["success" => false, "error" => "Invalid target tracking pointer."]);
    }
} else {
    echo json_encode(["success" => false, "error" => "Invalid direct parameter structure."]);
}
?>