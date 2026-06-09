<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

if (!isset($_SESSION['user_id'])) { header('Location: ../login.php'); exit; }

$orderId = (int)($_GET['id'] ?? 0);
$userId  = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT * FROM orders WHERE order_id = ? AND user_id = ? AND status = 'pending'");
$stmt->bind_param("ii", $orderId, $userId);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();

if (!$order) { header('Location: orders.php'); exit; }

// Update status ke diproses
$upd = $conn->prepare("UPDATE orders SET status='diproses', status_bayar='pending' WHERE order_id = ?");
$upd->bind_param("i", $orderId);
$upd->execute();
$order['status'] = 'diproses';

$rekening = [
    'transfer_bca' => ['bank' => 'BCA', 'no' => '123-456-7890', 'nama' => 'PT Lumière Parfum Indonesia'],
    'transfer_bni' => ['bank' => 'BNI', 'no' => '098-765-4321', 'nama' => 'PT Lumière Parfum Indonesia'],
    'transfer_mandiri' => ['bank' => 'Mandiri', 'no' => '111-222-3333', 'nama' => 'PT Lumière Parfum Indonesia'],
];
$isCod = $order['metode_bayar'] === 'cod';
$rek    = $rekening[$order['metode_bayar']] ?? null;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Pembayaran - Lumière Parfum</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .pay-card { background:#fff; border-radius:16px; padding:28px; box-shadow:0 2px 20px rgba(0,0,0,.06); max-width:520px; margin:0 auto; }
        .pay-amount { font-size:2rem; font-weight:700; color:var(--gold); text-align:center; margin:16px 0; }
        .pay-info { background:#f9f6f0; border-radius:10px; padding:16px; margin:16px 0; }
        .pay-info p { margin:6px 0; display:flex; justify-content:space-between; }
        .pay-info strong { font-size:1.1rem; }
        .copy-btn { background:none; border:1px solid var(--gold); color:var(--gold); padding:4px 10px; border-radius:6px; cursor:pointer; font-size:.8rem; }
        .copy-btn:hover { background:var(--gold); color:#fff; }
        .steps { counter-reset:step; }
        .step { display:flex; gap:12px; margin:12px 0; align-items:flex-start; }
        .step-num { background:var(--gold); color:#fff; width:24px; height:24px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:.8rem; font-weight:700; flex-shrink:0; }
    </style>
</head>
<body>
<?php include '../includes/header.php'; ?>

<section class="user-section">
<div class="container" style="max-width:600px; margin:0 auto;">

    <div style="margin-bottom:20px;">
        <a href="order-detail.php?id=<?php echo $orderId; ?>" style="color:var(--gold);"><i class="fas fa-arrow-left"></i> Kembali ke Detail Pesanan</a>
    </div>

    <div class="pay-card">
        <h2 style="text-align:center;margin-bottom:4px;">Instruksi Pembayaran</h2>
        <p style="text-align:center;color:#999;font-size:.9rem;"><?php echo $order['order_code']; ?></p>

        <div class="pay-amount"><?php echo formatRupiah($order['total']); ?></div>

        <?php if ($isCod): ?>
        <div class="pay-info" style="text-align:center;">
            <i class="fas fa-hand-holding-usd" style="font-size:2rem;color:var(--gold);"></i>
            <p style="display:block;margin-top:10px;font-weight:600;">Bayar di Tempat (COD)</p>
            <p style="display:block;color:#666;">Siapkan uang tunai sebesar <strong><?php echo formatRupiah($order['total']); ?></strong> saat kurir tiba.</p>
        </div>

        <?php elseif ($rek): ?>
        <div class="pay-info">
            <p><span>Bank</span><strong><?php echo $rek['bank']; ?></strong></p>
            <p>
                <span>No. Rekening</span>
                <span>
                    <strong id="norek"><?php echo $rek['no']; ?></strong>
                    <button class="copy-btn" onclick="copyText('<?php echo $rek['no']; ?>')">Salin</button>
                </span>
            </p>
            <p><span>Atas Nama</span><strong><?php echo $rek['nama']; ?></strong></p>
            <p style="border-top:1px solid #e8e0d0;padding-top:10px;margin-top:6px;">
                <span>Jumlah Transfer</span>
                <span>
                    <strong style="color:var(--gold);" id="jumlah"><?php echo formatRupiah($order['total']); ?></strong>
                    <button class="copy-btn" onclick="copyText('<?php echo $order['total']; ?>')">Salin</button>
                </span>
            </p>
        </div>

        <div class="steps">
            <p style="font-weight:600;margin-bottom:8px;">Langkah Pembayaran:</p>
            <div class="step"><div class="step-num">1</div><p style="margin:0;">Buka aplikasi mobile banking atau ATM <?php echo $rek['bank']; ?></p></div>
            <div class="step"><div class="step-num">2</div><p style="margin:0;">Transfer ke rekening di atas dengan jumlah yang tepat</p></div>
            <div class="step"><div class="step-num">3</div><p style="margin:0;">Simpan bukti transfer Anda</p></div>
            <div class="step"><div class="step-num">4</div><p style="margin:0;">Pesanan akan diproses setelah pembayaran dikonfirmasi (1x24 jam)</p></div>
        </div>
        <?php endif; ?>

        <div style="margin-top:24px;padding:12px;background:#fff8e1;border-radius:8px;font-size:.85rem;color:#888;">
            <i class="fas fa-clock" style="color:var(--gold);"></i> Batas waktu pembayaran: <strong>24 jam</strong> sejak pesanan dibuat.
        </div>

        <a href="orders.php" class="btn" style="display:block;text-align:center;margin-top:20px;">
            <i class="fas fa-list"></i> Lihat Semua Pesanan
        </a>
    </div>

</div>
</section>

<?php include '../includes/footer.php'; ?>

<script>
function copyText(text) {
    navigator.clipboard.writeText(text).then(() => {
        alert('Disalin: ' + text);
    });
}
</script>
</body>
</html>
