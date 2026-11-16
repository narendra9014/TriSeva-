<?php
// save_missing_persons.php
include("db.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $person_name           = $_POST['person_name'] ?? '';
    $age                   = $_POST['age'] ?? '';
    $gender                = $_POST['gender'] ?? '';
    $last_seen_location    = $_POST['last_seen_location'] ?? '';
    $last_seen_date        = $_POST['last_seen_date'] ?? '';
    $last_seen_time        = $_POST['last_seen_time'] ?? '';
    $height                = $_POST['height'] ?? '';
    $clothing              = $_POST['clothing'] ?? '';
    $distinguishing_features = $_POST['distinguishing_features'] ?? '';
    $contact_name          = $_POST['contact_name'] ?? '';
    $contact_phone         = $_POST['contact_phone'] ?? '';
    $contact_relation      = $_POST['contact_relation'] ?? '';
    $police_complaint      = $_POST['police_complaint'] ?? 'no';
    $additional_info       = $_POST['additional_info'] ?? '';

    $photo = "";

    // Upload handling
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $targetDir = "uploads/";
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }
        $photo = time() . '_' . basename($_FILES['photo']['name']);
        move_uploaded_file($_FILES['photo']['tmp_name'], $targetDir . $photo);
    }

    $stmt = $conn->prepare("INSERT INTO missing_persons (
        person_name, age, gender, last_seen_location, last_seen_date, last_seen_time,
        height, clothing, distinguishing_features, photo, contact_name, contact_phone,
        contact_relation, police_complaint, additional_info
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    $stmt->bind_param(
        "sisssssssssssss",
        $person_name, $age, $gender, $last_seen_location, $last_seen_date, $last_seen_time,
        $height, $clothing, $distinguishing_features, $photo, $contact_name, $contact_phone,
        $contact_relation, $police_complaint, $additional_info
    );

    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}
?>