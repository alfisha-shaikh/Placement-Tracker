<?php
session_start();
require_once 'connect.php';

// Get form data
$name = $_POST['name'];
$email = $_POST['email'];
$password = $_POST['password'];
$university = $_POST['university'];
$confirm_password = $_POST['confirm-password'];

// store old data
$_SESSION['old'] = $_POST;

if ($password !== $confirm_password) {
    $_SESSION['error'] = "Passwords do not match!";
    header("Location: register.php");
    exit();
}

// ✅ Use RETURNING id
$query = "INSERT INTO users (name, email, university, password) 
          VALUES ($1, $2, $3, $4) RETURNING id";

$result = pg_query_params($conn, $query, array($name, $email, $university, $password));

if ($result) {

    // ✅ Fetch inserted user ID
    $row = pg_fetch_assoc($result);

    $_SESSION['user_id'] = $row['id'];   // ✅ FIXED
    $_SESSION['name'] = $name;
    $_SESSION['user'] = $email;

    $_SESSION['success'] = "Registration successful! Welcome to Placement Tracker 🚀";

    header("Location: dashboard.php");
    exit();
} else {
    echo "Error: " . pg_last_error($conn);
}
