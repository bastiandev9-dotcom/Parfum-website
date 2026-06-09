<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php'); exit;
}

$message = '';

function generateResi($kurir) {
    $prefix = strtoupper(substr($kurir, 0, 3));
    return $prefix . date('ymd') . strtoupper(substr(md5(uniqid()), 0, 8));
}

// Update status
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $orderId = (int)$_POST['order_id'];
    $status  = $_POST['status'];
    $resi    = trim($_POST['resi'] ?? '');

    // Auto-generate resi jika status dikirim dan belum ada resi
    if ($status === 'dikirim' && empty($resi)) {
        $r = $conn->prepare("SELECT kurir, resi FROM orders WHERE order_id=?");
        $r->bind_param("i", $orderId); $r->execute();
        $row = $r->get_result()->fetch_assoc();
        if (empty($row['resi'])) $resi = generateResi($row['kurir']);
        else $resi = $row['resi'];
    }

    $extraCol = '';
    if ($status === 'dikirim')  $extraCol = ', shipped_at = NOW()';
    if ($status === 'selesai')  $extraCol = ', delivered_at = NOW()';
    if ($status === 'batal')    $extraCol = ', cancelled_at = NOW()';

    $stmt = $conn->prepare("UPDATE orders SET status=?, resi=? $extraCol WHERE order_id=?");
    $stmt->bind_param("ssi", $status, $resi, $orderId);
    $stmt->execute();

    // Log perubahan status
    $adminId = $_SESSION['user_id'];
    $logStmt = $conn->prepare("INSERT INTO order_status_logs (order_id, status_baru, changed_by) VALUES (?,?,?)");
    $logStmt->bind_param("isi", $orderId, $status, $adminId);
    $logStmt->execute();

    $message = 'Status pesanan berhasil diupdate.';
}

// Aksi cepat (tombol Kirim / Selesai)
if (isset($_GET['action']) && isset($_GET['id'])) {
    $orderId = (int)$_GET['id'];
    $action  = $_GET['action'];
    $map = ['kirim' => 'dikirim', 'selesai' => 'selesai'];
    if (isset($map[$action])) {
        $newStatus = $map[$action];
        $extraCol  = $action === 'kirim' ? ', shipped_at=NOW()' : ', delivered_at=NOW()';

        $autoResi = '';
        if ($action === 'kirim') {
            $r = $conn->prepare("SELECT kurir, resi FROM orders WHERE order_id=?");
            $r->bind_param("i", $orderId); $r->execute();
            $row = $r->get_result()->fetch_assoc();
            $autoResi = empty($row['resi']) ? generateResi($row['kurir']) : $row['resi'];
            $extraCol .= ", resi='$autoResi'";
        }

        $stmt = $conn->prepare("UPDATE orders SET status=? $extraCol WHERE order_id=?");
        $stmt->bind_param("si", $newStatus, $orderId);
        $stmt->execute();

        $adminId = $_SESSION['user_id'];
        $log = $conn->prepare("INSERT INTO order_status_logs (order_id, status_baru, changed_by) VALUES (?,?,?)");
        $log->bind_param("isi", $orderId, $newStatus, $adminId);
        $log->execute();
    }
    header('Location: orders.php'); exit;
}

// Hapus
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $conn->query("DELETE FROM order_items WHERE order_id=$id");
    $conn->query("DELETE FROM order_status_logs WHERE order_id=$id");
    $stmt = $conn->prepare("DELETE FROM orders WHERE order_id=?");
    $stmt->bind_param("i", $id); $stmt->execute();
    header('Location: orders.php'); exit;
}

// Fetch orders
$statusFilter = $_GET['status'] ?? '';
$sql = "SELECT o.*, u.nama, u.email, u.telepon,
               (o.subtotal + o.ongkir + o.cod_fee - COALESCE(o.diskon,0)) as total_calc
        FROM orders o JOIN users u ON o.user_id = u.user_id";
if ($statusFilter) $sql .= " WHERE o.status = '$statusFilter'";
$sql .= " ORDER BY o.created_at DESC";
$orders = $conn->query($sql)->fetch_all(MYSQLI_ASSOC);

$statusOptions = ['pending','diproses','dikirim','selesai','batal'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Pesanan - Admin</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .btn-action { width:34px; height:34px; border-radius:8px; border:none; cursor:pointer; text-decoration:none; display:inline-flex; align-items:center; justify-content:center; font-size:.95rem; }
        .btn-kirim  { background:#3498db; color:#fff; }
        .btn-selesai{ background:#27ae60; color:#fff; }
        .btn-kirim:hover  { background:#2980b9; color:#fff; }
        .btn-selesai:hover{ background:#219a52; color:#fff; }
    </style>
</head>
<body class="admin-body">

<?php include 'includes/admin-sidebar.php'; ?>

<main class="admin-main">
    <div class="admin-header">
        <h1>Kelola Pesanan</h1>
        <select class="admin-select" onchange="location.href='?status='+this.value">
            <option value="">Semua Status</option>
            <?php foreach ($statusOptions as $s): ?>
            <option value="<?php echo $s; ?>" <?php echo $statusFilter===$s?'selected':''; ?>><?php echo ucfirst($s); ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <?php if ($message): ?>
    <div class="auth-success" style="margin-bottom:16px;"><i class="fas fa-check-circle"></i> <?php echo $message; ?></div>
    <?php endif; ?>

    <div class="admin-card">
        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Pelanggan</th>
                        <th>Tanggal</th>
                        <th>Total</th>
                        <th>Kurir</th>
                        <th>Status</th>
                        <th style="min-width:120px;">Aksi Cepat</th>
                        <th style="min-width:160px;">Update</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($orders as $o): ?>
                <tr>
                    <td><strong><?php echo $o['order_code']; ?></strong></td>
                    <td><?php echo htmlspecialchars($o['nama']); ?><br><small style="color:#999"><?php echo $o['telepon']; ?></small></td>
                    <td><?php echo date('d M Y', strtotime($o['created_at'])); ?></td>
                    <td><strong><?php echo formatRupiah($o['total_calc']); ?></strong></td>
                    <td><?php echo strtoupper($o['kurir']); ?><?php if($o['resi']): ?><br><small style="color:var(--gold)"><?php echo $o['resi']; ?></small><?php endif; ?></td>
                    <td><span class="status-badge status-<?php echo $o['status']; ?>"><?php echo ucfirst($o['status']); ?></span></td>
                    <td>
                        <?php if ($o['status'] === 'diproses'): ?>
                            <a href="?action=kirim&id=<?php echo $o['order_id']; ?>" class="btn-action btn-kirim" title="Tandai Dikirim" onclick="return confirm('Tandai sebagai Dikirim?')">
                                <i class="fas fa-shipping-fast"></i>
                            </a>
                        <?php elseif ($o['status'] === 'dikirim'): ?>
                            <a href="?action=selesai&id=<?php echo $o['order_id']; ?>" class="btn-action btn-selesai" title="Tandai Selesai" onclick="return confirm('Tandai sebagai Selesai?')">
                                <i class="fas fa-check"></i>
                            </a>
                        <?php else: ?>
                            <span style="color:#ccc;font-size:.8rem;">—</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <form method="POST" style="min-width:140px;">
                            <input type="hidden" name="order_id" value="<?php echo $o['order_id']; ?>">
                            <input type="hidden" name="update_status" value="1">
                            <select name="status" class="status-select status-<?php echo $o['status']; ?>" onchange="this.form.submit()" style="width:100%;margin-bottom:4px;">
                                <?php foreach ($statusOptions as $s): ?>
                                <option value="<?php echo $s; ?>" <?php echo $o['status']===$s?'selected':''; ?>><?php echo ucfirst($s); ?></option>
                                <?php endforeach; ?>
                            </select>
                            <input type="text" name="resi" value="<?php echo htmlspecialchars($o['resi']??''); ?>" placeholder="No. Resi (opsional)" style="width:100%;padding:4px 6px;font-size:.78rem;border:1px solid #ddd;border-radius:4px;" onchange="this.form.submit()">
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($orders)): ?>
                <tr><td colspan="8" style="text-align:center;color:#999;padding:30px;">Tidak ada pesanan.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>


</body>
</html>
