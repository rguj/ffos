<?php
// api_get_orders_today.php
require_once 'config.php';
header('Content-Type: application/json');

try {
    $stmt = $pdo->prepare("
        SELECT id, display_number, total_amount, status, created_at, updated_at, paid_at
        FROM orders
        WHERE DATE(created_at) = CURDATE()
        ORDER BY updated_at ASC
    ");
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $orders = [];
    $seq = 1;
    foreach ($rows as $r) {
        $disp = isset($r['display_number']) && $r['display_number'] !== null
            ? (int)$r['display_number']
            : $seq;
        $orders[] = [
            'id'             => (int)$r['id'],
            'display_number' => $disp,
            'seq'            => $seq,
            'total_amount'   => (float)$r['total_amount'],
            'status'         => $r['status'],
            'ordered_on'     => $r['created_at'] ? date('Y-m-d H:i', strtotime($r['created_at'])) : '',
            'paid_on'        => $r['paid_at'] ? date('Y-m-d H:i', strtotime($r['paid_at'])) : null,
        ];
        $seq++;
    }

    echo json_encode(['success' => true, 'orders' => $orders]);
} catch (Throwable $e) {
    echo json_encode(['success' => false, 'error' => 'Server error']);
}
