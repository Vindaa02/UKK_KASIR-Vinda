<?php
require_once 'header.php';

// Get counts for dashboard
$stmtProd = $pdo->query("SELECT COUNT(*) FROM produk");
$totalProduk = $stmtProd->fetchColumn();

$stmtPel = $pdo->query("SELECT COUNT(*) FROM pelanggan");
$totalPelanggan = $stmtPel->fetchColumn();

$stmtPenj = $pdo->query("SELECT COUNT(*), SUM(TotalHarga) FROM penjualan");
$penjualan = $stmtPenj->fetch(PDO::FETCH_NUM);
$totalTransaksi = $penjualan[0];
$totalOmset = $penjualan[1] ? $penjualan[1] : 0;
?>

<div style="margin-bottom: 2rem;">
    <h1>Welcome back, <?= htmlspecialchars($_SESSION['nama_petugas']) ?>! 👋</h1>
    <p>Berikut adalah ringkasan sistem kasir hari ini.</p>
</div>

<div class="dashboard-cards">
    <div class="glass-panel stat-card">
        <div class="stat-icon"><i class="fas fa-box"></i></div>
        <div class="stat-info">
            <h3><?= number_format($totalProduk) ?></h3>
            <p>Total Produk</p>
        </div>
    </div>
    <div class="glass-panel stat-card">
        <div class="stat-icon" style="background: rgba(16, 185, 129, 0.2); color: var(--secondary-color);"><i class="fas fa-users"></i></div>
        <div class="stat-info">
            <h3><?= number_format($totalPelanggan) ?></h3>
            <p>Total Pelanggan</p>
        </div>
    </div>
    <div class="glass-panel stat-card">
        <div class="stat-icon" style="background: rgba(245, 158, 11, 0.2); color: #F59E0B;"><i class="fas fa-shopping-cart"></i></div>
        <div class="stat-info">
            <h3><?= number_format($totalTransaksi) ?></h3>
            <p>Total Transaksi</p>
        </div>
    </div>
    <div class="glass-panel stat-card">
        <div class="stat-icon" style="background: rgba(236, 72, 153, 0.2); color: #EC4899;"><i class="fas fa-wallet"></i></div>
        <div class="stat-info">
            <h3>Rp <?= number_format($totalOmset, 0, ',', '.') ?></h3>
            <p>Total Omset</p>
        </div>
    </div>
</div>

<div class="pos-layout">
    <div class="glass-panel">
        <h3 style="margin-bottom: 1rem;"><i class="fas fa-history"></i> Transaksi Terakhir</h3>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID Transaksi</th>
                        <th>Tanggal</th>
                        <th>Pelanggan</th>
                        <th>Total Pembayaran</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $stmtRecent = $pdo->query("SELECT p.PenjualanID, p.TanggalPenjualan, p.TotalHarga, pel.NamaPelanggan FROM penjualan p LEFT JOIN pelanggan pel ON p.PelangganID = pel.PelangganID ORDER BY p.PenjualanID DESC LIMIT 5");
                    while($row = $stmtRecent->fetch(PDO::FETCH_ASSOC)):
                    ?>
                    <tr>
                        <td>#<?= $row['PenjualanID'] ?></td>
                        <td><?= date('d M Y', strtotime($row['TanggalPenjualan'])) ?></td>
                        <td><?= htmlspecialchars($row['NamaPelanggan'] ?? 'Umum') ?></td>
                        <td>Rp <?= number_format($row['TotalHarga'], 0, ',', '.') ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="glass-panel" style="text-align: center;">
        <h3><i class="fas fa-bolt"></i> Aksi Cepat</h3>
        <hr style="border-color: var(--border-color); margin: 1rem 0;">
        <a href="penjualan.php" class="btn btn-primary" style="display: block; margin-bottom: 1rem;"><i class="fas fa-cash-register"></i> Buka Kasir</a>
        <a href="produk.php" class="btn btn-secondary" style="display: block; margin-bottom: 1rem;"><i class="fas fa-boxes"></i> Kelola Stok</a>
        <?php if($_SESSION['level'] == 'administrator'): ?>
            <a href="register.php" class="btn btn-secondary" style="display: block;"><i class="fas fa-user-plus"></i> Tambah Petugas</a>
        <?php endif; ?>
    </div>
</div>

<?php require_once 'footer.php'; ?>
