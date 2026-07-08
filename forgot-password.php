<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

session_start();

if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$success = '';
$error   = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Masukkan alamat email yang valid.';
    } else {
        // Pastikan tabel password_resets ada
        $conn->query("CREATE TABLE IF NOT EXISTS `password_resets` (
            `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
            `email` varchar(150) NOT NULL,
            `token` varchar(64) NOT NULL,
            `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
            `expires_at` timestamp NOT NULL DEFAULT (current_timestamp() + INTERVAL 1 HOUR),
            `used` tinyint(1) NOT NULL DEFAULT 0,
            PRIMARY KEY (`id`),
            KEY `idx_token` (`token`),
            KEY `idx_email` (`email`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

        // Cek apakah email terdaftar
        $stmt = $conn->prepare("SELECT user_id, nama FROM users WHERE email = ? AND status = 'aktif' LIMIT 1");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();

        if ($user) {
            // Hapus token lama yang belum dipakai untuk email ini
            $stmt = $conn->prepare("DELETE FROM password_resets WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();

            // Generate token baru
            $token     = bin2hex(random_bytes(32)); // 64 karakter hex
            $expiresAt = date('Y-m-d H:i:s', strtotime('+1 hour'));

            $stmt = $conn->prepare("INSERT INTO password_resets (email, token, expires_at) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $email, $token, $expiresAt);

            if ($stmt->execute()) {
                // Buat link reset
                $protocol  = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
                $baseUrl   = $protocol . '://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']);
                $resetLink = rtrim($baseUrl, '/') . '/reset-password.php?token=' . $token;

                /*
                 * Pada production, kirim email menggunakan PHPMailer / mail().
                 * Untuk keperluan development/demo, link ditampilkan langsung.
                 */
                $_SESSION['reset_link_demo'] = $resetLink;
                $_SESSION['reset_email']     = $email;

                $success = 'Link reset password telah dibuat. Silakan klik link di bawah ini.';
            } else {
                $error = 'Terjadi kesalahan. Coba lagi.';
            }
        } else {
            // Pesan netral agar tidak mengekspos email yang terdaftar
            $success = 'Jika email terdaftar, link reset akan ditampilkan di bawah.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password - Lumière Parfum</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .reset-info {
            background: #fff8e1;
            border: 1px solid #ffe082;
            border-radius: 8px;
            padding: 14px 16px;
            margin-top: 16px;
            font-size: 0.9rem;
        }
        .reset-info a {
            word-break: break-all;
            color: #b8860b;
            font-weight: 600;
        }
        .reset-info p { margin: 0 0 6px; }
        .dev-note {
            font-size: 0.78rem;
            color: #999;
            margin-top: 8px;
        }
    </style>
</head>
<body class="auth-body">

<section class="auth-section">
    <div class="auth-card">
        <div class="auth-header">
            <a href="index.php" class="auth-logo">LUMIÈRE</a>
            <h2>Lupa Password</h2>
            <p>Masukkan email Anda untuk mereset password</p>
        </div>

        <?php if ($error): ?>
        <div class="auth-error">
            <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
        </div>
        <?php endif; ?>

        <?php if ($success): ?>
        <div class="auth-success">
            <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($success); ?>
        </div>
        <?php if (!empty($_SESSION['reset_link_demo'])): ?>
        <div class="reset-info">
            <p><i class="fas fa-link"></i> <strong>Link Reset Password:</strong></p>
            <a href="<?php echo htmlspecialchars($_SESSION['reset_link_demo']); ?>">
                <?php echo htmlspecialchars($_SESSION['reset_link_demo']); ?>
            </a>
            <p class="dev-note">⚠️ Pada production, link ini dikirim ke email. Token berlaku 1 jam.</p>
        </div>
        <?php
            // Hapus dari session setelah ditampilkan
            unset($_SESSION['reset_link_demo']);
            unset($_SESSION['reset_email']);
        ?>
        <?php endif; ?>
        <p style="margin-top:16px; text-align:center;">
            <a href="login.php" class="link-gold"><i class="fas fa-arrow-left"></i> Kembali ke Login</a>
        </p>

        <?php else: ?>

        <form method="POST" action="forgot-password.php" class="auth-form">
            <div class="form-group">
                <label for="email">Alamat Email</label>
                <div class="auth-input-wrap">
                    <i class="fas fa-envelope"></i>
                    <input type="email" id="email" name="email" placeholder="nama@email.com" required
                           value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                </div>
            </div>

            <button type="submit" class="btn btn-auth">
                <i class="fas fa-paper-plane"></i> Kirim Link Reset
            </button>
        </form>

        <p class="auth-footer">
            Ingat password? <a href="login.php">Masuk di sini</a>
        </p>

        <?php endif; ?>
    </div>
</section>

</body>
</html>
