<?php
// api_get_claim_orders_today.php
require_once 'config.php';
header('Content-Type: application/json; charset=utf-8');

try {
    $stmt = $pdo->prepare("
        SELECT id,
               display_number,
               status,
               updated_at
        FROM orders
        WHERE DATE(created_at) = CURDATE()
          AND status IN ('IN_PROCESS', 'READY_FOR_CLAIM')
        ORDER BY updated_at ASC, id ASC
    ");
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $inProcess = [];
    $ready = [];

    foreach ($rows as $r) {
        $id       = (int)$r['id'];
        $disp     = $r['display_number'] !== null ? (int)$r['display_number'] : $id;
        $dispStr  = str_pad($disp, 4, '0', STR_PAD_LEFT);
        $status   = strtoupper(trim($r['status'] ?? 'IN_PROCESS'));
        $updated  = $r['updated_at'];

        $entry = [
            'id'                => $id,
            'display_number'    => $disp,
            'display_number_str'=> $dispStr,
            'status'            => $status,
            'updated_at'        => $updated,
        ];

        if ($status === 'READY_FOR_CLAIM') {
            $ready[] = $entry;
        } else {
            $inProcess[] = $entry;
        }
    }

    echo json_encode([
        'success'        => true,
        'in_process'     => $inProcess,
        'ready_for_claim'=> $ready,
    ]);
} catch (Throwable $e) {
    echo json_encode(['success' => false, 'error' => 'Server error']);
}
