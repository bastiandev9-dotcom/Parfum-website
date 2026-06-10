<?php
if (session_status() === PHP_SESSION_NONE) session_start();

function requireLogin() {
    if (!isset($_SESSION['user_id'])) {
        header('Location: ' . (strpos($_SERVER['PHP_SELF'], '/user/') !== false ? '../login.php' : 'login.php'));
        exit;
    }
}

function requireAdmin() {
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
        header('Location: ../login.php');
        exit;
    }
}
