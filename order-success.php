<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';
if (!isset($_SESSION['user_id'])) { header('Location: login.php'); exit; }

$orderCode = $_GET['order'] ?? '';
$order = null;
if ($orderCode) {
    $stmt = $conn->prepare("SELECT * FROM orders WHERE order_code=? AND user_id=?");
    $stmt->bind_param("si", $orderCode, $_SESSION['user_id']);
    $stmt->execute();
    $order = $stmt->get_result()->fetch_assoc();
}
if (!$order) { header('Location: index.php'); exit; }

$alamat = json_decode($order['alamat_snapshot'], true);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesanan Berhasil - Lumière Parfum</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
<?php include 'includes/header.php'; ?>
<section class="section" style="min-height:80vh;display:flex;align-items:center;justify-content:center;">
    <div style="width:100%;max-width:600px;padding:0 20px;">
        <div style="text-align:center;background:#fff;border-radius:20px;padding:50px 40px;box-shadow:0 8px 40px rgba(0,0,0,.1);">
            <div style="width:80px;height:80px;background:#27ae60;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 24px;font-size:2.5rem;color:#fff;">
                <i class="fas fa-check"></i>
            </div>
            <h2 style="font-size:1.8rem;margin-bottom:10px;">Pesanan Berhasil!</h2>
            <p style="color:#666;margin-bottom:30px;">Terima kasih telah berbelanja di Lumière Parfum.</p>

            <div style="background:var(--cream);border-radius:12px;padding:20px;text-align:left;margin-bottom:24px;">
                <div style="display:flex;justify-content:space-between;margin-bottom:10px;">
                    <span style="color:#888;">Kode Pesanan</span>
                    <strong style="color:var(--gold);"><?php echo $order['order_code']; ?></strong>
                </div>
                <div style="display:flex;justify-content:space-between;margin-bottom:10px;">
                    <span style="color:#888;">Total Pembayaran</span>
                    <strong><?php echo formatRupiah($order['total']); ?></strong>
                </div>
                <div style="display:flex;justify-content:space-between;margin-bottom:10px;">
                    <span style="color:#888;">Metode Bayar</span>
                    <strong><?php echo strtoupper(str_replace('_', ' ', $order['metode_bayar'])); ?></strong>
                </div>
                <?php if ($alamat): ?>
                <div style="display:flex;justify-content:space-between;">
                    <span style="color:#888;">Tujuan Pengiriman</span>
                    <strong><?php echo htmlspecialchars($alamat['kota'] ?? ''); ?></strong>
                </div>
                <?php endif; ?>
            </div>

            <?php if ($order['metode_bayar'] !== 'cod'): ?>
            <p style="color:#888;font-size:.9rem;margin-bottom:24px;">Selesaikan pembayaran dalam <strong>24 jam</strong> agar pesanan segera diproses.</p>
            <a href="user/payment.php?id=<?php echo $order['order_id']; ?>" class="btn" style="width:100%;display:block;margin-bottom:12px;"><i class="fas fa-credit-card"></i> Bayar Sekarang</a>
            <?php endif; ?>

            <div style="display:flex;gap:12px;">
                <a href="user/orders.php" class="btn btn-outline" style="flex:1;"><i class="fas fa-list"></i> Lihat Pesanan</a>
                <a href="products.php" class="btn btn-outline" style="flex:1;"><i class="fas fa-shopping-bag"></i> Belanja Lagi</a>
            </div>
        </div>
    </div>
</section>
<?php include 'includes/footer.php'; ?>
</body>
</html>
