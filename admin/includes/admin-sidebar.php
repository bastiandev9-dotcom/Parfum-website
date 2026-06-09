<?php
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<aside class="admin-sidebar">
    <div class="admin-brand">
        <i class="fas fa-crown"></i>
        <span>LUMIÈRE ADMIN</span>
    </div>

    <nav class="admin-nav">
        <a href="dashboard.php" class="<?php echo $currentPage == 'dashboard.php' ? 'active' : ''; ?>">
            <i class="fas fa-chart-line"></i> Dashboard
        </a>
        <a href="products.php" class="<?php echo $currentPage == 'products.php' ? 'active' : ''; ?>">
            <i class="fas fa-box-open"></i> Kelola Produk
        </a>
        <a href="orders.php" class="<?php echo $currentPage == 'orders.php' ? 'active' : ''; ?>">
            <i class="fas fa-shopping-cart"></i> Kelola Pesanan
        </a>
        <a href="users.php" class="<?php echo $currentPage == 'users.php' ? 'active' : ''; ?>">
            <i class="fas fa-users"></i> Kelola User
        </a>
        <a href="upload_gambar.php" class="<?php echo $currentPage == 'upload_gambar.php' ? 'active' : ''; ?>">
            <i class="fas fa-images"></i> Upload Gambar
        </a>
    </nav>

    <div class="admin-sidebar-footer">
        <a href="../index.php" target="_blank"><i class="fas fa-external-link-alt"></i> Lihat Website</a>
        <a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Keluar</a>
    </div>
</aside>