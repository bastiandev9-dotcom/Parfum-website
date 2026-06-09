<?php
session_start();
require_once '../config/database.php';
if (!isset($_SESSION['user_id'])) { http_response_code(401); exit; }

$uid = $_SESSION['user_id'];
$counts = ['pending'=>0,'diproses'=>0,'dikirim'=>0,'selesai'=>0];
$r = $conn->prepare("SELECT status, COUNT(*) as c FROM orders WHERE user_id=? GROUP BY status");
$r->bind_param("i", $uid);
$r->execute();
foreach ($r->get_result()->fetch_all(MYSQLI_ASSOC) as $row) {
    if (isset($counts[$row['status']])) $counts[$row['status']] = (int)$row['c'];
}
$counts['aktif'] = $counts['pending'] + $counts['diproses'] + $counts['dikirim'];

header('Content-Type: application/json');
echo json_encode($counts);
