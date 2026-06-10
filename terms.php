<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Syarat & Ketentuan - Lumière Parfum</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
<?php include 'includes/header.php'; ?>
<section class="page-hero" style="background:var(--black);color:#fff;padding:80px 0;text-align:center;">
    <div class="container">
        <h1 style="color:var(--gold);font-size:2.5rem;letter-spacing:3px;">SYARAT & KETENTUAN</h1>
        <p style="color:#ccc;margin-top:10px;">Terakhir diperbarui: <?php echo date('d F Y'); ?></p>
    </div>
</section>
<section class="section">
    <div class="container">
        <div style="background:#fff;border-radius:16px;padding:40px;box-shadow:0 4px 20px rgba(0,0,0,.06);line-height:1.9;color:#444;max-width:860px;margin:0 auto;">
            <?php
            $sections = [
                ['1. Penerimaan Syarat','Dengan mengakses dan menggunakan layanan Lumière Parfum, Anda menyetujui untuk terikat oleh syarat dan ketentuan ini. Jika Anda tidak setuju, mohon untuk tidak menggunakan layanan kami.'],
                ['2. Akun Pengguna','Anda bertanggung jawab untuk menjaga kerahasiaan akun dan password Anda. Segala aktivitas yang terjadi di bawah akun Anda adalah tanggung jawab Anda sepenuhnya.'],
                ['3. Pembelian dan Pembayaran','Semua harga yang tercantum dalam satuan Rupiah (IDR). Pembayaran harus diselesaikan dalam 24 jam setelah pesanan dibuat. Pesanan yang tidak dibayar akan otomatis dibatalkan.'],
                ['4. Pengiriman','Kami bermitra dengan kurir terpercaya (JNE, J&T, SiCepat, AnterAja). Estimasi pengiriman 2-5 hari kerja. Risiko kerusakan selama pengiriman menjadi tanggung jawab pihak kurir.'],
                ['5. Retur dan Pengembalian Dana','Produk dapat dikembalikan dalam 7 hari dengan kondisi tersegel. Pengembalian dana diproses dalam 3-5 hari kerja setelah produk diterima dan diverifikasi.'],
                ['6. Kekayaan Intelektual','Seluruh konten di website ini, termasuk teks, gambar, dan logo adalah milik Lumière Parfum dan dilindungi hak cipta.'],
                ['7. Perubahan Syarat','Kami berhak mengubah syarat dan ketentuan ini kapan saja. Perubahan berlaku efektif sejak dipublikasikan di website.'],
            ];
            foreach ($sections as $s): ?>
            <div style="margin-bottom:28px;">
                <h3 style="color:var(--black);margin-bottom:10px;"><?php echo $s[0]; ?></h3>
                <p><?php echo $s[1]; ?></p>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php include 'includes/footer.php'; ?>
</body>
</html>
