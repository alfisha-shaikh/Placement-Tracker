<?php
session_start();
require_once 'connect.php';

$email = $_POST['email'];
$password = $_POST['password'];

$query = "SELECT * FROM users WHERE email='$email'";
$result = pg_query($conn, $query);

if (pg_num_rows($result) > 0) {

    $row = pg_fetch_assoc($result);

    if ($password == $row['password']) {

        $_SESSION['user_id'] = $row['id'];
        $_SESSION['name'] = $row['name'];
        $_SESSION['user'] = $row['email'];

        $_SESSION['success'] = "Welcome back! Redirecting to your dashboard...";
        header("Location: dashboard.php");
        exit();
    } else {
        $_SESSION['error'] = "Invalid password!";
        header("Location: login.php");
        exit();
    }
} else {
    echo "Email not found";
}
