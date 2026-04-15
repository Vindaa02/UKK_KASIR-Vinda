<?php
require_once 'header.php';

// Initialize Cart
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Handle Add to Cart
if (isset($_GET['add_cart'])) {
    $id = $_GET['add_cart'];
    $stmt = $pdo->prepare("SELECT * FROM produk WHERE ProdukID = ?");
    $stmt->execute([$id]);
    $prod = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($prod && $prod['Stok'] > 0) {
        if (isset($_SESSION['cart'][$id])) {
            if ($_SESSION['cart'][$id]['qty'] < $prod['Stok']) {
                $_SESSION['cart'][$id]['qty']++;
            } else {
                $error = "Stok {$prod['NamaProduk']} tidak mencukupi!";
            }
        } else {
            $_SESSION['cart'][$id] = [
                'id' => $prod['ProdukID'],
                'nama' => $prod['NamaProduk'],
                'harga' => $prod['Harga'],
                'qty' => 1
            ];
        }
    }
    header("Location: penjualan.php" . (isset($error) ? "?error=".urlencode($error) : ""));
    exit();
}

// Handle Update/Delete Cart
if (isset($_GET['action'])) {
    $id = $_GET['id'];
    if ($_GET['action'] == 'min') {
        if ($_SESSION['cart'][$id]['qty'] > 1) {
            $_SESSION['cart'][$id]['qty']--;
        } else {
            unset($_SESSION['cart'][$id]);
        }
    } elseif ($_GET['action'] == 'del') {
        unset($_SESSION['cart'][$id]);
    } elseif ($_GET['action'] == 'clear') {
        $_SESSION['cart'] = [];
    }
    header("Location: penjualan.php");
    exit();
}

// Handle Checkout
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['checkout'])) {
    if (empty($_SESSION['cart'])) {
        $error = "Keranjang kosong!";
    } else {
        $pelangganId = $_POST['PelangganID'];
        $totalBayar = 0;
        foreach ($_SESSION['cart'] as $item) {
            $totalBayar += ($item['harga'] * $item['qty']);
        }

        try {
            $pdo->beginTransaction();
            // Insert Penjualan
            $stmtPenj = $pdo->prepare("INSERT INTO penjualan (TanggalPenjualan, TotalHarga, PelangganID, id_petugas) VALUES (CURDATE(), ?, ?, ?)");
            $stmtPenj->execute([$totalBayar, $pelangganId, $_SESSION['id_petugas']]);
            $penjualanId = $pdo->lastInsertId();

            // Insert Details and Update Stock
            $stmtDet = $pdo->prepare("INSERT INTO detailpenjualan (PenjualanID, ProdukID, JumlahProduk, Subtotal) VALUES (?, ?, ?, ?)");
            $stmtUpdateStok = $pdo->prepare("UPDATE produk SET Stok = Stok - ? WHERE ProdukID = ?");

            foreach ($_SESSION['cart'] as $item) {
                $subtotal = $item['harga'] * $item['qty'];
                $stmtDet->execute([$penjualanId, $item['id'], $item['qty'], $subtotal]);
                $stmtUpdateStok->execute([$item['qty'], $item['id']]);
            }

            $pdo->commit();
            $_SESSION['cart'] = []; // empty cart
            header("Location: struk.php?id=" . $penjualanId);
            exit();

        } catch (Exception $e) {
            $pdo->rollBack();
            $error = "Gagal memproses transaksi: " . $e->getMessage();
        }
    }
}

// Fetch Data for POS
$stmtProd = $pdo->query("SELECT * FROM produk WHERE Stok > 0 ORDER BY NamaProduk ASC");
$produk = $stmtProd->fetchAll(PDO::FETCH_ASSOC);

// If no customer yet, create "Umum" dummy or fetch actuals
$stmtPel = $pdo->query("SELECT * FROM pelanggan ORDER BY NamaPelanggan ASC");
$pelanggan = $stmtPel->fetchAll(PDO::FETCH_ASSOC);

$totalBelanja = 0;
?>

<div class="pos-layout">
    <!-- Area Produk -->
    <div>
        <div class="glass-panel" style="margin-bottom: 2rem;">
            <h2><i class="fas fa-cubes"></i> Pilih Produk</h2>
            <?php if(isset($_GET['error'])): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($_GET['error']) ?></div>
            <?php endif; ?>
            <?php if(isset($error)): ?>
                <div class="alert alert-danger"><?= $error ?></div>
            <?php endif; ?>

            <div class="product-grid">
                <?php foreach($produk as $p): ?>
                <div class="glass-panel product-card" onclick="window.location.href='penjualan.php?add_cart=<?= $p['ProdukID'] ?>'">
                    <i class="fas fa-box" style="font-size: 2rem; color: var(--primary-color); margin-bottom: 0.5rem;"></i>
                    <h4><?= htmlspecialchars($p['NamaProduk']) ?></h4>
                    <div class="price">Rp <?= number_format($p['Harga'], 0, ',', '.') ?></div>
                    <div style="font-size: 0.8rem; color: var(--text-secondary); margin-top: 0.5rem;">Sisa: <?= $p['Stok'] ?></div>
                </div>
                <?php endforeach; ?>
                <?php if (count($produk) == 0): ?>
                    <p>Produk tidak tersedia / Stok habis.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Area Keranjang (Cart) -->
    <div>
        <div class="glass-panel" style="position: sticky; top: 100px;">
            <h3><i class="fas fa-shopping-cart"></i> Keranjang Belanja</h3>
            <hr style="border-color: var(--border-color); margin-bottom: 1rem;">
            
            <div class="cart-items">
                <?php if(empty($_SESSION['cart'])): ?>
                    <p class="text-center" style="color: var(--text-secondary); margin: 2rem 0;">Keranjang kosong.</p>
                <?php else: ?>
                    <?php foreach($_SESSION['cart'] as $id => $item): 
                        $sub = $item['harga'] * $item['qty'];
                        $totalBelanja += $sub;
                    ?>
                    <div class="cart-item">
                        <div class="cart-item-info">
                            <h4><?= htmlspecialchars($item['nama']) ?></h4>
                            <p><?= $item['qty'] ?> x Rp <?= number_format($item['harga'], 0, ',', '.') ?></p>
                        </div>
                        <div style="text-align: right;">
                            <div style="font-weight: 600;">Rp <?= number_format($sub, 0, ',', '.') ?></div>
                            <div style="margin-top: 0.5rem; display: flex; gap: 0.5rem; justify-content: flex-end;">
                                <a href="penjualan.php?action=min&id=<?= $id ?>" class="btn btn-secondary" style="padding: 0.2rem 0.5rem; font-size: 0.8rem;"><i class="fas fa-minus"></i></a>
                                <a href="penjualan.php?add_cart=<?= $id ?>" class="btn btn-secondary" style="padding: 0.2rem 0.5rem; font-size: 0.8rem;"><i class="fas fa-plus"></i></a>
                                <a href="penjualan.php?action=del&id=<?= $id ?>" class="btn btn-danger" style="padding: 0.2rem 0.5rem; font-size: 0.8rem;"><i class="fas fa-trash"></i></a>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <?php if(!empty($_SESSION['cart'])): ?>
                <div class="cart-total">
                    <span>Total:</span>
                    <span>Rp <?= number_format($totalBelanja, 0, ',', '.') ?></span>
                </div>
                
                <form action="penjualan.php" method="POST" style="margin-top: 1.5rem;">
                    <div class="form-group">
                        <label class="form-label">Pelanggan</label>
                        <select name="PelangganID" class="form-control" required>
                            <option value="" disabled selected>-- Pilih Pelanggan --</option>
                            <?php foreach($pelanggan as $pl): ?>
                                <option value="<?= $pl['PelangganID'] ?>"><?= htmlspecialchars($pl['NamaPelanggan']) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <div style="font-size: 0.8rem; margin-top: 0.5rem;">
                            <a href="pelanggan.php" style="color: var(--primary-color);">+ Tambah Pelanggan Baru</a>
                        </div>
                    </div>
                    
                    <button type="submit" name="checkout" class="btn btn-primary" style="width: 100%; font-size: 1.1rem; padding: 1rem;"><i class="fas fa-check-circle"></i> Bayar Sekarang</button>
                    <a href="penjualan.php?action=clear" class="btn btn-secondary mt-4" style="width: 100%; text-align: center;"><i class="fas fa-times"></i> Kosongkan Keranjang</a>
                </form>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once 'footer.php'; ?>
