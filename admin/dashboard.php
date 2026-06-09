<?php
require_once '../config/database.php';
require_once '../includes/functions.php';
session_start();

// Cek admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

// Koneksi DB
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Check if tables exist
$tablesExist = tableExists('orders') && tableExists('products') && tableExists('users');

$stats = [
    'penjualan' => 0,
    'orders' => 0,
    'produk' => 0,
    'users' => 0,
];

$penjualanBulanan = [];
$produkTerlaris = [];
$recentOrders = [];

if ($tablesExist) {
    // FIXED: Calculate total as subtotal + ongkir + cod_fee - diskon
    $stats['penjualan'] = $conn->query("SELECT COALESCE(SUM(subtotal + ongkir + cod_fee - COALESCE(diskon, 0)), 0) FROM orders WHERE status = 'selesai'")->fetch_row()[0];
    $stats['orders'] = $conn->query("SELECT COUNT(*) FROM orders")->fetch_row()[0];
    $stats['produk'] = $conn->query("SELECT COUNT(*) FROM products WHERE status = 'aktif'")->fetch_row()[0];
    $stats['users'] = $conn->query("SELECT COUNT(*) FROM users WHERE role = 'customer'")->fetch_row()[0];

    // Penjualan bulanan - semua 12 bulan
    $result = $conn->query("
        SELECT MONTH(created_at) as bln, SUM(subtotal + ongkir + cod_fee - COALESCE(diskon, 0)) as nilai 
        FROM orders 
        WHERE status = 'selesai' AND YEAR(created_at) = YEAR(CURDATE())
        GROUP BY MONTH(created_at)
    ");
    $dataPerBulan = array_fill(1, 12, 0);
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $dataPerBulan[(int)$row['bln']] = (float)$row['nilai'];
        }
    }
    $bulanNames = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
    foreach ($bulanNames as $i => $nama) {
        $penjualanBulanan[] = ['bulan' => $nama, 'nilai' => $dataPerBulan[$i + 1]];
    }

    // Produk terlaris
    $result = $conn->query("
        SELECT p.nama_produk, p.total_terjual, (p.harga * p.total_terjual) as pendapatan
        FROM products p
        ORDER BY p.total_terjual DESC
        LIMIT 5
    ");
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $produkTerlaris[] = $row;
        }
    }

    // Pesanan terbaru - FIXED total calculation
    $result = $conn->query("
        SELECT o.order_code, u.nama, o.created_at, (o.subtotal + o.ongkir + o.cod_fee - COALESCE(o.diskon, 0)) as total, o.status
        FROM orders o
        JOIN users u ON o.user_id = u.user_id
        ORDER BY o.created_at DESC
        LIMIT 5
    ");
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $recentOrders[] = $row;
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - Lumière Parfum</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="admin-body">

<?php include 'includes/admin-sidebar.php'; ?>

<main class="admin-main">
    <div class="admin-header">
        <h1>Dashboard</h1>
        <div class="admin-user">
            <span><?php echo $_SESSION['nama'] ?? 'Admin'; ?></span>
            <div class="admin-avatar"><?php echo strtoupper(substr($_SESSION['nama'] ?? 'A', 0, 1)); ?></div>
        </div>
    </div>

    <?php if (!$tablesExist): ?>
    <div class="auth-error" style="margin-bottom:20px;">
        <i class="fas fa-exclamation-circle"></i> 
        Database belum di-setup. Silakan import file <code>lumier_parfum.sql</code> terlebih dahulu.
    </div>
    <?php endif; ?>

    <div class="admin-stat-grid">
        <div class="admin-stat-card stat-sales">
            <div class="stat-icon-wrap"><i class="fas fa-wallet"></i></div>
            <div><h3><?php echo formatRupiah($stats['penjualan']); ?></h3><span>Total Penjualan</span></div>
        </div>
        <div class="admin-stat-card stat-orders">
            <div class="stat-icon-wrap"><i class="fas fa-shopping-bag"></i></div>
            <div><h3><?php echo $stats['orders']; ?></h3><span>Total Pesanan</span></div>
        </div>
        <div class="admin-stat-card stat-products">
            <div class="stat-icon-wrap"><i class="fas fa-box"></i></div>
            <div><h3><?php echo $stats['produk']; ?></h3><span>Produk Aktif</span></div>
        </div>
        <div class="admin-stat-card stat-users">
            <div class="stat-icon-wrap"><i class="fas fa-users"></i></div>
            <div><h3><?php echo $stats['users']; ?></h3><span>Pelanggan</span></div>
        </div>
    </div>

    <div class="admin-grid-2">
        <div class="admin-card">
            <div class="admin-card-header"><h3>Laporan Penjualan</h3></div>
            <div style="padding:16px 8px;">
                <canvas id="salesChart" height="110"></canvas>
            </div>
        </div>

        <div class="admin-card">
            <div class="admin-card-header"><h3>Produk Terlaris</h3></div>
            <div class="top-products">
                <?php foreach ($produkTerlaris as $i => $p): ?>
                <div class="top-item">
                    <span class="top-rank"><?php echo $i+1; ?></span>
                    <div class="top-info"><h4><?php echo $p['nama_produk']; ?></h4><span><?php echo $p['total_terjual']; ?> terjual</span></div>
                    <span class="top-revenue"><?php echo formatRupiah($p['pendapatan']); ?></span>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <div class="admin-card">
        <div class="admin-card-header">
            <h3>Pesanan Terbaru</h3>
            <a href="orders.php" class="admin-link">Lihat Semua</a>
        </div>
        <div class="table-responsive">
            <table class="admin-table">
                <thead><tr><th>Kode</th><th>Pelanggan</th><th>Tanggal</th><th>Total</th><th>Status</th></tr></thead>
                <tbody>
                    <?php foreach ($recentOrders as $o): ?>
                    <tr>
                        <td><strong><?php echo $o['order_code']; ?></strong></td>
                        <td><?php echo $o['nama']; ?></td>
                        <td><?php echo date('d M Y', strtotime($o['created_at'])); ?></td>
                        <td><?php echo formatRupiah($o['total']); ?></td>
                        <td><span class="status-badge status-<?php echo $o['status']; ?>"><?php echo ucfirst($o['status']); ?></span></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
const labels = <?php echo json_encode(array_column($penjualanBulanan, 'bulan')); ?>;
const values = <?php echo json_encode(array_map('floatval', array_column($penjualanBulanan, 'nilai'))); ?>;

const ctx = document.getElementById('salesChart').getContext('2d');
const colors = [
    '#4e79a7','#f28e2b','#e15759','#76b7b2','#59a14f',
    '#edc948','#b07aa1','#ff9da7','#9c755f','#bab0ac',
    '#c9a84c','#6b8cba'
];

new Chart(ctx, {
    type: 'bar',
    data: {
        labels,
        datasets: [{
            label: 'Penjualan',
            data: values,
            backgroundColor: colors,
            borderColor: colors,
            borderWidth: 0,
            borderRadius: 6,
            borderSkipped: false,
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { display: false },
            tooltip: {
                callbacks: {
                    label: v => 'Rp ' + v.raw.toLocaleString('id-ID')
                }
            }
        },
        scales: {
            x: { grid: { display: false }, border: { display: false } },
            y: {
                grid: { color: '#f0f0f0' },
                border: { display: false },
                ticks: {
                    callback: v => 'Rp ' + (v >= 1e6 ? (v/1e6).toFixed(1)+'jt' : v.toLocaleString('id-ID')),
                    padding: 10
                },
                afterFit(axis) { axis.width = 90; }
            }
        }
    }
});
</script>
</body>
</html>