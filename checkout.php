<?php
// checkout.php - Halaman Checkout
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

// ── AJAX: validasi kupon ───────────────────────────────────
if (isset($_GET['check_promo'])) {
    header('Content-Type: application/json');
    $kode     = trim($_GET['check_promo']);
    $subtotal = (int)($_GET['subtotal'] ?? 0);
    $stmt = $conn->prepare("SELECT * FROM promo_codes WHERE kode=? AND is_active=1 AND berlaku_sampai >= CURDATE() AND (max_penggunaan IS NULL OR terpakai < max_penggunaan)");
    $stmt->bind_param("s", $kode); $stmt->execute();
    $promo = $stmt->get_result()->fetch_assoc();
    if (!$promo) { echo json_encode(['ok'=>false,'msg'=>'Kode promo tidak valid atau sudah kadaluarsa.']); exit; }
    if ($subtotal < $promo['min_pembelian']) { echo json_encode(['ok'=>false,'msg'=>'Minimum pembelian Rp '.number_format($promo['min_pembelian'],0,',','.')]); exit; }
    $diskon = $promo['tipe'] === 'persen' ? ($subtotal * $promo['nilai'] / 100) : $promo['nilai'];
    if ($promo['max_diskon']) $diskon = min($diskon, $promo['max_diskon']);
    echo json_encode(['ok'=>true,'diskon'=>(int)$diskon,'msg'=>'Promo berhasil! Hemat Rp '.number_format($diskon,0,',','.')]);
    exit;
}

// ==================== KURIR & PEMBAYARAN ====================
$kurirList = [
    'jne'      => ['nama' => 'JNE Reguler',      'harga' => 18000, 'estimasi' => '2-3 hari', 'icon' => 'fa-truck'],
    'jnt'      => ['nama' => 'J&T Express',       'harga' => 15000, 'estimasi' => '2-3 hari', 'icon' => 'fa-shipping-fast'],
    'sicepat'  => ['nama' => 'SiCepat BEST',      'harga' => 12000, 'estimasi' => '1-2 hari', 'icon' => 'fa-motorcycle'],
    'anteraja' => ['nama' => 'AnterAja',           'harga' => 14000, 'estimasi' => '2-3 hari', 'icon' => 'fa-box'],
];
$paymentMethods = [
    'transfer_bca' => ['nama' => 'Transfer BCA',         'icon' => 'fa-university', 'deskripsi' => 'Rekening BCA 123-456-7890 a/n Lumière Parfum'],
    'transfer_bni' => ['nama' => 'Transfer BNI',         'icon' => 'fa-university', 'deskripsi' => 'Rekening BNI 098-765-4321 a/n Lumière Parfum'],
    'cod'          => ['nama' => 'Bayar di Tempat (COD)','icon' => 'fa-hand-holding-usd', 'deskripsi' => 'Bayar saat barang diterima. Tambahan biaya COD: Rp 5.000'],
];

// ==================== AMBIL CART DARI DB ====================
$cart      = $_SESSION['cart'] ?? [];
$cartItems = [];
$subtotal  = 0;
$totalItems = 0;

foreach ($cart as $id => $item) {
    $stmt = $conn->prepare("SELECT p.*, b.nama_brand FROM products p LEFT JOIN brands b ON p.brand_id = b.brand_id WHERE p.product_id = ? AND p.status = 'aktif'");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $p = $stmt->get_result()->fetch_assoc();
    if ($p) {
        $price = $p['harga_diskon'] ?: $p['harga'];
        $total = $price * $item['qty'];
        $subtotal   += $total;
        $totalItems += $item['qty'];
        $cartItems[] = ['id' => $id, 'product' => $p, 'qty' => $item['qty'], 'price' => $price, 'total' => $total];
    }
}

if (empty($cartItems)) { header('Location: cart.php'); exit; }

// Ambil alamat tersimpan user
$savedAddresses = [];
if (!empty($_SESSION['user_id'])) {
    $stmtAddr = $conn->prepare("SELECT * FROM addresses WHERE user_id=? ORDER BY is_default DESC, created_at ASC");
    $stmtAddr->bind_param("i", $_SESSION['user_id']);
    $stmtAddr->execute();
    $savedAddresses = $stmtAddr->get_result()->fetch_all(MYSQLI_ASSOC);
}

$selectedKurir   = $_POST['kurir']   ?? 'sicepat';
$selectedPayment = $_POST['payment'] ?? 'transfer_bca';
$ongkir  = $kurirList[$selectedKurir]['harga']  ?? 12000;
$codFee  = ($selectedPayment === 'cod') ? 5000 : 0;
$grandTotal = $subtotal + $ongkir + $codFee;

// ==================== HANDLE SUBMIT ====================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['place_order'])) {
    $nama    = trim($_POST['nama']    ?? '');
    $telepon = trim($_POST['telepon'] ?? '');
    $alamat  = trim($_POST['alamat']  ?? '');
    $kota    = trim($_POST['kota']    ?? '');
    $kodepos = trim($_POST['kodepos'] ?? '');

    if ($nama && $telepon && $alamat && $kota && $kodepos) {
        // Generate order code
        $orderCode = 'LMR-' . strtoupper(substr(md5(uniqid()), 0, 8));

        // Simpan alamat sebagai JSON snapshot
        $alamatSnapshot = json_encode([
            'nama' => $nama, 'telepon' => $telepon,
            'alamat' => $alamat, 'kota' => $kota, 'kodepos' => $kodepos,
        ]);

        $userId = $_SESSION['user_id'] ?? null;

        // Proses kupon
        $promoKode  = trim($_POST['promo_kode'] ?? '');
        $promoDiskon = 0;
        if ($promoKode) {
            $stmtP = $conn->prepare("SELECT * FROM promo_codes WHERE kode=? AND is_active=1 AND berlaku_sampai >= CURDATE() AND (max_penggunaan IS NULL OR terpakai < max_penggunaan)");
            $stmtP->bind_param("s", $promoKode); $stmtP->execute();
            $promo = $stmtP->get_result()->fetch_assoc();
            if ($promo) {
                $promoDiskon = $promo['tipe'] === 'persen' ? ($subtotal * $promo['nilai'] / 100) : $promo['nilai'];
                if ($promo['max_diskon']) $promoDiskon = min($promoDiskon, $promo['max_diskon']);
                $conn->query("UPDATE promo_codes SET terpakai=terpakai+1 WHERE promo_id={$promo['promo_id']}");
            }
        }
        $grandTotal = $subtotal + $ongkir + $codFee - $promoDiskon;

        // Insert order
        $kurirNama   = $kurirList[$selectedKurir]['nama'];
        $statusBayar = ($selectedPayment === 'cod') ? 'pending' : 'belum';
        $statusOrder = 'pending';

        $stmt = $conn->prepare("INSERT INTO orders (order_code, user_id, alamat_snapshot, kurir, kurir_nama, ongkir, metode_bayar, cod_fee, subtotal, diskon, total, status, status_bayar) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)");
        $stmt->bind_param("sisssisiiiiss",
            $orderCode, $userId, $alamatSnapshot,
            $selectedKurir, $kurirNama, $ongkir,
            $selectedPayment, $codFee,
            $subtotal, $promoDiskon, $grandTotal, $statusOrder, $statusBayar
        );
        $stmt->execute();
        $orderId = $conn->insert_id;

        // Insert order items
        foreach ($cartItems as $item) {
            $p = $item['product'];
            $stmtItem = $conn->prepare("INSERT INTO order_items (order_id, product_id, nama_produk, brand, gambar, harga_satuan, qty, subtotal) VALUES (?,?,?,?,?,?,?,?)");
            $itemSubtotal = $item['price'] * $item['qty'];
            $stmtItem->bind_param("iisssiii",
                $orderId, $item['id'],
                $p['nama_produk'], $p['nama_brand'], $p['gambar_utama'],
                $item['price'], $item['qty'], $itemSubtotal
            );
            $stmtItem->execute();
        }

        // Insert payment
        $stmtPay = $conn->prepare("INSERT INTO payments (order_id, metode, jumlah, status) VALUES (?,?,?,?)");
        $payStatus = ($selectedPayment === 'cod') ? 'pending' : 'pending';
        $stmtPay->bind_param("isis", $orderId, $selectedPayment, $grandTotal, $payStatus);
        $stmtPay->execute();

        // Kosongkan cart
        $_SESSION['cart'] = [];
        $_SESSION['last_order_code'] = $orderCode;

        header('Location: user/orders.php?order=' . $orderCode);
        exit;
    }
}
?>



<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Lumière Parfum</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

<?php include 'includes/header.php'; ?>

<!-- Breadcrumb -->
<div class="breadcrumb">
    <div class="container">
        <a href="index.php">Beranda</a> / 
        <a href="cart.php">Keranjang</a> / 
        <span>Checkout</span>
    </div>
</div>

<!-- Checkout Section -->
<section class="checkout-section">
    <div class="container">
        <h1 class="checkout-title">Checkout <span class="checkout-step">Langkah terakhir</span></h1>
        
        <form method="POST" action="checkout.php" id="checkoutForm" class="checkout-layout" onsubmit="return validateCheckout()">
            <input type="hidden" name="promo_kode" id="promoKodeInput" value="">
            <input type="hidden" name="promo_diskon" id="promoDiskonInput" value="0">
            
            <!-- Kiri: Form Pengiriman & Pembayaran -->
            <div class="checkout-form-area">
                
                <!-- Alamat Pengiriman -->
                <div class="form-card">
                    <div class="form-header">
                        <span class="form-icon"><i class="fas fa-map-marker-alt"></i></span>
                        <h3>Alamat Pengiriman</h3>
                    </div>
                    
                    <div class="form-body">

                        <?php if (!empty($savedAddresses)): ?>
                        <div style="margin-bottom:16px;">
                            <label style="font-size:.85rem;font-weight:600;color:#555;display:block;margin-bottom:8px;">Pilih Alamat Tersimpan:</label>
                            <div style="display:flex;flex-direction:column;gap:8px;">
                                <?php foreach ($savedAddresses as $i => $addr): ?>
                                <label style="display:flex;align-items:flex-start;gap:10px;padding:12px;border:2px solid <?php echo $i===0?'var(--gold)':'#eee'; ?>;border-radius:10px;cursor:pointer;" class="addr-option">
                                    <input type="radio" name="pilih_alamat" value="<?php echo $i; ?>"
                                        <?php echo $i===0?'checked':''; ?>
                                        onchange="isiAlamat(<?php echo $i; ?>)"
                                        style="margin-top:3px;accent-color:var(--gold);">
                                    <div>
                                        <strong><?php echo htmlspecialchars($addr['label']); ?></strong>
                                        <?php if($addr['is_default']): ?><span style="font-size:.75rem;background:var(--gold);color:#fff;padding:1px 6px;border-radius:4px;margin-left:4px;">Utama</span><?php endif; ?><br>
                                        <span style="font-size:.85rem;color:#555;"><?php echo htmlspecialchars($addr['nama_penerima']); ?> • <?php echo htmlspecialchars($addr['telepon']); ?></span><br>
                                        <span style="font-size:.82rem;color:#888;"><?php echo htmlspecialchars($addr['alamat_lengkap']); ?>, <?php echo htmlspecialchars($addr['kota']); ?>, <?php echo htmlspecialchars($addr['kodepos']); ?></span>
                                    </div>
                                </label>
                                <?php endforeach; ?>
                                <label style="display:flex;align-items:center;gap:10px;padding:12px;border:2px dashed #ddd;border-radius:10px;cursor:pointer;" class="addr-option">
                                    <input type="radio" name="pilih_alamat" value="manual" onchange="isiAlamat('manual')" style="accent-color:var(--gold);">
                                    <span style="font-size:.85rem;color:#888;"><i class="fas fa-plus"></i> Gunakan alamat lain</span>
                                </label>
                            </div>
                        </div>
                        <div id="formAlamatManual" style="<?php echo empty($savedAddresses)?'':'display:none'; ?>">
                        <?php else: ?>
                        <div id="formAlamatManual">
                        <?php endif; ?>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="nama">Nama Lengkap <span class="required">*</span></label>
                                <input type="text" id="nama" name="nama" placeholder="Contoh: Budi Santoso" value="<?php echo htmlspecialchars($savedAddresses[0]['nama_penerima'] ?? ''); ?>">
                            </div>
                            <div class="form-group">
                                <label for="telepon">Nomor Telepon <span class="required">*</span></label>
                                <input type="tel" id="telepon" name="telepon" placeholder="Contoh: 08123456789" value="<?php echo htmlspecialchars($savedAddresses[0]['telepon'] ?? ''); ?>">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="alamat">Alamat Lengkap <span class="required">*</span></label>
                            <textarea id="alamat" name="alamat" rows="3" placeholder="Jalan, RT/RW, Kelurahan, Kecamatan"><?php echo htmlspecialchars($savedAddresses[0]['alamat_lengkap'] ?? ''); ?></textarea>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="kota">Kota / Kabupaten <span class="required">*</span></label>
                                <input type="text" id="kota" name="kota" placeholder="Contoh: Jakarta Selatan" value="<?php echo htmlspecialchars($savedAddresses[0]['kota'] ?? ''); ?>">
                            </div>
                            <div class="form-group">
                                <label for="kodepos">Kode Pos <span class="required">*</span></label>
                                <input type="text" id="kodepos" name="kodepos" placeholder="Contoh: 12345" value="<?php echo htmlspecialchars($savedAddresses[0]['kodepos'] ?? ''); ?>">
                            </div>
                        </div>
                        </div><!-- /formAlamatManual -->
                    </div>
                </div>
                
                <!-- Pilih Kurir -->
                <div class="form-card">
                    <div class="form-header">
                        <span class="form-icon"><i class="fas fa-shipping-fast"></i></span>
                        <h3>Pilih Kurir Pengiriman</h3>
                    </div>
                    
                    <div class="form-body">
                        <div class="option-list">
                            <?php foreach ($kurirList as $key => $kurir): ?>
                            <label class="option-item <?php echo $selectedKurir === $key ? 'selected' : ''; ?>" onclick="selectKurir('<?php echo $key; ?>')">
                                <input type="radio" name="kurir" value="<?php echo $key; ?>" 
                                    <?php echo $selectedKurir === $key ? 'checked' : ''; ?>
                                    class="kurir-radio">
                                <div class="option-content">
                                    <div class="option-main">
                                        <span class="option-icon"><i class="fas <?php echo $kurir['icon']; ?>"></i></span>
                                        <div class="option-info">
                                            <h4><?php echo $kurir['nama']; ?></h4>
                                            <p>Estimasi: <?php echo $kurir['estimasi']; ?></p>
                                        </div>
                                    </div>
                                    <span class="option-price" data-kurir-price="<?php echo $kurir['harga']; ?>"><?php echo formatRupiah($kurir['harga']); ?></span>
                                </div>
                            </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                
                <!-- Pilih Pembayaran -->
                <div class="form-card">
                    <div class="form-header">
                        <span class="form-icon"><i class="fas fa-credit-card"></i></span>
                        <h3>Metode Pembayaran</h3>
                    </div>
                    
                    <div class="form-body">
                        <div class="option-list">
                            <?php foreach ($paymentMethods as $key => $method): ?>
                            <label class="option-item <?php echo $selectedPayment === $key ? 'selected' : ''; ?>" onclick="selectPayment('<?php echo $key; ?>')">
                                <input type="radio" name="payment" value="<?php echo $key; ?>" 
                                    <?php echo $selectedPayment === $key ? 'checked' : ''; ?>
                                    class="payment-radio">
                                <div class="option-content">
                                    <div class="option-main">
                                        <span class="option-icon"><i class="fas <?php echo $method['icon']; ?>"></i></span>
                                        <div class="option-info">
                                            <h4><?php echo $method['nama']; ?></h4>
                                            <p><?php echo $method['deskripsi']; ?></p>
                                        </div>
                                    </div>
                                </div>
                            </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                
            </div>
            
            <!-- Kanan: Ringkasan Pesanan -->
            <aside class="checkout-summary-area">
                <div class="summary-sticky">
                    
                    <!-- Ringkasan Produk -->
                    <div class="form-card summary-card-light">
                        <div class="form-header">
                            <h3>Ringkasan Pesanan</h3>
                            <span class="item-count"><?php echo $totalItems; ?> item</span>
                        </div>
                        <input type="hidden" id="subtotalValue" data-value="<?php echo $subtotal; ?>">
                        
                        <div class="checkout-items">
                            <?php foreach ($cartItems as $item): $p = $item['product']; ?>
                            <div class="checkout-item">
                                <img src="<?php echo htmlspecialchars($p['gambar_utama']); ?>" alt="<?php echo htmlspecialchars($p['nama_produk']); ?>">
                                <div class="checkout-item-info">
                                    <h4><?php echo htmlspecialchars($p['nama_produk']); ?></h4>
                                    <p><?php echo htmlspecialchars($p['nama_brand']); ?> • <?php echo $item['qty']; ?>x</p>
                                    <span class="checkout-item-price"><?php echo formatRupiah($item['total']); ?></span>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <div class="checkout-divider"></div>
                        
                        <!-- Perhitungan -->
                        <div class="checkout-calc">
                            <div class="calc-row">
                                <span>Subtotal</span>
                                <span><?php echo formatRupiah($subtotal); ?></span>
                            </div>
                            <div class="calc-row">
                                <span>Ongkir (<span id="kurirNameDisplay"><?php echo $kurirList[$selectedKurir]['nama']; ?></span>)</span>
                                <span id="ongkirDisplay"><?php echo formatRupiah($ongkir); ?></span>
                            </div>
                            <div class="calc-row" id="codDisplay" style="<?php echo $codFee > 0 ? '' : 'display:none'; ?>">
                                <span>Biaya COD</span>
                                <span><?php echo formatRupiah($codFee); ?></span>
                            </div>
                            <div class="calc-row" id="diskonDisplay" style="display:none;color:green;">
                                <span>Diskon Kupon</span>
                                <span id="diskonVal">-Rp 0</span>
                            </div>
                        </div>
                        
                        <div class="checkout-divider"></div>

                        <!-- Kupon -->
                        <div style="margin-bottom:12px;">
                            <div style="display:flex;gap:8px;">
                                <input type="text" id="kuponInput" placeholder="Kode kupon..." style="flex:1;padding:8px 12px;border:1px solid #ddd;border-radius:8px;font-size:.85rem;">
                                <button type="button" onclick="pakaiKupon()" style="padding:8px 14px;background:var(--gold);color:#fff;border:none;border-radius:8px;cursor:pointer;font-size:.85rem;">Pakai</button>
                            </div>
                            <p id="kuponMsg" style="font-size:.82rem;margin-top:5px;"></p>
                        </div>
                        
                        <div class="checkout-divider"></div>
                        
                        <div class="calc-row calc-total">
                            <span>Total Pembayaran</span>
                            <span id="totalDisplay"><?php echo formatRupiah($grandTotal); ?></span>
                        </div>
                    </div>
                    
                    <!-- Tombol Bayar -->
                    <button type="submit" name="place_order" class="btn btn-bayar">
                        <i class="fas fa-lock"></i> Bayar Sekarang
                    </button>
                    
                    <p class="secure-note">
                        <i class="fas fa-shield-alt"></i> Pembayaran aman & terenkripsi
                    </p>
                    
                </div>
            </aside>
            
        </form>
    </div>
</section>

<?php include 'includes/footer.php'; ?>

<script>
let activeDiskon = 0;

function pakaiKupon() {
    const kode     = document.getElementById('kuponInput').value.trim();
    const msgEl    = document.getElementById('kuponMsg');
    const subtotal = parseInt(document.getElementById('subtotalValue').dataset.value);
    if (!kode) { msgEl.style.color='#e74c3c'; msgEl.textContent='Masukkan kode kupon.'; return; }

    fetch('checkout.php?check_promo=' + encodeURIComponent(kode) + '&subtotal=' + subtotal)
        .then(r => r.json())
        .then(data => {
            if (data.ok) {
                activeDiskon = data.diskon;
                msgEl.style.color = '#27ae60';
                msgEl.textContent = data.msg;
                document.getElementById('promoKodeInput').value   = kode;
                document.getElementById('promoDiskonInput').value = activeDiskon;
                document.getElementById('diskonDisplay').style.display = 'flex';
                document.getElementById('diskonVal').textContent = '-' + formatRupiah(activeDiskon);
                updateTotals();
            } else {
                activeDiskon = 0;
                document.getElementById('promoKodeInput').value   = '';
                document.getElementById('promoDiskonInput').value = 0;
                document.getElementById('diskonDisplay').style.display = 'none';
                msgEl.style.color = '#e74c3c';
                msgEl.textContent = data.msg;
                updateTotals();
            }
        });
}

// Data alamat tersimpan
const savedAddresses = <?php echo json_encode(array_values($savedAddresses)); ?>;

function isiAlamat(idx) {
    const manual = document.getElementById('formAlamatManual');
    // highlight border
    document.querySelectorAll('.addr-option').forEach(el => el.style.borderColor = '#eee');
    const selected = document.querySelectorAll('.addr-option')[typeof idx === 'number' ? idx : savedAddresses.length];
    if (selected) selected.style.borderColor = 'var(--gold)';

    if (idx === 'manual') {
        manual.style.display = 'block';
        ['nama','telepon','alamat','kota','kodepos'].forEach(f => {
            const el = document.getElementById(f);
            if (el) { el.value = ''; el.required = true; }
        });
        return;
    }
    const addr = savedAddresses[idx];
    if (!addr) return;
    manual.style.display = 'block';
    document.getElementById('nama').value    = addr.nama_penerima || '';
    document.getElementById('telepon').value = addr.telepon || '';
    document.getElementById('alamat').value  = addr.alamat_lengkap || '';
    document.getElementById('kota').value    = addr.kota || '';
    document.getElementById('kodepos').value = addr.kodepos || '';
}

// Store form data in sessionStorage to persist across refreshes
function saveFormData() {
    const form = document.getElementById('checkoutForm');
    const formData = new FormData(form);
    const data = {};
    for (let [key, value] of formData.entries()) {
        data[key] = value;
    }
    sessionStorage.setItem('checkoutData', JSON.stringify(data));
}

// Restore form data from sessionStorage
function restoreFormData() {
    const saved = sessionStorage.getItem('checkoutData');
    if (saved) {
        const data = JSON.parse(saved);
        const form = document.getElementById('checkoutForm');
        for (let key in data) {
            const field = form.querySelector('[name="' + key + '"]');
            if (field) {
                if (field.type === 'radio') {
                    const radio = form.querySelector('[name="' + key + '"][value="' + data[key] + '"]');
                    if (radio) radio.checked = true;
                } else {
                    field.value = data[key];
                }
            }
        }
    }
}

// Auto-save form data on input
const formFields = document.querySelectorAll('#checkoutForm input, #checkoutForm textarea');
formFields.forEach(field => {
    field.addEventListener('input', saveFormData);
    field.addEventListener('change', saveFormData);
});

// Restore on page load
window.addEventListener('load', restoreFormData);

// Kurir selection - update without refresh
function selectKurir(kurirKey) {
    // Update visual selection
    document.querySelectorAll('.kurir-radio').forEach(r => {
        r.checked = false;
        r.closest('.option-item').classList.remove('selected');
    });
    const selected = document.querySelector('.kurir-radio[value="' + kurirKey + '"]');
    if (selected) {
        selected.checked = true;
        selected.closest('.option-item').classList.add('selected');
    }

    // Update totals dynamically
    updateTotals();
    saveFormData();
}

// Payment selection - update without refresh
function selectPayment(paymentKey) {
    document.querySelectorAll('.payment-radio').forEach(r => {
        r.checked = false;
        r.closest('.option-item').classList.remove('selected');
    });
    const selected = document.querySelector('.payment-radio[value="' + paymentKey + '"]');
    if (selected) {
        selected.checked = true;
        selected.closest('.option-item').classList.add('selected');
    }

    // Update COD fee if applicable
    updateTotals();
    saveFormData();
}

// Update totals dynamically without page refresh
function updateTotals() {
    const kurirRadios = document.querySelectorAll('.kurir-radio');
    let selectedKurirPrice = 0;
    let selectedKurirName = '';

    kurirRadios.forEach(radio => {
        if (radio.checked) {
            const priceEl = radio.closest('.option-item').querySelector('[data-kurir-price]');
            if (priceEl) {
                selectedKurirPrice = parseInt(priceEl.dataset.kurirPrice);
                selectedKurirName = radio.closest('.option-item').querySelector('h4').textContent;
            }
        }
    });

    // Check if COD is selected
    const paymentRadios = document.querySelectorAll('.payment-radio');
    let codFee = 0;
    paymentRadios.forEach(radio => {
        if (radio.checked && radio.value === 'cod') {
            codFee = 5000;
        }
    });

    // Get subtotal from PHP (stored in data attribute)
    const subtotal = parseInt(document.getElementById('subtotalValue').dataset.value);

    // Update display
    const ongkirDisplay = document.getElementById('ongkirDisplay');
    const codDisplay = document.getElementById('codDisplay');
    const totalDisplay = document.getElementById('totalDisplay');
    const kurirNameDisplay = document.getElementById('kurirNameDisplay');

    if (ongkirDisplay) ongkirDisplay.textContent = formatRupiah(selectedKurirPrice);
    if (kurirNameDisplay) kurirNameDisplay.textContent = selectedKurirName;

    if (codFee > 0) {
        if (!codDisplay) {
            // Create COD fee row if doesn't exist
            const calcContainer = document.querySelector('.checkout-calc');
            const newRow = document.createElement('div');
            newRow.className = 'calc-row';
            newRow.id = 'codDisplay';
            newRow.innerHTML = '<span>Biaya COD</span><span>' + formatRupiah(codFee) + '</span>';
            calcContainer.appendChild(newRow);
        } else {
            codDisplay.querySelector('span:last-child').textContent = formatRupiah(codFee);
            codDisplay.style.display = 'flex';
        }
    } else if (codDisplay) {
        codDisplay.style.display = 'none';
    }

    const grandTotal = subtotal + selectedKurirPrice + codFee - (activeDiskon || 0);
    if (totalDisplay) totalDisplay.textContent = formatRupiah(grandTotal);
}

// Format rupiah helper
function formatRupiah(angka) {
    return 'Rp ' + angka.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
}

// Validate before submit
function validateCheckout() {
    saveFormData();
    const nama = document.getElementById('nama').value.trim();
    const telepon = document.getElementById('telepon').value.trim();
    const alamat = document.getElementById('alamat').value.trim();
    const kota = document.getElementById('kota').value.trim();
    const kodepos = document.getElementById('kodepos').value.trim();

    if (!nama || !telepon || !alamat || !kota || !kodepos) {
        alert('Mohon lengkapi semua data alamat pengiriman.');
        return false;
    }

    return true;
}
</script>

</body>
</html>