<?php
require_once 'includes/config.php';
require_once 'includes/header.php';

// Pagination settings
$limit = isset($_GET['limit']) ? (int) $_GET['limit'] : 10;
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
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
            <p class="text-emerald-950/60 text-lg max-w-2xl mx-auto">Explore our collection of Islamic literature, research papers, educational resources, magazines, and carefully selected public-domain and copyright-free books.</p>
        </div>

        <!-- Controls Bar -->
        <div class="bg-emerald-50/50 border border-emerald-100 rounded-[32px] p-6 mb-8">
            <form action="/library" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4" id="library-filter-form"
                hx-get="/library" hx-target="#main-content" hx-push-url="true" hx-select="#main-content"
                hx-trigger="change delay:300ms from:input, change from:select">

                <!-- Search -->
                <div class="relative">
                    <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-emerald-950/20"></i>
                    <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>"
                        placeholder="Search documents..."
                        class="w-full pl-12 pr-4 py-3 bg-white border border-emerald-100 rounded-2xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/20 transition-all">
                </div>

                <!-- Category -->
                <div>
                    <select name="category"
                        class="w-full px-4 py-3 bg-white border border-emerald-100 rounded-2xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/20 appearance-none cursor-pointer">
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
                    <select name="sort"
                        class="w-full px-4 py-3 bg-white border border-emerald-100 rounded-2xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/20 appearance-none cursor-pointer">
                        <option value="name_asc" <?php echo $sort == 'name_asc' ? 'selected' : ''; ?>>Name (A-Z)</option>
                        <option value="name_desc" <?php echo $sort == 'name_desc' ? 'selected' : ''; ?>>Name (Z-A)
                        </option>
                        <option value="size_asc" <?php echo $sort == 'size_asc' ? 'selected' : ''; ?>>Size (Smallest)
                        </option>
                        <option value="size_desc" <?php echo $sort == 'size_desc' ? 'selected' : ''; ?>>Size (Largest)
                        </option>
                        <option value="newest" <?php echo $sort == 'newest' ? 'selected' : ''; ?>>Newest First</option>
                    </select>
                </div>

                <!-- Limit -->
                <div>
                    <select name="limit"
                        class="w-full px-4 py-3 bg-white border border-emerald-100 rounded-2xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/20 appearance-none cursor-pointer">
                        <option value="10" <?php echo $limit == 10 ? 'selected' : ''; ?>>10 per page</option>
                        <option value="25" <?php echo $limit == 25 ? 'selected' : ''; ?>>25 per page</option>
                        <option value="50" <?php echo $limit == 50 ? 'selected' : ''; ?>>50 per page</option>
                    </select>
                </div>

                <input type="hidden" name="page" value="1">
            </form>
        </div>

        <!-- Mobile Card View (visible on small screens only) -->
        <div class="md:hidden space-y-3">
            <?php if (empty($documents)): ?>
                <div class="bg-white border border-emerald-950/5 rounded-3xl p-8 text-center">
                    <i class="fas fa-folder-open text-5xl text-emerald-950/10 mb-4"></i>
                    <p class="text-emerald-950/40 font-medium">No documents found matching your criteria.</p>
                </div>
            <?php else: ?>
                <?php foreach ($documents as $doc): ?>
                    <div class="bg-white border border-emerald-950/5 rounded-2xl p-4 hover:border-emerald-200 transition-all">
                        <!-- Top row: cover + title -->
                        <div class="flex items-start space-x-3 mb-3">
                            <div class="w-12 h-16 rounded-lg bg-emerald-50 flex items-center justify-center flex-none overflow-hidden border border-emerald-950/5">
                                <img src="/ajax/thumbnail.php?id=<?php echo $doc['id']; ?>"
                                     alt="Cover" loading="lazy"
                                     class="w-full h-full object-cover"
                                     onerror="this.parentElement.innerHTML='<i class=\'fas fa-file-pdf text-red-500\'></i>';this.parentElement.classList.add('bg-red-50');this.parentElement.classList.remove('bg-emerald-50');">
                            </div>
                            <div class="min-w-0 flex-1">
                                <a href="/<?php echo ltrim(htmlspecialchars($doc['file_path']), '/'); ?>"
                                    target="_blank"
                                    class="text-emerald-950 font-bold text-sm hover:text-emerald-700 transition-colors hover:underline line-clamp-2 block">
                                    <?php echo htmlspecialchars($doc['title']); ?>
                                </a>
                                <p class="text-[10px] text-emerald-950/30 font-bold uppercase tracking-wider mt-0.5 truncate">
                                    <?php echo htmlspecialchars($doc['filename']); ?>
                                </p>
                            </div>
                        </div>
                        <!-- Meta row: category, size, downloads -->
                        <div class="flex items-center flex-wrap gap-2 mb-3 pl-[60px]">
                            <span class="px-2.5 py-0.5 bg-emerald-100 text-emerald-800 text-[10px] font-bold rounded-full uppercase tracking-wider">
                                <?php echo htmlspecialchars($doc['category']); ?>
                            </span>
                            <span class="text-xs font-medium text-emerald-950/40">
                                <?php echo round($doc['file_size'] / 1024 / 1024, 2); ?> MB
                            </span>
                            <span class="text-xs text-emerald-950/30">•</span>
                            <span class="text-xs font-medium text-emerald-950/40">
                                <i class="fas fa-download text-[10px] mr-1"></i><?php echo number_format($doc['downloads']); ?>
                            </span>
                        </div>
                        <!-- Action row -->
                        <div class="flex items-center justify-end space-x-3 pl-[60px]">
                            <button
                                onclick="copyToClipboard('https://<?php echo $_SERVER['HTTP_HOST'] . '/document?id=' . $doc['id']; ?>', 'Link copied!')"
                                class="p-2 text-emerald-950/20 hover:text-emerald-600 transition-colors"
                                title="Share Document Link">
                                <i class="fas fa-share-alt"></i>
                            </button>
                            <a href="/<?php echo ltrim(htmlspecialchars($doc['file_path']), '/'); ?>"
                                target="_blank" download="<?php echo htmlspecialchars($doc['filename']); ?>"
                                onclick="trackDownload(<?php echo $doc['id']; ?>)"
                                class="inline-flex items-center space-x-2 bg-emerald-950 text-white text-xs font-bold px-4 py-2 rounded-xl hover:bg-emerald-800 transition-colors">
                                <span>Download</span>
                                <i class="fas fa-download text-[10px]"></i>
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- Desktop Table View (hidden on small screens) -->
        <div class="hidden md:block bg-white border border-emerald-950/5 rounded-[40px] overflow-hidden shadow-sm">
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
                                        <p class="text-emerald-950/40 font-medium">No documents found matching your
                                            criteria.</p>
                                    </div>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($documents as $doc): ?>
                                <tr class="hover:bg-emerald-50/50 transition-colors group">
                                    <td class="px-8 py-6">
                                        <div class="flex items-center space-x-4">
                                            <div
                                                class="w-10 h-14 rounded-lg bg-emerald-50 flex items-center justify-center flex-none overflow-hidden border border-emerald-950/5">
                                                <img src="/ajax/thumbnail.php?id=<?php echo $doc['id']; ?>"
                                                     alt="Cover" loading="lazy"
                                                     class="w-full h-full object-cover"
                                                     onerror="this.parentElement.innerHTML='<i class=\'fas fa-file-pdf text-red-500\'></i>';this.parentElement.classList.add('bg-red-50');this.parentElement.classList.remove('bg-emerald-50');">
                                            </div>
                                            <div>
                                                <a href="/<?php echo ltrim(htmlspecialchars($doc['file_path']), '/'); ?>"
                                                    target="_blank"
                                                    class="text-emerald-950 font-bold group-hover:text-emerald-700 transition-colors hover:underline">
                                                    <?php echo htmlspecialchars($doc['title']); ?>
                                                </a>
                                                <p
                                                    class="text-[10px] text-emerald-950/30 font-bold uppercase tracking-wider mt-0.5">
                                                    <?php echo htmlspecialchars($doc['filename']); ?></p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-8 py-6">
                                        <span
                                            class="px-3 py-1 bg-emerald-100 text-emerald-800 text-[10px] font-bold rounded-full uppercase tracking-wider">
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
                                        <div class="flex items-center justify-end space-x-4">
                                            <button
                                                onclick="copyToClipboard('https://<?php echo $_SERVER['HTTP_HOST'] . '/document?id=' . $doc['id']; ?>', 'Link copied!')"
                                                class="p-2 text-emerald-950/20 hover:text-emerald-600 transition-colors"
                                                title="Share Document Link">
                                                <i class="fas fa-share-alt"></i>
                                            </button>
                                            <a href="/<?php echo ltrim(htmlspecialchars($doc['file_path']), '/'); ?>"
                                                target="_blank" download="<?php echo htmlspecialchars($doc['filename']); ?>"
                                                onclick="trackDownload(<?php echo $doc['id']; ?>)"
                                                class="inline-flex items-center space-x-2 text-emerald-600 font-bold text-sm hover:text-emerald-800 transition-colors">
                                                <span>Download</span>
                                                <i class="fas fa-download text-xs"></i>
                                            </a>
                                        </div>
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
                    <a href="/library?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&category=<?php echo urlencode($category); ?>&sort=<?php echo $sort; ?>&limit=<?php echo $limit; ?>"
                        hx-get="/library?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&category=<?php echo urlencode($category); ?>&sort=<?php echo $sort; ?>&limit=<?php echo $limit; ?>"
                        hx-target="#main-content" hx-push-url="true" hx-select="#main-content"
                        class="w-10 h-10 rounded-xl flex items-center justify-center font-bold text-sm transition-all <?php echo $i == $page ? 'bg-emerald-950 text-white' : 'text-emerald-950/40 hover:bg-emerald-50'; ?>">
                        <?php echo $i; ?>
                    </a>
                <?php endfor; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<script>
    function trackDownload(id) {
        fetch(`ajax/download.php?id=${id}`)
            .catch(err => console.error('Tracking failed', err));
    }
</script>

<?php require_once 'includes/footer.php'; ?>