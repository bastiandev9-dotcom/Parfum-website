<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$user    = [];
$orders  = [];
$statusCount = ['pending' => 0, 'diproses' => 0, 'dikirim' => 0, 'selesai' => 0];

// Get user from DB
$stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$userData = $stmt->get_result()->fetch_assoc();

if ($userData) {
    $user = [
        'nama'         => $userData['nama'],
        'email'        => $userData['email'],
        'telepon'      => $userData['telepon'] ?? '',
        'avatar'       => strtoupper(substr($userData['nama'], 0, 2)),
        'member_since' => date('M Y', strtotime($userData['created_at'])),
    ];
}

// Ambil alamat utama dari tabel addresses
$alamatUtama = null;
$stmtAddr = $conn->prepare("SELECT * FROM addresses WHERE user_id=? AND is_default=1 LIMIT 1");
$stmtAddr->bind_param("i", $user_id);
$stmtAddr->execute();
$alamatUtama = $stmtAddr->get_result()->fetch_assoc();

// Get orders
$stmt2 = $conn->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
$stmt2->bind_param("i", $user_id);
$stmt2->execute();
$result = $stmt2->get_result();
while ($row = $result->fetch_assoc()) {
    $orders[] = $row;
    if (isset($statusCount[$row['status']])) $statusCount[$row['status']]++;
}

$totalOrders = $statusCount['pending'] + $statusCount['diproses'] + $statusCount['dikirim'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Lumière Parfum</title>
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
                    <a href="dashboard.php" class="active">
                        <i class="fas fa-th-large"></i> Dashboard
                    </a>
                    <a href="orders.php">
                        <i class="fas fa-shopping-bag"></i> Pesanan Saya
                        <?php if ($totalOrders > 0): ?>
                            <span class="nav-badge" id="badge-aktif"><?php echo $totalOrders; ?></span>
                        <?php else: ?>
                            <span class="nav-badge" id="badge-aktif" style="display:none">0</span>
                        <?php endif; ?>
                    </a>
                    <a href="profile.php">
                        <i class="fas fa-user"></i> Profil & Alamat
                    </a>
                    <a href="../logout.php">
                        <i class="fas fa-sign-out-alt"></i> Keluar
                    </a>
                </nav>
            </aside>
            
            <!-- Main Content -->
            <div class="user-content">
                
                <div class="content-header">
                    <h2>Selamat Datang, <?php echo explode(' ', $user['nama'])[0]; ?>!</h2>
                    <p>Ini ringkasan akun Anda hari ini.</p>
                </div>
                
                <!-- Stat Cards -->
                <div class="stat-grid">
                    <div class="stat-card stat-pending">
                        <div class="stat-icon"><i class="fas fa-clock"></i></div>
                        <div class="stat-info">
                            <h3 id="stat-pending"><?php echo $statusCount['pending']; ?></h3>
                            <span>Pending</span>
                        </div>
                    </div>
                    <div class="stat-card stat-process">
                        <div class="stat-icon"><i class="fas fa-box"></i></div>
                        <div class="stat-info">
                            <h3 id="stat-diproses"><?php echo $statusCount['diproses']; ?></h3>
                            <span>Diproses</span>
                        </div>
                    </div>
                    <div class="stat-card stat-shipped">
                        <div class="stat-icon"><i class="fas fa-shipping-fast"></i></div>
                        <div class="stat-info">
                            <h3 id="stat-dikirim"><?php echo $statusCount['dikirim']; ?></h3>
                            <span>Dikirim</span>
                        </div>
                    </div>
                    <div class="stat-card stat-done">
                        <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
                        <div class="stat-info">
                            <h3 id="stat-selesai"><?php echo $statusCount['selesai']; ?></h3>
                            <span>Selesai</span>
                        </div>
                    </div>
                </div>
                
                <!-- Recent Orders -->
                <div class="user-card">
                    <div class="card-header">
                        <h3>Pesanan Terbaru</h3>
                        <a href="orders.php" class="link-gold">Lihat Semua</a>
                    </div>
                    
                    <?php if (!empty($orders)): ?>
                    <div class="order-table-wrap">
                        <table class="order-table">
                            <thead>
                                <tr>
                                    <th>ID Pesanan</th>
                                    <th>Tanggal</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach (array_slice($orders, 0, 3) as $order): ?>
                                <tr>
                                    <td><strong><?php echo $order['order_code']; ?></strong></td>
                                    <td><?php echo date('d M Y', strtotime($order['created_at'])); ?></td>
                                    <td><?php echo formatRupiah($order['total']); ?></td>
                                    <td>
                                        <span class="status-badge status-<?php echo $order['status']; ?>">
                                            <?php echo ucfirst($order['status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="orders.php?id=<?php echo $order['order_id']; ?>" class="btn-table">
                                            Detail
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php else: ?>
                    <div class="empty-mini">
                        <p>Belum ada pesanan. <a href="../products.php">Belanja sekarang</a></p>
                    </div>
                    <?php endif; ?>
                </div>
                
                <!-- Quick Info -->
                <div class="info-grid">
                    <div class="user-card">
                        <div class="card-header">
                            <h3>Alamat Utama</h3>
                            <a href="profile.php?tab=alamat" class="link-gold">Ubah</a>
                        </div>
                        <div class="info-body">
                            <?php if ($alamatUtama): ?>
                            <p><i class="fas fa-map-marker-alt"></i> <strong><?php echo htmlspecialchars($alamatUtama['nama_penerima']); ?></strong></p>
                            <p><?php echo htmlspecialchars($alamatUtama['alamat_lengkap']); ?></p>
                            <p><?php echo htmlspecialchars($alamatUtama['kota']); ?>, <?php echo htmlspecialchars($alamatUtama['kodepos']); ?></p>
                            <?php else: ?>
                            <p style="color:#aaa;"><i class="fas fa-map-marker-alt"></i> Belum ada alamat tersimpan.</p>
                            <a href="profile.php?tab=alamat" class="link-gold" style="font-size:.85rem;">+ Tambah Alamat</a>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="user-card">
                        <div class="card-header">
                            <h3>Profil Singkat</h3>
                            <a href="profile.php" class="link-gold">Edit</a>
                        </div>
                        <div class="info-body">
                            <p><i class="fas fa-envelope"></i> <?php echo $user['email']; ?></p>
                            <p><i class="fas fa-phone"></i> <?php echo $user['telepon'] ?: '-'; ?></p>
                        </div>
                    </div>
                </div>
                
            </div>
        </div>
    </div>
</section>

<?php include '../includes/footer.php'; ?>

<script>
function updateStats(d) {
    ['pending','diproses','dikirim','selesai'].forEach(s => {
        const el = document.getElementById('stat-'+s);
        if (el) el.textContent = d[s];
    });
    const badge = document.getElementById('badge-aktif');
    if (badge) {
        badge.textContent = d.aktif;
        badge.style.display = d.aktif > 0 ? '' : 'none';
    }
}
document.addEventListener('visibilitychange', () => {
    if (!document.hidden) fetch('order-stats.php').then(r => r.json()).then(updateStats);
});
</script>
</body>
</html>