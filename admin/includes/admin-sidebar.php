<?php
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<aside class="admin-sidebar">
    <div class="admin-brand">
        <i class="fas fa-crown"></i>
        <span>LUMIÈRE ADMIN</span>
    </div>

    <nav class="admin-nav">
        <a href="dashboard.php" class="<?php echo $currentPage === 'dashboard.php' ? 'active' : ''; ?>">
            <i class="fas fa-chart-line"></i> Dashboard
        </a>
        <a href="products.php" class="<?php echo in_array($currentPage, ['products.php','product-add.php','product-edit.php']) ? 'active' : ''; ?>">
            <i class="fas fa-box-open"></i> Kelola Produk
        </a>
        <a href="categories.php" class="<?php echo $currentPage === 'categories.php' ? 'active' : ''; ?>">
            <i class="fas fa-tags"></i> Kategori
        </a>
        <a href="orders.php" class="<?php echo in_array($currentPage, ['orders.php','order-detail.php']) ? 'active' : ''; ?>">
            <i class="fas fa-shopping-cart"></i> Kelola Pesanan
        </a>
        <a href="users.php" class="<?php echo $currentPage === 'users.php' ? 'active' : ''; ?>">
            <i class="fas fa-users"></i> Kelola User
        </a>
        <a href="reports.php" class="<?php echo $currentPage === 'reports.php' ? 'active' : ''; ?>">
            <i class="fas fa-chart-bar"></i> Laporan
        </a>
        <a href="settings.php" class="<?php echo $currentPage === 'settings.php' ? 'active' : ''; ?>">
            <i class="fas fa-cog"></i> Pengaturan
        </a>
    </nav>

    <div class="admin-sidebar-footer">
        <a href="../index.php" target="_blank"><i class="fas fa-external-link-alt"></i> Lihat Website</a>
        <a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Keluar</a>
    </div>
</aside>
