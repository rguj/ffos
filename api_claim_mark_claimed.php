<?php
// api_claim_mark_claimed.php
require_once 'config.php';
header('Content-Type: application/json; charset=utf-8');

$orderId = isset($_POST['order_id']) ? (int)$_POST['order_id'] : 0;
if ($orderId <= 0) {
    echo json_encode(['success' => false, 'error' => 'Invalid order ID']);
    exit;
}

try {
    // Only READY_FOR_CLAIM can be moved to CLAIMED
    $stmt = $pdo->prepare("SELECT status FROM orders WHERE id = ? LIMIT 1");
    $stmt->execute([$orderId]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
        echo json_encode(['success' => false, 'error' => 'Order not found']);
        exit;
    }

    $currentStatus = strtoupper(trim($row['status'] ?? ''));
    if ($currentStatus !== 'READY_FOR_CLAIM') {
        echo json_encode(['success' => false, 'error' => 'Only READY_FOR_CLAIM orders can be marked CLAIMED']);
        exit;
    }

    $now = date('Y-m-d H:i:s');

    $upd = $pdo->prepare("
        UPDATE orders
        SET status = 'CLAIMED', updated_at = ?
        WHERE id = ?
    ");
    $upd->execute([$now, $orderId]);

    if (function_exists('send_ws_message')) {
        send_ws_message([
            'type'     => 'order_updated',
            'order_id' => $orderId,
        ]);
    }

    echo json_encode(['success' => true]);
} catch (Throwable $e) {
    echo json_encode(['success' => false, 'error' => 'Server error']);
}
