<?php
session_start();
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Pragma: no-cache');
require_once '../config/database.php';
require_once '../includes/functions.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

$user = ['nama' => $_SESSION['nama'] ?? 'User', 'avatar' => strtoupper(substr($_SESSION['nama'] ?? 'U', 0, 2)), 'member_since' => 'Mei 2026'];
$orders = [];

$stmt = $conn->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $orders[] = $row;
}

$filterStatus = isset($_GET['status']) ? $_GET['status'] : 'all';
$successOrder = $_GET['order'] ?? null;

// Tracking by order code (search)
$trackOrder = null;
$trackLogs  = [];
$trackError = '';
$trackCode  = trim($_GET['track'] ?? $_POST['track_code'] ?? '');
if ($trackCode) {
    $stmt = $conn->prepare("SELECT * FROM orders WHERE order_code=? AND user_id=?");
    $stmt->bind_param("si", $trackCode, $user_id);
    $stmt->execute();
    $trackOrder = $stmt->get_result()->fetch_assoc();
    if ($trackOrder) {
        $stmt2 = $conn->prepare("SELECT * FROM order_status_logs WHERE order_id=? ORDER BY changed_at ASC");
        $stmt2->bind_param("i", $trackOrder['order_id']);
        $stmt2->execute();
        $trackLogs = $stmt2->get_result()->fetch_all(MYSQLI_ASSOC);
    } else {
        $trackError = 'Pesanan tidak ditemukan.';
    }
}
$trackSteps  = ['pending','diproses','dikirim','selesai'];
$trackLabels = ['pending'=>'Pesanan Diterima','diproses'=>'Sedang Diproses','dikirim'=>'Dalam Pengiriman','selesai'=>'Pesanan Selesai'];
$trackIcons  = ['pending'=>'fas fa-clock','diproses'=>'fas fa-box','dikirim'=>'fas fa-shipping-fast','selesai'=>'fas fa-check-circle'];
$trackStep   = $trackOrder ? array_search($trackOrder['status'], $trackSteps) : -1;

if ($filterStatus !== 'all') {
    $orders = array_filter($orders, fn($o) => $o['status'] === $filterStatus);
    $orders = array_values($orders);
}

$statusLabels = ['all' => 'Semua Pesanan', 'pending' => 'Pending', 'diproses' => 'Diproses', 'dikirim' => 'Dikirim', 'selesai' => 'Selesai'];

// Fetch order_items + cek review untuk order selesai
$orderItems = []; // [order_id => [items]]
$reviewed   = []; // [order_id_product_id => true]
foreach ($orders as $o) {
    if ($o['status'] !== 'selesai') continue;
    $oid = $o['order_id'];
    $s = $conn->prepare("SELECT oi.*, r.review_id FROM order_items oi LEFT JOIN reviews r ON r.order_id=oi.order_id AND r.product_id=oi.product_id AND r.user_id=? WHERE oi.order_id=?");
    $s->bind_param("ii", $user_id, $oid);
    $s->execute();
    $rows = $s->get_result()->fetch_all(MYSQLI_ASSOC);
    $orderItems[$oid] = $rows;
    foreach ($rows as $row) {
        if ($row['review_id']) $reviewed[$oid.'_'.$row['product_id']] = true;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Pesanan Saya - Lumière Parfum</title>
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
                    <a href="dashboard.php"><i class="fas fa-th-large"></i> Dashboard</a>
                    <a href="orders.php" class="active"><i class="fas fa-shopping-bag"></i> Pesanan Saya</a>
                    <a href="wishlist.php"><i class="fas fa-heart"></i> Wishlist</a>
                    <a href="profile.php"><i class="fas fa-user"></i> Profil & Alamat</a>
                    <a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Keluar</a>
                </nav>
            </aside>
            
            <!-- Main Content -->
            <div class="user-content">
                
                <div class="content-header">
                    <div>
                        <h2>Riwayat Pesanan</h2>
                        <p>Kelola dan lacak status pesanan Anda.</p>
                    </div>
                </div>
                
                <?php if ($successOrder): ?>
                <div class="auth-success" style="margin-bottom:20px;">
                    <i class="fas fa-check-circle"></i> Pesanan <strong><?php echo htmlspecialchars($successOrder); ?></strong> berhasil dibuat! Silakan lakukan pembayaran.
                </div>
                <?php endif; ?>
                
                <!-- Filter Tabs -->
                <div class="status-filter">
                    <?php foreach ($statusLabels as $key => $label): ?>
                    <a href="?status=<?php echo $key; ?>" class="filter-pill <?php echo $filterStatus === $key ? 'active' : ''; ?>">
                        <?php echo $label; ?>
                    </a>
                    <?php endforeach; ?>
                </div>

                <!-- Lacak Pesanan -->
                <div style="background:#fff;border-radius:14px;padding:20px 24px;box-shadow:0 2px 12px rgba(0,0,0,.07);margin-bottom:24px;">
                    <h4 style="margin:0 0 12px;font-size:.95rem;"><i class="fas fa-search" style="color:var(--gold);margin-right:6px;"></i> Cari Pesanan</h4>
                    <form method="GET" style="display:flex;gap:10px;">
                        <?php if($filterStatus !== 'all'): ?><input type="hidden" name="status" value="<?php echo htmlspecialchars($filterStatus); ?>"><?php endif; ?>
                        <input type="text" name="track" value="<?php echo htmlspecialchars($trackCode); ?>" placeholder="Masukkan kode pesanan, contoh: LMR-XXXXXXXX"
                            style="flex:1;padding:10px 14px;border:1.5px solid #ddd;border-radius:8px;font-size:.9rem;outline:none;">
                        <button type="submit" class="btn" style="white-space:nowrap;"><i class="fas fa-search"></i> Cari</button>
                    </form>

                    <?php if ($trackCode): ?>
                    <div style="margin-top:16px;">
                    <?php if ($trackError): ?>
                        <p style="color:#e74c3c;font-size:.9rem;"><i class="fas fa-exclamation-circle"></i> <?php echo $trackError; ?></p>
                    <?php elseif ($trackOrder): ?>
                        <div style="border-top:1px solid #eee;padding-top:16px;">
                            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
                                <div>
                                    <strong style="color:var(--gold);"><?php echo $trackOrder['order_code']; ?></strong>
                                    <p style="color:#888;font-size:.8rem;margin:2px 0 0;"><?php echo date('d F Y', strtotime($trackOrder['created_at'])); ?></p>
                                </div>
                                <span class="status-badge status-<?php echo $trackOrder['status']; ?>"><?php echo ucfirst($trackOrder['status']); ?></span>
                            </div>
                            <!-- Progress -->
                            <div style="display:flex;justify-content:space-between;position:relative;margin-bottom:24px;">
                                <div style="position:absolute;top:18px;left:0;right:0;height:4px;background:#eee;z-index:0;">
                                    <div style="height:100%;background:var(--gold);width:<?php $pct=['pending'=>0,'diproses'=>33,'dikirim'=>66,'selesai'=>100]; echo $pct[$trackOrder['status']]??0; ?>%;"></div>
                                </div>
                                <?php foreach ($trackSteps as $i => $step): ?>
                                <div style="text-align:center;z-index:1;width:25%;">
                                    <div style="width:36px;height:36px;border-radius:50%;background:<?php echo $i<=$trackStep?'var(--gold)':'#eee'; ?>;display:flex;align-items:center;justify-content:center;margin:0 auto 6px;color:<?php echo $i<=$trackStep?'#fff':'#aaa'; ?>;font-size:.85rem;">
                                        <i class="<?php echo $trackIcons[$step]; ?>"></i>
                                    </div>
                                    <p style="font-size:.7rem;color:<?php echo $i<=$trackStep?'var(--black)':'#aaa'; ?>;font-weight:<?php echo $i<=$trackStep?'600':'400'; ?>;"><?php echo $trackLabels[$step]; ?></p>
                                </div>
                                <?php endforeach; ?>
                            </div>
                            <?php if ($trackOrder['resi']): ?>
                            <div style="background:var(--cream);border-radius:8px;padding:10px 16px;display:flex;justify-content:space-between;">
                                <span style="font-size:.85rem;color:#888;">No. Resi</span>
                                <strong style="color:var(--gold);"><?php echo $trackOrder['resi']; ?> <span style="font-weight:400;color:#888;font-size:.8rem;">(<?php echo strtoupper($trackOrder['kurir']); ?>)</span></strong>
                            </div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                    </div>
                    <?php endif; ?>
                </div>
                
                <!-- Orders List -->
                <?php if (!empty($orders)): ?>
                <div class="orders-list">
                    <?php foreach ($orders as $order): ?>
                    <div class="order-card">
                        <div class="order-header">
                            <div class="order-meta">
                                <span class="order-id"><?php echo $order['order_code']; ?></span>
                                <span class="order-date"><?php echo date('d M Y', strtotime($order['created_at'])); ?></span>
                            </div>
                            <span class="status-badge status-<?php echo $order['status']; ?>">
                                <?php echo ucfirst($order['status']); ?>
                            </span>
                        </div>
                        
                        <div class="order-body">
                            <div class="order-info-row">
                                <div class="info-col">
                                    <span class="info-label">Total Pembayaran</span>
                                    <span class="info-value"><?php echo formatRupiah($order['total']); ?></span>
                                </div>
                                <div class="info-col">
                                    <span class="info-label">Kurir</span>
                                    <span class="info-value"><?php echo strtoupper($order['kurir']); ?></span>
                                </div>
                                <div class="info-col">
                                    <span class="info-label">No. Resi</span>
                                    <span class="info-value resi-code" style="color:<?php echo $order['resi'] ? 'var(--gold)' : '#aaa'; ?>;font-weight:<?php echo $order['resi'] ? '700' : '400'; ?>;">
                                        <?php echo $order['resi'] ?: 'Belum tersedia'; ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="order-footer">
                            <?php if ($order['status'] === 'pending'): ?>
                                <a href="payment.php?id=<?php echo $order['order_id']; ?>" class="btn btn-sm btn-pay">
                                    <i class="fas fa-credit-card"></i> Bayar Sekarang
                                </a>
                            <?php elseif ($order['status'] === 'dikirim'): ?>
                                <a href="#" class="btn btn-sm btn-track" onclick="openTrackModal('<?php echo htmlspecialchars($order['resi']); ?>'); return false;">
                                    <i class="fas fa-map-marker-alt"></i> Lacak Pengiriman
                                </a>
                            <?php elseif ($order['status'] === 'selesai'): ?>
                                <a href="../products.php" class="btn btn-sm btn-outline">
                                    <i class="fas fa-shopping-bag"></i> Beli Lagi
                                </a>
                                <button class="btn btn-sm btn-review" onclick="openReviewModal(<?php echo $order['order_id']; ?>)">
                                    <i class="fas fa-star"></i> Tulis Ulasan
                                </button>
                            <?php endif; ?>
                            
                            <a href="order-detail.php?id=<?php echo $order['order_id']; ?>" class="link-detail">Lihat Detail <i class="fas fa-chevron-right"></i></a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-box-open"></i>
                    <h3>Tidak ada pesanan</h3>
                    <p>Belum ada pesanan dengan status ini.</p>
                </div>
                <?php endif; ?>
                
            </div>
        </div>
    </div>
</section>

<?php include '../includes/footer.php'; ?>

<!-- Data order items untuk JS -->
<script>
const orderItemsData = <?php echo json_encode($orderItems); ?>;
const reviewedData   = <?php echo json_encode($reviewed); ?>;
</script>

<!-- Modal Tulis Ulasan -->
<div id="reviewModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:9999;align-items:center;justify-content:center;">
    <div style="background:#fff;border-radius:16px;width:90%;max-width:520px;box-shadow:0 10px 40px rgba(0,0,0,.3);overflow:hidden;">
        <div style="padding:16px 20px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid #eee;">
            <h3 style="margin:0;font-size:16px;"><i class="fas fa-star" style="color:#f5a623;margin-right:8px;"></i> Tulis Ulasan</h3>
            <button onclick="closeReviewModal()" style="background:none;border:none;font-size:20px;cursor:pointer;color:#666;">&times;</button>
        </div>
        <div style="padding:20px;" id="reviewModalBody">
            <!-- diisi JS -->
        </div>
    </div>
</div>

<!-- Modal Lacak Pengiriman -->
<div id="trackModal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:9999;align-items:center;justify-content:center;">
    <div style="background:#fff;border-radius:12px;width:90%;max-width:700px;overflow:hidden;box-shadow:0 10px 40px rgba(0,0,0,.3);">
        <div style="padding:16px 20px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid #eee;">
            <h3 style="margin:0;font-size:16px;"><i class="fas fa-map-marker-alt" style="color:#e91e8c;margin-right:8px;"></i> Lacak Pengiriman — No. Resi: <span id="modalResi"></span></h3>
            <button onclick="closeTrackModal()" style="background:none;border:none;font-size:20px;cursor:pointer;color:#666;">&times;</button>
        </div>
        <div id="map" style="height:420px;"></div>
    </div>
</div>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
var map;
function openTrackModal(resi) {
    document.getElementById('modalResi').textContent = resi || '-';
    var modal = document.getElementById('trackModal');
    modal.style.display = 'flex';
    setTimeout(function() {
        if (!map) {
            map = L.map('map').setView([-2.5, 118], 5);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© OpenStreetMap'
            }).addTo(map);
        } else {
            map.invalidateSize();
        }
    }, 100);
}
function closeTrackModal() {
    document.getElementById('trackModal').style.display = 'none';
}
</script>

<style>
.btn-review { background: var(--gold); color: #fff; border: none; cursor: pointer; }
.btn-review:hover { opacity: .85; }
.star-input { display:flex; gap:6px; margin:8px 0 16px; }
.star-input i { font-size:1.6rem; color:#ddd; cursor:pointer; transition:color .15s; }
.star-input i.active { color:#f5a623; }
.review-item { border:1px solid #eee; border-radius:12px; padding:16px; margin-bottom:12px; display:flex; flex-direction:column; gap:10px; }
.review-item h5 { margin:0; font-size:.95rem; font-weight:600; color:#222; }
.review-item .brand-label { margin:0; font-size:.8rem; color:#999; }
.star-input { display:flex; gap:8px; }
.star-input i { font-size:1.8rem; color:#ddd; cursor:pointer; transition:color .15s; }
.star-input i.active { color:#f5a623; }
.review-textarea { display:block; width:100%; border:1px solid #ddd; border-radius:8px; padding:10px 12px; font-size:.88rem; resize:vertical; box-sizing:border-box; font-family:inherit; min-height:72px; outline:none; }
.review-textarea:focus { border-color:var(--gold); }
.review-done { color:#27ae60; font-size:.85rem; }
</style>
<script>
let currentOrderId = null;

function openReviewModal(orderId) {
    currentOrderId = orderId;
    const items = orderItemsData[orderId] || [];
    let html = '';
    items.forEach(item => {
        const key = orderId + '_' + item.product_id;
        if (reviewedData[key]) {
            html += `<div class="review-item">
                <h5>${item.nama_produk}</h5>
                <span class="review-done"><i class="fas fa-check-circle"></i> Sudah diulas</span>
            </div>`;
        } else {
            html += `<div class="review-item" id="ri_${item.product_id}">
                <div>
                    <h5>${item.nama_produk}</h5>
                    <p class="brand-label">${item.brand || ''}</p>
                </div>
                <div class="star-input" id="stars_${item.product_id}">
                    ${[1,2,3,4,5].map(n=>`<i class="fas fa-star" data-val="${n}" onclick="setRating(${item.product_id},${n})"></i>`).join('')}
                </div>
                <input type="hidden" id="rat_${item.product_id}" value="5">
                <textarea id="kom_${item.product_id}" class="review-textarea" placeholder="Tulis komentar produk ini..."></textarea>
                <button onclick="submitReview(${orderId},${item.product_id})" class="btn btn-sm btn-review" style="width:100%;">
                    <i class="fas fa-paper-plane"></i> Kirim Ulasan
                </button>
            </div>`;
        }
    });
    if (!html) html = '<p style="color:#999;text-align:center;">Tidak ada produk untuk diulas.</p>';
    document.getElementById('reviewModalBody').innerHTML = html;
    // set default bintang 5
    items.forEach(item => {
        if (!reviewedData[orderId+'_'+item.product_id]) setRating(item.product_id, 5);
    });
    document.getElementById('reviewModal').style.display = 'flex';
}

function closeReviewModal() {
    document.getElementById('reviewModal').style.display = 'none';
}

function setRating(productId, val) {
    document.getElementById('rat_'+productId).value = val;
    document.querySelectorAll('#stars_'+productId+' i').forEach(s => {
        s.classList.toggle('active', parseInt(s.dataset.val) <= val);
    });
}

function submitReview(orderId, productId) {
    const rating   = document.getElementById('rat_'+productId).value;
    const komentar = document.getElementById('kom_'+productId).value.trim();
    if (!komentar) { alert('Tulis komentar terlebih dahulu.'); return; }

    const fd = new FormData();
    fd.append('submit_review', '1');
    fd.append('rating', rating);
    fd.append('komentar', komentar);
    fd.append('order_id', orderId);

    // product_id dikirim via URL
    fetch('../product-detail.php?id='+productId, { method:'POST', body:fd })
        .then(() => {
            reviewedData[orderId+'_'+productId] = true;
            const el = document.getElementById('ri_'+productId);
            el.innerHTML = `<h5>${el.querySelector('h5').textContent}</h5><span class="review-done"><i class="fas fa-check-circle"></i> Ulasan terkirim!</span>`;
        })
        .catch(() => alert('Gagal mengirim ulasan.'));
}
</script>



</body>
</html>