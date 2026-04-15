<?php
session_start();
require_once 'config.php';

// Check if user is logged in
if (!isset($_SESSION['id_petugas']) && basename($_SERVER['PHP_SELF']) != 'login.php') {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kasir Premium Vinda</title>
    <!-- Include FontAwesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<?php if(isset($_SESSION['id_petugas'])): ?>
<nav class="glass-navbar">
    <a href="index.php" class="nav-brand">
        <i class="fas fa-shopping-basket"></i> Kasir<span>Vinda</span>
    </a>
    <div class="nav-links">
        <a href="index.php" class="<?= basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : '' ?>">Dashboard</a>
        <a href="produk.php" class="<?= basename($_SERVER['PHP_SELF']) == 'produk.php' ? 'active' : '' ?>">Produk</a>
        <a href="pelanggan.php" class="<?= basename($_SERVER['PHP_SELF']) == 'pelanggan.php' ? 'active' : '' ?>">Pelanggan</a>
        <a href="penjualan.php" class="<?= basename($_SERVER['PHP_SELF']) == 'penjualan.php' ? 'active' : '' ?>">Kasir / POS</a>
        
        <?php if($_SESSION['level'] == 'administrator'): ?>
            <a href="register.php" class="<?= basename($_SERVER['PHP_SELF']) == 'register.php' ? 'active' : '' ?>">Registrasi Akun</a>
        <?php endif; ?>
    </div>
    <div class="nav-user">
        <span class="badge <?= $_SESSION['level'] == 'administrator' ? 'badge-admin' : 'badge-petugas' ?>">
            <?= strtoupper($_SESSION['level']) ?>
        </span>
        <span class="uname"><i class="fas fa-user-circle"></i> <?= htmlspecialchars($_SESSION['nama_petugas']) ?></span>
        <a href="logout.php" class="btn btn-danger" style="padding: 0.5rem 1rem; font-size: 0.85rem;">
            <i class="fas fa-sign-out-alt"></i> Logout
        </a>
    </div>
</nav>
<?php endif; ?>

<main class="container">
