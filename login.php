<?php
session_start();
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Check in clients
    $query = "SELECT * FROM clients WHERE email = '$email' AND password = '$password'";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $_SESSION['name'] = $user['name'];
        $_SESSION['role'] = 'client';
        header("Location: dashboard-client.php");
        exit;
    }

    // Check in admins
    $query = "SELECT * FROM admins WHERE email = '$email' AND password = '$password'";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $_SESSION['name'] = $user['name'];
        $_SESSION['role'] = 'admin';
        header("Location: dashboard-admin.php");
        exit;
    }

    // Invalid login
    echo "Invalid email or password.";
    header("refresh:2;url=index.php");
    exit;
}
?>