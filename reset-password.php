<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

session_start();

if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$token   = trim($_GET['token'] ?? '');
$success = '';
$error   = '';
$valid   = false;
$record  = null;

// Validasi token
if (empty($token)) {
    $error = 'Token tidak ditemukan. Silakan minta ulang link reset password.';
} else {
    $now  = date('Y-m-d H:i:s');
    $stmt = $conn->prepare(
        "SELECT id, email FROM password_resets 
         WHERE token = ? AND used = 0 AND expires_at > ? 
         LIMIT 1"
    );
    $stmt->bind_param("ss", $token, $now);
    $stmt->execute();
    $record = $stmt->get_result()->fetch_assoc();

    if ($record) {
        $valid = true;
    } else {
        $error = 'Link reset tidak valid atau sudah kedaluwarsa. Silakan minta link baru.';
    }
}

// Proses form ganti password
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $valid) {
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm_password'] ?? '';

    if (empty($password) || strlen($password) < 6) {
        $error = 'Password minimal 6 karakter.';
    } elseif ($password !== $confirm) {
        $error = 'Konfirmasi password tidak cocok.';
    } else {
        $hash = password_hash($password, PASSWORD_DEFAULT);

        // Update password user
        $stmt = $conn->prepare("UPDATE users SET password_hash = ?, updated_at = NOW() WHERE email = ?");
        $stmt->bind_param("ss", $hash, $record['email']);
        $updateOk = $stmt->execute();

        if ($updateOk && $stmt->affected_rows > 0) {
            // Tandai token sudah dipakai
            $stmt = $conn->prepare("UPDATE password_resets SET used = 1 WHERE id = ?");
            $stmt->bind_param("i", $record['id']);
            $stmt->execute();

            $success = 'Password berhasil diubah! Silakan login dengan password baru.';
            $valid   = false; // Sembunyikan form setelah berhasil
        } else {
            $error = 'Gagal mengubah password. Coba lagi.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - Lumière Parfum</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="auth-body">

<section class="auth-section">
    <div class="auth-card">
        <div class="auth-header">
            <a href="index.php" class="auth-logo">LUMIÈRE</a>
            <h2>Reset Password</h2>
            <p>Buat password baru untuk akun Anda</p>
        </div>

        <?php if ($error): ?>
        <div class="auth-error">
            <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
            <?php if (!$valid): ?>
            <br><br>
            <a href="forgot-password.php" class="link-gold">
                <i class="fas fa-redo"></i> Minta link baru
            </a>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <?php if ($success): ?>
        <div class="auth-success">
            <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($success); ?>
        </div>
        <p style="text-align:center; margin-top:16px;">
            <a href="login.php" class="btn btn-auth" style="display:inline-block;">
                <i class="fas fa-sign-in-alt"></i> Login Sekarang
            </a>
        </p>

        <?php elseif ($valid): ?>

        <p style="text-align:center; font-size:0.88rem; color:#777; margin-bottom:16px;">
            Mengatur ulang password untuk: <strong><?php echo htmlspecialchars($record['email']); ?></strong>
        </p>

        <form method="POST" action="reset-password.php?token=<?php echo urlencode($token); ?>" class="auth-form">
            <div class="form-group">
                <label for="password">Password Baru</label>
                <div class="auth-input-wrap">
                    <i class="fas fa-lock"></i>
                    <input type="password" id="password" name="password"
                           placeholder="Minimal 6 karakter" required minlength="6">
                    <i class="fas fa-eye toggle-pass" onclick="togglePassword('password', this)"></i>
                </div>
            </div>

            <div class="form-group">
                <label for="confirm_password">Konfirmasi Password Baru</label>
                <div class="auth-input-wrap">
                    <i class="fas fa-lock"></i>
                    <input type="password" id="confirm_password" name="confirm_password"
                           placeholder="Ulangi password baru" required>
                    <i class="fas fa-eye toggle-pass" onclick="togglePassword('confirm_password', this)"></i>
                </div>
            </div>

            <button type="submit" class="btn btn-auth">
                <i class="fas fa-save"></i> Simpan Password Baru
            </button>
        </form>

        <?php endif; ?>

        <p class="auth-footer">
            <a href="login.php"><i class="fas fa-arrow-left"></i> Kembali ke Login</a>
        </p>
    </div>
</section>

<script>
function togglePassword(id, icon) {
    const input = document.getElementById(id);
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.replace('fa-eye', 'fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.replace('fa-eye-slash', 'fa-eye');
    }
}
</script>

</body>
</html>
