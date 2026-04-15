<?php
session_start();
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if (empty($username) || empty($password)) {
        header("Location: login.php?error=empty_fields");
        exit();
    }

    $stmt = $pdo->prepare("SELECT * FROM petugas WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Password validation: Check hashed password OR plaintext '123' (as a fallback in case DB was populated with raw '123')
    if ($user && (password_verify($password, $user['password']) || $user['password'] === $password || $user['password'] === md5($password))) {
        $_SESSION['id_petugas'] = $user['id_petugas'];
        $_SESSION['nama_petugas'] = $user['nama_petugas'];
        $_SESSION['level'] = $user['level'];
        
        header("Location: index.php");
        exit();
    } else {
        header("Location: login.php?error=invalid_credentials");
        exit();
    }
} else {
    header("Location: login.php");
    exit();
}
?>
