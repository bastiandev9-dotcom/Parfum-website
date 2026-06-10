<?php
require_once '../config/database.php';
require_once '../includes/functions.php';
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') { header('Location: ../login.php'); exit; }

$message = $error = '';

// Delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $chk = $conn->query("SELECT COUNT(*) FROM products WHERE category_id=$id")->fetch_row()[0];
    if ($chk > 0) $error = 'Kategori tidak bisa dihapus karena masih digunakan produk.';
    else { $conn->query("DELETE FROM categories WHERE category_id=$id"); $message = 'Kategori dihapus.'; }
}

// Save
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id   = (int)($_POST['category_id'] ?? 0);
    $nama = trim($_POST['nama_kategori'] ?? '');
    $slug = trim($_POST['slug'] ?? '') ?: generateSlug($nama);
    $desk = trim($_POST['deskripsi'] ?? '');
    $urutan = (int)($_POST['urutan'] ?? 0);
    if ($id) {
        $stmt = $conn->prepare("UPDATE categories SET nama_kategori=?,slug=?,deskripsi=?,urutan=? WHERE category_id=?");
        $stmt->bind_param("sssii", $nama, $slug, $desk, $urutan, $id);
    } else {
        $stmt = $conn->prepare("INSERT INTO categories (nama_kategori,slug,deskripsi,urutan) VALUES (?,?,?,?)");
        $stmt->bind_param("sssi", $nama, $slug, $desk, $urutan);
    }
    $stmt->execute() ? $message = 'Kategori disimpan.' : $error = 'Gagal menyimpan.';
}

$editCat = null;
if (isset($_GET['edit'])) {
    $stmt = $conn->prepare("SELECT * FROM categories WHERE category_id=?");
    $stmt->bind_param("i", (int)$_GET['edit']); $stmt->execute();
    $editCat = $stmt->get_result()->fetch_assoc();
}

$cats = $conn->query("SELECT c.*, (SELECT COUNT(*) FROM products WHERE category_id=c.category_id) as total_produk FROM categories c ORDER BY c.urutan")->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html><html lang="id"><head><meta charset="UTF-8"><title>Kategori - Admin</title>
<link rel="stylesheet" href="../assets/css/style.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head><body class="admin-body">
<?php include 'includes/admin-sidebar.php'; ?>
<main class="admin-main">
    <div class="admin-header"><h1>Kelola Kategori</h1></div>
    <?php if ($message): ?><div class="auth-success" style="margin-bottom:16px;"><?php echo $message; ?></div><?php endif; ?>
    <?php if ($error): ?><div class="auth-error" style="margin-bottom:16px;"><?php echo $error; ?></div><?php endif; ?>

    <div style="display:grid;grid-template-columns:340px 1fr;gap:24px;">
        <div class="admin-card">
            <div class="admin-card-header"><h3><?php echo $editCat ? 'Edit' : 'Tambah'; ?> Kategori</h3></div>
            <form method="POST" class="admin-form">
                <input type="hidden" name="category_id" value="<?php echo $editCat['category_id'] ?? ''; ?>">
                <div class="form-group"><label>Nama Kategori *</label><input type="text" name="nama_kategori" value="<?php echo htmlspecialchars($editCat['nama_kategori'] ?? ''); ?>" required></div>
                <div class="form-group"><label>Slug</label><input type="text" name="slug" value="<?php echo htmlspecialchars($editCat['slug'] ?? ''); ?>" placeholder="auto"></div>
                <div class="form-group"><label>Urutan</label><input type="number" name="urutan" value="<?php echo $editCat['urutan'] ?? 0; ?>"></div>
                <div class="form-group"><label>Deskripsi</label><textarea name="deskripsi" rows="3"><?php echo htmlspecialchars($editCat['deskripsi'] ?? ''); ?></textarea></div>
                <button type="submit" class="btn btn-save-admin"><i class="fas fa-save"></i> Simpan</button>
                <?php if ($editCat): ?><a href="categories.php" class="btn btn-outline" style="margin-left:8px;">Batal</a><?php endif; ?>
            </form>
        </div>
        <div class="admin-card">
            <div class="admin-card-header"><h3>Daftar Kategori</h3></div>
            <div class="table-responsive">
                <table class="admin-table">
                    <thead><tr><th>ID</th><th>Nama</th><th>Slug</th><th>Produk</th><th>Urutan</th><th>Aksi</th></tr></thead>
                    <tbody>
                    <?php foreach ($cats as $c): ?>
                    <tr>
                        <td><?php echo $c['category_id']; ?></td>
                        <td><strong><?php echo htmlspecialchars($c['nama_kategori']); ?></strong></td>
                        <td><?php echo $c['slug']; ?></td>
                        <td><?php echo $c['total_produk']; ?></td>
                        <td><?php echo $c['urutan']; ?></td>
                        <td class="table-actions">
                            <a href="?edit=<?php echo $c['category_id']; ?>" class="btn-icon btn-edit"><i class="fas fa-pen"></i></a>
                            <a href="?delete=<?php echo $c['category_id']; ?>" class="btn-icon btn-delete" onclick="return confirm('Hapus kategori ini?')"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>
</body></html>
