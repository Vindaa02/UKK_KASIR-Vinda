<?php
require_once 'header.php';

// Process Add/Edit/Delete
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add'])) {
        $nama = $_POST['NamaPelanggan'];
        $alamat = $_POST['Alamat'];
        $no_telp = $_POST['NomorTelepon'];
        $stmt = $pdo->prepare("INSERT INTO pelanggan (NamaPelanggan, Alamat, NomorTelepon) VALUES (?, ?, ?)");
        $stmt->execute([$nama, $alamat, $no_telp]);
    } elseif (isset($_POST['edit'])) {
        $id = $_POST['PelangganID'];
        $nama = $_POST['NamaPelanggan'];
        $alamat = $_POST['Alamat'];
        $no_telp = $_POST['NomorTelepon'];
        $stmt = $pdo->prepare("UPDATE pelanggan SET NamaPelanggan=?, Alamat=?, NomorTelepon=? WHERE PelangganID=?");
        $stmt->execute([$nama, $alamat, $no_telp, $id]);
    }
    header("Location: pelanggan.php");
    exit();
}

if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM pelanggan WHERE PelangganID=?");
    $stmt->execute([$id]);
    header("Location: pelanggan.php");
    exit();
}

// Fetch Customers
$stmt = $pdo->query("SELECT * FROM pelanggan ORDER BY PelangganID DESC");
$pelanggan = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Check if Edit Request
$editData = null;
if (isset($_GET['edit_id'])) {
    $stmtEdit = $pdo->prepare("SELECT * FROM pelanggan WHERE PelangganID=?");
    $stmtEdit->execute([$_GET['edit_id']]);
    $editData = $stmtEdit->fetch(PDO::FETCH_ASSOC);
}
?>

<div class="pos-layout">
    <div>
        <div class="glass-panel">
            <h3 style="margin-bottom: 1rem;"><i class="fas fa-users"></i> Data Pelanggan</h3>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>Alamat</th>
                            <th>No Telepon</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; foreach($pelanggan as $p): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= htmlspecialchars($p['NamaPelanggan']) ?></td>
                            <td><?= htmlspecialchars($p['Alamat']) ?></td>
                            <td><?= htmlspecialchars($p['NomorTelepon']) ?></td>
                            <td>
                                <a href="pelanggan.php?edit_id=<?= $p['PelangganID'] ?>" class="btn btn-secondary" style="padding: 0.4rem 0.8rem; font-size: 0.8rem;"><i class="fas fa-edit"></i></a>
                                <a href="pelanggan.php?delete=<?= $p['PelangganID'] ?>" class="btn btn-danger" style="padding: 0.4rem 0.8rem; font-size: 0.8rem;" onclick="return confirm('Hapus pelanggan ini?')"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (count($pelanggan) == 0): ?>
                        <tr><td colspan="5" class="text-center">Belum ada pelanggan terdaftar.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <div>
        <div class="glass-panel">
            <h3> <?= $editData ? '<i class="fas fa-edit"></i> Edit Pelanggan' : '<i class="fas fa-plus"></i> Tambah Pelanggan' ?></h3>
            <hr style="border-color: var(--border-color); margin-bottom: 1.5rem;">
            
            <form method="POST" action="pelanggan.php">
                <?php if($editData): ?>
                    <input type="hidden" name="PelangganID" value="<?= $editData['PelangganID'] ?>">
                <?php endif; ?>
                
                <div class="form-group">
                    <label class="form-label">Nama Pelanggan</label>
                    <input type="text" name="NamaPelanggan" class="form-control" value="<?= $editData ? $editData['NamaPelanggan'] : '' ?>" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Alamat</label>
                    <textarea name="Alamat" class="form-control" rows="3" required><?= $editData ? $editData['Alamat'] : '' ?></textarea>
                </div>
                <div class="form-group">
                    <label class="form-label">Nomor Telepon</label>
                    <input type="text" name="NomorTelepon" class="form-control" value="<?= $editData ? $editData['NomorTelepon'] : '' ?>" required>
                </div>
                
                <?php if($editData): ?>
                    <button type="submit" name="edit" class="btn btn-primary" style="width: 100%;"><i class="fas fa-save"></i> Simpan Perubahan</button>
                    <a href="pelanggan.php" class="btn btn-secondary mt-4" style="width: 100%;">Batal</a>
                <?php else: ?>
                    <button type="submit" name="add" class="btn btn-primary" style="width: 100%;"><i class="fas fa-plus"></i> Tambah Pelanggan</button>
                <?php endif; ?>
            </form>
        </div>
    </div>
</div>

<?php require_once 'footer.php'; ?>
