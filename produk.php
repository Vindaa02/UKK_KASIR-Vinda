<?php
require_once 'header.php';

// Process Add/Edit/Delete
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add'])) {
        $nama = $_POST['NamaProduk'];
        $harga = str_replace('.', '', $_POST['Harga']);
        $stok = $_POST['Stok'];
        $stmt = $pdo->prepare("INSERT INTO produk (NamaProduk, Harga, Stok) VALUES (?, ?, ?)");
        $stmt->execute([$nama, $harga, $stok]);
    } elseif (isset($_POST['edit'])) {
        $id = $_POST['ProdukID'];
        $nama = $_POST['NamaProduk'];
        $harga = str_replace('.', '', $_POST['Harga']);
        $stok = $_POST['Stok'];
        $stmt = $pdo->prepare("UPDATE produk SET NamaProduk=?, Harga=?, Stok=? WHERE ProdukID=?");
        $stmt->execute([$nama, $harga, $stok, $id]);
    }
    header("Location: produk.php");
    exit();
}

if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM produk WHERE ProdukID=?");
    $stmt->execute([$id]);
    header("Location: produk.php");
    exit();
}

// Fetch Products
$stmt = $pdo->query("SELECT * FROM produk ORDER BY ProdukID DESC");
$produk = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Check if Edit Request
$editData = null;
if (isset($_GET['edit_id'])) {
    $stmtEdit = $pdo->prepare("SELECT * FROM produk WHERE ProdukID=?");
    $stmtEdit->execute([$_GET['edit_id']]);
    $editData = $stmtEdit->fetch(PDO::FETCH_ASSOC);
}
?>

<div class="pos-layout">
    <div>
        <div class="glass-panel">
            <h3 style="margin-bottom: 1rem;"><i class="fas fa-boxes"></i> Data Produk & Stok</h3>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Produk</th>
                            <th>Harga</th>
                            <th>Stok</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; foreach($produk as $p): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= htmlspecialchars($p['NamaProduk']) ?></td>
                            <td>Rp <?= number_format($p['Harga'], 0, ',', '.') ?></td>
                            <td>
                                <span class="badge <?= $p['Stok'] < 5 ? 'badge-admin' : 'badge-petugas' ?>">
                                    <?= $p['Stok'] ?> Unit
                                </span>
                            </td>
                            <td>
                                <a href="produk.php?edit_id=<?= $p['ProdukID'] ?>" class="btn btn-secondary" style="padding: 0.4rem 0.8rem; font-size: 0.8rem;"><i class="fas fa-edit"></i></a>
                                <a href="produk.php?delete=<?= $p['ProdukID'] ?>" class="btn btn-danger" style="padding: 0.4rem 0.8rem; font-size: 0.8rem;" onclick="return confirm('Hapus produk ini?')"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (count($produk) == 0): ?>
                        <tr><td colspan="5" class="text-center">Belum ada produk.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <div>
        <div class="glass-panel">
            <h3> <?= $editData ? '<i class="fas fa-edit"></i> Edit Produk' : '<i class="fas fa-plus"></i> Tambah Produk' ?></h3>
            <hr style="border-color: var(--border-color); margin-bottom: 1.5rem;">
            
            <form method="POST" action="produk.php">
                <?php if($editData): ?>
                    <input type="hidden" name="ProdukID" value="<?= $editData['ProdukID'] ?>">
                <?php endif; ?>
                
                <div class="form-group">
                    <label class="form-label">Nama Produk</label>
                    <input type="text" name="NamaProduk" class="form-control" value="<?= $editData ? $editData['NamaProduk'] : '' ?>" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Harga (Rp)</label>
                    <input type="number" name="Harga" class="form-control" value="<?= $editData ? (int)$editData['Harga'] : '' ?>" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Stok Awal</label>
                    <input type="number" name="Stok" class="form-control" value="<?= $editData ? $editData['Stok'] : '' ?>" required>
                </div>
                
                <?php if($editData): ?>
                    <button type="submit" name="edit" class="btn btn-primary" style="width: 100%;"><i class="fas fa-save"></i> Simpan Perubahan</button>
                    <a href="produk.php" class="btn btn-secondary mt-4" style="width: 100%;">Batal</a>
                <?php else: ?>
                    <button type="submit" name="add" class="btn btn-primary" style="width: 100%;"><i class="fas fa-plus"></i> Tambah Produk</button>
                <?php endif; ?>
            </form>
        </div>
    </div>
</div>

<?php require_once 'footer.php'; ?>
