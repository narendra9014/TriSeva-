<?php
// db.php
$host = 'sql211.infinityfree.com'; 
$dbname = 'if0_42196384_triseva_db'; 
$username = 'if0_42196384';     
$password = 'trisevacse';         

// 1. Establish PDO Connection (Used in your login processing & donor logic)
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);
} catch (PDOException $e) {
    die("PDO Database Connection Failure: " . $e->getMessage());
}

// 2. Establish legacy MySQLi Connection (Used across your existing data feeds)
$conn = mysqli_connect($host, $username, $password, $dbname);

if (!$conn) {
    die("MySQLi Database Connection Failure: " . mysqli_connect_error());
}
?>