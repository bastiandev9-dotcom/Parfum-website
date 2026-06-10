<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

$message = $error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Simpan ke DB atau kirim email - untuk sekarang hanya tampilkan sukses
    $message = 'Pesan Anda berhasil dikirim! Kami akan merespons dalam 1x24 jam.';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hubungi Kami - Lumière Parfum</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
<?php include 'includes/header.php'; ?>

<section class="page-hero" style="background:var(--black);color:#fff;padding:80px 0;text-align:center;">
    <div class="container">
        <h1 style="color:var(--gold);font-size:2.5rem;letter-spacing:3px;">HUBUNGI KAMI</h1>
        <p style="color:#ccc;margin-top:10px;">Kami siap membantu Anda</p>
    </div>
</section>

<section class="section">
    <div class="container">
        <div style="display:grid;grid-template-columns:1fr 1.4fr;gap:60px;align-items:start;">

            <!-- Info Kontak -->
            <div>
                <h2 style="margin-bottom:30px;">Informasi <span style="color:var(--gold);">Kontak</span></h2>
                <?php
                $contacts = [
                    ['icon'=>'fas fa-map-marker-alt','title'=>'Alamat','val'=>'Jl. Parfum Indah No. 1, Jakarta Selatan, DKI Jakarta 12345'],
                    ['icon'=>'fas fa-phone','title'=>'Telepon','val'=>'+62 812-3456-7890'],
                    ['icon'=>'fas fa-envelope','title'=>'Email','val'=>'info@lumiereparfum.com'],
                    ['icon'=>'fab fa-whatsapp','title'=>'WhatsApp','val'=>'+62 812-3456-7890'],
                ];
                foreach ($contacts as $c): ?>
                <div style="display:flex;gap:16px;margin-bottom:24px;">
                    <div style="width:48px;height:48px;background:var(--gold);border-radius:50%;display:flex;align-items:center;justify-content:center;color:#fff;font-size:1.1rem;flex-shrink:0;"><i class="<?php echo $c['icon']; ?>"></i></div>
                    <div>
                        <h4 style="margin-bottom:4px;"><?php echo $c['title']; ?></h4>
                        <p style="color:#666;"><?php echo $c['val']; ?></p>
                    </div>
                </div>
                <?php endforeach; ?>

                <div style="margin-top:30px;">
                    <h4 style="margin-bottom:16px;">Ikuti Kami</h4>
                    <div style="display:flex;gap:12px;">
                        <?php
                        $socials = [['fab fa-instagram','#'],['fab fa-facebook','#'],['fab fa-tiktok','#']];
                        foreach ($socials as $s): ?>
                        <a href="<?php echo $s[1]; ?>" style="width:40px;height:40px;background:var(--black);color:#fff;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:1rem;" target="_blank"><i class="<?php echo $s[0]; ?>"></i></a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <!-- Form Kontak -->
            <div style="background:#fff;border-radius:16px;padding:40px;box-shadow:0 4px 20px rgba(0,0,0,.08);">
                <h2 style="margin-bottom:24px;">Kirim <span style="color:var(--gold);">Pesan</span></h2>
                <?php if ($message): ?><div class="auth-success" style="margin-bottom:16px;"><i class="fas fa-check-circle"></i> <?php echo $message; ?></div><?php endif; ?>
                <form method="POST">
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                        <div class="form-group">
                            <label>Nama Lengkap</label>
                            <input type="text" name="nama" class="form-control" placeholder="Nama Anda" required style="width:100%;padding:12px;border:1px solid #ddd;border-radius:8px;">
                        </div>
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" name="email" class="form-control" placeholder="email@anda.com" required style="width:100%;padding:12px;border:1px solid #ddd;border-radius:8px;">
                        </div>
                    </div>
                    <div class="form-group" style="margin-top:16px;">
                        <label>Subjek</label>
                        <input type="text" name="subjek" class="form-control" placeholder="Perihal pesan" required style="width:100%;padding:12px;border:1px solid #ddd;border-radius:8px;">
                    </div>
                    <div class="form-group" style="margin-top:16px;">
                        <label>Pesan</label>
                        <textarea name="pesan" rows="5" placeholder="Tulis pesan Anda..." required style="width:100%;padding:12px;border:1px solid #ddd;border-radius:8px;resize:vertical;font-family:inherit;"></textarea>
                    </div>
                    <button type="submit" class="btn" style="width:100%;margin-top:16px;"><i class="fas fa-paper-plane"></i> Kirim Pesan</button>
                </form>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
</body>
</html>
