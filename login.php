<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

session_start();

// Kalau sudah login, redirect
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] === 'admin') {
        header('Location: admin/dashboard.php');
    } else {
        header('Location: user/dashboard.php');
    }
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $conn->prepare("SELECT user_id, nama, email, password_hash, role, status FROM users WHERE email = ? LIMIT 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();

    if ($user && password_verify($password, $user['password_hash'])) {
        if ($user['status'] === 'aktif') {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['nama']    = $user['nama'];
            $_SESSION['email']   = $user['email'];
            $_SESSION['role']    = $user['role'];

            header('Location: ' . ($user['role'] === 'admin' ? 'admin/dashboard.php' : 'user/dashboard.php'));
            exit;
        } else {
            $error = 'Akun Anda tidak aktif. Hubungi admin.';
        }
    } else {
        $error = 'Email atau password salah.';
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login - Lumière Parfum</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="auth-body">

<section class="auth-section">
    <div class="auth-card">
        <div class="auth-header">
            <a href="index.php" class="auth-logo">LUMIÈRE</a>
            <h2>Selamat Datang Kembali</h2>
            <p>Masuk untuk melanjutkan perjalanan aroma Anda</p>
        </div>
        
        <?php if ($error): ?>
        <div class="auth-error">
            <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
        </div>
        <?php endif; ?>
        
        <form method="POST" action="login.php" class="auth-form">
            <div class="form-group">
                <label for="email">Email</label>
                <div class="auth-input-wrap">
                    <i class="fas fa-envelope"></i>
                    <input type="email" id="email" name="email" placeholder="nama@email.com" required 
                           value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                </div>
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <div class="auth-input-wrap">
                    <i class="fas fa-lock"></i>
                    <input type="password" id="password" name="password" placeholder="Masukkan password" required>
                    <i class="fas fa-eye toggle-pass" onclick="togglePassword('password', this)"></i>
                </div>
            </div>
            
            <div class="auth-options">
                <label class="remember-me">
                    <input type="checkbox" name="remember"> Ingat saya
                </label>
                <a href="forgot-password.php" class="forgot-link">Lupa password?</a>
            </div>
            
            <button type="submit" class="btn btn-auth">
                <i class="fas fa-sign-in-alt"></i> Masuk
            </button>
        </form>
        
        <div class="auth-divider">
            <span>atau</span>
        </div>
        
        <div class="auth-demo">
            <p><i class="fas fa-info-circle"></i> Demo Login:</p>
            <div class="demo-buttons">
                <button type="button" onclick="fillLogin('customer@email.com', 'password123')" class="btn btn-demo">
                    Customer
                </button>
                <button type="button" onclick="fillLogin('admin@lumier.com', 'admin123')" class="btn btn-demo">
                    Admin
                </button>
            </div>
        </div>
        
        <p class="auth-footer">
            Belum punya akun? <a href="register.php">Daftar sekarang</a>
        </p>
    </div>
</section>

<script>
function togglePassword(id, icon) {
    const input = document.getElementById(id);
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}

function fillLogin(email, pass) {
    document.getElementById('email').value = email;
    document.getElementById('password').value = pass;
}
</script>

</body>
</html>