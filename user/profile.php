<?php
// user/profile.php - Profil, Password & Alamat
require_once '../config/database.php';
require_once '../includes/functions.php';

session_start();
if (!isset($_SESSION['user_id'])) { header('Location: ../login.php'); exit; }

$userId  = $_SESSION['user_id'];
$message = '';
$error   = '';

// Ambil data user dari DB
$stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt->bind_param("i", $userId); $stmt->execute();
$userData = $stmt->get_result()->fetch_assoc();

// ── UPDATE PROFIL ──────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profil'])) {
    $nama    = trim($_POST['nama'] ?? '');
    $email   = trim($_POST['email'] ?? '');
    $telepon = trim($_POST['telepon'] ?? '');
    $stmt = $conn->prepare("UPDATE users SET nama=?, email=?, telepon=? WHERE user_id=?");
    $stmt->bind_param("sssi", $nama, $email, $telepon, $userId);
    if ($stmt->execute()) {
        $_SESSION['nama'] = $nama;
        $userData['nama'] = $nama; $userData['email'] = $email; $userData['telepon'] = $telepon;
        $message = 'Profil berhasil diperbarui.';
    } else { $error = 'Gagal memperbarui profil.'; }
}

// ── UPDATE PASSWORD ────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_password'])) {
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

// ── TAMBAH ALAMAT ──────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tambah_alamat'])) {
    $label   = trim($_POST['label'] ?? 'Rumah');
    $nama    = trim($_POST['nama_penerima'] ?? $userData['nama']);
    $telepon = trim($_POST['telepon_penerima'] ?? '');
    $alamat  = trim($_POST['alamat'] ?? '');
    $kota    = trim($_POST['kota'] ?? '');
    $kecamatan = trim($_POST['kecamatan'] ?? '');
    $kodepos = trim($_POST['kodepos'] ?? '');
    $default = isset($_POST['is_default']) ? 1 : 0;
    if ($default) $conn->query("UPDATE addresses SET is_default=0 WHERE user_id=$userId");
    $stmt = $conn->prepare("INSERT INTO addresses (user_id, label, nama_penerima, telepon, alamat_lengkap, kota, kecamatan, kodepos, is_default) VALUES (?,?,?,?,?,?,?,?,?)");
    $stmt->bind_param("isssssssi", $userId, $label, $nama, $telepon, $alamat, $kota, $kecamatan, $kodepos, $default);
    $stmt->execute();
    $message = 'Alamat berhasil ditambahkan.';
}

// ── HAPUS ALAMAT ───────────────────────────────────────────
if (isset($_GET['hapus_alamat'])) {
    $aid = (int)$_GET['hapus_alamat'];
    $stmt = $conn->prepare("DELETE FROM addresses WHERE address_id=? AND user_id=?");
    $stmt->bind_param("ii", $aid, $userId); $stmt->execute();
    header('Location: profile.php?tab=alamat'); exit;
}

// ── AMBIL ALAMAT ───────────────────────────────────────────
$stmt = $conn->prepare("SELECT * FROM addresses WHERE user_id=? ORDER BY is_default DESC, created_at ASC");
$stmt->bind_param("i", $userId); $stmt->execute();
$alamatList = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$user = [
    'nama'         => $userData['nama'],
    'email'        => $userData['email'],
    'telepon'      => $userData['telepon'] ?? '',
    'avatar'       => strtoupper(substr($userData['nama'], 0, 2)),
    'member_since' => date('M Y', strtotime($userData['created_at'])),
];

$activeTab = $_GET['tab'] ?? 'profil';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Saya - Lumière Parfum</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

<?php include '../includes/header.php'; ?>

<section class="user-section">
    <div class="container">
        <div class="user-layout">
            
            <!-- Sidebar -->
            <aside class="user-sidebar">
                <div class="user-profile-mini">
                    <div class="mini-avatar"><?php echo $user['avatar']; ?></div>
                    <div class="mini-info">
                        <h4><?php echo $user['nama']; ?></h4>
                        <span>Member since <?php echo $user['member_since']; ?></span>
                    </div>
                </div>
                <nav class="user-nav">
                    <a href="dashboard.php"><i class="fas fa-th-large"></i> Dashboard</a>
                    <a href="orders.php"><i class="fas fa-shopping-bag"></i> Pesanan Saya</a>
                    <a href="wishlist.php"><i class="fas fa-heart"></i> Wishlist</a>
                    <a href="profile.php" class="active"><i class="fas fa-user"></i> Profil & Alamat</a>
                    <a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Keluar</a>
                </nav>
            </aside>
            
            <!-- Main Content -->
            <div class="user-content">
                
                <div class="content-header">
                    <h2>Pengaturan Akun</h2>
                    <p>Kelola profil, keamanan, dan alamat pengiriman Anda.</p>
                </div>
                
                <?php if ($message): ?>
                <div class="auth-success" style="margin-bottom:16px;"><i class="fas fa-check-circle"></i> <?php echo $message; ?></div>
                <?php endif; ?>
                <?php if ($error): ?>
                <div class="auth-error" style="margin-bottom:16px;"><i class="fas fa-exclamation-circle"></i> <?php echo $error; ?></div>
                <?php endif; ?>

                <!-- Tabs -->
                <div class="profile-tabs">
                    <a href="?tab=profil" class="profile-tab <?php echo $activeTab === 'profil' ? 'active' : ''; ?>">
                        <i class="fas fa-user"></i> Profil
                    </a>
                    <a href="?tab=password" class="profile-tab <?php echo $activeTab === 'password' ? 'active' : ''; ?>">
                        <i class="fas fa-lock"></i> Password
                    </a>
                    <a href="?tab=alamat" class="profile-tab <?php echo $activeTab === 'alamat' ? 'active' : ''; ?>">
                        <i class="fas fa-map-marker-alt"></i> Alamat
                    </a>
                </div>
                
                <!-- Tab Content -->
                <div class="tab-content-wrapper">
                    
                    <?php if ($activeTab === 'profil'): ?>
                    <div class="user-card">
                        <div class="card-header"><h3>Edit Profil</h3></div>
                        <form method="POST" class="profile-form">
                            <input type="hidden" name="update_profil" value="1">
                            <div class="avatar-edit">
                                <div class="edit-avatar"><?php echo $user['avatar']; ?></div>
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label>Nama Lengkap</label>
                                    <input type="text" name="nama" value="<?php echo htmlspecialchars($user['nama']); ?>" required>
                                </div>
                                <div class="form-group">
                                    <label>Email</label>
                                    <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Nomor Telepon</label>
                                <input type="tel" name="telepon" value="<?php echo htmlspecialchars($user['telepon']); ?>">
                            </div>
                            <button type="submit" class="btn btn-save">Simpan Perubahan</button>
                        </form>
                    </div>
                    <?php endif; ?>
                    
                    <?php if ($activeTab === 'password'): ?>
                    <div class="user-card">
                        <div class="card-header"><h3>Ubah Password</h3></div>
                        <form method="POST" class="profile-form">
                            <input type="hidden" name="update_password" value="1">
                            <div class="form-group">
                                <label>Password Saat Ini</label>
                                <div class="password-wrap">
                                    <input type="password" name="old_password" placeholder="Masukkan password lama" required>
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
                            <div class="password-hint">
                                <p><i class="fas fa-info-circle"></i> Gunakan kombinasi huruf, angka, dan simbol.</p>
                            </div>
                            <button type="submit" class="btn btn-save">Update Password</button>
                        </form>
                    </div>
                    <?php endif; ?>
                    
                    <?php if ($activeTab === 'alamat'): ?>
                    <div class="user-card">
                        <div class="card-header">
                            <h3>Alamat Pengiriman</h3>
                            <button type="button" class="btn btn-sm btn-gold" onclick="toggleAddressForm()">
                                <i class="fas fa-plus"></i> Tambah Alamat
                            </button>
                        </div>

                        <div id="addressForm" class="address-form-wrapper" style="display:none;">
                            <form method="POST" class="profile-form">
                                <input type="hidden" name="tambah_alamat" value="1">
                                <div class="form-row">
                                    <div class="form-group">
                                        <label>Label Alamat</label>
                                        <input type="text" name="label" placeholder="Contoh: Rumah, Kantor" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Kode Pos</label>
                                        <input type="text" name="kodepos" placeholder="12345" required>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group">
                                        <label>Nama Penerima</label>
                                        <input type="text" name="nama_penerima" value="<?php echo htmlspecialchars($user['nama']); ?>" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Telepon</label>
                                        <input type="text" name="telepon_penerima" value="<?php echo htmlspecialchars($user['telepon']); ?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Alamat Lengkap</label>
                                    <textarea name="alamat" rows="3" placeholder="Jalan, RT/RW, Kelurahan, Kecamatan" required></textarea>
                                </div>
                                <div class="form-row">
                                    <div class="form-group">
                                        <label>Kota / Kabupaten</label>
                                        <input type="text" name="kota" placeholder="Jakarta Selatan" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Kecamatan</label>
                                        <input type="text" name="kecamatan" placeholder="Kebayoran Baru">
                                    </div>
                                </div>
                                <div class="form-group checkbox-group">
                                    <label class="checkbox-label">
                                        <input type="checkbox" name="is_default"> Jadikan alamat utama
                                    </label>
                                </div>
                                <div class="form-actions">
                                    <button type="button" class="btn btn-cancel" onclick="toggleAddressForm()">Batal</button>
                                    <button type="submit" class="btn btn-save">Simpan Alamat</button>
                                </div>
                            </form>
                        </div>

                        <div class="address-list">
                            <?php if (empty($alamatList)): ?>
                                <p style="color:#999;padding:16px 0;">Belum ada alamat tersimpan.</p>
                            <?php else: foreach ($alamatList as $addr): ?>
                            <div class="address-item <?php echo $addr['is_default'] ? 'address-default' : ''; ?>">
                                <div class="address-content">
                                    <div class="address-header">
                                        <h4><?php echo htmlspecialchars($addr['label']); ?></h4>
                                        <?php if ($addr['is_default']): ?><span class="default-badge">Utama</span><?php endif; ?>
                                    </div>
                                    <p><strong><?php echo htmlspecialchars($addr['nama_penerima']); ?></strong> • <?php echo htmlspecialchars($addr['telepon']); ?></p>
                                    <p><?php echo htmlspecialchars($addr['alamat_lengkap']); ?></p>
                                    <p><?php echo htmlspecialchars($addr['kota']); ?><?php echo $addr['kecamatan'] ? ', '.$addr['kecamatan'] : ''; ?>, <?php echo htmlspecialchars($addr['kodepos']); ?></p>
                                </div>
                                <div class="address-actions">
                                    <?php if (!$addr['is_default']): ?>
                                    <a href="?hapus_alamat=<?php echo $addr['address_id']; ?>&tab=alamat" class="action-delete" title="Hapus" onclick="return confirm('Hapus alamat ini?')"><i class="fas fa-trash"></i></a>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php endforeach; endif; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                </div>
                
            </div>
        </div>
    </div>
</section>

<?php include '../includes/footer.php'; ?>

<script>
function toggleAddressForm() {
    const form = document.getElementById('addressForm');
    form.style.display = form.style.display === 'none' ? 'block' : 'none';
}

// Toggle show/hide password
document.querySelectorAll('.toggle-pass').forEach(icon => {
    icon.addEventListener('click', function() {
        const input = this.previousElementSibling;
        if (input.type === 'password') {
            input.type = 'text';
            this.classList.remove('fa-eye');
            this.classList.add('fa-eye-slash');
        } else {
            input.type = 'password';
            this.classList.remove('fa-eye-slash');
            this.classList.add('fa-eye');
        }
    });
});
</script>

</body>
</html>