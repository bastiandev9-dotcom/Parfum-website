<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php?redirect=wishlist.php');
    exit;
}
header('Location: user/wishlist.php');
exit;
