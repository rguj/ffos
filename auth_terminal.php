<?php
// auth_terminal.php
require_once 'config.php';

if (!isset($_SESSION['terminal_id'], $_SESSION['terminal_type'])) {
    header('Location: terminal_login.php');
    exit;
}
