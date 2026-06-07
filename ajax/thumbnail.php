<?php
/**
 * PDF Thumbnail Generator
 * 
 * Generates and caches thumbnails from PDF first pages using ImageMagick.
 * Usage: /ajax/thumbnail.php?id=123
 * 
 * Falls back gracefully if ImageMagick/Ghostscript are not available.
 */

require_once dirname(__DIR__) . '/includes/config.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$id) {
    http_response_code(400);
    exit;
}

// Cache directory
$cacheDir = dirname(__DIR__) . '/uploads/thumbnails/';
if (!is_dir($cacheDir)) {
    mkdir($cacheDir, 0755, true);
}

$cachePath = $cacheDir . $id . '.jpg';

// Serve from cache if it exists and is less than 30 days old
if (file_exists($cachePath) && (time() - filemtime($cachePath)) < 2592000) {
    header('Content-Type: image/jpeg');
    header('Cache-Control: public, max-age=2592000'); // 30 days browser cache
    header('Content-Length: ' . filesize($cachePath));
    readfile($cachePath);
    exit;
}

// Look up the document's file path from DB
if (!$pdo) {
    http_response_code(500);
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT file_path FROM library_documents WHERE id = ?");
    $stmt->execute([$id]);
    $doc = $stmt->fetch();
} catch (PDOException $e) {
    http_response_code(500);
    exit;
}

if (!$doc) {
    http_response_code(404);
    exit;
}

// Resolve the absolute path to the PDF
$pdfPath = dirname(__DIR__) . '/' . ltrim($doc['file_path'], '/');

if (!file_exists($pdfPath)) {
    http_response_code(404);
    exit;
}

// Check if ImageMagick is available
if (!extension_loaded('imagick')) {
    http_response_code(501); // Not Implemented
    exit;
}

// Generate thumbnail from first page of PDF
try {
    $imagick = new Imagick();
    $imagick->setResolution(150, 150); // DPI - good balance of quality vs size
    $imagick->readImage($pdfPath . '[0]'); // [0] = first page only
    $imagick->setImageFormat('jpeg');
    $imagick->setImageCompressionQuality(85);
    
    // Resize to a reasonable thumbnail size (maintain aspect ratio)
    $imagick->thumbnailImage(400, 0); // 400px wide, auto height
    
    // Flatten (remove alpha/transparency from PDF)
    $imagick->setImageBackgroundColor('white');
    $imagick = $imagick->flattenImages();
    
    // Save to cache
    $imagick->writeImage($cachePath);
    
    // Serve the image
    header('Content-Type: image/jpeg');
    header('Cache-Control: public, max-age=2592000');
    header('Content-Length: ' . filesize($cachePath));
    readfile($cachePath);
    
    $imagick->clear();
    $imagick->destroy();
} catch (Exception $e) {
    // ImageMagick failed (likely Ghostscript not installed)
    http_response_code(501);
    exit;
}
