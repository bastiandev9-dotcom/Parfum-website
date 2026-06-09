<?php
// cart.php - Halaman Keranjang Belanja
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

// ==================== FUNGSI HELPER ====================
function getProductFromDB($conn, $id) {
    $stmt = $conn->prepare("SELECT p.*, b.nama_brand FROM products p LEFT JOIN brands b ON p.brand_id = b.brand_id WHERE p.product_id = ? AND p.status = 'aktif'");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

// ==================== HANDLE ACTIONS ====================

// Tambah ke cart
if (isset($_GET['add'])) {
    $id  = (int)$_GET['add'];
    $p   = getProductFromDB($conn, $id);
    if ($p) {
        if (!isset($_SESSION['cart'][$id])) {
            $_SESSION['cart'][$id] = ['qty' => 1];
        } else {
            $_SESSION['cart'][$id]['qty']++;
        }
        // Sync ke DB
        if (!empty($_SESSION['user_id'])) {
            $uid = $_SESSION['user_id'];
            $qty = $_SESSION['cart'][$id]['qty'];
            $conn->query("INSERT INTO carts (user_id, product_id, qty, harga_satuan) VALUES ($uid, $id, $qty, {$p['harga']}) ON DUPLICATE KEY UPDATE qty=$qty");
        }
    }
    header('Location: cart.php');
    exit;
}

// Hapus item
if (isset($_GET['remove'])) {
    $id = (int)$_GET['remove'];
    unset($_SESSION['cart'][$id]);
    if (!empty($_SESSION['user_id'])) {
        $uid = $_SESSION['user_id'];
        $conn->query("DELETE FROM carts WHERE user_id=$uid AND product_id=$id");
    }
    header('Location: cart.php');
    exit;
}

// Update qty via GET (AJAX)
if (isset($_GET['update']) && isset($_GET['qty'])) {
    $id  = (int)$_GET['update'];
    $qty = (int)$_GET['qty'];
    $p   = getProductFromDB($conn, $id);
    if ($p && $qty > 0 && $qty <= $p['stok']) {
        $_SESSION['cart'][$id]['qty'] = $qty;
        if (!empty($_SESSION['user_id'])) {
            $uid = $_SESSION['user_id'];
            $conn->query("UPDATE carts SET qty=$qty WHERE user_id=$uid AND product_id=$id");
        }
    } elseif ($qty <= 0) {
        unset($_SESSION['cart'][$id]);
        if (!empty($_SESSION['user_id'])) {
            $uid = $_SESSION['user_id'];
            $conn->query("DELETE FROM carts WHERE user_id=$uid AND product_id=$id");
        }
    }
    header('Content-Type: application/json');
    echo json_encode(['ok' => true]);
    exit;
}

// Kosongkan cart
if (isset($_GET['clear'])) {
    $_SESSION['cart'] = [];
    header('Location: cart.php');
    exit;
}

// ── VALIDASI PROMO CODE (AJAX) ─────────────────────────────
if (isset($_GET['check_promo'])) {
    header('Content-Type: application/json');
    $kode     = trim($_GET['check_promo']);
    $subtotal = (float)($_GET['subtotal'] ?? 0);
    $stmt = $conn->prepare("SELECT * FROM promo_codes WHERE kode=? AND is_active=1 AND (berlaku_sampai IS NULL OR berlaku_sampai >= CURDATE()) AND (max_penggunaan IS NULL OR terpakai < max_penggunaan)");
    $stmt->bind_param("s", $kode); $stmt->execute();
    $promo = $stmt->get_result()->fetch_assoc();
    if (!$promo) { echo json_encode(['ok'=>false,'msg'=>'Kode promo tidak valid atau sudah kadaluarsa.']); exit; }
    if ($subtotal < $promo['min_belanja']) { echo json_encode(['ok'=>false,'msg'=>'Minimum belanja '.number_format($promo['min_belanja'],0,',','.').' untuk kode ini.']); exit; }
    $diskon = $promo['tipe'] === 'persen' ? ($subtotal * $promo['nilai'] / 100) : $promo['nilai'];
    if ($promo['max_diskon']) $diskon = min($diskon, $promo['max_diskon']);
    echo json_encode(['ok'=>true,'diskon'=>$diskon,'msg'=>'Promo berhasil! Hemat Rp '.number_format($diskon,0,',','.')]);
    exit;
}

// Update qty via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_cart'])) {
    foreach ($_POST['qty'] as $id => $qty) {
        $id  = (int)$id;
        $qty = (int)$qty;
        $p   = getProductFromDB($conn, $id);
        if ($p && $qty > 0 && $qty <= $p['stok']) {
            $_SESSION['cart'][$id]['qty'] = $qty;
        } elseif ($qty <= 0) {
            unset($_SESSION['cart'][$id]);
        }
    }
    header('Location: cart.php');
    exit;
}

// ==================== BUILD CART ITEMS DARI DB ====================
$cart      = $_SESSION['cart'] ?? [];
$cartItems = [];
$subtotal  = 0;
$totalItems = 0;

foreach ($cart as $id => $item) {
    $p = getProductFromDB($conn, (int)$id);
    if ($p) {
        $price = $p['harga_diskon'] ?: $p['harga'];
        $total = $price * $item['qty'];
        $subtotal   += $total;
        $totalItems += $item['qty'];
        $cartItems[] = [
            'id'      => $id,
            'product' => $p,
            'qty'     => $item['qty'],
            'price'   => $price,
            'total'   => $total,
        ];
    }
}

$grandTotal = $subtotal; // ongkir dihitung di checkout
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keranjang Belanja - Lumière Parfum</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

<?php include 'includes/header.php'; ?>

<!-- Breadcrumb -->
<div class="breadcrumb">
    <div class="container">
        <a href="index.php">Beranda</a> / 
        <a href="products.php">Katalog</a> / 
        <span>Keranjang</span>
    </div>
</div>

<!-- Cart Section -->
<section class="cart-section">
    <div class="container">
        <h1 class="cart-title">Keranjang Belanja <span class="cart-count">(<?php echo $totalItems; ?> item)</span></h1>
        
        <?php if (!empty($cartItems)): ?>
        <div class="cart-layout">
            
            <!-- Cart Items -->
            <div class="cart-items-area">
                <form method="POST" action="cart.php" id="cartForm">
                    <input type="hidden" name="update_cart" value="1">
                    
                    <div class="cart-items-list">
                        <?php foreach ($cartItems as $item): $p = $item['product']; ?>
                        <div class="cart-item">
                            <div class="item-image">
                                <img src="<?php echo htmlspecialchars($p['gambar_utama']); ?>" alt="<?php echo htmlspecialchars($p['nama_produk']); ?>">
                            </div>
                            <div class="item-details">
                                <div class="item-header">
                                    <div>
                                        <p class="item-brand"><?php echo htmlspecialchars($p['nama_brand']); ?></p>
                                        <h3 class="item-name"><?php echo htmlspecialchars($p['nama_produk']); ?></h3>
                                    </div>
                                    <a href="cart.php?remove=<?php echo $item['id']; ?>" class="btn-remove" onclick="return confirm('Hapus produk ini?')" title="Hapus">
                                        <i class="fas fa-trash-alt"></i>
                                    </a>
                                </div>
                                <div class="item-footer">
                                    <div class="item-qty">
                                        <button type="button" class="qty-btn-sm" onclick="changeQty(<?php echo $item['id']; ?>, -1)">-</button>
                                        <input type="number" name="qty[<?php echo $item['id']; ?>]" value="<?php echo $item['qty']; ?>" min="1" max="<?php echo $p['stok']; ?>" class="qty-input-sm" id="qty-<?php echo $item['id']; ?>" data-price="<?php echo $item['price']; ?>" onchange="document.getElementById('cartForm').submit()">
                                        <button type="button" class="qty-btn-sm" onclick="changeQty(<?php echo $item['id']; ?>, 1)">+</button>
                                    </div>
                                    <div class="item-price">
                                        <?php if($p['harga_diskon']): ?>
                                            <span class="price-each"><?php echo formatRupiah($item['price']); ?> /pcs</span>
                                        <?php endif; ?>
                                        <span class="price-total" id="item-total-<?php echo $item['id']; ?>"><?php echo formatRupiah($item['total']); ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <!-- Mobile: tombol update terpisah -->
                    <button type="submit" class="btn btn-update-mobile">Perbarui Keranjang</button>
                </form>
                
                <div class="cart-actions-bottom">
                    <a href="products.php" class="btn btn-continue">
                        <i class="fas fa-arrow-left"></i> Lanjutkan Belanja
                    </a>
                    <a href="cart.php" class="btn-clear" onclick="return confirm('Kosongkan keranjang?')">
                        <i class="fas fa-trash"></i> Kosongkan
                    </a>
                </div>
            </div>
            
            <!-- Order Summary -->
            <aside class="cart-summary">
                <div class="summary-card">
                    <h3>Ringkasan Pesanan</h3>
                    
                    <div class="summary-row">
                        <span>Subtotal (<?php echo $totalItems; ?> item)</span>
                        <span id="cart-subtotal"><?php echo formatRupiah($subtotal); ?></span>
                    </div>
                    
                    <div class="summary-row">
                        <span>Ongkir</span>
                        <span class="free-shipping">GRATIS</span>
                    </div>
                    
                    <div class="summary-divider"></div>
                    
                    <div class="summary-row summary-total">
                        <span>Total Estimasi</span>
                        <span id="cart-total"><?php echo formatRupiah($grandTotal); ?></span>
                    </div>
                    
                    <div class="summary-note">
                        <i class="fas fa-info-circle"></i>
                        Harga sudah termasuk pajak. Ongkir gratis untuk wilayah Jawa & Bali.
                    </div>
                    
                    <a href="checkout.php" class="btn btn-checkout">
                        Lanjut ke Checkout <i class="fas fa-arrow-right"></i>
                    </a>
                    
                    <div class="payment-icons">
                        <span><i class="fas fa-shield-alt"></i> Pembayaran Aman</span>
                    </div>
                </div>
                
                <!-- Promo Code -->
                <div class="promo-card">
                    <p class="promo-label">Punya kode promo?</p>
                    <div class="promo-input-group">
                        <input type="text" placeholder="Masukkan kode..." id="promoCode">
                        <button type="button" class="btn-promo" onclick="applyPromo()">Pakai</button>
                    </div>
                    <p id="promoMsg" style="font-size:.85rem;margin-top:6px;"></p>
                </div>
            </aside>
            
        </div>
        
        <?php else: ?>
        <!-- Empty Cart -->
        <div class="empty-cart">
            <div class="empty-icon">
                <i class="fas fa-shopping-bag"></i>
            </div>
            <h2>Keranjang Masih Kosong</h2>
            <p>Yuk, jelajahi koleksi parfum premium kami dan temukan aroma favorit Anda.</p>
            <a href="products.php" class="btn btn-shop-now">
                <i class="fas fa-perfume"></i> Jelajahi Katalog
            </a>
        </div>
        <?php endif; ?>
        
    </div>
</section>

<?php include 'includes/footer.php'; ?>

<script>
function formatRupiah(n) {
    return 'Rp ' + parseInt(n).toLocaleString('id-ID');
}

function recalcTotal() {
    let subtotal = 0;
    document.querySelectorAll('.qty-input-sm').forEach(input => {
        const id    = input.id.replace('qty-', '');
        const price = parseInt(input.dataset.price);
        const qty   = parseInt(input.value);
        subtotal   += price * qty;
        const el = document.getElementById('item-total-' + id);
        if (el) el.textContent = formatRupiah(price * qty);
    });
    const subtotalEl = document.getElementById('cart-subtotal');
    const totalEl    = document.getElementById('cart-total');
    if (subtotalEl) subtotalEl.textContent = formatRupiah(subtotal);
    if (totalEl)    totalEl.textContent    = formatRupiah(subtotal);
}

function changeQty(id, change) {
    const input = document.getElementById('qty-' + id);
    let val = parseInt(input.value) + change;
    if (val < 1) val = 1;
    if (val > parseInt(input.max)) val = parseInt(input.max);
    input.value = val;
    recalcTotal();

    // Simpan ke server via AJAX tanpa refresh
    fetch('cart.php?update=' + id + '&qty=' + val, { method: 'GET' });
}

function applyPromo() {
    const kode     = document.getElementById('promoCode').value.trim();
    const msgEl    = document.getElementById('promoMsg');
    const totalEl  = document.getElementById('cart-total');
    const subtotal = parseInt(document.getElementById('cart-subtotal').textContent.replace(/\D/g,''));
    if (!kode) { msgEl.style.color='#e74c3c'; msgEl.textContent='Masukkan kode promo.'; return; }
    fetch('cart.php?check_promo=' + encodeURIComponent(kode) + '&subtotal=' + subtotal)
        .then(r => r.json())
        .then(data => {
            if (data.ok) {
                msgEl.style.color = '#27ae60';
                msgEl.textContent = data.msg;
                if (totalEl) totalEl.textContent = formatRupiah(subtotal - data.diskon);
                sessionStorage.setItem('promoKode', kode);
                sessionStorage.setItem('promoDiskon', data.diskon);
            } else {
                msgEl.style.color = '#e74c3c';
                msgEl.textContent = data.msg;
            }
        });
}
</script>

</body>
</html>