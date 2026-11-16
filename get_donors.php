<?php

require_once "db.php";

try {

    $stmt = $pdo->query("SELECT * FROM donors");

    $donors = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($donors);

} catch (PDOException $e) {

    echo json_encode([
        "error" => $e->getMessage()
    ]);

}

?>