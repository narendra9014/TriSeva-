<?php
// update_status.php
header('Content-Type: application/json; charset=utf-8');
include("db.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['id'] ?? 0);
    $status = trim($_POST['status'] ?? '');

    $allowed_statuses = ['missing', 'found', 'closed'];

    if ($id > 0 && in_array($status, $allowed_statuses)) {
        $stmt = $conn->prepare("UPDATE missing_persons SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $status, $id);

        if ($stmt->execute()) {
            echo json_encode(["status" => "success", "message" => "Status updated successfully"]);
        } else {
            echo json_encode(["status" => "error", "message" => $stmt->error]);
        }
        $stmt->close();
    } else {
        echo json_encode(["status" => "error", "message" => "Invalid parameters"]);
    }
}
?>