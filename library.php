<?php
require_once 'includes/config.php';
require_once 'includes/header.php';

// Pagination settings
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Search and Filter
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$category = isset($_GET['category']) ? trim($_GET['category']) : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'name_asc';

$documents = getLibraryDocuments($category, $search, $sort, $limit, $offset);
$totalDocs = getLibraryCount($category, $search);
$totalPages = ceil($totalDocs / $limit);

// Get unique categories for filter
$stmt = $pdo->query("SELECT DISTINCT category FROM library_documents ORDER BY category ASC");
$categories = $stmt->fetchAll(PDO::FETCH_COLUMN);
?>

<section class="py-24 bg-white min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-16 text-center">
            <h1 class="text-4xl md:text-6xl font-display font-bold text-emerald-950 mb-6 tracking-tight">Digital Library</h1>
            <p class="text-emerald-950/60 text-lg max-w-2xl mx-auto">Explore our collection of Islamic literature, research papers, and educational resources.</p>
        </div>

        <!-- Controls Bar -->
        <div class="bg-emerald-50/50 border border-emerald-100 rounded-[32px] p-6 mb-8">
            <form action="library.php" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4" id="library-filter-form"
                  hx-get="library.php" hx-target="#main-content" hx-push-url="true" hx-select="#main-content" hx-trigger="change delay:300ms from:input, change from:select">
                
                <!-- Search -->
                <div class="relative">
                    <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-emerald-950/20"></i>
                    <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" 
                           placeholder="Search documents..." 
                           class="w-full pl-12 pr-4 py-3 bg-white border border-emerald-100 rounded-2xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/20 transition-all">
                </div>

                <!-- Category -->
                <div>
                    <select name="category" class="w-full px-4 py-3 bg-white border border-emerald-100 rounded-2xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/20 appearance-none cursor-pointer">
                        <option value="">All Categories</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?php echo htmlspecialchars($cat); ?>" <?php echo $category == $cat ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($cat); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Sort -->
                <div>
                    <select name="sort" class="w-full px-4 py-3 bg-white border border-emerald-100 rounded-2xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/20 appearance-none cursor-pointer">
                        <option value="name_asc" <?php echo $sort == 'name_asc' ? 'selected' : ''; ?>>Name (A-Z)</option>
                        <option value="name_desc" <?php echo $sort == 'name_desc' ? 'selected' : ''; ?>>Name (Z-A)</option>
                        <option value="size_asc" <?php echo $sort == 'size_asc' ? 'selected' : ''; ?>>Size (Smallest)</option>
                        <option value="size_desc" <?php echo $sort == 'size_desc' ? 'selected' : ''; ?>>Size (Largest)</option>
                        <option value="newest" <?php echo $sort == 'newest' ? 'selected' : ''; ?>>Newest First</option>
                    </select>
                </div>

                <!-- Limit -->
                <div>
                    <select name="limit" class="w-full px-4 py-3 bg-white border border-emerald-100 rounded-2xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/20 appearance-none cursor-pointer">
                        <option value="10" <?php echo $limit == 10 ? 'selected' : ''; ?>>10 per page</option>
                        <option value="25" <?php echo $limit == 25 ? 'selected' : ''; ?>>25 per page</option>
                        <option value="50" <?php echo $limit == 50 ? 'selected' : ''; ?>>50 per page</option>
                    </select>
                </div>
                
                <input type="hidden" name="page" value="1">
            </form>
        </div>

        <!-- Table Container -->
        <div class="bg-white border border-emerald-950/5 rounded-[40px] overflow-hidden shadow-sm">
            <div class="overflow-x-auto no-scrollbar">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-emerald-950 text-white">
                            <th class="px-8 py-5 text-xs font-bold uppercase tracking-widest">Document Name</th>
                            <th class="px-8 py-5 text-xs font-bold uppercase tracking-widest">Category</th>
                            <th class="px-8 py-5 text-xs font-bold uppercase tracking-widest">Size</th>
                            <th class="px-8 py-5 text-xs font-bold uppercase tracking-widest text-center">Downloads</th>
                            <th class="px-8 py-5 text-xs font-bold uppercase tracking-widest text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-emerald-950/5">
                        <?php if (empty($documents)): ?>
                            <tr>
                                <td colspan="5" class="px-8 py-20 text-center">
                                    <div class="flex flex-col items-center">
                                        <i class="fas fa-folder-open text-5xl text-emerald-950/10 mb-4"></i>
                                        <p class="text-emerald-950/40 font-medium">No documents found matching your criteria.</p>
                                    </div>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($documents as $doc): ?>
                                <tr class="hover:bg-emerald-50/50 transition-colors group">
                                    <td class="px-8 py-6">
                                        <div class="flex items-center space-x-4">
                                            <div class="w-10 h-10 rounded-xl bg-red-50 text-red-600 flex items-center justify-center flex-none">
                                                <i class="fas fa-file-pdf"></i>
                                            </div>
                                            <div>
                                                <p class="text-emerald-950 font-bold group-hover:text-emerald-700 transition-colors"><?php echo htmlspecialchars($doc['title']); ?></p>
                                                <p class="text-[10px] text-emerald-950/30 font-bold uppercase tracking-wider mt-0.5"><?php echo htmlspecialchars($doc['filename']); ?></p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-8 py-6">
                                        <span class="px-3 py-1 bg-emerald-100 text-emerald-800 text-[10px] font-bold rounded-full uppercase tracking-wider">
                                            <?php echo htmlspecialchars($doc['category']); ?>
                                        </span>
                                    </td>
                                    <td class="px-8 py-6 text-sm font-medium text-emerald-950/60">
                                        <?php echo round($doc['file_size'] / 1024 / 1024, 2); ?> MB
                                    </td>
                                    <td class="px-8 py-6 text-sm font-bold text-emerald-950/40 text-center">
                                        <?php echo number_format($doc['downloads']); ?>
                                    </td>
                                    <td class="px-8 py-6 text-right">
                                        <a href="javascript:void(0)" 
                                           onclick="trackAndDownload(<?php echo $doc['id']; ?>, 'ajax/download.php?id=<?php echo $doc['id']; ?>&skip_count=1')"
                                           class="inline-flex items-center space-x-2 text-emerald-600 font-bold text-sm hover:text-emerald-800 transition-colors">
                                            <span>Download</span>
                                            <i class="fas fa-download text-xs"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
            <div class="mt-12 flex justify-center items-center space-x-2">
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <a href="library.php?page=<?php echo $i; ?>&limit=<?php echo $limit; ?>&search=<?php echo urlencode($search); ?>&category=<?php echo urlencode($category); ?>&sort=<?php echo $sort; ?>"
                       hx-get="library.php?page=<?php echo $i; ?>&limit=<?php echo $limit; ?>&search=<?php echo urlencode($search); ?>&category=<?php echo urlencode($category); ?>&sort=<?php echo $sort; ?>"
                       hx-target="#main-content" hx-push-url="true" hx-select="#main-content"
                       class="w-10 h-10 flex items-center justify-center rounded-xl font-bold text-sm transition-all <?php echo $page == $i ? 'bg-emerald-950 text-white shadow-lg' : 'bg-white text-emerald-950/40 hover:bg-emerald-50'; ?>">
                        <?php echo $i; ?>
                    </a>
                <?php endfor; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<script>
    async function trackAndDownload(id, downloadUrl) {
        try {
            // Increment count via fetch
            await fetch(`ajax/increment_download.php?id=${id}`);
            
            // Trigger actual download
            const link = document.createElement('a');
            link.href = downloadUrl;
            link.target = '_blank';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            
            // Optionally update the UI counter (real-time feel)
            const countCell = event.target.closest('tr').querySelector('td:nth-child(4)');
            if (countCell) {
                let current = parseInt(countCell.textContent.replace(/,/g, ''));
                countCell.textContent = (current + 1).toLocaleString();
            }
        } catch (error) {
            console.error('Download tracking failed:', error);
            // Fallback: still try to download if tracking fails
            window.open(downloadUrl, '_blank');
        }
    }
</script>

<?php require_once 'includes/footer.php'; ?>
