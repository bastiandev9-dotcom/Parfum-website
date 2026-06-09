<?php
// product-detail.php - Halaman Detail Produk
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

$productId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$userId    = $_SESSION['user_id'] ?? null;

// ── AJAX: toggle wishlist ──────────────────────────────────
if (isset($_GET['toggle_wishlist']) && $userId) {
    header('Content-Type: application/json');
    $pid = (int)$_GET['toggle_wishlist'];
    $chk = $conn->prepare("SELECT wishlist_id FROM wishlists WHERE user_id=? AND product_id=?");
    $chk->bind_param("ii", $userId, $pid); $chk->execute();
    if ($chk->get_result()->fetch_assoc()) {
        $conn->query("DELETE FROM wishlists WHERE user_id=$userId AND product_id=$pid");
        echo json_encode(['wishlisted' => false]);
    } else {
        $conn->query("INSERT IGNORE INTO wishlists (user_id, product_id) VALUES ($userId, $pid)");
        echo json_encode(['wishlisted' => true]);
    }
    exit;
}

// ── SUBMIT REVIEW ──────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_review']) && $userId) {
    $rating   = (int)($_POST['rating'] ?? 5);
    $komentar = trim($_POST['komentar'] ?? '');
    $orderId  = (int)($_POST['order_id'] ?? 0);
    if ($komentar && $rating >= 1 && $rating <= 5) {
        $stmt = $conn->prepare("INSERT INTO reviews (user_id, product_id, order_id, rating, komentar, is_approved) VALUES (?,?,?,?,?,1)");
        $stmt->bind_param("iiiis", $userId, $productId, $orderId, $rating, $komentar);
        $stmt->execute();
        // Update rating_avg di products
        $conn->query("UPDATE products SET rating_avg=(SELECT AVG(rating) FROM reviews WHERE product_id=$productId AND is_approved=1), total_review=(SELECT COUNT(*) FROM reviews WHERE product_id=$productId AND is_approved=1) WHERE product_id=$productId");
    }
    header("Location: product-detail.php?id=$productId#review");
    exit;
}

// Ambil produk dari database
$stmt = $conn->prepare("
    SELECT p.*, b.nama_brand, c.nama_kategori
    FROM products p
    LEFT JOIN brands b ON p.brand_id = b.brand_id
    LEFT JOIN categories c ON p.category_id = c.category_id
    WHERE p.product_id = ? AND p.status = 'aktif'
");
$stmt->bind_param("i", $productId);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();

if (!$product) {
    header('Location: products.php');
    exit;
}

// Ambil gallery
$galleryResult = $conn->prepare("SELECT gambar FROM product_gallery WHERE product_id = ? ORDER BY urutan ASC");
$galleryResult->bind_param("i", $productId);
$galleryResult->execute();
$galleryRows = $galleryResult->get_result()->fetch_all(MYSQLI_ASSOC);
$gallery = array_column($galleryRows, 'gambar');
if (empty($gallery)) $gallery = [$product['gambar_utama']];

// Ambil notes aroma
$notesResult = $conn->prepare("SELECT note, tipe_note FROM product_notes WHERE product_id = ? ORDER BY urutan ASC");
$notesResult->bind_param("i", $productId);
$notesResult->execute();
$notes = $notesResult->get_result()->fetch_all(MYSQLI_ASSOC);

// Ambil reviews
$reviewResult = $conn->prepare("
    SELECT r.*, u.nama, u.avatar
    FROM reviews r
    LEFT JOIN users u ON r.user_id = u.user_id
    WHERE r.product_id = ? AND r.is_approved = 1
    ORDER BY r.created_at DESC
    LIMIT 10
");
$reviewResult->bind_param("i", $productId);
$reviewResult->execute();
$reviews = $reviewResult->get_result()->fetch_all(MYSQLI_ASSOC);

// Ambil variants (ukuran)
$variantResult = $conn->prepare("SELECT * FROM product_variants WHERE product_id = ? AND is_active = 1");
$variantResult->bind_param("i", $productId);
$variantResult->execute();
$variants = $variantResult->get_result()->fetch_all(MYSQLI_ASSOC);

// Cek wishlist
$isWishlisted = false;
if ($userId) {
    $wChk = $conn->prepare("SELECT wishlist_id FROM wishlists WHERE user_id=? AND product_id=?");
    $wChk->bind_param("ii", $userId, $productId); $wChk->execute();
    $isWishlisted = (bool)$wChk->get_result()->fetch_assoc();
}

// Cek apakah user punya order selesai untuk produk ini (untuk review)
$userOrderId = 0;
if ($userId) {
    $oChk = $conn->query("SELECT o.order_id FROM orders o JOIN order_items oi ON o.order_id=oi.order_id WHERE o.user_id=$userId AND oi.product_id=$productId AND o.status='selesai' AND NOT EXISTS (SELECT 1 FROM reviews r WHERE r.user_id=$userId AND r.product_id=$productId) LIMIT 1");
    $oRow = $oChk->fetch_assoc();
    $userOrderId = $oRow['order_id'] ?? 0;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['nama_produk']); ?> - Lumière Parfum</title>
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
        <span><?php echo htmlspecialchars($product['nama_produk']); ?></span>
    </div>
</div>

<!-- Product Detail Section -->
<section class="product-detail-v2">
    <div class="container">

        <div class="product-detail-grid-v2">

            <!-- Kiri: Gallery -->
            <div class="product-gallery-v2">
                <div class="gallery-main-v2" id="galleryMain">
                    <?php if($product['is_best_seller']): ?>
                        <span class="badge badge-detail">Best Seller</span>
                    <?php elseif($product['is_new_arrival']): ?>
                        <span class="badge badge-detail">Baru</span>
                    <?php endif; ?>
                    <img src="<?php echo htmlspecialchars($gallery[0]); ?>" alt="<?php echo htmlspecialchars($product['nama_produk']); ?>" id="mainImage">
                    <div class="zoom-lens" id="zoomLens"></div>
                </div>
                <div class="zoom-result" id="zoomResult"></div>
                <div class="gallery-thumbs-v2">
                    <?php foreach($gallery as $index => $img): ?>
                        <div class="thumb-v2 <?php echo $index === 0 ? 'active' : ''; ?>" onclick="changeImage('<?php echo htmlspecialchars($img); ?>', this)">
                            <img src="<?php echo htmlspecialchars($img); ?>" alt="<?php echo htmlspecialchars($product['nama_produk']); ?> <?php echo $index+1; ?>">
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Kanan: Informasi -->
            <div class="product-info-v2">
                <div class="info-header-v2">
                    <p class="brand-name-v2"><?php echo htmlspecialchars($product['nama_brand']); ?></p>
                    <h1 class="product-title-v2"><?php echo htmlspecialchars($product['nama_produk']); ?></h1>
                    <div class="detail-rating-v2">
                        <div class="stars-v2">
                            <?php
                            $r = floatval($product['rating_avg']);
                            $fullStars = floor($r); $halfStar = ($r - $fullStars) >= 0.5;
                            for($i=1;$i<=5;$i++){
                                if($i<=$fullStars) echo '<i class="fas fa-star"></i>';
                                elseif($i==$fullStars+1&&$halfStar) echo '<i class="fas fa-star-half-alt"></i>';
                                else echo '<i class="far fa-star"></i>';
                            }
                            ?>
                        </div>
                        <span class="rating-text-v2"><?php echo $product['rating_avg']; ?> / 5.0</span>
                        <span class="sold-count-v2">• <?php echo $product['total_terjual']; ?> terjual</span>
                    </div>
                </div>

                <!-- Harga -->
                <div class="detail-price-v2">
                    <?php if($product['harga_diskon']): ?>
                        <span class="price-original-v2"><?php echo formatRupiah($product['harga']); ?></span>
                        <span class="price-final-v2"><?php echo formatRupiah($product['harga_diskon']); ?></span>
                        <span class="discount-badge-v2">-<?php echo round((1 - $product['harga_diskon']/$product['harga'])*100); ?>%</span>
                    <?php else: ?>
                        <span class="price-final-v2"><?php echo formatRupiah($product['harga']); ?></span>
                    <?php endif; ?>
                </div>

                <!-- Notes -->
                <?php if(!empty($notes)): ?>
                <div class="fragrance-notes-v2">
                    <p class="notes-label-v2"><i class="fas fa-wind"></i> Notes Aroma:</p>
                    <div class="notes-tags-v2">
                        <?php foreach($notes as $note): ?>
                            <span class="note-tag-v2"><?php echo htmlspecialchars($note['note']); ?></span>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Ukuran & Kategori -->
                <div style="display:flex;gap:24px;align-items:center;flex-wrap:wrap;">
                <?php if(!empty($variants)): ?>
                <p class="selector-label-v2">Ukuran: <strong><?php echo htmlspecialchars($variants[0]['ukuran'] ?? $variants[0]['nama_variant'] ?? '-'); ?></strong></p>
                <?php endif; ?>
                <?php if(!empty($product['nama_kategori'])): ?>
                <p class="selector-label-v2">Kategori: <strong><?php echo htmlspecialchars($product['nama_kategori']); ?></strong></p>
                <?php endif; ?>
                </div>

                <!-- Quantity -->
                <div class="quantity-selector-v2">
                    <p class="selector-label-v2">Jumlah:</p>
                    <div class="qty-control-v2">
                        <button type="button" class="qty-btn-v2" onclick="updateQty(-1)">-</button>
                        <input type="number" id="qtyInput" value="1" min="1" max="<?php echo $product['stok']; ?>" readonly>
                        <button type="button" class="qty-btn-v2" onclick="updateQty(1)">+</button>
                    </div>
                    <p class="stock-info-v2"><i class="fas fa-check-circle"></i> Stok tersedia: <?php echo $product['stok']; ?> pcs</p>
                </div>

                <!-- Action Buttons -->
                <div class="detail-actions-v2">
                    <a href="cart.php?add=<?php echo $product['product_id']; ?>" class="btn btn-cart-v2">
                        <i class="fas fa-shopping-bag"></i> Tambah ke Keranjang
                    </a>
                    <a href="checkout.php?buy=<?php echo $product['product_id']; ?>" class="btn btn-buy-v2">
                        <i class="fas fa-bolt"></i> Beli Sekarang
                    </a>
                    <?php if ($userId): ?>
                    <button type="button" id="btnWishlist" onclick="toggleWishlist(<?php echo $productId; ?>)"
                        style="width:44px;height:44px;border-radius:50%;border:2px solid <?php echo $isWishlisted?'#e74c3c':'#ddd'; ?>;background:#fff;cursor:pointer;font-size:1.1rem;color:<?php echo $isWishlisted?'#e74c3c':'#aaa'; ?>;"
                        title="<?php echo $isWishlisted?'Hapus dari Wishlist':'Tambah ke Wishlist'; ?>">
                        <i class="<?php echo $isWishlisted?'fas':'far'; ?> fa-heart"></i>
                    </button>
                    <?php endif; ?>
                </div>

                <!-- Tags -->
                <div class="detail-meta-v2">
                    <span class="meta-tag-v2"><i class="fas fa-tag"></i> <?php echo ucfirst($product['aroma']); ?></span>
                    <span class="meta-tag-v2"><i class="fas fa-venus-mars"></i> <?php echo ucfirst($product['gender']); ?></span>
                    <span class="meta-tag-v2"><i class="fas fa-shipping-fast"></i> Gratis Ongkir</span>
                </div>
            </div>
        </div>

        <!-- Tabs Section -->
        <div class="tabs-card-v2">
            <div class="tabs-header-v2">
                <button class="tab-btn-v2 active" onclick="switchTab('deskripsi')">Deskripsi</button>
                <button class="tab-btn-v2" onclick="switchTab('review')">Review (<?php echo count($reviews); ?>)</button>
                <button class="tab-btn-v2" onclick="switchTab('pengiriman')">Pengiriman</button>
            </div>

            <div class="tabs-content-v2">
                <!-- Tab Deskripsi -->
                <div id="deskripsi" class="tab-panel-v2 active">
                    <div class="desc-content-v2">
                        <p><?php echo nl2br(htmlspecialchars($product['deskripsi'])); ?></p>
                        <div class="feature-list-v2">
                            <div class="feature-item-v2">
                                <i class="fas fa-clock"></i>
                                <div><h4>Longevity</h4><p>8-12 jam tergantung aktivitas dan jenis kulit</p></div>
                            </div>
                            <div class="feature-item-v2">
                                <i class="fas fa-cloud"></i>
                                <div><h4>Sillage</h4><p>Moderate to Heavy — aroma menyebar 2-3 meter</p></div>
                            </div>
                            <div class="feature-item-v2">
                                <i class="fas fa-tag"></i>
                                <div><h4>Kategori</h4><p><?php echo ucfirst($product['gender']); ?> • <?php echo ucfirst($product['aroma']); ?></p></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tab Review -->
                <div id="review" class="tab-panel-v2">
                    <div class="reviews-section-v2">
                        <div class="rating-summary-v2">
                            <div class="summary-left-v2">
                                <span class="big-rating-v2"><?php echo $product['rating_avg']; ?></span>
                                <div class="stars-v2">
                                    <?php
                                    $r = floatval($product['rating_avg']);
                                    $fs = floor($r); $hs = ($r-$fs)>=0.5;
                                    for($i=1;$i<=5;$i++){
                                        if($i<=$fs) echo '<i class="fas fa-star"></i>';
                                        elseif($i==$fs+1&&$hs) echo '<i class="fas fa-star-half-alt"></i>';
                                        else echo '<i class="far fa-star"></i>';
                                    }
                                    ?>
                                </div>
                                <span class="total-reviews-v2"><?php echo count($reviews); ?> review</span>
                            </div>
                        </div>

                        <div class="reviews-list-v2">
                            <?php if(empty($reviews)): ?>
                                <p style="color:#999;padding:20px 0;">Belum ada review untuk produk ini.</p>
                            <?php else: foreach($reviews as $review): ?>
                            <div class="review-item-v2">
                                <div class="review-avatar-v2"><?php echo strtoupper(substr($review['nama'] ?? 'U', 0, 2)); ?></div>
                                <div class="review-content-v2">
                                    <div class="review-header-v2">
                                        <h4><?php echo htmlspecialchars($review['nama'] ?? 'Anonim'); ?></h4>
                                        <span class="review-date-v2"><?php echo date('d M Y', strtotime($review['created_at'])); ?></span>
                                    </div>
                                    <div class="review-stars-v2">
                                        <?php for($i=1;$i<=5;$i++) echo '<i class="fas fa-star '.($i<=$review['rating']?'':'empty-v2').'"></i>'; ?>
                                    </div>
                                    <p class="review-text-v2"><?php echo htmlspecialchars($review['komentar']); ?></p>
                                </div>
                            </div>
                            <?php endforeach; endif; ?>
                        </div>

                        <?php if ($userId && $userOrderId): ?>
                        <div style="margin-top:24px;padding:20px;background:#f9f6f0;border-radius:12px;">
                            <h4 style="margin:0 0 12px;font-size:1rem;">Tulis Review Anda</h4>
                            <form method="POST">
                                <input type="hidden" name="submit_review" value="1">
                                <input type="hidden" name="order_id" value="<?php echo $userOrderId; ?>">
                                <div style="margin-bottom:10px;">
                                    <?php for($i=1;$i<=5;$i++): ?>
                                    <label style="cursor:pointer;font-size:1.4rem;color:#ddd;" class="star-lbl">
                                        <input type="radio" name="rating" value="<?php echo $i; ?>" style="display:none;" <?php echo $i==5?'checked':''; ?> onchange="highlightStars(<?php echo $i; ?>)">
                                        <i class="<?php echo $i<=5?'fas':'far'; ?> fa-star" id="star<?php echo $i; ?>" style="color:<?php echo $i<=5?'var(--gold)':'#ddd'; ?>"></i>
                                    </label>
                                    <?php endfor; ?>
                                </div>
                                <textarea name="komentar" rows="3" placeholder="Ceritakan pengalaman Anda..." required style="width:100%;padding:10px;border:1px solid #ddd;border-radius:8px;font-size:.9rem;resize:vertical;"></textarea>
                                <button type="submit" class="btn" style="margin-top:10px;">Kirim Review</button>
                            </form>
                        </div>
                        <?php elseif($userId && !$userOrderId): ?>
                        <p style="color:#aaa;font-size:.85rem;margin-top:16px;"><i class="fas fa-info-circle"></i> Hanya pembeli yang sudah menerima pesanan yang dapat memberikan review.</p>
                        <?php else: ?>
                        <p style="color:#aaa;font-size:.85rem;margin-top:16px;"><a href="login.php" style="color:var(--gold);">Login</a> untuk menulis review.</p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Tab Pengiriman -->
                <div id="pengiriman" class="tab-panel-v2">
                    <div class="shipping-info-v2">
                        <div class="shipping-item-v2">
                            <i class="fas fa-truck"></i>
                            <div>
                                <h4>JNE Reguler</h4>
                                <p>Rp 18.000 • Estimasi 2-3 hari kerja</p>
                            </div>
                        </div>
                        <div class="shipping-item-v2">
                            <i class="fas fa-shipping-fast"></i>
                            <div>
                                <h4>J&T Express</h4>
                                <p>Rp 15.000 • Estimasi 2-3 hari kerja</p>
                            </div>
                        </div>
                        <div class="shipping-item-v2">
                            <i class="fas fa-motorcycle"></i>
                            <div>
                                <h4>SiCepat BEST</h4>
                                <p>Rp 12.000 • Estimasi 1-2 hari kerja</p>
                            </div>
                        </div>
                        <div class="shipping-note-v2">
                            <i class="fas fa-info-circle"></i>
                            <p>Gratis ongkir untuk pembelian di atas Rp 500.000 di wilayah Jawa & Bali.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</section>

<?php include 'includes/footer.php'; ?>

<script>
// Ganti gambar utama dari thumbnail
function changeImage(src, thumb) {
    const mainImg = document.getElementById('mainImage');
    mainImg.style.opacity = '0';
    setTimeout(() => {
        mainImg.src = src;
        mainImg.style.opacity = '1';
    }, 200);

    document.querySelectorAll('.thumb-v2').forEach(t => t.classList.remove('active'));
    thumb.classList.add('active');
}

// Update quantity
function updateQty(change) {
    const input = document.getElementById('qtyInput');
    let val = parseInt(input.value) + change;
    const max = parseInt(input.max);
    if(val < 1) val = 1;
    if(val > max) val = max;
    input.value = val;
}

// Switch tab
function switchTab(tabName) {
    document.querySelectorAll('.tab-btn-v2').forEach(btn => btn.classList.remove('active'));
    document.querySelectorAll('.tab-panel-v2').forEach(panel => panel.classList.remove('active'));

    event.target.classList.add('active');
    document.getElementById(tabName).classList.add('active');
}

// Set rating (visual only)
function setRating(n) {
    const stars = document.querySelectorAll('.star-input-v2 i');
    stars.forEach((star, index) => {
        if(index < n) {
            star.classList.remove('far');
            star.classList.add('fas');
        } else {
            star.classList.remove('fas');
            star.classList.add('far');
        }
    });
}

function toggleWishlist(pid) {
    fetch('product-detail.php?id=' + pid + '&toggle_wishlist=' + pid)
        .then(r => r.json())
        .then(data => {
            const btn = document.getElementById('btnWishlist');
            const icon = btn.querySelector('i');
            if (data.wishlisted) {
                icon.className = 'fas fa-heart';
                btn.style.color = '#e74c3c';
                btn.style.borderColor = '#e74c3c';
            } else {
                icon.className = 'far fa-heart';
                btn.style.color = '#aaa';
                btn.style.borderColor = '#ddd';
            }
        });
}

function highlightStars(n) {
    for (let i = 1; i <= 5; i++) {
        const s = document.getElementById('star' + i);
        if (s) s.style.color = i <= n ? 'var(--gold)' : '#ddd';
    }
}

// Zoom functionality
const galleryMain = document.getElementById('galleryMain');
const mainImage = document.getElementById('mainImage');
const zoomLens = document.getElementById('zoomLens');
const zoomResult = document.getElementById('zoomResult');

if(galleryMain && mainImage && zoomLens && zoomResult) {
    galleryMain.addEventListener('mousemove', function(e) {
        const rect = galleryMain.getBoundingClientRect();
        const x = e.clientX - rect.left;
        const y = e.clientY - rect.top;

        const lensSize = 80;
        const lensX = x - lensSize/2;
        const lensY = y - lensSize/2;

        zoomLens.style.display = 'block';
        zoomLens.style.left = lensX + 'px';
        zoomLens.style.top = lensY + 'px';

        zoomResult.style.display = 'block';
        zoomResult.style.backgroundImage = 'url(' + mainImage.src + ')';
        zoomResult.style.backgroundSize = (mainImage.width * 2.5) + 'px ' + (mainImage.height * 2.5) + 'px';
        zoomResult.style.backgroundPosition = '-' + ((x / rect.width) * mainImage.width * 2.5 - zoomResult.offsetWidth/2) + 'px -' + ((y / rect.height) * mainImage.height * 2.5 - zoomResult.offsetHeight/2) + 'px';
    });

    galleryMain.addEventListener('mouseleave', function() {
        zoomLens.style.display = 'none';
        zoomResult.style.display = 'none';
    });
}
</script>

</body>
</html>