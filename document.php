<?php
/**
 * Document Viewer / OG Preview Page
 * 
 * Provides Open Graph meta tags for PDF link sharing (WhatsApp, Facebook, etc.)
 * so that the PDF cover thumbnail appears as the preview image.
 * 
 * Usage: /document?id=123  or  /document/123
 */

require_once 'includes/config.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$id) {
    header('Location: /library');
    exit;
}

// Fetch document details
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

// Build URLs
$baseUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'];
$pdfUrl = $baseUrl . '/' . ltrim($doc['file_path'], '/');
$thumbnailUrl = $baseUrl . '/ajax/thumbnail.php?id=' . $doc['id'];
$pageUrl = $baseUrl . '/document?id=' . $doc['id'];

// Set OG variables for header.php
$ogTitle = htmlspecialchars($doc['title']) . ' | ' . SITE_NAME . ' Library';
$ogDescription = 'Download "' . htmlspecialchars($doc['title']) . '" (' . round($doc['file_size'] / 1024 / 1024, 2) . ' MB) from the ' . SITE_NAME . ' Digital Library. Category: ' . htmlspecialchars($doc['category']) . '.';
$ogImage = $thumbnailUrl;
$ogUrl = $pageUrl;
$ogType = 'article';

require_once 'includes/header.php';
?>

<section class="py-24 bg-white min-h-screen">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Back Link -->
        <a href="/library" class="inline-flex items-center space-x-2 text-emerald-950/40 hover:text-emerald-700 font-bold text-sm mb-8 transition-colors">
            <i class="fas fa-arrow-left text-xs"></i>
            <span>Back to Library</span>
        </a>

        <!-- Document Card -->
        <div class="bg-white border border-emerald-950/5 rounded-[32px] overflow-hidden shadow-sm">
            <!-- Cover Image -->
            <div class="bg-emerald-50 flex items-center justify-center p-8 border-b border-emerald-950/5">
                <div class="w-48 md:w-56 rounded-xl overflow-hidden shadow-lg border border-emerald-950/5">
                    <img src="/ajax/thumbnail.php?id=<?php echo $doc['id']; ?>" 
                         alt="<?php echo htmlspecialchars($doc['title']); ?> Cover"
                         class="w-full h-auto"
                         onerror="this.parentElement.innerHTML='<div class=\'flex items-center justify-center h-64 bg-red-50\'><i class=\'fas fa-file-pdf text-5xl text-red-400\'></i></div>';">
                </div>
            </div>

            <!-- Details -->
            <div class="p-8 md:p-10">
                <h1 class="text-2xl md:text-3xl font-display font-bold text-emerald-950 mb-4">
                    <?php echo htmlspecialchars($doc['title']); ?>
                </h1>

                <div class="flex items-center flex-wrap gap-3 mb-6">
                    <span class="px-3 py-1 bg-emerald-100 text-emerald-800 text-[10px] font-bold rounded-full uppercase tracking-wider">
                        <?php echo htmlspecialchars($doc['category']); ?>
                    </span>
                    <span class="text-sm font-medium text-emerald-950/40">
                        <?php echo round($doc['file_size'] / 1024 / 1024, 2); ?> MB
                    </span>
                    <span class="text-emerald-950/20">•</span>
                    <span class="text-sm font-medium text-emerald-950/40">
                        <i class="fas fa-download text-xs mr-1"></i><?php echo number_format($doc['downloads']); ?> downloads
                    </span>
                </div>

                <p class="text-xs text-emerald-950/30 font-bold uppercase tracking-wider mb-6">
                    <?php echo htmlspecialchars($doc['filename']); ?>
                </p>

                <!-- Actions -->
                <div class="flex items-center space-x-4">
                    <a href="/<?php echo ltrim(htmlspecialchars($doc['file_path']), '/'); ?>"
                       target="_blank" download="<?php echo htmlspecialchars($doc['filename']); ?>"
                       onclick="trackDownload(<?php echo $doc['id']; ?>)"
                       class="inline-flex items-center space-x-3 bg-emerald-950 text-white font-bold text-sm px-6 py-3 rounded-2xl hover:bg-emerald-800 transition-colors">
                        <span>Download PDF</span>
                        <i class="fas fa-download"></i>
                    </a>
                    <a href="/<?php echo ltrim(htmlspecialchars($doc['file_path']), '/'); ?>"
                       target="_blank"
                       class="inline-flex items-center space-x-3 border border-emerald-950/10 text-emerald-950 font-bold text-sm px-6 py-3 rounded-2xl hover:bg-emerald-50 transition-colors">
                        <span>View PDF</span>
                        <i class="fas fa-external-link-alt text-xs"></i>
                    </a>
                    <button
                        onclick="copyToClipboard('<?php echo $pageUrl; ?>', 'Link copied!')"
                        class="p-3 border border-emerald-950/10 rounded-2xl text-emerald-950/30 hover:text-emerald-600 hover:border-emerald-200 transition-colors"
                        title="Copy shareable link">
                        <i class="fas fa-share-alt"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    function trackDownload(id) {
        fetch(`ajax/download.php?id=${id}`)
            .catch(err => console.error('Tracking failed', err));
    }
</script>

<?php require_once 'includes/footer.php'; ?>
