<?php
// api_get_order.php
require_once 'config.php';

// Make sure PHP notices/warnings don't break JSON
error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING & ~E_DEPRECATED & ~E_USER_DEPRECATED);
ini_set('display_errors', 0);

header('Content-Type: application/json; charset=utf-8');

$orderId = isset($_GET['order_id']) ? (int)$_GET['order_id'] : 0;
if ($orderId <= 0) {
    echo json_encode(['success' => false, 'error' => 'Invalid order ID']);
    exit;
}

try {
    // Get main order info
    $stmt = $pdo->prepare("
        SELECT id,
               display_number,
               total_amount,
               status,
               created_at,
               updated_at,
               paid_at,
               cash_received,
               change_amount
        FROM orders
        WHERE id = ?
        LIMIT 1
    ");
    $stmt->execute([$orderId]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$order) {
        echo json_encode(['success' => false, 'error' => 'Order not found']);
        exit;
    }

    // Normalize numeric fields
    $orderOut = [
        'id'            => (int)$order['id'],
        'display_number'=> $order['display_number'] !== null ? (int)$order['display_number'] : null,
        'total_amount'  => $order['total_amount'] !== null ? (float)$order['total_amount'] : 0.0,
        'status'        => $order['status'],
        'created_at'    => $order['created_at'],
        'updated_at'    => $order['updated_at'],
        'paid_at'       => $order['paid_at'],
        'cash_received' => $order['cash_received'] !== null ? (float)$order['cash_received'] : 0.0,
        'change_amount' => $order['change_amount'] !== null ? (float)$order['change_amount'] : 0.0,
    ];

    // Get order items with product names
    $stmt = $pdo->prepare("
        SELECT oi.id,
               oi.menu_item_id,
               oi.quantity,
               oi.price,
               oi.source,
               m.name
        FROM order_items oi
        JOIN menu_items m ON m.id = oi.menu_item_id
        WHERE oi.order_id = ?
        ORDER BY oi.id ASC
    ");
    $stmt->execute([$orderId]);
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'order'   => $orderOut,
        'items'   => $items,
    ]);
} catch (Throwable $e) {
    echo json_encode(['success' => false, 'error' => 'Server error']);
}
