<?php 
require_once 'header.php'; 

// Cek hak akses administrator
if ($_SESSION['level'] !== 'administrator') {
    echo "<div class='glass-panel text-center'>
            <h2 style='color: var(--danger-color);'><i class='fas fa-exclamation-triangle'></i> Akses Ditolak</h2>
            <p>Hanya Administrator yang dapat mendaftarkan akun pengguna baru.</p>
            <a href='index.php' class='btn btn-primary mt-4'>Kembali ke Dashboard</a>
          </div>";
    require_once 'footer.php';
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = trim($_POST['nama_petugas']);
    $user = trim($_POST['username']);
    $pass = $_POST['password'];
    $role = $_POST['level'];

    if (!empty($nama) && !empty($user) && !empty($pass) && !empty($role)) {
        // Cek username sudah ada atau belum
        $check = $pdo->prepare("SELECT id_petugas FROM petugas WHERE username = ?");
        $check->execute([$user]);
        
        if ($check->rowCount() > 0) {
            $error = "Username sudah digunakan!";
        } else {
            // Enkripsi password menggunakan bcrypt
            $hashedPass = password_hash($pass, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO petugas (nama_petugas, username, password, level) VALUES (?, ?, ?, ?)");
            $stmt->execute([$nama, $user, $hashedPass, $role]);
            $success = "Akun berhasil diregistrasi!";
        }
    } else {
        $error = "Semua field wajib diisi!";
    }
}

// Ambil daftar petugas
$stmtList = $pdo->query("SELECT id_petugas, nama_petugas, username, level FROM petugas ORDER BY level ASC");
$petugasList = $stmtList->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="pos-layout">
    <div>
        <div class="glass-panel" style="margin-bottom: 2rem;">
            <h2><i class="fas fa-users"></i> Daftar Pengguna Sistem</h2>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Petugas</th>
                            <th>Username</th>
                            <th>Level Akses</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; foreach($petugasList as $p): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= htmlspecialchars($p['nama_petugas']) ?></td>
                            <td><?= htmlspecialchars($p['username']) ?></td>
                            <td>
                                <span class="badge <?= $p['level'] == 'administrator' ? 'badge-admin' : 'badge-petugas' ?>">
                                    <?= strtoupper($p['level']) ?>
                                </span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <div>
        <div class="glass-panel">
            <h3><i class="fas fa-user-plus"></i> Register Akun Baru</h3>
            <hr style="border-color: var(--border-color); margin-bottom: 1.5rem;">
            
            <?php if(isset($error)): ?>
                <div class="alert alert-danger"><?= $error ?></div>
            <?php endif; ?>
            
            <?php if(isset($success)): ?>
                <div class="alert alert-success"><?= $success ?></div>
            <?php endif; ?>

            <form method="POST" action="register.php">
                <div class="form-group">
                    <label class="form-label">Nama Lengkap</label>
                    <input type="text" name="nama_petugas" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Username</label>
                    <input type="text" name="username" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Tingkat Akses (Role)</label>
                    <select name="level" class="form-control" required>
                        <option value="petugas">Petugas Kasir</option>
                        <option value="administrator">Administrator</option>
                    </select>
                </div>
                
                <button type="submit" class="btn btn-primary" style="width: 100%;">
                    <i class="fas fa-save"></i> Daftarkan Akun
                </button>
            </form>
        </div>
    </div>
</div>

<?php require_once 'footer.php'; ?>
