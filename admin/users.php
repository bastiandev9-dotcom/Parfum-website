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

// === HANDLE STATUS UPDATE ===
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_user'])) {
    $user_id = (int)$_POST['user_id'];
    $status = $_POST['status'];
    $role = $_POST['role'];

    $stmt = $conn->prepare("UPDATE users SET status = ?, role = ? WHERE user_id = ?");
    $stmt->bind_param("ssi", $status, $role, $user_id);
    if ($stmt->execute()) {
        $message = 'User diupdate!';
    }
}

// === HANDLE DELETE ===
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    // Prevent delete self
    if ($id !== ($_SESSION['user_id'] ?? 0)) {
        $stmt = $conn->prepare("DELETE FROM users WHERE user_id = ? AND role != 'admin'");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $message = 'User dihapus!';
    } else {
        $message = 'Tidak bisa hapus diri sendiri!';
    }
}

// === FETCH USERS ===
$result = $conn->query("
    SELECT u.*, 
        (SELECT COUNT(*) FROM orders WHERE user_id = u.user_id) as total_order
    FROM users u
    ORDER BY u.created_at DESC
");
$users = [];
while ($row = $result->fetch_assoc()) {
    $users[] = $row;
}

$conn->close();

$statusOptions = ['aktif', 'nonaktif', 'diblokir'];
$roleOptions = ['customer', 'admin'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola User - Admin</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="admin-body">

<?php include 'includes/admin-sidebar.php'; ?>

<main class="admin-main">
    <div class="admin-header">
        <h1>Kelola User</h1>
        <div class="table-search" style="width:300px;"><input type="text" placeholder="Cari nama atau email..." id="searchUser" onkeyup="searchTable()"><i class="fas fa-search"></i></div>
    </div>

    <?php if ($message): ?>
    <div class="auth-success" style="margin-bottom:20px;"><i class="fas fa-check-circle"></i> <?php echo $message; ?></div>
    <?php endif; ?>

    <div class="admin-card">
        <div class="table-responsive">
            <table class="admin-table" id="userTable">
                <thead>
                    <tr><th>ID</th><th>Nama</th><th>Email</th><th>Telepon</th><th>Order</th><th>Role</th><th>Status</th><th>Aksi</th></tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $u): ?>
                    <tr>
                        <td>#<?php echo $u['user_id']; ?></td>
                        <td>
                            <div class="user-cell">
                                <div class="cell-avatar"><?php echo strtoupper(substr($u['nama'], 0, 2)); ?></div>
                                <span><?php echo $u['nama']; ?></span>
                            </div>
                        </td>
                        <td><?php echo $u['email']; ?></td>
                        <td><?php echo $u['telepon'] ?: '-'; ?></td>
                        <td><?php echo $u['total_order']; ?>x</td>
                        <td>
                            <form method="POST" style="display:inline">
                                <input type="hidden" name="user_id" value="<?php echo $u['user_id']; ?>">
                                <input type="hidden" name="update_user" value="1">
                                <input type="hidden" name="status" value="<?php echo $u['status']; ?>">
                                <select name="role" class="admin-select" style="padding:6px 10px;font-size:0.8rem;" onchange="this.form.submit()">
                                    <?php foreach ($roleOptions as $r): ?>
                                    <option value="<?php echo $r; ?>" <?php echo $u['role'] === $r ? 'selected' : ''; ?>><?php echo ucfirst($r); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </form>
                        </td>
                        <td>
                            <form method="POST" style="display:inline">
                                <input type="hidden" name="user_id" value="<?php echo $u['user_id']; ?>">
                                <input type="hidden" name="update_user" value="1">
                                <input type="hidden" name="role" value="<?php echo $u['role']; ?>">
                                <select name="status" class="status-select status-<?php echo $u['status']; ?>" style="padding:6px 10px;font-size:0.8rem;" onchange="this.form.submit()">
                                    <?php foreach ($statusOptions as $s): ?>
                                    <option value="<?php echo $s; ?>" <?php echo $u['status'] === $s ? 'selected' : ''; ?>><?php echo ucfirst($s); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </form>
                        </td>
                        <td class="table-actions">
                            <?php if ($u['user_id'] !== ($_SESSION['user_id'] ?? 0)): ?>
                            <a href="?delete=<?php echo $u['user_id']; ?>" class="btn-icon btn-delete" onclick="return confirm('Yakin hapus user ini?')" title="Hapus"><i class="fas fa-trash"></i></a>
                            <?php endif; ?>
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
    const input = document.getElementById('searchUser');
    const filter = input.value.toLowerCase();
    const table = document.getElementById('userTable');
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

</body>
</html>