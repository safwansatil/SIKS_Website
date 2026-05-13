<?php
require_once '../includes/config.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : null;

if (!$id) {
    die("Invalid request");
}

try {
    $stmt = $pdo->prepare("SELECT * FROM library_documents WHERE id = ?");
    $stmt->execute([$id]);
    $doc = $stmt->fetch();

    if ($doc) {
        // Increment download count unless skipped
        if (!isset($_GET['skip_count'])) {
            incrementDownloadCount($id);
        }

        $filePath = '../' . $doc['file_path'];
        
        if (file_exists($filePath)) {
            // Serve the file
            header('Content-Description: File Transfer');
            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment; filename="' . $doc['filename'] . '"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($filePath));
            readfile($filePath);
            exit;
        } else {
            die("File not found on server.");
        }
    } else {
        die("Document not found in database.");
    }
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
