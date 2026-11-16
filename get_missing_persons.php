<?php
// get_missing_persons.php
header('Content-Type: application/json');
include("db.php");

$sql = "SELECT * FROM missing_persons ORDER BY id DESC";
$result = mysqli_query($conn, $sql);

$data = [];
if ($result) {
    while($row = mysqli_fetch_assoc($result)) {
        if (empty($row['photo'])) {
            $row['photo'] = 'placeholder.png'; // Make sure placeholder.png exists inside uploads/
        }
        $data[] = $row;
    }
}

echo json_encode($data);
exit();
?>