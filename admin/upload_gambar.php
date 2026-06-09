<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php'); exit;
}

$msg = '';
$uploadDir = '../assets/images/products/';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_FILES['gambar']['name'][0])) {
    $uploaded = [];
    $errors   = [];

    foreach ($_FILES['gambar']['tmp_name'] as $i => $tmp) {
        $originalName = $_FILES['gambar']['name'][$i];
        $ext  = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        $allowed = ['jpg','jpeg','png','webp'];

        if (!in_array($ext, $allowed)) {
            $errors[] = "$originalName: format tidak didukung.";
            continue;
        }

        // Buat nama file bersih dari nama asli
        $slug     = preg_replace('/[^a-z0-9]+/', '-', strtolower(pathinfo($originalName, PATHINFO_FILENAME)));
        $filename = $slug . '.' . $ext;
        $dest     = $uploadDir . $filename;

        // Hindari overwrite
        $counter = 1;
        while (file_exists($dest)) {
            $filename = $slug . '-' . $counter . '.' . $ext;
            $dest     = $uploadDir . $filename;
            $counter++;
        }

        if (move_uploaded_file($tmp, $dest)) {
            $uploaded[] = 'assets/images/products/' . $filename;
        } else {
            $errors[] = "$originalName: gagal diupload.";
        }
    }

    if ($uploaded) {
        $msg = '<div class="auth-success"><i class="fas fa-check-circle"></i> ' . count($uploaded) . ' gambar berhasil diupload:<br>';
        foreach ($uploaded as $path) {
            $msg .= '<code>' . $path . '</code><br>';
        }
        $msg .= '</div>';
    }
    if ($errors) {
        $msg .= '<div class="auth-error"><i class="fas fa-exclamation-circle"></i> ' . implode('<br>', $errors) . '</div>';
    }
}

// Ambil daftar gambar yang sudah ada
$existingFiles = glob($uploadDir . '*.{jpg,jpeg,png,webp}', GLOB_BRACE) ?: [];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Upload Gambar - Lumière Admin</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .upload-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(150px,1fr)); gap: 12px; margin-top: 20px; }
        .upload-thumb { border: 1px solid #ddd; border-radius: 8px; overflow: hidden; text-align: center; background: #fafafa; }
        .upload-thumb img { width: 100%; height: 120px; object-fit: cover; }
        .upload-thumb p { font-size: .75rem; padding: 6px; word-break: break-all; color: #555; margin: 0; }
        .upload-thumb code { font-size: .7rem; background: #f0f0f0; padding: 2px 4px; border-radius: 3px; display: block; margin: 0 6px 6px; }
        .drop-zone { border: 2px dashed #c9a84c; border-radius: 12px; padding: 40px; text-align: center; cursor: pointer; color: #888; transition: background .2s; }
        .drop-zone:hover, .drop-zone.dragover { background: #fffbf0; }
    </style>
</head>
<body class="admin-body">

<?php include 'includes/admin-sidebar.php'; ?>

<main class="admin-main">
    <div class="admin-header">
        <h1><i class="fas fa-images"></i> Upload Gambar Produk</h1>
    </div>

    <?php echo $msg; ?>

    <div class="admin-card" style="margin-bottom:24px;">
        <form method="POST" enctype="multipart/form-data">
            <div class="drop-zone" onclick="document.getElementById('fileInput').click()" id="dropZone">
                <i class="fas fa-cloud-upload-alt" style="font-size:2rem;color:#c9a84c;"></i>
                <p style="margin:10px 0 4px;">Klik atau drag & drop gambar di sini</p>
                <small>Format: JPG, PNG, WEBP • Bisa pilih banyak sekaligus</small>
            </div>
            <input type="file" id="fileInput" name="gambar[]" multiple accept=".jpg,.jpeg,.png,.webp" style="display:none" onchange="previewFiles(this)">
            <div id="previewArea" class="upload-grid"></div>
            <button type="submit" class="btn" style="margin-top:16px;"><i class="fas fa-upload"></i> Upload Sekarang</button>
        </form>
    </div>

    <div class="admin-card">
        <div class="admin-card-header"><h3>Gambar Tersedia (<?php echo count($existingFiles); ?>)</h3></div>
        <?php if ($existingFiles): ?>
        <div class="upload-grid">
            <?php foreach ($existingFiles as $file):
                $filename = basename($file);
                $path = 'assets/images/products/' . $filename;
            ?>
            <div class="upload-thumb">
                <img src="../<?php echo $path; ?>" alt="<?php echo $filename; ?>">
                <p><?php echo $filename; ?></p>
                <code><?php echo $path; ?></code>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <p style="color:#999;padding:20px 0;">Belum ada gambar. Upload dulu di atas.</p>
        <?php endif; ?>
    </div>
</main>

<script>
const dropZone = document.getElementById('dropZone');
dropZone.addEventListener('dragover', e => { e.preventDefault(); dropZone.classList.add('dragover'); });
dropZone.addEventListener('dragleave', () => dropZone.classList.remove('dragover'));
dropZone.addEventListener('drop', e => {
    e.preventDefault();
    dropZone.classList.remove('dragover');
    document.getElementById('fileInput').files = e.dataTransfer.files;
    previewFiles(document.getElementById('fileInput'));
});

function previewFiles(input) {
    const area = document.getElementById('previewArea');
    area.innerHTML = '';
    Array.from(input.files).forEach(file => {
        const reader = new FileReader();
        reader.onload = e => {
            area.innerHTML += `<div class="upload-thumb">
                <img src="${e.target.result}" style="height:120px;object-fit:cover;width:100%">
                <p>${file.name}</p>
            </div>`;
        };
        reader.readAsDataURL(file);
    });
}
</script>
</body>
</html>
