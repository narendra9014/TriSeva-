<?php
// get_waste_reports.php
header('Content-Type: application/json; charset=utf-8');
include("db.php");

$sql = "SELECT * FROM waste_reports ORDER BY id DESC";
$result = mysqli_query($conn, $sql);

$data = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        if (empty($row['photo'])) {
            $row['photo'] = 'placeholder.png';
        }
        $data[] = $row;
    }
}

echo json_encode($data);
exit();
?>