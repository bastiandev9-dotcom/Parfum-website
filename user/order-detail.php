<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

if (!isset($_SESSION['user_id'])) { header('Location: ../login.php'); exit; }

$orderId = (int)($_GET['id'] ?? 0);
$userId  = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT * FROM orders WHERE order_id = ? AND user_id = ?");
$stmt->bind_param("ii", $orderId, $userId);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();
if (!$order) { header('Location: orders.php'); exit; }

$stmt2 = $conn->prepare("SELECT * FROM order_items WHERE order_id = ?");
$stmt2->bind_param("i", $orderId);
$stmt2->execute();
$items = $stmt2->get_result()->fetch_all(MYSQLI_ASSOC);

$alamat = json_decode($order['alamat_snapshot'], true) ?? [];

$statusSteps = ['pending' => 0, 'diproses' => 1, 'dikirim' => 2, 'selesai' => 3];
$currentStep = $statusSteps[$order['status']] ?? 0;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Detail Pesanan <?php echo $order['order_code']; ?> - Lumière Parfum</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .detail-hero {
            background: linear-gradient(135deg, #1a1a2e 0%, #2d1b4e 100%);
            color: #fff;
            border-radius: 20px;
            padding: 28px 32px;
            margin-bottom: 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 16px;
        }
        .detail-hero h2 { margin: 0; font-size: 1.4rem; color: #fff; }
        .detail-hero p  { margin: 4px 0 0; color: rgba(255,255,255,.6); font-size: .9rem; }
        .hero-total { text-align: right; }
        .hero-total span { display: block; color: rgba(255,255,255,.6); font-size: .85rem; }
        .hero-total strong { font-size: 1.8rem; color: var(--gold); }

        /* Progress tracker */
        .progress-track {
            background: #fff;
            border-radius: 16px;
            padding: 24px 28px;
            margin-bottom: 20px;
            box-shadow: 0 2px 16px rgba(0,0,0,.05);
        }
        .progress-track h4 { margin: 0 0 20px; font-size: 1rem; color: #333; }
        .steps-row {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            position: relative;
        }
        .steps-row::before {
            content: '';
            position: absolute;
            top: 18px;
            left: 10%;
            right: 10%;
            height: 3px;
            background: #eee;
            z-index: 0;
        }
        .step-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
            flex: 1;
            position: relative;
            z-index: 1;
        }
        .step-circle {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            background: #eee;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: .9rem;
            color: #aaa;
            border: 3px solid #eee;
            transition: all .3s;
        }
        .step-item.done .step-circle  { background: var(--gold); border-color: var(--gold); color: #fff; }
        .step-item.active .step-circle { background: #fff; border-color: var(--gold); color: var(--gold); box-shadow: 0 0 0 4px rgba(201,168,76,.15); }
        .step-label { font-size: .78rem; color: #aaa; text-align: center; }
        .step-item.done .step-label, .step-item.active .step-label { color: #333; font-weight: 600; }

        /* Grid layout */
        .detail-grid { display: grid; grid-template-columns: 1fr 340px; gap: 20px; }
        @media(max-width:768px) { .detail-grid { grid-template-columns: 1fr; } }

        .detail-card {
            background: #fff;
            border-radius: 16px;
            padding: 24px;
            box-shadow: 0 2px 16px rgba(0,0,0,.05);
            margin-bottom: 20px;
        }
        .detail-card h4 { margin: 0 0 16px; font-size: 1rem; color: #333; display: flex; align-items: center; gap: 8px; }
        .detail-card h4 i { color: var(--gold); }

        /* Item produk */
        .order-item-row {
            display: flex;
            gap: 14px;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid #f5f5f5;
        }
        .order-item-row:last-child { border-bottom: none; }
        .order-item-img { width: 64px; height: 64px; border-radius: 10px; object-fit: cover; background: #f5f5f5; flex-shrink: 0; }
        .order-item-info { flex: 1; }
        .order-item-info h5 { margin: 0 0 2px; font-size: .95rem; }
        .order-item-info p  { margin: 0; font-size: .82rem; color: #999; }
        .order-item-price { font-weight: 700; color: #333; white-space: nowrap; }

        /* Biaya */
        .cost-row { display: flex; justify-content: space-between; padding: 7px 0; font-size: .9rem; color: #555; }
        .cost-row.total { border-top: 2px solid #f0f0f0; margin-top: 6px; padding-top: 14px; font-size: 1.05rem; font-weight: 700; color: #222; }
        .cost-row.total span:last-child { color: var(--gold); }

        /* Alamat */
        .addr-box { background: #f9f6f0; border-radius: 10px; padding: 14px 16px; line-height: 1.9; }
        .addr-box p { margin: 0; font-size: .9rem; }

        /* Badge metode bayar */
        .pay-badge { display: inline-flex; align-items: center; gap: 6px; background: #f0f0f0; border-radius: 8px; padding: 6px 12px; font-size: .85rem; font-weight: 600; }
    </style>
</head>
<body>
<?php include '../includes/header.php'; ?>

<section class="user-section">
<div class="container">
<div class="user-layout">

    <aside class="user-sidebar">
        <div class="user-profile-mini">
            <div class="mini-avatar"><?php echo strtoupper(substr($_SESSION['nama']??'U',0,2)); ?></div>
            <div class="mini-info"><h4><?php echo $_SESSION['nama']??'User'; ?></h4></div>
        </div>
        <nav class="user-nav">
            <a href="dashboard.php"><i class="fas fa-th-large"></i> Dashboard</a>
            <a href="orders.php" class="active"><i class="fas fa-shopping-bag"></i> Pesanan Saya</a>
            <a href="wishlist.php"><i class="fas fa-heart"></i> Wishlist</a>
            <a href="profile.php"><i class="fas fa-user"></i> Profil & Alamat</a>
            <a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Keluar</a>
        </nav>
    </aside>

    <div class="user-content">

        <a href="orders.php" style="color:var(--gold);font-size:.9rem;display:inline-block;margin-bottom:16px;">
            <i class="fas fa-arrow-left"></i> Kembali ke Pesanan
        </a>

        <!-- Hero -->
        <div class="detail-hero">
            <div>
                <h2><?php echo $order['order_code']; ?></h2>
                <p><i class="fas fa-calendar-alt"></i> <?php echo date('d M Y, H:i', strtotime($order['created_at'])); ?></p>
                <p style="margin-top:8px;">
                    <span class="status-badge status-<?php echo $order['status']; ?>">
                        <?php echo ucfirst($order['status']); ?>
                    </span>
                </p>
            </div>
            <div class="hero-total">
                <span>Total Pembayaran</span>
                <strong><?php echo formatRupiah($order['total']); ?></strong>
                <?php if ($order['status'] === 'pending'): ?>
                <a href="payment.php?id=<?php echo $orderId; ?>" class="btn" style="margin-top:10px;display:inline-block;font-size:.85rem;padding:8px 16px;">
                    <i class="fas fa-credit-card"></i> Bayar Sekarang
                </a>
                <?php endif; ?>
            </div>
        </div>

        <!-- Progress Tracker -->
        <?php if (!in_array($order['status'], ['batal','refund'])): ?>
        <div class="progress-track">
            <h4><i class="fas fa-route" style="color:var(--gold)"></i> Status Pesanan</h4>
            <div class="steps-row">
                <?php
                $steps = [
                    ['icon'=>'fa-file-alt',      'label'=>'Pesanan Dibuat'],
                    ['icon'=>'fa-box',            'label'=>'Sedang Diproses'],
                    ['icon'=>'fa-shipping-fast',  'label'=>'Dalam Pengiriman'],
                    ['icon'=>'fa-check-circle',   'label'=>'Pesanan Selesai'],
                ];
                foreach ($steps as $i => $s):
                    $cls = $i < $currentStep ? 'done' : ($i === $currentStep ? 'active' : '');
                ?>
                <div class="step-item <?php echo $cls; ?>">
                    <div class="step-circle">
                        <?php if ($i < $currentStep): ?>
                            <i class="fas fa-check"></i>
                        <?php else: ?>
                            <i class="fas <?php echo $s['icon']; ?>"></i>
                        <?php endif; ?>
                    </div>
                    <span class="step-label"><?php echo nl2br($s['label']); ?></span>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <div class="detail-grid">
            <!-- Kiri -->
            <div>
                <!-- Produk -->
                <div class="detail-card">
                    <h4><i class="fas fa-box-open"></i> Produk Dipesan (<?php echo count($items); ?> item)</h4>
                    <?php foreach ($items as $item): ?>
                    <div class="order-item-row">
                        <img src="../<?php echo htmlspecialchars($item['gambar']??''); ?>" class="order-item-img"
                             onerror="this.src='../assets/images/products/placeholder.jpg'">
                        <div class="order-item-info">
                            <h5><?php echo htmlspecialchars($item['nama_produk']); ?></h5>
                            <p><?php echo htmlspecialchars($item['brand']); ?><?php echo $item['ukuran'] ? ' • '.$item['ukuran'] : ''; ?></p>
                            <p><?php echo $item['qty']; ?>x <?php echo formatRupiah($item['harga_satuan']); ?></p>
                        </div>
                        <span class="order-item-price"><?php echo formatRupiah($item['subtotal']); ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>

                <!-- Alamat -->
                <div class="detail-card">
                    <h4><i class="fas fa-map-marker-alt"></i> Alamat Pengiriman</h4>
                    <div class="addr-box">
                        <p><strong><?php echo htmlspecialchars($alamat['nama']??'-'); ?></strong></p>
                        <p><i class="fas fa-phone" style="color:var(--gold);width:16px"></i> <?php echo htmlspecialchars($alamat['telepon']??'-'); ?></p>
                        <p><i class="fas fa-home" style="color:var(--gold);width:16px"></i> <?php echo htmlspecialchars($alamat['alamat']??'-'); ?></p>
                        <p><i class="fas fa-city" style="color:var(--gold);width:16px"></i> <?php echo htmlspecialchars($alamat['kota']??''); ?>, <?php echo htmlspecialchars($alamat['kodepos']??''); ?></p>
                    </div>
                </div>
            </div>

            <!-- Kanan -->
            <div>
                <!-- Ringkasan Biaya -->
                <div class="detail-card">
                    <h4><i class="fas fa-receipt"></i> Ringkasan Biaya</h4>
                    <div class="cost-row"><span>Subtotal</span><span><?php echo formatRupiah($order['subtotal']); ?></span></div>
                    <div class="cost-row"><span>Ongkir (<?php echo strtoupper($order['kurir']); ?>)</span><span><?php echo formatRupiah($order['ongkir']); ?></span></div>
                    <?php if ($order['cod_fee']): ?>
                    <div class="cost-row"><span>Biaya COD</span><span><?php echo formatRupiah($order['cod_fee']); ?></span></div>
                    <?php endif; ?>
                    <?php if ($order['diskon']): ?>
                    <div class="cost-row" style="color:green;"><span>Diskon</span><span>-<?php echo formatRupiah($order['diskon']); ?></span></div>
                    <?php endif; ?>
                    <div class="cost-row total"><span>Total</span><span><?php echo formatRupiah($order['total']); ?></span></div>
                </div>

                <!-- Info Pengiriman -->
                <div class="detail-card">
                    <h4><i class="fas fa-truck"></i> Info Pengiriman</h4>
                    <div class="cost-row"><span>Kurir</span><span><strong><?php echo strtoupper($order['kurir']); ?></strong></span></div>
                    <div class="cost-row" id="resi-row">
                        <span>No. Resi</span>
                        <span id="resi-val" style="font-weight:700;color:var(--gold);">
                            <?php echo $order['resi'] ?: '<span style="color:#aaa">Belum tersedia</span>'; ?>
                        </span>
                    </div>
                    <div class="cost-row"><span>Metode Bayar</span>
                        <span class="pay-badge"><i class="fas fa-credit-card"></i> <?php echo strtoupper(str_replace('_',' ',$order['metode_bayar'])); ?></span>
                    </div>
                    <div class="cost-row">
                        <span>Status Pesanan</span>
                        <span id="status-val" class="status-badge status-<?php echo $order['status']; ?>">
                            <?php echo ucfirst($order['status']); ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
</div>
</section>

<?php include '../includes/footer.php'; ?>

<script>
const orderId   = <?php echo $orderId; ?>;
const stepMap   = { pending: 0, diproses: 1, dikirim: 2, selesai: 3 };
const stepEls   = document.querySelectorAll('.step-item');
let   lastStatus = '<?php echo $order['status']; ?>';

function updateUI(data) {
    if (!data.status) return;

    // Update status badge
    const badge = document.getElementById('status-val');
    if (badge) {
        badge.className = 'status-badge status-' + data.status;
        badge.textContent = data.status.charAt(0).toUpperCase() + data.status.slice(1);
    }

    // Update resi
    const resiEl = document.getElementById('resi-val');
    if (resiEl && data.resi) resiEl.innerHTML = '<span style="font-weight:700;color:var(--gold)">' + data.resi + '</span>';

    // Update progress tracker
    const step = stepMap[data.status] ?? 0;
    stepEls.forEach((el, i) => {
        el.classList.remove('done', 'active');
        if (i < step)      el.classList.add('done');
        else if (i === step) el.classList.add('active');
        // Update icon
        const circle = el.querySelector('.step-circle i');
        if (circle) {
            if (i < step) { circle.className = 'fas fa-check'; }
        }
    });

    lastStatus = data.status;
}

// Poll setiap 5 detik
setInterval(() => {
    fetch('order-status.php?id=' + orderId)
        .then(r => r.json())
        .then(data => { if (data.status !== lastStatus || data.resi) updateUI(data); });
}, 5000);
</script>
</body>
</html>
