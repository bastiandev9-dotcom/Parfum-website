<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';
if (!isset($_SESSION['user_id'])) { header('Location: ../login.php'); exit; }

$userId = $_SESSION['user_id'];
$user = ['nama' => $_SESSION['nama'] ?? 'User', 'avatar' => strtoupper(substr($_SESSION['nama'] ?? 'U', 0, 2))];

// Hapus dari wishlist
if (isset($_GET['remove'])) {
    $pid = (int)$_GET['remove'];
    $stmt = $conn->prepare("DELETE FROM wishlists WHERE user_id=? AND product_id=?");
    $stmt->bind_param("ii", $userId, $pid);
    $stmt->execute();
    header('Location: wishlist.php'); exit;
}

// Ambil wishlist
$stmt = $conn->prepare("SELECT w.*, p.nama_produk, p.harga, p.harga_diskon, p.gambar_utama, p.slug, b.nama_brand
    FROM wishlists w JOIN products p ON w.product_id=p.product_id
    LEFT JOIN brands b ON p.brand_id=b.brand_id
    WHERE w.user_id=? ORDER BY w.created_at DESC");
$stmt->bind_param("i", $userId);
$stmt->execute();
$items = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Wishlist - Lumière Parfum</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
<?php include '../includes/header.php'; ?>
<section class="user-section">
<div class="container">
<div class="user-layout">
    <aside class="user-sidebar">
        <div class="user-profile-mini">
            <div class="mini-avatar"><?php echo $user['avatar']; ?></div>
            <div class="mini-info"><h4><?php echo $user['nama']; ?></h4></div>
        </div>
        <nav class="user-nav">
            <a href="dashboard.php"><i class="fas fa-th-large"></i> Dashboard</a>
            <a href="orders.php"><i class="fas fa-shopping-bag"></i> Pesanan Saya</a>
            <a href="profile.php"><i class="fas fa-user"></i> Profil & Alamat</a>
            <a href="wishlist.php" class="active"><i class="fas fa-heart"></i> Wishlist</a>
            <a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Keluar</a>
        </nav>
    </aside>
    <div class="user-content">
        <div class="content-header">
            <h2>Wishlist Saya</h2>
            <p><?php echo count($items); ?> produk favorit</p>
        </div>
        <?php if (empty($items)): ?>
        <div class="empty-state">
            <i class="fas fa-heart"></i>
            <h3>Wishlist Kosong</h3>
            <p>Belum ada produk di wishlist Anda.</p>
            <a href="../products.php" class="btn">Jelajahi Produk</a>
        </div>
        <?php else: ?>
        <div class="product-grid" style="grid-template-columns:repeat(auto-fill,minmax(220px,1fr));">
            <?php foreach ($items as $item): ?>
            <div class="product-card">
                <div class="product-image">
                    <img src="../<?php echo htmlspecialchars($item['gambar_utama']); ?>" alt="<?php echo htmlspecialchars($item['nama_produk']); ?>">
                    <div class="product-overlay">
                        <a href="../product-detail.php?id=<?php echo $item['product_id']; ?>" class="overlay-btn"><i class="fas fa-eye"></i></a>
                        <a href="../cart.php?add=<?php echo $item['product_id']; ?>" class="overlay-btn"><i class="fas fa-shopping-bag"></i></a>
                        <a href="wishlist.php?remove=<?php echo $item['product_id']; ?>" class="overlay-btn" style="color:#e74c3c;" title="Hapus"><i class="fas fa-trash"></i></a>
                    </div>
                </div>
                <div class="product-info">
                    <p class="brand"><?php echo htmlspecialchars($item['nama_brand']); ?></p>
                    <h3><a href="../product-detail.php?id=<?php echo $item['product_id']; ?>"><?php echo htmlspecialchars($item['nama_produk']); ?></a></h3>
                    <p class="price">
                        <?php if ($item['harga_diskon']): ?>
                            <span style="text-decoration:line-through;color:#999;font-size:.85em"><?php echo formatRupiah($item['harga']); ?></span>
                            <?php echo formatRupiah($item['harga_diskon']); ?>
                        <?php else: ?>
                            <?php echo formatRupiah($item['harga']); ?>
                        <?php endif; ?>
                    </p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</div>
</div>
</section>
<?php include '../includes/footer.php'; ?>
</body>
</html>
