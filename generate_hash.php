<?php
// generate_hash.php - Jalankan sekali untuk mendapatkan hash password
// Akses: http://localhost/perfume/generate_hash.php
// Hapus file ini setelah selesai!

$passwords = [
    'admin123'    => password_hash('admin123', PASSWORD_DEFAULT),
    'password123' => password_hash('password123', PASSWORD_DEFAULT),
];

foreach ($passwords as $plain => $hash) {
    echo "<p><strong>$plain</strong>: <code>$hash</code></p>";
    echo "<p>SQL: <code>UPDATE users SET password_hash = '$hash' WHERE email = 'admin\@lumier.com';</code></p><hr>";
}
?>
