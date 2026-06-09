<?php
session_start();
require_once '../config/database.php';
header('Content-Type: application/json');
if (!isset($_SESSION['user_id'])) { echo json_encode(['error'=>'unauthorized']); exit; }
$orderId = (int)($_GET['id'] ?? 0);
$stmt = $conn->prepare("SELECT status, resi FROM orders WHERE order_id = ? AND user_id = ?");
$stmt->bind_param("ii", $orderId, $_SESSION['user_id']);
$stmt->execute();
echo json_encode($stmt->get_result()->fetch_assoc() ?: ['error'=>'not found']);
