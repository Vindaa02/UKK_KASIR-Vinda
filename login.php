<?php require_once 'header.php'; ?>

<div style="display: flex; justify-content: center; align-items: center; min-height: 80vh;">
    <div class="glass-panel" style="width: 100%; max-width: 400px;">
        <h2 class="text-center" style="margin-bottom: 2rem;">
            <i class="fas fa-shopping-basket" style="color: var(--primary-color);"></i> Kasir Vinda
        </h2>

        <?php if(isset($_GET['error'])): ?>
            <div class="alert alert-danger">
                <?php 
                    if($_GET['error'] == 'empty_fields') echo "Username dan Password tidak boleh kosong!";
                    else if($_GET['error'] == 'invalid_credentials') echo "Username atau Password salah!";
                    else echo "Terjadi kesalahan.";
                ?>
            </div>
        <?php endif; ?>

        <?php if(isset($_GET['success'])): ?>
            <div class="alert alert-success">
                <?php 
                    if($_GET['success'] == 'registered') echo "Registrasi berhasil! Silakan login.";
                ?>
            </div>
        <?php endif; ?>

        <form action="auth.php" method="POST">
            <div class="form-group">
                <label class="form-label" for="username"><i class="fas fa-user"></i> Username</label>
                <input type="text" id="username" name="username" class="form-control" autocomplete="off" required>
            </div>
            <div class="form-group" style="margin-bottom: 2rem;">
                <label class="form-label" for="password"><i class="fas fa-lock"></i> Password</label>
                <input type="password" id="password" name="password" class="form-control" required>
            </div>
            
            <button type="submit" class="btn btn-primary" style="width: 100%;">
                <i class="fas fa-sign-in-alt"></i> Login Masuk
            </button>
        </form>
    </div>
</div>

<?php require_once 'footer.php'; ?>
