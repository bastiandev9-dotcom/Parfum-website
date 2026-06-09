<?php
// index.php - Homepage Parfum Shop
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

// Ambil best seller dari database
$bestSellers = $conn->query("
    SELECT p.*, b.nama_brand FROM products p
    LEFT JOIN brands b ON p.brand_id = b.brand_id
    WHERE p.status = 'aktif' AND p.is_best_seller = 1
    ORDER BY p.total_terjual DESC LIMIT 4
")->fetch_all(MYSQLI_ASSOC);

// Fallback jika belum ada data best seller
if (empty($bestSellers)) {
    $bestSellers = $conn->query("
        SELECT p.*, b.nama_brand FROM products p
        LEFT JOIN brands b ON p.brand_id = b.brand_id
        WHERE p.status = 'aktif'
        ORDER BY p.total_terjual DESC LIMIT 4
    ")->fetch_all(MYSQLI_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lumière Parfum - Toko Parfum Premium</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

    <!-- Header -->
    <?php include 'includes/header.php'; ?>

    <!-- Hero Banner -->
    <section class="hero">
        <div class="container">
            <div class="hero-content">
                <h1>Koleksi Parfum <span>Terbaru 2026</span></h1>
                <p>Temukan aroma signature Anda dari koleksi parfum eksklusif pilihan. Diskon spesial 20% untuk pembelian pertama.</p>
                <div class="hero-buttons">
                    <a href="products.php" class="btn">Lihat Katalog</a>
                    <a href="#best-seller" class="btn btn-outline">Best Seller</a>
                </div>
            </div>
            <div class="hero-image">
                <img src="https://images.unsplash.com/photo-1594035910387-fea47794261f?w=600&h=500&fit=crop" alt="Koleksi Parfum Terbaru">
            </div>
        </div>
    </section>

    <!-- Koleksi Unggulan / Best Seller -->
    <section class="section best-seller" id="best-seller">
        <div class="container">
            <h2 class="section-title">Koleksi Unggulan</h2>
            <p class="section-subtitle">Parfum best seller pilihan pelanggan kami</p>
            
            <div class="product-grid">
                <?php foreach ($bestSellers as $produk): ?>
                <div class="product-card">
                    <div class="product-image">
                        <?php if($produk['is_best_seller']): ?><span class="badge">Best Seller</span><?php endif; ?>
                        <img src="<?php echo htmlspecialchars($produk['gambar_utama']); ?>" alt="<?php echo htmlspecialchars($produk['nama_produk']); ?>">
                        <div class="product-overlay">
                            <a href="product-detail.php?id=<?php echo $produk['product_id']; ?>" class="overlay-btn"><i class="fas fa-eye"></i></a>
                            <a href="cart.php?add=<?php echo $produk['product_id']; ?>" class="overlay-btn"><i class="fas fa-shopping-bag"></i></a>
                        </div>
                    </div>
                    <div class="product-info">
                        <p class="brand"><?php echo htmlspecialchars($produk['nama_brand']); ?></p>
                        <h3><a href="product-detail.php?id=<?php echo $produk['product_id']; ?>"><?php echo htmlspecialchars($produk['nama_produk']); ?></a></h3>
                        <p class="price">
                            <?php if($produk['harga_diskon']): ?>
                                <span style="text-decoration:line-through;color:#999;font-size:.9em"><?php echo formatRupiah($produk['harga']); ?></span>
                                <?php echo formatRupiah($produk['harga_diskon']); ?>
                            <?php else: ?>
                                <?php echo formatRupiah($produk['harga']); ?>
                            <?php endif; ?>
                        </p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            
            <div style="text-align: center; margin-top: 40px;">
                <a href="products.php" class="btn">Lihat Semua Produk</a>
            </div>
        </div>
    </section>

    <!-- Kategori Parfum -->
    <section class="section categories">
        <div class="container">
            <h2 class="section-title">Kategori Parfum</h2>
            <p class="section-subtitle">Pilih aroma yang sesuai dengan kepribadian Anda</p>
            
            <div class="category-grid">
                <div class="category-card">
                    <img src="https://images.unsplash.com/photo-1615634260167-c8cdede054de?w=500&h=600&fit=crop" alt="Parfum Pria">
                    <div class="category-overlay">
                        <h3>Parfum Pria</h3>
                        <p>Aroma woody, fresh, dan maskulin untuk pria modern</p>
                    </div>
                </div>
                <div class="category-card">
                    <img src="assets/images/products/wanita.jpg">
                    <div class="category-overlay">
                        <h3>Parfum Wanita</h3>
                        <p>Aroma floral, sweet, dan elegan untuk wanita berkelas</p>
                    </div>
                </div>
                <div class="category-card">
                    <img src="https://images.unsplash.com/photo-1592914610354-fd354ea45e48?w=500&h=600&fit=crop" alt="Parfum Unisex">
                    <div class="category-overlay">
                        <h3>Unisex</h3>
                        <p>Aroma netral yang cocok untuk semua gender</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimoni -->
    <section class="section testimonials">
        <div class="container">
            <h2 class="section-title">Testimoni Pelanggan</h2>
            <p class="section-subtitle">Apa kata mereka tentang Lumière Parfum</p>
            
            <div class="testimonial-grid">
                <div class="testimonial-card">
                    <div class="stars">
                        <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                    </div>
                    <p class="testimonial-text">"Midnight Oud benar-benar parfum signature saya. Ketahanannya luar biasa, seharian tetap wangi. Packagingnya juga sangat premium!"</p>
                    <div class="testimonial-author">
                        <div class="author-avatar">AR</div>
                        <div class="author-info"><h4>Andi Raharjo</h4><span>Jakarta</span></div>
                    </div>
                </div>
                
                <div class="testimonial-card">
                    <div class="stars">
                        <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                    </div>
                    <p class="testimonial-text">"Pelayanan cepat dan parfum original. Rose Élégante jadi favorit saya untuk acara formal. Highly recommended!"</p>
                    <div class="testimonial-author">
                        <div class="author-avatar">SM</div>
                        <div class="author-info"><h4>Siti Muliani</h4><span>Surabaya</span></div>
                    </div>
                </div>
                
                <div class="testimonial-card">
                    <div class="stars">
                        <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star-half-alt"></i>
                    </div>
                    <p class="testimonial-text">"Beli parfum unisex untuk hadiah ulang tahun pacar. Dia suka banget! Next time mau coba koleksi yang lain."</p>
                    <div class="testimonial-author">
                        <div class="author-avatar">BD</div>
                        <div class="author-info"><h4>Budi Darmawan</h4><span>Bandung</span></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Newsletter -->
    <section class="newsletter">
        <div class="container">
            <h2>Dapatkan Diskon 15%</h2>
            <p>Subscribe newsletter kami dan dapatkan kode diskon eksklusif untuk pembelian pertama</p>
            <form class="newsletter-form" action="#" method="POST">
                <input type="email" name="email" placeholder="Masukkan email Anda..." required>
                <button type="submit">Subscribe</button>
            </form>
        </div>
    </section>

    <!-- Footer -->
    <?php include 'includes/footer.php'; ?>

</body>
</html>