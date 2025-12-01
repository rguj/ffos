<?php
require_once 'auth_terminal.php';

if ($_SESSION['terminal_type'] !== 'KITCHEN') {
    header('Location: index.php');
    exit;
}

require_once 'config.php';

// Fetch today's kitchen-relevant orders: IN_PROCESS + READY_FOR_CLAIM
$ordersStmt = $pdo->prepare("
    SELECT id, display_number, total_amount, status, created_at, updated_at, paid_at
    FROM orders
    WHERE DATE(created_at) = CURDATE()
      AND status IN ('IN_PROCESS','READY_FOR_CLAIM')
    ORDER BY paid_at ASC, updated_at ASC, id ASC
");
$ordersStmt->execute();
$orders = $ordersStmt->fetchAll(PDO::FETCH_ASSOC);

$orderIds = array_column($orders, 'id');
$itemsByOrder = [];

if (!empty($orderIds)) {
    $inPlaceholders = implode(',', array_fill(0, count($orderIds), '?'));
    $itemsStmt = $pdo->prepare("
        SELECT oi.order_id,
               oi.quantity,
               m.name
        FROM order_items oi
        JOIN menu_items m ON m.id = oi.menu_item_id
        WHERE oi.order_id IN ($inPlaceholders)
        ORDER BY oi.order_id ASC, oi.id ASC
    ");
    $itemsStmt->execute($orderIds);
    while ($row = $itemsStmt->fetch(PDO::FETCH_ASSOC)) {
        $oid = (int)$row['order_id'];
        if (!isset($itemsByOrder[$oid])) {
            $itemsByOrder[$oid] = [];
        }
        $itemsByOrder[$oid][] = [
            'name' => $row['name'],
            'qty'  => (int)$row['quantity'],
        ];
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Kitchen Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-size: 0.9rem; }

        .table-scroll {
            max-height: 480px;
            overflow-y: auto;
            overflow-x: hidden;
        }
        .table-scroll table { margin-bottom: 0; }
        .table-scroll thead th {
            position: sticky;
            top: 0;
            z-index: 5;
            background-color: #f8f9fa;
        }

        .status-filter-btn.active {
            font-weight: 600;
        }

        .items-list div {
            line-height: 1.2;
        }
    </style>
</head>
<body class="bg-light">
<nav class="navbar navbar-expand-lg navbar-dark bg-success mb-3">
    <div class="container-fluid">
        <span class="navbar-brand">Kitchen Dashboard</span>
        <div class="ms-auto d-flex align-items-center">
            <span class="navbar-text text-white me-3">
                <?= htmlspecialchars($_SESSION['employee_name']) ?>
            </span>
            <a href="logout.php" class="btn btn-outline-light btn-sm">Logout</a>
        </div>
    </div>
</nav>

<div class="container-fluid mb-4">
    <div class="card shadow-sm">
        <div class="card-header py-2">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center">
                <div class="mb-2 mb-md-0">
                    <strong>Today's Orders Queue</strong>
                    <span class="text-muted small ms-2">(paid_at ascending)</span>
                </div>
                <div></div>
            </div>
            <div class="mt-2">
                <div class="btn-group btn-group-sm" role="group">
                    <button type="button" class="btn btn-outline-secondary status-filter-btn active"
                            data-status="ALL" onclick="setStatusFilter('ALL')">
                        ALL
                    </button>
                    <button type="button" class="btn btn-outline-secondary status-filter-btn"
                            data-status="IN_PROCESS" onclick="setStatusFilter('IN_PROCESS')">
                        IN_PROCESS
                    </button>
                    <button type="button" class="btn btn-outline-secondary status-filter-btn"
                            data-status="READY_FOR_CLAIM" onclick="setStatusFilter('READY_FOR_CLAIM')">
                        READY_FOR_CLAIM
                    </button>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-scroll">
                <table class="table table-sm align-middle">
                    <thead class="table-light">
                    <tr>
                        <th style="width:90px;">Order #</th>
                        <th>Items</th>
                        <th class="text-end" style="width:100px;">Total</th>
                        <th style="width:130px;">Status</th>
                        <th style="width:150px;">Paid At</th>
                        <th style="width:140px;" class="text-end">Action</th>
                    </tr>
                    </thead>
                    <tbody id="ordersTbody">
                    <?php if ($orders): ?>
                        <?php foreach ($orders as $o):
                            $id = (int)$o['id'];
                            $dispNumber = $o['display_number'] !== null
                                ? str_pad((int)$o['display_number'], 4, '0', STR_PAD_LEFT)
                                : str_pad($id, 4, '0', STR_PAD_LEFT);
                            $status = strtoupper(trim($o['status'] ?? 'IN_PROCESS'));
                            $paidAt = $o['paid_at'] ? date('Y-m-d H:i', strtotime($o['paid_at'])) : '';
                            $items  = $itemsByOrder[$id] ?? [];
                            $showButton = ($status === 'IN_PROCESS');
                        ?>
                        <tr data-order-id="<?= $id ?>"
                            data-status="<?= htmlspecialchars($status) ?>">
                            <td class="fw-bold"><?= htmlspecialchars($dispNumber) ?></td>
                            <td>
                                <div class="items-list">
                                    <?php if ($items): ?>
                                        <?php foreach ($items as $it): ?>
                                            <div>
                                                <span class="fw-semibold"><?= (int)$it['qty'] ?>×</span>
                                                <?= htmlspecialchars($it['name'] ?? '') ?>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <span class="text-muted small">No items</span>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td class="text-end">₱<?= number_format((float)$o['total_amount'], 2) ?></td>
                            <td><?= htmlspecialchars($status) ?></td>
                            <td><?= $paidAt ? htmlspecialchars($paidAt) : '<span class="text-muted">-</span>' ?></td>
                            <td class="text-end">
                                <?php if ($showButton): ?>
                                    <button class="btn btn-sm btn-success"
                                            onclick="markReadyForClaim(<?= $id ?>, '<?= $dispNumber ?>')">
                                        Ready for Claim
                                    </button>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted py-3">
                                No orders in queue for today.
                            </td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
let currentStatusFilter = 'ALL';
let ws = null;

document.addEventListener('DOMContentLoaded', () => {
    initWebSocket();
    applyStatusFilter();
});

function setStatusFilter(status) {
    currentStatusFilter = status;
    document.querySelectorAll('.status-filter-btn').forEach(btn => {
        if (btn.dataset.status === status) {
            btn.classList.add('active');
        } else {
            btn.classList.remove('active');
        }
    });
    applyStatusFilter();
}

function applyStatusFilter() {
    document.querySelectorAll('#ordersTbody tr[data-order-id]').forEach(tr => {
        const status = (tr.dataset.status || '').toUpperCase();
        let visible = true;

        if (currentStatusFilter !== 'ALL' && status !== currentStatusFilter) {
            visible = false;
        }

        if (visible) tr.classList.remove('d-none');
        else tr.classList.add('d-none');
    });
}

// WebSocket: listen for order_created / order_updated and reload
function initWebSocket() {
    try {
        const loc = window.location;
        const wsUrl = (loc.protocol === 'https:' ? 'wss://' : 'ws://') + loc.hostname + ':8080';
        ws = new WebSocket(wsUrl);

        ws.onmessage = (event) => {
            try {
                const data = JSON.parse(event.data);
                if (data.type === 'order_created' || data.type === 'order_updated') {
                    reloadOrders();
                }
            } catch (e) {
                console.error('Bad WS message', e);
            }
        };
        ws.onclose = () => {
            setTimeout(initWebSocket, 3000);
        };
    } catch (e) {
        console.error('WS init error', e);
    }
}

// Reload kitchen orders via AJAX
function reloadOrders() {
    fetch('api_get_kitchen_orders_today.php')
        .then(r => r.json())
        .then(res => {
            if (!res.success) return;
            const tbody = document.getElementById('ordersTbody');
            tbody.innerHTML = '';
            const orders = res.orders || [];

            if (!orders.length) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="6" class="text-center text-muted py-3">
                            No orders in queue for today.
                        </td>
                    </tr>`;
                return;
            }

            orders.forEach(o => {
                const status = (o.status || 'IN_PROCESS').toUpperCase();
                const dispNumber = o.display_number_str;
                const paidAt = o.paid_at ? o.paid_at : '<span class="text-muted">-</span>';
                const showButton = (status === 'IN_PROCESS');

                const tr = document.createElement('tr');
                tr.dataset.orderId = o.id;
                tr.dataset.status = status;

                let itemsHtml = '';
                if (o.items && o.items.length) {
                    o.items.forEach(it => {
                        itemsHtml += `
                            <div>
                                <span class="fw-semibold">${it.qty}×</span>
                                ${escapeHtml(it.name)}
                            </div>
                        `;
                    });
                } else {
                    itemsHtml = `<span class="text-muted small">No items</span>`;
                }

                tr.innerHTML = `
                    <td class="fw-bold">${dispNumber}</td>
                    <td><div class="items-list">${itemsHtml}</div></td>
                    <td class="text-end">₱${parseFloat(o.total_amount).toFixed(2)}</td>
                    <td>${status}</td>
                    <td>${paidAt}</td>
                    <td class="text-end">
                        ${showButton
                            ? `<button class="btn btn-sm btn-success"
                                        onclick="markReadyForClaim(${o.id}, '${dispNumber}')">
                                    Ready for Claim
                               </button>`
                            : ''}
                    </td>
                `;
                tbody.appendChild(tr);
            });

            applyStatusFilter();
        })
        .catch(err => console.error('reloadOrders error', err));
}

// Mark order as READY_FOR_CLAIM
function markReadyForClaim(orderId, displayNumber) {
    if (!confirm('Mark order #' + displayNumber + ' as READY_FOR_CLAIM?')) return;

    const fd = new FormData();
    fd.append('order_id', orderId);

    fetch('api_kitchen_ready_for_claim.php', {
        method: 'POST',
        body: fd
    })
        .then(r => r.json())
        .then(res => {
            if (!res.success) {
                alert(res.error || 'Error updating order status.');
                return;
            }
            reloadOrders();
        })
        .catch(() => {
            alert('Network error updating order status.');
        });
}

// Simple HTML escape for injected strings
function escapeHtml(str) {
    if (str == null) return '';
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}
</script>
</body>
</html>
