<?php
require_once 'auth_terminal.php';
require_once 'config.php';

header('Content-Type: application/json');

if ($_SESSION['terminal_type'] !== 'CLAIM') {
    echo json_encode(['success' => false, 'error' => 'Not authorized']);
    exit;
}

$id = (int)($_POST['id'] ?? 0);
if (!$id) {
    echo json_encode(['success' => false, 'error' => 'Invalid id']);
    exit;
}

$stmt = $pdo->prepare("UPDATE orders SET status = 'COMPLETED' WHERE id = ?");
$stmt->execute([$id]);

send_ws_message([
    'type'  => 'order.updated',
    'scope' => ['TELLER','KITCHEN','CLAIM'],
    'order' => ['id' => $id, 'status' => 'COMPLETED']
]);

echo json_encode(['success' => true]);
