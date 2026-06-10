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
    <title>Tentang Kami - Lumière Parfum</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
<?php include 'includes/header.php'; ?>

<section class="page-hero" style="background:var(--black);color:#fff;padding:80px 0;text-align:center;">
    <div class="container">
        <h1 style="color:var(--gold);font-size:2.5rem;letter-spacing:3px;">TENTANG KAMI</h1>
        <p style="color:#ccc;margin-top:10px;">Kisah di balik setiap aroma Lumière</p>
    </div>
</section>

<section class="section">
    <div class="container">
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:60px;align-items:center;margin-bottom:80px;">
            <div>
                <h2 style="font-size:2rem;margin-bottom:20px;">Perjalanan <span style="color:var(--gold);">Lumière</span></h2>
                <p style="color:#555;line-height:1.8;margin-bottom:16px;">Lumière Parfum lahir dari kecintaan mendalam terhadap seni wewangian. Didirikan pada 2020, kami hadir untuk membawa koleksi parfum premium dari seluruh penjuru dunia ke tangan Anda.</p>
                <p style="color:#555;line-height:1.8;margin-bottom:16px;">Kami percaya bahwa aroma adalah ekspresi diri yang paling personal. Setiap parfum yang kami hadirkan dipilih dengan cermat — dari rumah mode ternama Prancis, Italia, hingga Timur Tengah.</p>
                <p style="color:#555;line-height:1.8;">Dengan jaminan 100% original dan layanan pelanggan terbaik, kami berkomitmen memberikan pengalaman belanja parfum yang tak terlupakan.</p>
            </div>
            <div style="background:var(--cream);border-radius:16px;padding:40px;text-align:center;">
                <div style="font-size:5rem;color:var(--gold);margin-bottom:16px;"><i class="fas fa-crown"></i></div>
                <h3 style="font-size:1.5rem;color:var(--gold);letter-spacing:3px;">LUMIÈRE PARFUM</h3>
                <p style="color:#888;margin-top:10px;font-style:italic;">"Setiap tetes adalah sebuah cerita"</p>
            </div>
        </div>

        <!-- Stats -->
        <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:30px;margin-bottom:80px;text-align:center;">
            <?php
            $stats = [
                ['icon'=>'fas fa-box-open','val'=>'50+','label'=>'Koleksi Parfum'],
                ['icon'=>'fas fa-users','val'=>'1000+','label'=>'Pelanggan Puas'],
                ['icon'=>'fas fa-star','val'=>'4.9','label'=>'Rating Rata-rata'],
                ['icon'=>'fas fa-shipping-fast','val'=>'24h','label'=>'Pengiriman Cepat'],
            ];
            foreach ($stats as $s): ?>
            <div style="background:#fff;border-radius:16px;padding:30px 20px;box-shadow:0 4px 20px rgba(0,0,0,.06);">
                <div style="font-size:2rem;color:var(--gold);margin-bottom:12px;"><i class="<?php echo $s['icon']; ?>"></i></div>
                <h3 style="font-size:2rem;font-weight:700;margin-bottom:4px;"><?php echo $s['val']; ?></h3>
                <p style="color:#888;font-size:.9rem;"><?php echo $s['label']; ?></p>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Values -->
        <h2 style="text-align:center;font-size:1.8rem;margin-bottom:40px;">Nilai-nilai <span style="color:var(--gold);">Kami</span></h2>
        <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:30px;">
            <?php
            $values = [
                ['icon'=>'fas fa-certificate','title'=>'100% Original','desc'=>'Semua produk kami dijamin keasliannya langsung dari distributor resmi.'],
                ['icon'=>'fas fa-shield-alt','title'=>'Transaksi Aman','desc'=>'Keamanan data dan transaksi Anda adalah prioritas utama kami.'],
                ['icon'=>'fas fa-headset','title'=>'Layanan Prima','desc'=>'Tim kami siap membantu Anda menemukan aroma yang sempurna.'],
            ];
            foreach ($values as $v): ?>
            <div style="text-align:center;padding:30px 20px;background:#fff;border-radius:16px;box-shadow:0 4px 20px rgba(0,0,0,.06);">
                <div style="width:60px;height:60px;background:var(--gold);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;font-size:1.4rem;color:#fff;"><i class="<?php echo $v['icon']; ?>"></i></div>
                <h4 style="margin-bottom:10px;"><?php echo $v['title']; ?></h4>
                <p style="color:#666;font-size:.9rem;line-height:1.7;"><?php echo $v['desc']; ?></p>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
</body>
</html>
