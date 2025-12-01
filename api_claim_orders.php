<?php
require_once 'auth_terminal.php';
require_once 'config.php';

header('Content-Type: application/json');

if ($_SESSION['terminal_type'] !== 'CLAIM') {
    echo json_encode(['success' => false, 'error' => 'Not authorized']);
    exit;
}

$stmt = $pdo->query("SELECT id FROM orders WHERE status = 'IN_PROCESS' ORDER BY created_at ASC");
$in = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->query("SELECT id FROM orders WHERE status = 'READY_FOR_CLAIM' ORDER BY created_at ASC");
$ready = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode(['success' => true, 'in_process' => $in, 'ready' => $ready]);
