<?php
require_once '../config/database.php';
require_once '../includes/functions.php';
session_start();

if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
    header('Location: dashboard.php'); exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $pass  = $_POST['password'] ?? '';
    $stmt = $conn->prepare("SELECT * FROM users WHERE email=? AND role='admin' LIMIT 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    if ($user && password_verify($pass, $user['password_hash']) && $user['status'] === 'aktif') {
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['nama']    = $user['nama'];
        $_SESSION['email']   = $user['email'];
        $_SESSION['role']    = $user['role'];
        header('Location: dashboard.php'); exit;
    }
    $error = 'Email atau password salah.';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Admin Login - Lumière Parfum</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="auth-body">
<section class="auth-section">
    <div class="auth-card">
        <div class="auth-header">
            <span class="auth-logo" style="display:flex;align-items:center;gap:8px;justify-content:center;"><i class="fas fa-crown" style="color:var(--gold);"></i> LUMIÈRE</span>
            <h2>Admin Panel</h2>
            <p>Masuk sebagai administrator</p>
        </div>
        <?php if ($error): ?>
        <div class="auth-error"><i class="fas fa-exclamation-circle"></i> <?php echo $error; ?></div>
        <?php endif; ?>
        <form method="POST" class="auth-form">
            <div class="form-group">
                <label>Email</label>
                <div class="auth-input-wrap">
                    <i class="fas fa-envelope"></i>
                    <input type="email" name="email" placeholder="admin@email.com" required value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                </div>
            </div>
            <div class="form-group">
                <label>Password</label>
                <div class="auth-input-wrap">
                    <i class="fas fa-lock"></i>
                    <input type="password" id="password" name="password" placeholder="Password" required>
                    <i class="fas fa-eye toggle-pass" onclick="togglePassword('password',this)"></i>
                </div>
            </div>
            <button type="submit" class="btn btn-auth"><i class="fas fa-sign-in-alt"></i> Masuk</button>
        </form>
        <p style="text-align:center;margin-top:20px;font-size:.85rem;"><a href="../index.php" style="color:var(--gold);"><i class="fas fa-arrow-left"></i> Kembali ke Website</a></p>
    </div>
</section>
<script>
function togglePassword(id, icon) {
    const input = document.getElementById(id);
    input.type = input.type === 'password' ? 'text' : 'password';
    icon.classList.toggle('fa-eye');
    icon.classList.toggle('fa-eye-slash');
}
</script>
</body>
</html>
