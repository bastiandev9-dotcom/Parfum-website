<?php
// products.php - Halaman Katalog Produk
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

// ==================== AMBIL PARAMETER GET ====================
$sort          = $_GET['sort']     ?? 'terbaru';
$filterHarga   = isset($_GET['harga'])  ? (array)$_GET['harga']  : [];
$filterBrand   = isset($_GET['brand'])  ? (array)$_GET['brand']  : [];
$filterAroma   = isset($_GET['aroma'])  ? (array)$_GET['aroma']  : [];
$filterUkuran  = isset($_GET['ukuran']) ? (array)$_GET['ukuran'] : [];
$filterKategori = $_GET['kategori'] ?? '';

// ==================== BUILD QUERY DARI DATABASE ====================
$where  = ["p.status = 'aktif'"];
$params = [];
$types  = '';

if ($filterKategori) {
    $where[]  = "p.gender = ?";
    $params[] = $filterKategori;
    $types   .= 's';
}

if (!empty($filterHarga)) {
    $hargaCond = [];
    foreach ($filterHarga as $h) {
        if ($h === '0-500')    $hargaCond[] = "p.harga <= 500000";
        if ($h === '500-1000') $hargaCond[] = "(p.harga > 500000 AND p.harga <= 1000000)";
        if ($h === '1000+')    $hargaCond[] = "p.harga > 1000000";
    }
    if ($hargaCond) $where[] = '(' . implode(' OR ', $hargaCond) . ')';
}

if (!empty($filterBrand)) {
    $placeholders = implode(',', array_fill(0, count($filterBrand), '?'));
    $where[]  = "b.nama_brand IN ($placeholders)";
    $params   = array_merge($params, $filterBrand);
    $types   .= str_repeat('s', count($filterBrand));
}

if (!empty($filterAroma)) {
    $placeholders = implode(',', array_fill(0, count($filterAroma), '?'));
    $where[]  = "p.aroma IN ($placeholders)";
    $params   = array_merge($params, $filterAroma);
    $types   .= str_repeat('s', count($filterAroma));
}

if (!empty($filterUkuran)) {
    $placeholders = implode(',', array_fill(0, count($filterUkuran), '?'));
    $where[]  = "EXISTS (SELECT 1 FROM product_variants pv WHERE pv.product_id=p.product_id AND pv.ukuran IN ($placeholders) AND pv.is_active=1)";
    $params   = array_merge($params, $filterUkuran);
    $types   .= str_repeat('s', count($filterUkuran));
}

// Ambil semua ukuran unik dari DB untuk filter
$ukuranList = $conn->query("SELECT DISTINCT ukuran FROM product_variants WHERE is_active=1 AND ukuran IS NOT NULL ORDER BY ukuran")->fetch_all(MYSQLI_ASSOC);

$orderBy = match($sort) {
    'termurah' => 'p.harga ASC',
    'terlaris' => 'p.total_terjual DESC',
    default    => 'p.created_at DESC',
};

$sql = "SELECT p.*, b.nama_brand
        FROM products p
        LEFT JOIN brands b ON p.brand_id = b.brand_id
        WHERE " . implode(' AND ', $where) . "
        ORDER BY $orderBy";

$stmt = $conn->prepare($sql);
if ($params) $stmt->bind_param($types, ...$params);
$stmt->execute();
$result      = $stmt->get_result();
$allProducts = $result->fetch_all(MYSQLI_ASSOC);
$totalProduk = count($allProducts);

// ==================== PAGINATION ====================
$perPage     = 12;
$totalPages  = max(1, ceil($totalProduk / $perPage));
$currentPage = max(1, min((int)($_GET['page'] ?? 1), $totalPages));
$offset      = ($currentPage - 1) * $perPage;
$products    = array_slice($allProducts, $offset, $perPage);

// ==================== AMBIL LIST BRAND & AROMA UNIK ====================
$allBrands = $conn->query("SELECT DISTINCT nama_brand FROM brands ORDER BY nama_brand")->fetch_all(MYSQLI_ASSOC);
$allBrands = array_column($allBrands, 'nama_brand');
$allAromas = $conn->query("SELECT DISTINCT aroma FROM products WHERE status='aktif' ORDER BY aroma")->fetch_all(MYSQLI_ASSOC);
$allAromas = array_column($allAromas, 'aroma');
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Katalog Produk - Lumière Parfum</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

<?php include 'includes/header.php'; ?>

<!-- Breadcrumb -->
<div class="breadcrumb">
    <div class="container">
        <a href="index.php">Beranda</a> / <span>Katalog Produk</span>
        <?php if($filterKategori): ?>
            / <span style="text-transform: capitalize;"><?php echo $filterKategori; ?></span>
        <?php endif; ?>
    </div>
</div>

<!-- Shop Section -->
<section class="shop-page">
    <div class="container">
        <div class="shop-layout">
            
            <!-- Sidebar Filter -->
            <aside class="shop-sidebar">
                <div class="sidebar-header">
                    <h3><i class="fas fa-filter"></i> Filter</h3>
                    <a href="products.php<?php echo $filterKategori ? '?kategori='.$filterKategori : ''; ?>" class="reset-filter">Reset</a>
                </div>
                
                <form method="GET" action="products.php" id="filterForm">
                    <?php if($filterKategori): ?>
                        <input type="hidden" name="kategori" value="<?php echo $filterKategori; ?>">
                    <?php endif; ?>
                    
                    <!-- Filter Harga -->
                    <div class="filter-group">
                        <h4 class="filter-title">Rentang Harga</h4>
                        <div class="filter-options">
                            <label class="filter-checkbox">
                                <input type="checkbox" name="harga[]" value="0-500" <?php echo in_array('0-500', $filterHarga) ? 'checked' : ''; ?>>
                                <span>Dibawah Rp 500.000</span>
                            </label>
                            <label class="filter-checkbox">
                                <input type="checkbox" name="harga[]" value="500-1000" <?php echo in_array('500-1000', $filterHarga) ? 'checked' : ''; ?>>
                                <span>Rp 500.000 - Rp 1.000.000</span>
                            </label>
                            <label class="filter-checkbox">
                                <input type="checkbox" name="harga[]" value="1000+" <?php echo in_array('1000+', $filterHarga) ? 'checked' : ''; ?>>
                                <span>Diatas Rp 1.000.000</span>
                            </label>
                        </div>
                    </div>
                    
                    <!-- Filter Brand -->
                    <div class="filter-group">
                        <h4 class="filter-title">Brand</h4>
                        <div class="filter-options">
                            <?php foreach($allBrands as $brand): ?>
                            <label class="filter-checkbox">
                                <input type="checkbox" name="brand[]" value="<?php echo $brand; ?>" <?php echo in_array($brand, $filterBrand) ? 'checked' : ''; ?>>
                                <span><?php echo $brand; ?></span>
                            </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    
                    <!-- Filter Aroma -->
                    <div class="filter-group">
                        <h4 class="filter-title">Jenis Aroma</h4>
                        <div class="filter-options">
                            <?php foreach($allAromas as $aroma): ?>
                            <label class="filter-checkbox">
                                <input type="checkbox" name="aroma[]" value="<?php echo $aroma; ?>" <?php echo in_array($aroma, $filterAroma) ? 'checked' : ''; ?>>
                                <span style="text-transform: capitalize;"><?php echo $aroma; ?></span>
                            </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    
                    <!-- Filter Ukuran -->
                    <div class="filter-group">
                        <h4 class="filter-title">Ukuran</h4>
                        <div class="filter-options">
                            <?php foreach ($ukuranList as $u): ?>
                            <label class="filter-checkbox">
                                <input type="checkbox" name="ukuran[]" value="<?php echo htmlspecialchars($u['ukuran']); ?>" <?php echo in_array($u['ukuran'], $filterUkuran) ? 'checked' : ''; ?>>
                                <span><?php echo htmlspecialchars($u['ukuran']); ?></span>
                            </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-filter">Terapkan Filter</button>
                </form>
            </aside>
            
            <!-- Product Grid Area -->
            <div class="shop-main">
                
                <!-- Toolbar -->
                <div class="shop-toolbar">
                    <p class="result-count">Menampilkan <strong><?php echo $totalProduk; ?></strong> produk</p>
                    
                    <div class="sort-box">
                        <label for="sort">Urutkan:</label>
                        <select name="sort" id="sort" onchange="applySort(this.value)">
                            <option value="terbaru" <?php echo $sort == 'terbaru' ? 'selected' : ''; ?>>Terbaru</option>
                            <option value="termurah" <?php echo $sort == 'termurah' ? 'selected' : ''; ?>>Termurah</option>
                            <option value="terlaris" <?php echo $sort == 'terlaris' ? 'selected' : ''; ?>>Terlaris</option>
                        </select>
                    </div>
                </div>
                
                <!-- Grid -->
                <?php if($totalProduk > 0): ?>
                <div class="product-grid shop-grid">
                    <?php foreach($products as $p): ?>
                    <div class="product-card">
                        <div class="product-image">
                            <?php if($p['is_best_seller']): ?>
                                <span class="badge">Best Seller</span>
                            <?php elseif($p['is_new_arrival']): ?>
                                <span class="badge">Baru</span>
                            <?php endif; ?>
                            <?php if($p['harga_diskon']): ?>
                                <span class="badge badge-discount">-<?php echo round((1 - $p['harga_diskon']/$p['harga'])*100); ?>%</span>
                            <?php endif; ?>
                            <img src="<?php echo htmlspecialchars($p['gambar_utama']); ?>" alt="<?php echo htmlspecialchars($p['nama_produk']); ?>">
                            <div class="product-overlay">
                                <a href="product-detail.php?id=<?php echo $p['product_id']; ?>" class="overlay-btn" title="Lihat Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="cart.php?add=<?php echo $p['product_id']; ?>" class="overlay-btn" title="Tambah ke Keranjang">
                                    <i class="fas fa-shopping-bag"></i>
                                </a>
                            </div>
                        </div>
                        <div class="product-info">
                            <p class="brand"><?php echo htmlspecialchars($p['nama_brand']); ?></p>
                            <h3><a href="product-detail.php?id=<?php echo $p['product_id']; ?>"><?php echo htmlspecialchars($p['nama_produk']); ?></a></h3>
                            <div class="product-rating">
                                <div class="stars">
                                    <?php
                                    $r = floatval($p['rating_avg']);
                                    $full = floor($r); $half = ($r - $full) >= 0.5;
                                    for($i=1;$i<=5;$i++){
                                        if($i<=$full) echo '<i class="fas fa-star"></i>';
                                        elseif($i==$full+1&&$half) echo '<i class="fas fa-star-half-alt"></i>';
                                        else echo '<i class="far fa-star"></i>';
                                    }
                                    ?>
                                </div>
                                <span class="rating-count">(<?php echo $p['rating_avg']; ?>) • <?php echo $p['total_terjual']; ?> terjual</span>
                            </div>
                            <div class="product-price">
                                <?php if($p['harga_diskon']): ?>
                                    <span class="price-old"><?php echo formatRupiah($p['harga']); ?></span>
                                    <span class="price"><?php echo formatRupiah($p['harga_diskon']); ?></span>
                                <?php else: ?>
                                    <span class="price"><?php echo formatRupiah($p['harga']); ?></span>
                                <?php endif; ?>
                            </div>
                            <div class="product-tags">
                                <span class="tag"><?php echo ucfirst($p['aroma']); ?></span>
                                <span class="tag"><?php echo ucfirst($p['gender']); ?></span>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-search"></i>
                    <h3>Tidak ada produk yang sesuai</h3>
                    <p>Coba ubah filter atau sorting yang Anda pilih.</p>
                    <a href="products.php" class="btn">Lihat Semua Produk</a>
                </div>
                <?php endif; ?>

                <?php if($totalPages > 1): ?>
                <?php
                    $qp = $_GET;
                    function pageUrl($p, $qp) {
                        $qp['page'] = $p;
                        return 'products.php?' . http_build_query($qp);
                    }
                ?>
                <div class="pagination">
                    <?php if($currentPage > 1): ?>
                        <a href="<?php echo pageUrl($currentPage-1, $qp); ?>" class="page-btn">&lsaquo;</a>
                    <?php else: ?>
                        <span class="page-btn disabled">&lsaquo;</span>
                    <?php endif; ?>

                    <?php
                    $pages = [];
                    if ($totalPages <= 7) {
                        $pages = range(1, $totalPages);
                    } else {
                        $pages = [1, 2];
                        if ($currentPage > 4) $pages[] = '...';
                        for ($i = max(3, $currentPage-1); $i <= min($totalPages-2, $currentPage+1); $i++) $pages[] = $i;
                        if ($currentPage < $totalPages - 3) $pages[] = '...';
                        $pages[] = $totalPages - 1;
                        $pages[] = $totalPages;
                        $pages = array_unique($pages);
                    }
                    foreach ($pages as $p):
                        if ($p === '...'):
                    ?>
                        <span class="page-btn disabled">...</span>
                    <?php else: ?>
                        <a href="<?php echo pageUrl($p, $qp); ?>" class="page-btn <?php echo $p == $currentPage ? 'active' : ''; ?>"><?php echo $p; ?></a>
                    <?php endif; endforeach; ?>

                    <?php if($currentPage < $totalPages): ?>
                        <a href="<?php echo pageUrl($currentPage+1, $qp); ?>" class="page-btn">&rsaquo;</a>
                    <?php else: ?>
                        <span class="page-btn disabled">&rsaquo;</span>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
                
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>

<script>
// Auto-submit filter saat checkbox diklik (opsional, bisa juga pakai tombol)
// document.querySelectorAll('.filter-checkbox input').forEach(cb => {
//     cb.addEventListener('change', () => document.getElementById('filterForm').submit());
// });

function applySort(value) {
    const url = new URL(window.location.href);
    url.searchParams.set('sort', value);
    window.location.href = url.toString();
}
</script>

</body>
</html>