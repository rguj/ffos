<?php
require_once 'auth_terminal.php';
require_once 'config.php';

header('Content-Type: application/json');

if ($_SESSION['terminal_type'] !== 'KITCHEN') {
    echo json_encode(['success' => false, 'error' => 'Not authorized']);
    exit;
}

$id = (int)($_POST['id'] ?? 0);
$status = $_POST['status'] ?? '';

if (!$id || !in_array($status, ['IN_PROCESS','READY_FOR_CLAIM'], true)) {
    echo json_encode(['success' => false, 'error' => 'Invalid']);
    exit;
}

$stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
$stmt->execute([$status, $id]);

send_ws_message([
    'type'  => 'order.updated',
    'scope' => ['TELLER','KITCHEN','CLAIM'],
    'order' => ['id' => $id, 'status' => $status]
]);

echo json_encode(['success' => true]);
