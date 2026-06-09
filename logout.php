<?php
// logout.php
session_start();

// Hapus semua session
$_SESSION = [];
session_destroy();

// Hapus cookie session kalau ada
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 3600, '/');
}

header('Location: login.php');
exit;
?>