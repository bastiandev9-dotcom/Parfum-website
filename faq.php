<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

$faqs = [
    ['q'=>'Apakah parfum yang dijual 100% original?','a'=>'Ya, semua produk yang kami jual adalah 100% original dan bersumber langsung dari distributor resmi. Kami berikan garansi keaslian produk.'],
    ['q'=>'Berapa lama pengiriman setelah pembayaran?','a'=>'Pesanan akan diproses dalam 1x24 jam setelah pembayaran dikonfirmasi. Estimasi pengiriman 2-5 hari kerja tergantung lokasi tujuan.'],
    ['q'=>'Metode pembayaran apa saja yang tersedia?','a'=>'Kami menerima transfer bank (BCA, BNI, Mandiri), COD (Bayar di Tempat), dan e-wallet. Semua transaksi diproses dengan aman.'],
    ['q'=>'Apakah bisa melakukan retur atau pengembalian produk?','a'=>'Pengembalian produk dapat dilakukan dalam 7 hari setelah diterima, dengan syarat produk masih tersegel dan tidak digunakan. Hubungi kami untuk proses retur.'],
    ['q'=>'Bagaimana cara melacak pesanan?','a'=>'Setelah pesanan dikirim, Anda akan mendapatkan nomor resi pengiriman. Anda dapat melacak pesanan melalui halaman "Pesanan Saya" di akun Anda.'],
    ['q'=>'Apakah ada minimum pembelian?','a'=>'Tidak ada minimum pembelian. Anda bisa membeli mulai dari 1 produk saja.'],
    ['q'=>'Bagaimana cara menggunakan kode promo?','a'=>'Masukkan kode promo pada halaman checkout di kolom "Kode Promo" sebelum melakukan pembayaran.'],
    ['q'=>'Apakah parfum tersedia dalam berbagai ukuran?','a'=>'Ya, sebagian besar produk tersedia dalam beberapa pilihan ukuran (30ml, 50ml, 100ml). Pilih ukuran yang Anda inginkan di halaman detail produk.'],
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FAQ - Lumière Parfum</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
<?php include 'includes/header.php'; ?>

<section class="page-hero" style="background:var(--black);color:#fff;padding:80px 0;text-align:center;">
    <div class="container">
        <h1 style="color:var(--gold);font-size:2.5rem;letter-spacing:3px;">FAQ</h1>
        <p style="color:#ccc;margin-top:10px;">Pertanyaan yang sering diajukan</p>
    </div>
</section>

<section class="section">
    <div class="container">
        <div style="max-width:860px;margin:0 auto;">
        <div class="faq-list">
            <?php foreach ($faqs as $i => $faq): ?>
            <div class="faq-item" style="background:#fff;border-radius:12px;margin-bottom:12px;overflow:hidden;box-shadow:0 2px 10px rgba(0,0,0,.06);width:100%;">
                <button class="faq-q" onclick="toggleFaq(<?php echo $i; ?>)" style="width:100%;text-align:left;padding:20px 24px;background:none;border:none;cursor:pointer;font-size:1rem;font-weight:600;display:flex;justify-content:space-between;align-items:center;gap:16px;">
                    <?php echo htmlspecialchars($faq['q']); ?>
                    <i class="fas fa-chevron-down faq-icon" id="icon-<?php echo $i; ?>" style="transition:.3s;flex-shrink:0;color:var(--gold);"></i>
                </button>
                <div id="faq-<?php echo $i; ?>" style="display:none;padding:0 24px 20px;color:#555;line-height:1.8;">
                    <?php echo htmlspecialchars($faq['a']); ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <div style="text-align:center;margin-top:50px;padding:40px;background:var(--black);border-radius:16px;color:#fff;">
            <h3 style="color:var(--gold);margin-bottom:12px;">Masih punya pertanyaan?</h3>
            <p style="color:#ccc;margin-bottom:20px;">Tim kami siap membantu Anda 24/7</p>
            <a href="contact.php" class="btn">Hubungi Kami</a>
        </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
<script>
function toggleFaq(i) {
    const el = document.getElementById('faq-'+i);
    const icon = document.getElementById('icon-'+i);
    const isOpen = el.style.display === 'block';
    el.style.display = isOpen ? 'none' : 'block';
    icon.style.transform = isOpen ? 'rotate(0)' : 'rotate(180deg)';
}
</script>
</body>
</html>
