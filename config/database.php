<?php
// config/database.php - Koneksi Database

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');          // Ganti sesuai password MySQL Anda
define('DB_NAME', 'lumier_parfum');  // FIXED: sesuai nama DB di SQL

// Buat koneksi
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi database gagal: " . $conn->connect_error);
}

// Set charset ke UTF-8
$conn->set_charset("utf8mb4");

// Helper function to check if table exists
function tableExists($tableName) {
    global $conn;
    $result = $conn->query("SHOW TABLES LIKE '$tableName'");
    return $result && $result->num_rows > 0;
}

// Helper function to check if column exists
function columnExists($tableName, $columnName) {
    global $conn;
    $result = $conn->query("SHOW COLUMNS FROM `$tableName` LIKE '$columnName'");
    return $result && $result->num_rows > 0;
}
?>