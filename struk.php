<?php
session_start();
require_once 'config.php';

// Check if user is logged in
if (!isset($_SESSION['id_petugas'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$id = $_GET['id'];

// Get Penjualan Info
$stmt = $pdo->prepare("SELECT p.*, pel.NamaPelanggan, pel.Alamat, pel.NomorTelepon, pet.nama_petugas 
                      FROM penjualan p 
                      JOIN pelanggan pel ON p.PelangganID = pel.PelangganID 
                      JOIN petugas pet ON p.id_petugas = pet.id_petugas
                      WHERE p.PenjualanID = ?");
$stmt->execute([$id]);
$penjualan = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$penjualan) {
    echo "Transaksi tidak ditemukan!";
    exit();
}

// Get Details
$stmtDet = $pdo->prepare("SELECT d.*, pr.NamaProduk FROM detailpenjualan d 
                         JOIN produk pr ON d.ProdukID = pr.ProdukID 
                         WHERE d.PenjualanID = ?");
$stmtDet->execute([$id]);
$details = $stmtDet->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Struk Pembayaran #<?= $penjualan['PenjualanID'] ?></title>
    <style>
        body { font-family: monospace; background: #f0f0f0; padding: 20px; display: flex; justify-content: center; }
        .struk { background: #fff; width: 300px; padding: 20px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .divider { border-bottom: 1px dashed #000; margin: 10px 0; }
        table { width: 100%; border-collapse: collapse; }
        td { font-size: 14px; }
        .btn-print { margin-top: 20px; width: 100%; padding: 10px; background: #000; color: #fff; cursor: pointer; border: none; font-family: monospace; font-size: 16px; }
        @media print {
            body { background: #fff; padding: 0; display: block; }
            .btn-print, .btn-back { display: none; }
            .struk { box-shadow: none; width: 100%; max-width: 300px; margin: 0 auto; }
        }
    </style>
</head>
<body>

<div class="struk">
    <h3 class="text-center" style="margin: 0; font-size: 18px;">KASIR VINDA</h3>
    <div class="text-center" style="font-size: 12px; margin-top: 5px;">Sistem Toko Modern Premium</div>
    <div class="divider"></div>
    <table>
        <tr><td>No. Trans</td><td class="text-right">#<?= str_pad($penjualan['PenjualanID'], 5, '0', STR_PAD_LEFT) ?></td></tr>
        <tr><td>Tanggal</td><td class="text-right"><?= date('d-m-Y H:i', strtotime($penjualan['TanggalPenjualan'])) ?></td></tr>
        <tr><td>Kasir</td><td class="text-right"><?= htmlspecialchars($penjualan['nama_petugas']) ?></td></tr>
        <tr><td>Pelanggan</td><td class="text-right"><?= htmlspecialchars($penjualan['NamaPelanggan']) ?></td></tr>
    </table>
    <div class="divider"></div>
    <table style="margin-bottom: 10px;">
        <?php foreach($details as $d): ?>
        <tr>
            <td colspan="2"><?= htmlspecialchars($d['NamaProduk']) ?></td>
        </tr>
        <tr>
            <td><?= $d['JumlahProduk'] ?> x <?= number_format($d['Subtotal']/$d['JumlahProduk'], 0, ',', '.') ?></td>
            <td class="text-right"><?= number_format($d['Subtotal'], 0, ',', '.') ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
    <div class="divider"></div>
    <table>
        <tr>
            <td style="font-size: 16px; font-weight: bold;">TOTAL</td>
            <td class="text-right" style="font-size: 16px; font-weight: bold;">Rp <?= number_format($penjualan['TotalHarga'], 0, ',', '.') ?></td>
        </tr>
    </table>
    <div class="divider"></div>
    <div class="text-center" style="font-size: 12px; margin-top: 10px;">
        Terima kasih atas kunjungan Anda!
    </div>
    
    <button class="btn-print" onclick="window.print()">CETAK STRUK</button>
    <a href="penjualan.php" style="display: block; text-align: center; margin-top: 10px; color: #666; text-decoration: none; font-family: monospace;" class="btn-back">Kembali ke Kasir</a>
</div>

</body>
</html>
