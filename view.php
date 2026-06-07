<?php
/**
 * Full-screen PDF Viewer
 * 
 * Wraps the raw PDF in a full-screen iframe. This ensures the browser tab 
 * displays the custom document title instead of any random internal PDF metadata.
 */

require_once 'includes/config.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$id) {
    header('Location: /library');
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT * FROM library_documents WHERE id = ?");
    $stmt->execute([$id]);
    $doc = $stmt->fetch();
} catch (PDOException $e) {
    header('Location: /library');
    exit;
}

if (!$doc) {
    header('Location: /library');
    exit;
}

$pdfUrl = '/' . ltrim($doc['file_path'], '/');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($doc['title']); ?></title>
    <link rel="icon" type="image/png" href="/assets/images/Logo-green.png?v=2">
    <style>
        body, html {
            margin: 0;
            padding: 0;
            height: 100%;
            overflow: hidden;
            background-color: #525659; /* Standard PDF viewer background */
        }
        iframe {
            width: 100%;
            height: 100%;
            border: none;
        }
    </style>
</head>
<body>
    <!-- #toolbar=1 ensures the PDF controls are visible -->
    <!-- #view=FitH fits the PDF width to the screen -->
    <iframe src="<?php echo htmlspecialchars($pdfUrl); ?>#toolbar=1&view=FitH" 
            title="<?php echo htmlspecialchars($doc['title']); ?>">
    </iframe>
</body>
</html>
