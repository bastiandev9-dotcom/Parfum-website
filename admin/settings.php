<?php
require_once '../config/database.php';
require_once '../includes/functions.php';
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') { header('Location: ../login.php'); exit; }

$message = $error = '';

// Buat tabel settings jika belum ada
$conn->query("CREATE TABLE IF NOT EXISTS `settings` (
    `s_key` varchar(100) NOT NULL PRIMARY KEY,
    `value` text DEFAULT NULL,
    `label` varchar(200) DEFAULT NULL,
    `updated_at` timestamp DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

// Default settings
$defaults = [
    'store_name'    => ['label'=>'Nama Toko',        'default'=>'Lumière Parfum'],
    'store_email'   => ['label'=>'Email Toko',        'default'=>'info@lumiereparfum.com'],
    'store_phone'   => ['label'=>'Nomor Telepon',     'default'=>'+62 812-3456-7890'],
    'store_address' => ['label'=>'Alamat Toko',       'default'=>'Jl. Parfum Indah No. 1, Jakarta'],
    'store_whatsapp'=> ['label'=>'WhatsApp',          'default'=>'6281234567890'],
    'store_instagram'=>['label'=>'Instagram',         'default'=>'@lumiereparfum'],
    'bank_bca'      => ['label'=>'No. Rekening BCA',  'default'=>'1234567890'],
    'bank_bni'      => ['label'=>'No. Rekening BNI',  'default'=>'0987654321'],
    'bank_mandiri'  => ['label'=>'No. Rekening Mandiri','default'=>'1122334455'],
    'cod_fee'       => ['label'=>'Biaya COD (Rp)',    'default'=>'5000'],
];

// Insert defaults jika belum ada
foreach ($defaults as $key => $d) {
    $conn->query("INSERT IGNORE INTO settings (`s_key`,`value`,`label`) VALUES ('$key','{$d['default']}','{$d['label']}')");
}

// Save
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($_POST as $key => $val) {
        if (array_key_exists($key, $defaults)) {
            $val = $conn->real_escape_string(trim($val));
            $conn->query("UPDATE settings SET `value`='$val' WHERE `s_key`='$key'");
        }
    }
    $message = 'Pengaturan berhasil disimpan.';
}

$settings = [];
$res = $conn->query("SELECT * FROM settings");
while ($row = $res->fetch_assoc()) $settings[$row['s_key']] = $row['value'];
?>
<!DOCTYPE html><html lang="id"><head><meta charset="UTF-8"><title>Pengaturan - Admin</title>
<link rel="stylesheet" href="../assets/css/style.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head><body class="admin-body">
<?php include 'includes/admin-sidebar.php'; ?>
<main class="admin-main">
    <div class="admin-header"><h1>Pengaturan Toko</h1></div>
    <?php if ($message): ?><div class="auth-success" style="margin-bottom:16px;"><i class="fas fa-check-circle"></i> <?php echo $message; ?></div><?php endif; ?>

    <form method="POST">
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:24px;">
            <div class="admin-card">
                <div class="admin-card-header"><h3>Informasi Toko</h3></div>
                <div class="admin-form">
                    <?php
                    $infoKeys = ['store_name','store_email','store_phone','store_address','store_whatsapp','store_instagram'];
                    foreach ($infoKeys as $key): ?>
                    <div class="form-group">
                        <label><?php echo $defaults[$key]['label']; ?></label>
                        <input type="text" name="<?php echo $key; ?>" value="<?php echo htmlspecialchars($settings[$key] ?? $defaults[$key]['default']); ?>">
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="admin-card">
                <div class="admin-card-header"><h3>Pembayaran</h3></div>
                <div class="admin-form">
                    <?php
                    $bayarKeys = ['bank_bca','bank_bni','bank_mandiri','cod_fee'];
                    foreach ($bayarKeys as $key): ?>
                    <div class="form-group">
                        <label><?php echo $defaults[$key]['label']; ?></label>
                        <input type="text" name="<?php echo $key; ?>" value="<?php echo htmlspecialchars($settings[$key] ?? $defaults[$key]['default']); ?>">
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <div style="margin-top:20px;">
            <button type="submit" class="btn btn-save-admin"><i class="fas fa-save"></i> Simpan Pengaturan</button>
        </div>
    </form>
</main>
</body></html>
