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
    <title>Kebijakan Privasi - Lumière Parfum</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
<?php include 'includes/header.php'; ?>
<section class="page-hero" style="background:var(--black);color:#fff;padding:80px 0;text-align:center;">
    <div class="container">
        <h1 style="color:var(--gold);font-size:2.5rem;letter-spacing:3px;">KEBIJAKAN PRIVASI</h1>
        <p style="color:#ccc;margin-top:10px;">Terakhir diperbarui: <?php echo date('d F Y'); ?></p>
    </div>
</section>
<section class="section">
    <div class="container">
        <div style="background:#fff;border-radius:16px;padding:40px;box-shadow:0 4px 20px rgba(0,0,0,.06);line-height:1.9;color:#444;max-width:860px;margin:0 auto;">
            <?php
            $sections = [
                ['1. Informasi yang Kami Kumpulkan','Kami mengumpulkan informasi yang Anda berikan saat mendaftar (nama, email, nomor telepon), informasi alamat pengiriman, riwayat transaksi, dan data penggunaan website.'],
                ['2. Penggunaan Informasi','Informasi Anda digunakan untuk memproses pesanan, mengirimkan konfirmasi dan pembaruan status pesanan, meningkatkan layanan kami, dan mengirimkan penawaran promosi (jika Anda menyetujui).'],
                ['3. Keamanan Data','Kami menerapkan langkah-langkah keamanan teknis dan organisasi untuk melindungi data pribadi Anda dari akses tidak sah, kehilangan, atau penyalahgunaan.'],
                ['4. Berbagi Data','Kami tidak menjual atau menyewakan data pribadi Anda kepada pihak ketiga. Data hanya dibagikan kepada mitra pengiriman yang diperlukan untuk memproses pesanan Anda.'],
                ['5. Cookie','Website kami menggunakan cookie untuk meningkatkan pengalaman pengguna. Anda dapat menonaktifkan cookie melalui pengaturan browser, namun ini dapat mempengaruhi fungsi website.'],
                ['6. Hak Pengguna','Anda memiliki hak untuk mengakses, memperbarui, atau menghapus data pribadi Anda. Hubungi kami melalui halaman kontak untuk mengajukan permintaan.'],
                ['7. Perubahan Kebijakan','Kami dapat memperbarui kebijakan privasi ini dari waktu ke waktu. Perubahan signifikan akan diberitahukan melalui email atau notifikasi di website.'],
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
