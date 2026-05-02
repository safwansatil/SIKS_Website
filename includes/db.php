<?php
/**
 * Database Connection Configuration
 * 
 * Update these values with your actual CWP database credentials.
 */

$host = 'localhost';
$dbname = 'iutsiks_dynamic';
$username = 'iutsiks_admin';
$password = '~cc++404';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // In production, you might want to log this and show a friendly message
    // die("Connection failed: " . $e->getMessage());
    
    // For now, we'll just set it to null so other pages can handle it gracefully
    $pdo = null;
}
?>
