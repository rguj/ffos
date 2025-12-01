<?php
// index.php
require_once 'config.php';

// If already logged in as admin, go straight to admin dashboard
if (!empty($_SESSION['admin']) && $_SESSION['admin'] === true) {
    header('Location: admin_dashboard.php');
    exit;
}

// If already logged in as a terminal, go straight to the proper screen
if (isset($_SESSION['terminal_id'], $_SESSION['terminal_type'])) {
    switch ($_SESSION['terminal_type']) {
        case 'CUSTOMER':
            header('Location: customer_kiosk.php');
            exit;
        case 'TELLER':
            header('Location: teller_dashboard.php');
            exit;
        case 'KITCHEN':
            header('Location: kitchen_dashboard.php');
            exit;
        case 'CLAIM':
            header('Location: claim_display.php');
            exit;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>McDo-Style Ordering System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center" style="min-height: 100vh;">
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow-sm">
                <div class="card-body text-center">
                    <h1 class="h3 mb-3">Ordering System</h1>
                    <p class="text-muted mb-4">Select how you want to log in:</p>

                    <a class="btn btn-warning w-100 mb-2" href="terminal_login.php">
                        Terminal Login (Customer / Teller / Kitchen / Claim)
                    </a>

                    <a class="btn btn-danger w-100 mb-2" href="admin_login.php">
                        Admin Login
                    </a>

                    <small class="text-muted d-block mt-2">
                        Use Terminal PIN for kiosks / teller / kitchen / claim. Use admin credentials for system setup.
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
