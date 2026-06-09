<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<header class="site-header">
    <div class="container">
        <a href="index.php" class="logo">LUMIÈRE</a>
        
        <ul class="nav-menu">
            <li><a href="index.php">Beranda</a></li>
            <li><a href="products.php">Katalog</a></li>
            <li><a href="products.php?kategori=pria">Pria</a></li>
            <li><a href="products.php?kategori=wanita">Wanita</a></li>
            <li><a href="products.php?kategori=unisex">Unisex</a></li>
        </ul>
        
        <div class="nav-icons">
            <a href="cart.php"><i class="fas fa-shopping-bag"></i></a>
            
            <?php if(isset($_SESSION['user_id'])): ?>
                <?php if($_SESSION['role'] === 'admin'): ?>
                    <a href="admin/dashboard.php" title="Admin Panel"><i class="fas fa-cog"></i></a>
                <?php else: ?>
                    <a href="user/dashboard.php" title="Akun Saya"><i class="fas fa-user"></i></a>
                <?php endif; ?>
                <a href="logout.php" title="Keluar"><i class="fas fa-sign-out-alt"></i></a>
            <?php else: ?>
                <a href="login.php" title="Masuk"><i class="fas fa-user"></i></a>
            <?php endif; ?>
        </div>
    </div>
</header>