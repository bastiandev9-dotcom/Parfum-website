<?php
require_once '../config/database.php';
require_once '../includes/functions.php';
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') { header('Location: ../login.php'); exit; }

// Filter bulan/tahun
$bulan = (int)($_GET['bulan'] ?? date('m'));
$tahun = (int)($_GET['tahun'] ?? date('Y'));

// === EXPORT CSV ===
if (isset($_GET['export']) && $_GET['export'] === 'csv') {
    $filename = 'laporan-' . date('Y', mktime(0,0,0,$bulan,1,$tahun)) . '-' . str_pad($bulan, 2, '0', STR_PAD_LEFT) . '.csv';
    header('Content-Type: text/csv; charset=UTF-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    echo "\xEF\xBB\xBF"; // BOM UTF-8
    $out = fopen('php://output', 'w');
    fputcsv($out, ['Kode Pesanan', 'Tanggal', 'Pelanggan', 'Total (Rp)', 'Status', 'Metode Bayar', 'Kurir']);
    $rows = $conn->query("SELECT o.order_code, o.created_at, u.nama, o.total, o.status, o.metode_bayar, o.kurir
        FROM orders o JOIN users u ON o.user_id=u.user_id
        WHERE MONTH(o.created_at)=$bulan AND YEAR(o.created_at)=$tahun
        ORDER BY o.created_at DESC");
    while ($row = $rows->fetch_assoc()) {
        fputcsv($out, [
            $row['order_code'],
            date('d/m/Y H:i', strtotime($row['created_at'])),
            $row['nama'],
            $row['total'],
            ucfirst($row['status']),
            strtoupper(str_replace('_', ' ', $row['metode_bayar'])),
            strtoupper($row['kurir']),
        ]);
    }
    fclose($out);
    exit;
}

// Summary
$summary = $conn->query("
    SELECT COUNT(*) as total_order,
           SUM(total) as total_penjualan,
           SUM(CASE WHEN status='selesai' THEN total ELSE 0 END) as revenue,
           SUM(CASE WHEN status='batal' THEN 1 ELSE 0 END) as dibatal
    FROM orders
    WHERE MONTH(created_at)=$bulan AND YEAR(created_at)=$tahun
")->fetch_assoc();

// Harian
$harian = $conn->query("
    SELECT DAY(created_at) as hari,
           COUNT(*) as total_order,
           SUM(CASE WHEN status='selesai' THEN total ELSE 0 END) as revenue
    FROM orders
    WHERE MONTH(created_at)=$bulan AND YEAR(created_at)=$tahun
    GROUP BY DAY(created_at) ORDER BY hari
")->fetch_all(MYSQLI_ASSOC);

// Produk terlaris bulan ini
$produkTop = $conn->query("
    SELECT oi.nama_produk, SUM(oi.qty) as total_terjual, SUM(oi.subtotal) as pendapatan
    FROM order_items oi JOIN orders o ON oi.order_id=o.order_id
    WHERE MONTH(o.created_at)=$bulan AND YEAR(o.created_at)=$tahun AND o.status='selesai'
    GROUP BY oi.nama_produk ORDER BY total_terjual DESC LIMIT 10
")->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html><html lang="id"><head><meta charset="UTF-8"><title>Laporan - Admin</title>
<link rel="stylesheet" href="../assets/css/style.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head><body class="admin-body">
<?php include 'includes/admin-sidebar.php'; ?>
<main class="admin-main">
    <div class="admin-header">
        <h1>Laporan Penjualan</h1>
        <div style="display:flex;gap:8px;align-items:center;flex-wrap:wrap;">
        <form method="GET" style="display:flex;gap:8px;">
            <select name="bulan" style="padding:8px;border:1px solid #ddd;border-radius:8px;">
                <?php for ($m=1;$m<=12;$m++): ?>
                <option value="<?php echo $m; ?>" <?php echo $m===$bulan?'selected':''; ?>><?php echo date('F', mktime(0,0,0,$m,1)); ?></option>
                <?php endfor; ?>
            </select>
            <select name="tahun" style="padding:8px;border:1px solid #ddd;border-radius:8px;">
                <?php for ($y=date('Y');$y>=2024;$y--): ?>
                <option value="<?php echo $y; ?>" <?php echo $y===$tahun?'selected':''; ?>><?php echo $y; ?></option>
                <?php endfor; ?>
            </select>
            <button type="submit" class="btn" style="padding:8px 16px;">Filter</button>
        </form>
        <a href="?bulan=<?php echo $bulan; ?>&tahun=<?php echo $tahun; ?>&export=csv" class="btn btn-outline" style="border-color:#27ae60;color:#27ae60;padding:8px 16px;"><i class="fas fa-file-csv"></i> Export CSV</a>
        </div>
    </div>

    <div class="admin-stat-grid">
        <div class="admin-stat-card stat-sales"><div class="stat-icon-wrap"><i class="fas fa-wallet"></i></div><div><h3><?php echo formatRupiah($summary['revenue'] ?? 0); ?></h3><span>Revenue Selesai</span></div></div>
        <div class="admin-stat-card stat-orders"><div class="stat-icon-wrap"><i class="fas fa-shopping-bag"></i></div><div><h3><?php echo $summary['total_order'] ?? 0; ?></h3><span>Total Pesanan</span></div></div>
        <div class="admin-stat-card stat-products"><div class="stat-icon-wrap"><i class="fas fa-times-circle"></i></div><div><h3><?php echo $summary['dibatal'] ?? 0; ?></h3><span>Dibatalkan</span></div></div>
        <div class="admin-stat-card stat-users"><div class="stat-icon-wrap"><i class="fas fa-chart-line"></i></div><div><h3><?php echo formatRupiah($summary['total_penjualan'] ?? 0); ?></h3><span>Total Nilai</span></div></div>
    </div>

    <div class="admin-grid-2">
        <div class="admin-card">
            <div class="admin-card-header"><h3>Grafik Harian</h3></div>
            <div style="padding:16px;"><canvas id="harianChart" height="120"></canvas></div>
        </div>
        <div class="admin-card">
            <div class="admin-card-header"><h3>Produk Terlaris</h3></div>
            <div class="table-responsive">
                <table class="admin-table">
                    <thead><tr><th>#</th><th>Produk</th><th>Terjual</th><th>Pendapatan</th></tr></thead>
                    <tbody>
                    <?php foreach ($produkTop as $i => $p): ?>
                    <tr><td><?php echo $i+1; ?></td><td><?php echo htmlspecialchars($p['nama_produk']); ?></td><td><?php echo $p['total_terjual']; ?></td><td><?php echo formatRupiah($p['pendapatan']); ?></td></tr>
                    <?php endforeach; ?>
                    <?php if (empty($produkTop)): ?><tr><td colspan="4" style="text-align:center;color:#999;">Belum ada data.</td></tr><?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
const labels = <?php echo json_encode(array_column($harian, 'hari')); ?>;
const values = <?php echo json_encode(array_map('floatval', array_column($harian, 'revenue'))); ?>;
new Chart(document.getElementById('harianChart').getContext('2d'), {
    type: 'line',
    data: { labels, datasets: [{ label: 'Revenue', data: values, borderColor: '#C9A962', backgroundColor: 'rgba(201,169,98,.1)', fill: true, tension: .3 }] },
    options: { responsive: true, plugins: { legend: { display: false } }, scales: { y: { ticks: { callback: v => 'Rp ' + (v/1e6).toFixed(1) + 'jt' } } } }
});
</script>
</body></html>
