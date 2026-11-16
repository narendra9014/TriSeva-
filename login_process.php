<?php
session_start();

require_once 'db.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email    = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    if (empty($email) || empty($password)) {
        echo "<script>
            alert('Please fill in all required fields.');
            window.location='login.php';
        </script>";
        exit();
    }

    try {
        $sql = "SELECT id, fullname, email, password FROM users WHERE email = :email LIMIT 1";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            
            session_regenerate_id(true);

            $_SESSION['user_id']  = $user['id'];
            $_SESSION['fullname'] = $user['fullname'];
            $_SESSION['email']    = $user['email'];

            header("Location: home.php");
            exit();

        } else {
            echo "<script>
                alert('Invalid email or password.');
                window.location='login.php';
            </script>";
            exit();
        }

    } catch (PDOException $e) {
        die("Authentication error: " . $e->getMessage());
    }
}
?>