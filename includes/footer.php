<?php
// includes/footer.php
?>
<footer class="site-footer">
    <div class="container">
        <div class="footer-grid">
            <div class="footer-brand">
                <h3>LUMIÈRE PARFUM</h3>
                <p>Toko parfum premium dengan koleksi aroma eksklusif dari berbagai merek ternama. Kami menjual produk 100% original dengan garansi kepuasan.</p>
            </div>
            <div class="footer-links">
                <h4>Menu</h4>
                <ul>
                    <li><a href="index.php">Beranda</a></li>
                    <li><a href="products.php">Katalog</a></li>
                    <li><a href="about.php">Tentang Kami</a></li>
                    <li><a href="contact.php">Kontak</a></li>
                </ul>
            </div>
            <div class="footer-links">
                <h4>Akun</h4>
                <ul>
                    <li><a href="login.php">Login</a></li>
                    <li><a href="register.php">Register</a></li>
                    <li><a href="user/orders.php">Pesanan Saya</a></li>
                    <li><a href="wishlist.php">Wishlist</a></li>
                </ul>
            </div>
            <div class="footer-links">
                <h4>Informasi</h4>
                <ul>
                    <li><a href="faq.php">FAQ</a></li>
                    <li><a href="tracking.php">Lacak Pesanan</a></li>
                    <li><a href="terms.php">Syarat & Ketentuan</a></li>
                    <li><a href="privacy.php">Kebijakan Privasi</a></li>
                </ul>
            </div>
        </div>
    </div>
</footer>
<script src="<?php echo (strpos($_SERVER['PHP_SELF'], '/user/') !== false || strpos($_SERVER['PHP_SELF'], '/admin/') !== false) ? '../' : ''; ?>assets/js/main.js"></script>
</body>
</html>