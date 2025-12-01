<?php
// api_get_order.php
require_once 'config.php';
header('Content-Type: application/json');

$orderId = isset($_GET['order_id']) ? (int)$_GET['order_id'] : 0;
if ($orderId <= 0) {
    echo json_encode(['success' => false, 'error' => 'Invalid order ID']);
    exit;
}

try {
    // Get main order info
    $stmt = $pdo->prepare("
        SELECT id, display_number, total_amount, status, created_at, updated_at, paid_at
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
        'order'   => $order,
        'items'   => $items,
    ]);
} catch (Throwable $e) {
    echo json_encode(['success' => false, 'error' => 'Server error']);
}
