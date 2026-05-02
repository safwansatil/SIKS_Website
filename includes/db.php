<?php
/**
 * Database Connection Configuration
 * Uses utf8mb4 for full Unicode support (Arabic, Bengali, special characters)
 */

$host = 'localhost';
$dbname = 'siks_local';
$username = 'root';
$password = '';

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $username,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
        ]
    );
} catch (PDOException $e) {
    // In production, log this and show a friendly message
    $db_error = $e->getMessage();
    $pdo = null;
}
?>
