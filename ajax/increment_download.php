<?php
require_once '../includes/config.php';

header('Content-Type: application/json');

$id = isset($_GET['id']) ? (int)$_GET['id'] : null;

if (!$id) {
    echo json_encode(['success' => false, 'message' => 'Invalid ID']);
    exit;
}

if (incrementDownloadCount($id)) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to increment count']);
}
