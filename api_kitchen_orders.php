<?php
require_once 'auth_terminal.php';
require_once 'config.php';

header('Content-Type: application/json');

if ($_SESSION['terminal_type'] !== 'KITCHEN') {
    echo json_encode(['success' => false, 'error' => 'Not authorized']);
    exit;
}

$stmt = $pdo->query(
    "SELECT id, total_amount, status
     FROM orders
     WHERE status IN ('PAID','IN_PROCESS')
     ORDER BY created_at ASC"
);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode(['success' => true, 'orders' => $orders]);
