<?php
require_once __DIR__ . '/../includes/config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Ensure database is connected for admin operations
if (!isset($pdo) || !$pdo) {
    // If it's not login.php, show a fatal error or redirect
    $current_page = basename($_SERVER['PHP_SELF']);
    if ($current_page !== 'login.php') {
        die("<h1>Database Error</h1><p>The admin panel requires a database connection. Please check your credentials in <code>includes/db.php</code>.</p><p>Error: " . htmlspecialchars($db_error ?? 'Unknown error') . "</p>");
    }
}

// Redirect to login if not authenticated
if (!isset($_SESSION['admin_id'])) {
    $current_page = basename($_SERVER['PHP_SELF']);
    if ($current_page !== 'login.php') {
        header('Location: login.php');
        exit;
    }
}
