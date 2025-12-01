<?php
// api_get_kitchen_orders_today.php
require_once 'config.php';
header('Content-Type: application/json; charset=utf-8');

try {
    $stmt = $pdo->prepare("
        SELECT id,
               display_number,
               total_amount,
               status,
               created_at,
               updated_at,
               paid_at
        FROM orders
        WHERE DATE(created_at) = CURDATE()
          AND status IN ('IN_PROCESS','READY_FOR_CLAIM')
        ORDER BY paid_at ASC, updated_at ASC, id ASC
    ");
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $orderIds = array_column($rows, 'id');
    $itemsByOrder = [];

    if (!empty($orderIds)) {
        $placeholders = implode(',', array_fill(0, count($orderIds), '?'));
        $itemsStmt = $pdo->prepare("
            SELECT oi.order_id,
                   oi.quantity,
                   m.name
            FROM order_items oi
            JOIN menu_items m ON m.id = oi.menu_item_id
            WHERE oi.order_id IN ($placeholders)
            ORDER BY oi.order_id ASC, oi.id ASC
        ");
        $itemsStmt->execute($orderIds);
        while ($r = $itemsStmt->fetch(PDO::FETCH_ASSOC)) {
            $oid = (int)$r['order_id'];
            if (!isset($itemsByOrder[$oid])) {
                $itemsByOrder[$oid] = [];
            }
            $itemsByOrder[$oid][] = [
                'name' => $r['name'],
                'qty'  => (int)$r['quantity'],
            ];
        }
    }

    $orders = [];
    foreach ($rows as $r) {
        $id = (int)$r['id'];
        $dispNo = $r['display_number'] !== null ? (int)$r['display_number'] : $id;
        $status = strtoupper(trim($r['status'] ?? 'IN_PROCESS'));
        $paidAt = $r['paid_at'] ? date('Y-m-d H:i', strtotime($r['paid_at'])) : null;
        $orders[] = [
            'id'               => $id,
            'display_number'   => $dispNo,
            'display_number_str'=> str_pad($dispNo, 4, '0', STR_PAD_LEFT),
            'total_amount'     => (float)$r['total_amount'],
            'status'           => $status,
            'paid_at'          => $paidAt,
            'items'            => $itemsByOrder[$id] ?? [],
        ];
    }

    echo json_encode(['success' => true, 'orders' => $orders]);
} catch (Throwable $e) {
    echo json_encode(['success' => false, 'error' => 'Server error']);
}
