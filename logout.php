<?php
require_once 'config.php';
session_destroy();
header('Location: terminal_login.php');
