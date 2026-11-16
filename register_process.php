<?php

include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $fullname = $_POST['fullname'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password != $confirm_password) {

        echo "<script>
        alert('Passwords do not match');
        window.location='register.php';
        </script>";
        exit();
    }

    $check = mysqli_query($conn,
    "SELECT * FROM users WHERE email='$email'");

    if (mysqli_num_rows($check) > 0) {

        echo "<script>
        alert('Email already registered');
        window.location='register.php';
        </script>";
        exit();
    }

    $hashed_password =
    password_hash($password, PASSWORD_DEFAULT);

    $sql = "INSERT INTO users
    (fullname,email,phone,password)
    VALUES
    ('$fullname','$email','$phone','$hashed_password')";

    if (mysqli_query($conn, $sql)) {

        echo "<script>
        alert('Registration Successful');
        window.location='login.php';
        </script>";

    } else {

        echo "Database Error: " . mysqli_error($conn);
    }

}

?>