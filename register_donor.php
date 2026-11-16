<?php
require_once __DIR__ . '/db.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

try {

    $sql = "INSERT INTO donors
    (fullname, age, bloodgroup, phone, email, city, state, pincode, address)
    VALUES
    (?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        $_POST['fullname'],
        $_POST['age'],
        $_POST['bloodgroup'],
        $_POST['phone'],
        $_POST['email'],
        $_POST['city'],
        $_POST['state'],
        $_POST['pincode'],
        $_POST['address']
    ]);

    echo "success";

} catch(PDOException $e) {

    echo $e->getMessage();

}
?>