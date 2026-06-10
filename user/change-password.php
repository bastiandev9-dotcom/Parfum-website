<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';
if (!isset($_SESSION['user_id'])) { header('Location: ../login.php'); exit; }

$userId = $_SESSION['user_id'];
$message = $error = '';

$stmt = $conn->prepare("SELECT password_hash FROM users WHERE user_id=?");
$stmt->bind_param("i", $userId); $stmt->execute();
$userData = $stmt->get_result()->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $old  = $_POST['old_password'] ?? '';
    $new  = $_POST['new_password'] ?? '';
    $conf = $_POST['confirm_password'] ?? '';
    if (!password_verify($old, $userData['password_hash'])) {
        $error = 'Password lama tidak sesuai.';
    } elseif (strlen($new) < 8) {
        $error = 'Password baru minimal 8 karakter.';
    } elseif ($new !== $conf) {
        $error = 'Konfirmasi password tidak cocok.';
    } else {
        $hash = password_hash($new, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE users SET password_hash=? WHERE user_id=?");
        $stmt->bind_param("si", $hash, $userId);
        $stmt->execute();
        $message = 'Password berhasil diubah.';
    }
}

$user = ['nama' => $_SESSION['nama'] ?? 'User', 'avatar' => strtoupper(substr($_SESSION['nama'] ?? 'U', 0, 2))];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Ganti Password - Lumière Parfum</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
<?php include '../includes/header.php'; ?>
<section class="user-section">
<div class="container">
<div class="user-layout">
    <aside class="user-sidebar">
        <div class="user-profile-mini">
            <div class="mini-avatar"><?php echo $user['avatar']; ?></div>
            <div class="mini-info"><h4><?php echo $user['nama']; ?></h4></div>
        </div>
        <nav class="user-nav">
            <a href="dashboard.php"><i class="fas fa-th-large"></i> Dashboard</a>
            <a href="orders.php"><i class="fas fa-shopping-bag"></i> Pesanan Saya</a>
            <a href="profile.php"><i class="fas fa-user"></i> Profil & Alamat</a>
            <a href="wishlist.php"><i class="fas fa-heart"></i> Wishlist</a>
            <a href="change-password.php" class="active"><i class="fas fa-lock"></i> Ganti Password</a>
            <a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Keluar</a>
        </nav>
    </aside>
    <div class="user-content">
        <div class="content-header">
            <h2>Ganti Password</h2>
            <p>Perbarui keamanan akun Anda.</p>
        </div>
        <?php if ($message): ?><div class="auth-success" style="margin-bottom:16px;"><i class="fas fa-check-circle"></i> <?php echo $message; ?></div><?php endif; ?>
        <?php if ($error): ?><div class="auth-error" style="margin-bottom:16px;"><i class="fas fa-exclamation-circle"></i> <?php echo $error; ?></div><?php endif; ?>
        <div class="user-card" style="max-width:480px;">
            <div class="card-header"><h3>Ubah Password</h3></div>
            <form method="POST" class="profile-form">
                <div class="form-group">
                    <label>Password Saat Ini</label>
                    <div class="password-wrap">
                        <input type="password" name="old_password" placeholder="Password lama" required>
                        <i class="fas fa-eye toggle-pass"></i>
                    </div>
                </div>
                <div class="form-group">
                    <label>Password Baru</label>
                    <div class="password-wrap">
                        <input type="password" name="new_password" placeholder="Minimal 8 karakter" required>
                        <i class="fas fa-eye toggle-pass"></i>
                    </div>
                </div>
                <div class="form-group">
                    <label>Konfirmasi Password Baru</label>
                    <div class="password-wrap">
                        <input type="password" name="confirm_password" placeholder="Ulangi password baru" required>
                        <i class="fas fa-eye toggle-pass"></i>
                    </div>
                </div>
                <button type="submit" class="btn btn-save">Update Password</button>
            </form>
        </div>
    </div>
</div>
</div>
</section>
<?php include '../includes/footer.php'; ?>
<script>
document.querySelectorAll('.toggle-pass').forEach(icon => {
    icon.addEventListener('click', function() {
        const input = this.previousElementSibling;
        input.type = input.type === 'password' ? 'text' : 'password';
        this.classList.toggle('fa-eye');
        this.classList.toggle('fa-eye-slash');
    });
});
</script>
</body>
</html>
