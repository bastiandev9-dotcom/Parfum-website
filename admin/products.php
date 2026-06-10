<?php
require_once '../config/database.php';
require_once '../includes/functions.php';
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
$message = '';
$error = '';

// === HANDLE DELETE ===
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM products WHERE product_id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        $message = 'Produk berhasil dihapus!';
    } else {
        $error = 'Gagal menghapus produk.';
    }
}

// === HANDLE ADD/EDIT ===
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = $_POST['product_id'] ?? '';
    $nama = trim($_POST['nama_produk'] ?? '');
    $slug = trim($_POST['slug'] ?? '');
    $brand_id = (int)($_POST['brand_id'] ?? 0);
    $category_id = (int)($_POST['category_id'] ?? 0);
    $harga = (int)($_POST['harga'] ?? 0);
    $harga_diskon = !empty($_POST['harga_diskon']) ? (int)$_POST['harga_diskon'] : null;
    $deskripsi = trim($_POST['deskripsi'] ?? '');
    $aroma = $_POST['aroma'] ?? 'woody';
    $gender = $_POST['gender'] ?? 'unisex';
    $stok = (int)($_POST['stok'] ?? 0);
    $status = $_POST['status'] ?? 'aktif';
    $gambar = trim($_POST['gambar_utama'] ?? '');

    if (empty($slug)) {
        $slug = strtolower(str_replace(' ', '-', $nama));
    }

    if ($product_id) {
        // UPDATE
        $stmt = $conn->prepare("
            UPDATE products SET 
                nama_produk=?, slug=?, brand_id=?, category_id=?, harga=?, harga_diskon=?,
                deskripsi=?, aroma=?, gender=?, stok=?, status=?, gambar_utama=?, updated_at=NOW()
            WHERE product_id=?
        ");
        $stmt->bind_param("ssiiiisssissi", $nama, $slug, $brand_id, $category_id, $harga, $harga_diskon,
            $deskripsi, $aroma, $gender, $stok, $status, $gambar, $product_id);
        if ($stmt->execute()) {
            $message = 'Produk berhasil diupdate!';
        } else {
            $error = 'Gagal mengupdate produk: ' . $stmt->error;
        }
    } else {
        // INSERT
        $stmt = $conn->prepare("
            INSERT INTO products (nama_produk, slug, brand_id, category_id, harga, harga_diskon,
                deskripsi, aroma, gender, stok, status, gambar_utama)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->bind_param("ssiiiisssiss", $nama, $slug, $brand_id, $category_id, $harga, $harga_diskon,
            $deskripsi, $aroma, $gender, $stok, $status, $gambar);
        if ($stmt->execute()) {
            $message = 'Produk berhasil ditambahkan!';
        } else {
            $error = 'Gagal menambahkan produk: ' . $stmt->error;
        }
    }
}

// === GET DATA FOR EDIT ===
$editProduct = null;
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $id = (int)$_GET['edit'];
    $stmt = $conn->prepare("SELECT * FROM products WHERE product_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $editProduct = $stmt->get_result()->fetch_assoc();
}

// === FETCH LISTS ===
$brands = $conn->query("SELECT brand_id, nama_brand FROM brands ORDER BY nama_brand");
$categories = $conn->query("SELECT category_id, nama_kategori FROM categories ORDER BY nama_kategori");

// === FETCH PRODUCTS ===
$result = $conn->query("
    SELECT p.*, b.nama_brand, c.nama_kategori 
    FROM products p
    LEFT JOIN brands b ON p.brand_id = b.brand_id
    LEFT JOIN categories c ON p.category_id = c.category_id
    ORDER BY p.created_at DESC
");
$products = [];
while ($row = $result->fetch_assoc()) {
    $products[] = $row;
}

// conn dibiarkan terbuka untuk query export PDF di bawah

$action = isset($_GET['action']) ? $_GET['action'] : '';
if ($editProduct) $action = 'edit';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Produk - Admin</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="admin-body">

<?php include 'includes/admin-sidebar.php'; ?>

<main class="admin-main">
    <div class="admin-header">
        <h1>Kelola Produk</h1>
        <div style="display:flex;gap:8px;">
            <button onclick="exportPDF()" class="btn btn-outline" style="border-color:#C9A962;color:#C9A962;background:#fff;cursor:pointer;"><i class="fas fa-file-pdf"></i> Export PDF</button>
            <a href="?action=add" class="btn btn-admin-add"><i class="fas fa-plus"></i> Tambah Produk</a>
        </div>
    </div>

    <?php if ($message): ?>
    <div class="auth-success" style="margin-bottom:20px;"><i class="fas fa-check-circle"></i> <?php echo $message; ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
    <div class="auth-error" style="margin-bottom:20px;"><i class="fas fa-exclamation-circle"></i> <?php echo $error; ?></div>
    <?php endif; ?>

    <!-- FORM ADD/EDIT -->
    <?php if ($action === 'add' || $action === 'edit'): ?>
    <div class="admin-card form-card-admin">
        <div class="admin-card-header">
            <h3><?php echo $action === 'edit' ? 'Edit Produk' : 'Tambah Produk Baru'; ?></h3>
            <a href="products.php" class="admin-link"><i class="fas fa-times"></i> Batal</a>
        </div>
        <form method="POST" action="products.php" class="admin-form">
            <input type="hidden" name="product_id" value="<?php echo $editProduct['product_id'] ?? ''; ?>">

            <div class="form-row">
                <div class="form-group"><label>Nama Produk *</label><input type="text" name="nama_produk" value="<?php echo htmlspecialchars($editProduct['nama_produk'] ?? ''); ?>" required></div>
                <div class="form-group"><label>Slug</label><input type="text" name="slug" value="<?php echo htmlspecialchars($editProduct['slug'] ?? ''); ?>" placeholder="auto-generate"></div>
            </div>

            <div class="form-row">
                <div class="form-group"><label>Brand *</label>
                    <select name="brand_id" required>
                        <option value="">Pilih Brand</option>
                        <?php while ($b = $brands->fetch_assoc()): ?>
                        <option value="<?php echo $b['brand_id']; ?>" <?php echo ($editProduct['brand_id'] ?? '') == $b['brand_id'] ? 'selected' : ''; ?>><?php echo $b['nama_brand']; ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="form-group"><label>Kategori *</label>
                    <select name="category_id" required>
                        <option value="">Pilih Kategori</option>
                        <?php while ($c = $categories->fetch_assoc()): ?>
                        <option value="<?php echo $c['category_id']; ?>" <?php echo ($editProduct['category_id'] ?? '') == $c['category_id'] ? 'selected' : ''; ?>><?php echo $c['nama_kategori']; ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group"><label>Harga (Rp) *</label><input type="number" name="harga" value="<?php echo $editProduct['harga'] ?? ''; ?>" required></div>
                <div class="form-group"><label>Harga Diskon (Rp)</label><input type="number" name="harga_diskon" value="<?php echo $editProduct['harga_diskon'] ?? ''; ?>"></div>
            </div>

            <div class="form-row">
                <div class="form-group"><label>Stok *</label><input type="number" name="stok" value="<?php echo $editProduct['stok'] ?? '0'; ?>" required></div>
                <div class="form-group"><label>Status</label>
                    <select name="status">
                        <option value="aktif" <?php echo ($editProduct['status'] ?? '') === 'aktif' ? 'selected' : ''; ?>>Aktif</option>
                        <option value="habis" <?php echo ($editProduct['status'] ?? '') === 'habis' ? 'selected' : ''; ?>>Habis</option>
                        <option value="nonaktif" <?php echo ($editProduct['status'] ?? '') === 'nonaktif' ? 'selected' : ''; ?>>Nonaktif</option>
                        <option value="draft" <?php echo ($editProduct['status'] ?? '') === 'draft' ? 'selected' : ''; ?>>Draft</option>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group"><label>Aroma</label>
                    <select name="aroma">
                        <option value="woody" <?php echo ($editProduct['aroma'] ?? '') === 'woody' ? 'selected' : ''; ?>>Woody</option>
                        <option value="floral" <?php echo ($editProduct['aroma'] ?? '') === 'floral' ? 'selected' : ''; ?>>Floral</option>
                        <option value="fresh" <?php echo ($editProduct['aroma'] ?? '') === 'fresh' ? 'selected' : ''; ?>>Fresh</option>
                        <option value="oriental" <?php echo ($editProduct['aroma'] ?? '') === 'oriental' ? 'selected' : ''; ?>>Oriental</option>
                        <option value="citrus" <?php echo ($editProduct['aroma'] ?? '') === 'citrus' ? 'selected' : ''; ?>>Citrus</option>
                        <option value="spicy" <?php echo ($editProduct['aroma'] ?? '') === 'spicy' ? 'selected' : ''; ?>>Spicy</option>
                    </select>
                </div>
                <div class="form-group"><label>Gender</label>
                    <select name="gender">
                        <option value="pria" <?php echo ($editProduct['gender'] ?? '') === 'pria' ? 'selected' : ''; ?>>Pria</option>
                        <option value="wanita" <?php echo ($editProduct['gender'] ?? '') === 'wanita' ? 'selected' : ''; ?>>Wanita</option>
                        <option value="unisex" <?php echo ($editProduct['gender'] ?? '') === 'unisex' ? 'selected' : ''; ?>>Unisex</option>
                    </select>
                </div>
            </div>

            <div class="form-group"><label>URL Gambar Utama</label><input type="text" name="gambar_utama" value="<?php echo htmlspecialchars($editProduct['gambar_utama'] ?? ''); ?>" placeholder="https://images.unsplash.com/..."></div>

            <div class="form-group"><label>Deskripsi</label><textarea name="deskripsi" rows="4"><?php echo htmlspecialchars($editProduct['deskripsi'] ?? ''); ?></textarea></div>

            <button type="submit" class="btn btn-save-admin"><i class="fas fa-save"></i> <?php echo $action === 'edit' ? 'Update Produk' : 'Simpan Produk'; ?></button>
        </form>
    </div>
    <?php endif; ?>

    <!-- TABLE -->
    <div class="admin-card">
        <div class="admin-card-header">
            <h3>Daftar Produk</h3>
            <div class="table-search"><input type="text" id="searchProduct" placeholder="Cari produk..." onkeyup="searchTable()"><i class="fas fa-search"></i></div>
        </div>
        <div class="table-responsive">
            <table class="admin-table" id="productTable">
                <thead>
                    <tr><th>ID</th><th>Produk</th><th>Brand</th><th>Harga</th><th>Stok</th><th>Status</th><th>Aksi</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $p): ?>
                    <tr>
                        <td>#<?php echo $p['product_id']; ?></td>
                        <td><strong><?php echo $p['nama_produk']; ?></strong></td>
                        <td><?php echo $p['nama_brand']; ?></td>
                        <td><?php echo formatRupiah($p['harga']); ?></td>
                        <td><?php echo $p['stok']; ?></td>
                        <td><span class="status-dot <?php echo $p['status'] === 'aktif' ? 'dot-green' : ($p['status'] === 'habis' ? 'dot-red' : 'dot-gray'); ?>"><?php echo ucfirst($p['status']); ?></span></td>
                        <td class="table-actions">
                            <a href="?edit=<?php echo $p['product_id']; ?>" class="btn-icon btn-edit" title="Edit"><i class="fas fa-pen"></i></a>
                            <a href="?delete=<?php echo $p['product_id']; ?>" class="btn-icon btn-delete" title="Hapus" onclick="return confirm('Yakin hapus produk ini?')"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<script>
function searchTable() {
    const input = document.getElementById('searchProduct');
    const filter = input.value.toLowerCase();
    const table = document.getElementById('productTable');
    const tr = table.getElementsByTagName('tr');
    for (let i = 1; i < tr.length; i++) {
        const td = tr[i].getElementsByTagName('td');
        let found = false;
        for (let j = 0; j < td.length; j++) {
            if (td[j] && td[j].textContent.toLowerCase().indexOf(filter) > -1) {
                found = true; break;
            }
        }
        tr[i].style.display = found ? '' : 'none';
    }
}
</script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.8.2/jspdf.plugin.autotable.min.js"></script>
<script>
window.exportPDFConfig = {
    orientation: 'landscape',
    subtitle: 'Daftar Produk — Dicetak: <?php echo date("d/m/Y H:i"); ?>',
    filename: 'produk-lumiere.pdf',
    head: ['#','Nama Produk','Brand','Harga','Harga Diskon','Stok','Aroma','Gender','Status'],
    body: (<?php
        $res2 = $conn->query("SELECT p.nama_produk, b.nama_brand, p.harga, p.harga_diskon, p.stok, p.aroma, p.gender, p.status FROM products p LEFT JOIN brands b ON p.brand_id=b.brand_id ORDER BY p.nama_produk");
        echo json_encode($res2->fetch_all(MYSQLI_ASSOC));
    ?>).map((p, i) => [
        i + 1, p.nama_produk, p.nama_brand || '-',
        'Rp ' + parseInt(p.harga).toLocaleString('id-ID'),
        p.harga_diskon ? 'Rp ' + parseInt(p.harga_diskon).toLocaleString('id-ID') : '-',
        p.stok, p.aroma, p.gender, p.status
    ])
};
</script>
<script src="../assets/js/export-pdf.js"></script>

</body>
</html>
