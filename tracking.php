<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

$order = null;
$logs = [];
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' || isset($_GET['order'])) {
    $orderCode = trim($_POST['order_code'] ?? $_GET['order'] ?? '');
    if ($orderCode) {
        $stmt = $conn->prepare("SELECT o.*, u.nama FROM orders o JOIN users u ON o.user_id=u.user_id WHERE o.order_code=?");
        $stmt->bind_param("s", $orderCode);
        $stmt->execute();
        $order = $stmt->get_result()->fetch_assoc();
        if ($order) {
            $stmt2 = $conn->prepare("SELECT * FROM order_status_logs WHERE order_id=? ORDER BY changed_at ASC");
            $stmt2->bind_param("i", $order['order_id']);
            $stmt2->execute();
            $logs = $stmt2->get_result()->fetch_all(MYSQLI_ASSOC);
        } else {
            $error = 'Pesanan tidak ditemukan.';
        }
    }
}

$statusSteps = ['pending','diproses','dikirim','selesai'];
$statusLabels = ['pending'=>'Pesanan Diterima','diproses'=>'Sedang Diproses','dikirim'=>'Dalam Pengiriman','selesai'=>'Pesanan Selesai'];
$statusIcons = ['pending'=>'fas fa-clock','diproses'=>'fas fa-box','dikirim'=>'fas fa-shipping-fast','selesai'=>'fas fa-check-circle'];
$currentStep = $order ? array_search($order['status'], $statusSteps) : -1;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lacak Pesanan - Lumière Parfum</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
<?php include 'includes/header.php'; ?>

<section class="page-hero" style="background:var(--black);color:#fff;padding:80px 0;text-align:center;">
    <div class="container">
        <h1 style="color:var(--gold);font-size:2.5rem;letter-spacing:3px;">LACAK PESANAN</h1>
        <p style="color:#ccc;margin-top:10px;">Pantau status pengiriman pesanan Anda</p>
    </div>
</section>

<section class="section">
    <div class="container" style="max-width:700px;">
        <!-- Form cari -->
        <div style="background:#fff;border-radius:16px;padding:32px;box-shadow:0 4px 20px rgba(0,0,0,.08);margin-bottom:30px;">
            <form method="POST">
                <label style="display:block;margin-bottom:8px;font-weight:600;">Masukkan Kode Pesanan</label>
                <div style="display:flex;gap:12px;">
                    <input type="text" name="order_code" placeholder="Contoh: LMR-XXXXXXXX"
                        value="<?php echo htmlspecialchars($_POST['order_code'] ?? $_GET['order'] ?? ''); ?>"
                        style="flex:1;padding:12px 16px;border:1px solid #ddd;border-radius:8px;font-size:1rem;">
                    <button type="submit" class="btn"><i class="fas fa-search"></i> Lacak</button>
                </div>
            </form>
        </div>

        <?php if ($error): ?>
        <div class="auth-error"><i class="fas fa-exclamation-circle"></i> <?php echo $error; ?></div>
        <?php endif; ?>

        <?php if ($order): ?>
        <div style="background:#fff;border-radius:16px;padding:32px;box-shadow:0 4px 20px rgba(0,0,0,.08);">
            <!-- Info pesanan -->
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:28px;padding-bottom:20px;border-bottom:1px solid #eee;">
                <div>
                    <h3 style="color:var(--gold);"><?php echo $order['order_code']; ?></h3>
                    <p style="color:#888;font-size:.9rem;"><?php echo date('d F Y', strtotime($order['created_at'])); ?></p>
                </div>
                <span class="status-badge status-<?php echo $order['status']; ?>"><?php echo ucfirst($order['status']); ?></span>
            </div>

            <!-- Progress bar -->
            <div style="display:flex;justify-content:space-between;position:relative;margin-bottom:40px;">
                <div style="position:absolute;top:20px;left:0;right:0;height:4px;background:#eee;z-index:0;">
                    <div style="height:100%;background:var(--gold);width:<?php
                        $pct = ['pending'=>0,'diproses'=>33,'dikirim'=>66,'selesai'=>100];
                        echo $pct[$order['status']] ?? 0;
                    ?>%;transition:width .3s;"></div>
                </div>
                <?php foreach ($statusSteps as $i => $step): ?>
                <div style="text-align:center;z-index:1;width:25%;">
                    <div style="width:44px;height:44px;border-radius:50%;background:<?php echo $i <= $currentStep ? 'var(--gold)' : '#eee'; ?>;display:flex;align-items:center;justify-content:center;margin:0 auto 8px;color:<?php echo $i <= $currentStep ? '#fff' : '#aaa'; ?>;">
                        <i class="<?php echo $statusIcons[$step]; ?>"></i>
                    </div>
                    <p style="font-size:.75rem;color:<?php echo $i <= $currentStep ? 'var(--black)' : '#aaa'; ?>;font-weight:<?php echo $i <= $currentStep ? '600' : '400'; ?>;"><?php echo $statusLabels[$step]; ?></p>
                </div>
                <?php endforeach; ?>
            </div>

            <?php if ($order['resi']): ?>
            <div style="background:var(--cream);border-radius:10px;padding:16px 20px;margin-bottom:20px;">
                <p style="font-size:.85rem;color:#888;">No. Resi</p>
                <p style="font-size:1.1rem;font-weight:700;color:var(--gold);"><?php echo $order['resi']; ?> <span style="font-size:.85rem;font-weight:400;color:#888;">(<?php echo strtoupper($order['kurir']); ?>)</span></p>
            </div>
            <?php endif; ?>

            <!-- Riwayat status -->
            <?php if (!empty($logs)): ?>
            <h4 style="margin-bottom:16px;">Riwayat Status</h4>
            <div>
                <?php foreach (array_reverse($logs) as $log): ?>
                <div style="display:flex;gap:14px;margin-bottom:14px;">
                    <div style="width:10px;height:10px;border-radius:50%;background:var(--gold);margin-top:6px;flex-shrink:0;"></div>
                    <div>
                        <p style="font-weight:600;margin-bottom:2px;"><?php echo ucfirst($log['status_baru']); ?></p>
                        <p style="font-size:.8rem;color:#999;"><?php echo date('d M Y H:i', strtotime($log['changed_at'])); ?></p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
</body>
</html>
