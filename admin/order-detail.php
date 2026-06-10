<?php
require_once '../config/database.php';
require_once '../includes/functions.php';
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') { header('Location: ../login.php'); exit; }

$orderId = (int)($_GET['id'] ?? 0);
if (!$orderId) { header('Location: orders.php'); exit; }

$stmt = $conn->prepare("SELECT o.*, u.nama, u.email, u.telepon FROM orders o JOIN users u ON o.user_id=u.user_id WHERE o.order_id=?");
$stmt->bind_param("i", $orderId); $stmt->execute();
$order = $stmt->get_result()->fetch_assoc();
if (!$order) { header('Location: orders.php'); exit; }

$itemsStmt = $conn->prepare("SELECT oi.*, p.gambar_utama FROM order_items oi LEFT JOIN products p ON oi.product_id=p.product_id WHERE oi.order_id=?");
$itemsStmt->bind_param("i", $orderId); $itemsStmt->execute();
$items = $itemsStmt->get_result()->fetch_all(MYSQLI_ASSOC);

$logsStmt = $conn->prepare("SELECT l.*, u.nama FROM order_status_logs l LEFT JOIN users u ON l.changed_by=u.user_id WHERE l.order_id=? ORDER BY l.changed_at DESC");
$logsStmt->bind_param("i", $orderId); $logsStmt->execute();
$logs = $logsStmt->get_result()->fetch_all(MYSQLI_ASSOC);

$alamat = json_decode($order['alamat_snapshot'], true) ?? [];
$statusOptions = ['pending','diproses','dikirim','selesai','batal','refund'];

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $status = $_POST['status'];
    $resi   = trim($_POST['resi'] ?? '');
    $conn->prepare("UPDATE orders SET status=?, resi=? WHERE order_id=?")->bind_param("ssi",$status,$resi,$orderId) && true;
    $s = $conn->prepare("UPDATE orders SET status=?, resi=? WHERE order_id=?");
    $s->bind_param("ssi", $status, $resi, $orderId); $s->execute();
    $l = $conn->prepare("INSERT INTO order_status_logs (order_id, status_baru, changed_by) VALUES (?,?,?)");
    $l->bind_param("isi", $orderId, $status, $_SESSION['user_id']); $l->execute();
    $message = 'Status diperbarui.';
    header("Location: order-detail.php?id=$orderId&msg=1"); exit;
}
?>
<!DOCTYPE html><html lang="id"><head><meta charset="UTF-8"><title>Detail Pesanan - Admin</title>
<link rel="stylesheet" href="../assets/css/style.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head><body class="admin-body">
<?php include 'includes/admin-sidebar.php'; ?>
<main class="admin-main">
    <div class="admin-header">
        <div><a href="orders.php" style="color:#999;font-size:.85rem;"><i class="fas fa-arrow-left"></i> Kembali</a><h1><?php echo $order['order_code']; ?></h1></div>
        <span class="status-badge status-<?php echo $order['status']; ?>"><?php echo ucfirst($order['status']); ?></span>
    </div>

    <?php if (isset($_GET['msg'])): ?><div class="auth-success" style="margin-bottom:16px;">Status berhasil diperbarui.</div><?php endif; ?>

    <div style="display:grid;grid-template-columns:1fr 340px;gap:24px;">
        <div>
            <!-- Items -->
            <div class="admin-card" style="margin-bottom:24px;">
                <div class="admin-card-header"><h3>Item Pesanan</h3></div>
                <div class="table-responsive">
                    <table class="admin-table">
                        <thead><tr><th>Produk</th><th>Qty</th><th>Harga</th><th>Subtotal</th></tr></thead>
                        <tbody>
                        <?php foreach ($items as $item): ?>
                        <tr>
                            <td>
                                <?php if ($item['gambar_utama']): ?>
                                <img src="../<?php echo htmlspecialchars($item['gambar_utama']); ?>" alt="" style="width:40px;height:40px;object-fit:cover;border-radius:6px;margin-right:8px;vertical-align:middle;">
                                <?php endif; ?>
                                <?php echo htmlspecialchars($item['nama_produk']); ?>
                            </td>
                            <td><?php echo $item['qty']; ?></td>
                            <td><?php echo formatRupiah($item['harga_satuan']); ?></td>
                            <td><strong><?php echo formatRupiah($item['subtotal']); ?></strong></td>
                        </tr>
                        <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr><td colspan="3" style="text-align:right;">Subtotal</td><td><?php echo formatRupiah($order['subtotal']); ?></td></tr>
                            <tr><td colspan="3" style="text-align:right;">Ongkir (<?php echo strtoupper($order['kurir']); ?>)</td><td><?php echo formatRupiah($order['ongkir']); ?></td></tr>
                            <?php if ($order['diskon']): ?><tr><td colspan="3" style="text-align:right;color:green;">Diskon</td><td style="color:green;">-<?php echo formatRupiah($order['diskon']); ?></td></tr><?php endif; ?>
                            <tr style="font-weight:700;font-size:1.05rem;"><td colspan="3" style="text-align:right;">TOTAL</td><td style="color:var(--gold);"><?php echo formatRupiah($order['total']); ?></td></tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <!-- Riwayat status -->
            <div class="admin-card">
                <div class="admin-card-header"><h3>Riwayat Status</h3></div>
                <div style="padding:16px;">
                    <?php foreach ($logs as $log): ?>
                    <div style="display:flex;gap:12px;margin-bottom:14px;">
                        <div style="width:10px;height:10px;background:var(--gold);border-radius:50%;margin-top:5px;flex-shrink:0;"></div>
                        <div>
                            <strong><?php echo ucfirst($log['status_baru']); ?></strong>
                            <span style="margin-left:10px;font-size:.8rem;color:#999;"><?php echo date('d M Y H:i', strtotime($log['changed_at'])); ?></span>
                            <?php if ($log['nama']): ?><span style="margin-left:8px;font-size:.8rem;color:#888;">oleh <?php echo $log['nama']; ?></span><?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <div>
            <!-- Update status -->
            <div class="admin-card" style="margin-bottom:20px;">
                <div class="admin-card-header"><h3>Update Status</h3></div>
                <form method="POST" class="admin-form">
                    <input type="hidden" name="update_status" value="1">
                    <div class="form-group"><label>Status</label>
                        <select name="status">
                            <?php foreach ($statusOptions as $s): ?>
                            <option value="<?php echo $s; ?>" <?php echo $order['status']===$s?'selected':''; ?>><?php echo ucfirst($s); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group"><label>No. Resi</label><input type="text" name="resi" value="<?php echo htmlspecialchars($order['resi'] ?? ''); ?>" placeholder="Nomor resi pengiriman"></div>
                    <button type="submit" class="btn btn-save-admin" style="width:100%;"><i class="fas fa-save"></i> Simpan</button>
                </form>
            </div>

            <!-- Info pelanggan -->
            <div class="admin-card" style="margin-bottom:20px;">
                <div class="admin-card-header"><h3>Pelanggan</h3></div>
                <div style="padding:16px;font-size:.9rem;line-height:1.8;">
                    <p><strong><?php echo htmlspecialchars($order['nama']); ?></strong></p>
                    <p style="color:#666;"><?php echo htmlspecialchars($order['email']); ?></p>
                    <p style="color:#666;"><?php echo htmlspecialchars($order['telepon']); ?></p>
                </div>
            </div>

            <!-- Alamat -->
            <div class="admin-card">
                <div class="admin-card-header"><h3>Alamat Pengiriman</h3></div>
                <div style="padding:16px;font-size:.9rem;line-height:1.8;color:#555;">
                    <p><strong><?php echo htmlspecialchars($alamat['nama'] ?? ''); ?></strong></p>
                    <p><?php echo htmlspecialchars($alamat['telepon'] ?? ''); ?></p>
                    <p><?php echo htmlspecialchars($alamat['alamat'] ?? ''); ?></p>
                    <p><?php echo htmlspecialchars($alamat['kota'] ?? ''); ?>, <?php echo htmlspecialchars($alamat['kodepos'] ?? ''); ?></p>
                    <hr style="margin:10px 0;border-color:#eee;">
                    <p><strong>Metode Bayar:</strong> <?php echo strtoupper(str_replace('_',' ',$order['metode_bayar'])); ?></p>
                </div>
            </div>
        </div>
    </div>
</main>
</body></html>
