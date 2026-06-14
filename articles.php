<?php
require_once 'includes/config.php';

// SEO: Unique title and description for this page
$ogTitle = 'Society Articles | ' . SITE_NAME;
$ogDescription = 'Read insights, reflections, and knowledge shared by the SIKS community at the Islamic University of Technology.';

// Check if this is an HTMX request
$isHtmx = isset($_SERVER['HTTP_HX_REQUEST']);

if (!$isHtmx) {
    require_once 'includes/header.php';
} else {
    // For HTMX requests, we wrap the content in the same main tag structure
    // so that hx-select="#main-content" still works on the client side.
    echo '<main id="main-content" class="animate-page">';
}

// Pagination & Sorting Configuration
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 6;
$offset = ($page - 1) * $limit;
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'newest';

// Build SQL based on sorting
$orderBy = "last_edited DESC";
if ($sort === 'oldest') $orderBy = "last_edited ASC";
if ($sort === 'title') $orderBy = "title ASC";
if ($sort === 'reading') $orderBy = "reading_time ASC";

// Fetch articles with pagination
$articles = [];
$totalArticles = 0;
if ($pdo) {
    try {
        // Get total count
        $totalArticles = $pdo->query("SELECT COUNT(*) FROM articles")->fetchColumn();
        $totalPages = ceil($totalArticles / $limit);

        $stmt = $pdo->prepare("SELECT * FROM articles ORDER BY $orderBy LIMIT ? OFFSET ?");
        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->bindValue(2, $offset, PDO::PARAM_INT);
        $stmt->execute();
        $articles = $stmt->fetchAll();
    } catch (PDOException $e) {
        $articles = [];
    }
}
?>

<section class="py-24">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h1 class="text-4xl font-display font-bold text-emerald-950 mb-4 tracking-tight">Society Articles</h1>
            <p class="text-emerald-950/40 max-w-2xl mx-auto font-medium italic">Insights, reflections, and knowledge shared by our community.</p>
        </div>

        <!-- Search & Sort Bar -->
        <div class="max-w-4xl mx-auto mb-12 flex flex-col md:flex-row gap-4">
            <div class="relative flex-1">
                <i class="fas fa-search absolute left-5 top-1/2 -translate-y-1/2 text-emerald-950/30"></i>
                <input type="text" id="article-search" 
                       placeholder="Search articles..." 
                       class="w-full pl-12 pr-12 py-4 rounded-2xl border border-emerald-950/10 bg-white text-emerald-950 font-medium placeholder-emerald-950/30 focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/10 transition-all"
                       autocomplete="off">
                <button id="search-clear" class="absolute right-4 top-1/2 -translate-y-1/2 text-emerald-950/20 hover:text-emerald-950/60 transition-colors hidden" onclick="clearSearch()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <div class="flex-none">
                <select onchange="location.href='/articles?sort=' + this.value" 
                        class="h-full px-6 py-4 rounded-2xl border border-emerald-950/10 bg-white text-emerald-950 font-bold focus:outline-none focus:border-emerald-500 transition-all appearance-none cursor-pointer">
                    <option value="newest" <?php echo $sort === 'newest' ? 'selected' : ''; ?>>Newest First</option>
                    <option value="oldest" <?php echo $sort === 'oldest' ? 'selected' : ''; ?>>Oldest First</option>
                    <option value="title" <?php echo $sort === 'title' ? 'selected' : ''; ?>>Alphabetical</option>
                    <option value="reading" <?php echo $sort === 'reading' ? 'selected' : ''; ?>>Shortest Read</option>
                </select>
            </div>
        </div>
        <p id="search-count" class="text-center text-emerald-950/30 text-xs font-bold uppercase tracking-widest -mt-6 mb-12 hidden"></p>

        <div id="articles-container">
            <?php if ($articles): ?>
                <div class="space-y-8">
                    <?php foreach ($articles as $article): 
                        $readingTime = $article['reading_time'] ?? calculateReadingTime($article['description'] ?? '');
                    ?>
                        <a href="/article/<?php echo $article['id']; ?>/<?php echo ($article['slug'] ?: generateSlug($article['title'])); ?>" 
                           hx-get="/article/<?php echo $article['id']; ?>/<?php echo ($article['slug'] ?: generateSlug($article['title'])); ?>"
                           hx-target="#main-content" hx-push-url="true" hx-select="#main-content"
                           class="article-card block card-professional group">
                            <div class="flex flex-col md:flex-row">
                                <!-- Cover Image -->
                                <?php if (!empty($article['cover_image'])): ?>
                                    <div class="md:w-80 flex-none">
                                        <div class="aspect-[16/10] md:aspect-auto md:h-full overflow-hidden rounded-t-[24px] md:rounded-l-[24px] md:rounded-tr-none">
                                            <img src="/<?php echo ltrim(htmlspecialchars($article['cover_image']), '/'); ?>" 
                                                 alt="<?php echo htmlspecialchars($article['title']); ?>"
                                                 class="article-image w-full h-full object-cover">
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <!-- Content -->
                                <div class="flex-1 p-8 md:p-10 flex flex-col justify-center">
                                    <!-- Meta -->
                                    <div class="flex items-center space-x-4 mb-4 text-emerald-950/40 text-[11px] font-bold uppercase tracking-widest">
                                        <span><?php echo date('F d, Y', strtotime($article['last_edited'])); ?></span>
                                        <span class="w-1 h-1 rounded-full bg-emerald-950/20"></span>
                                        <span><?php echo $readingTime; ?> min read</span>
                                    </div>

                                    <!-- Title -->
                                    <h3 class="text-2xl md:text-3xl font-display font-bold text-emerald-950 mb-4 leading-tight group-hover:text-emerald-700 transition-colors">
                                        <?php echo htmlspecialchars($article['title']); ?>
                                    </h3>

                                    <!-- Excerpt -->
                                    <p class="text-emerald-950/60 text-sm leading-relaxed line-clamp-3 mb-6 font-medium">
                                        <?php echo htmlspecialchars(mb_substr(strip_tags($article['description']), 0, 300)); ?>...
                                    </p>

                                    <!-- Author -->
                                        <div class="flex items-center justify-between mt-auto pt-6 border-t border-emerald-950/5">
                                            <span class="text-sm font-bold text-emerald-950"><?php echo htmlspecialchars($article['writer']); ?></span>
                                            <span class="text-emerald-600 font-bold text-xs group-hover:translate-x-1 transition-transform flex items-center">
                                                Read Article <i class="fas fa-arrow-right ml-2"></i>
                                            </span>
                                        </div>
                                </div>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>

                <!-- Pagination -->
                <?php if ($totalPages > 1): ?>
                    <div id="pagination-container" class="mt-16 flex justify-center items-center space-x-2">
                        <?php if ($page > 1): ?>
                            <a href="/articles?page=<?php echo $page - 1; ?>&sort=<?php echo $sort; ?>" 
                               hx-get="/articles?page=<?php echo $page - 1; ?>&sort=<?php echo $sort; ?>" 
                               hx-target="#main-content" 
                               hx-select="#main-content"
                               hx-push-url="true"
                               class="w-12 h-12 rounded-xl border border-emerald-950/10 flex items-center justify-center text-emerald-950 hover:bg-emerald-50 transition-colors">
                                <i class="fas fa-chevron-left"></i>
                            </a>
                        <?php endif; ?>

                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <a href="/articles?page=<?php echo $i; ?>&sort=<?php echo $sort; ?>" 
                               hx-get="/articles?page=<?php echo $i; ?>&sort=<?php echo $sort; ?>" 
                               hx-target="#main-content" 
                               hx-select="#main-content"
                               hx-push-url="true"
                               class="w-12 h-12 rounded-xl border <?php echo $i === $page ? 'bg-emerald-950 text-white border-emerald-950' : 'border-emerald-950/10 text-emerald-950 hover:bg-emerald-50'; ?> flex items-center justify-center font-bold transition-colors">
                                <?php echo $i; ?>
                            </a>
                        <?php endfor; ?>

                        <?php if ($page < $totalPages): ?>
                            <a href="/articles?page=<?php echo $page + 1; ?>&sort=<?php echo $sort; ?>" 
                               hx-get="/articles?page=<?php echo $page + 1; ?>&sort=<?php echo $sort; ?>" 
                               hx-target="#main-content" 
                               hx-select="#main-content"
                               hx-push-url="true"
                               class="w-12 h-12 rounded-xl border border-emerald-950/10 flex items-center justify-center text-emerald-950 hover:bg-emerald-50 transition-colors">
                                <i class="fas fa-chevron-right"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <div class="col-span-full py-20 text-center border border-dashed border-emerald-950/10 rounded-[40px]">
                    <i class="fas fa-pen-fancy text-4xl text-emerald-950/10 mb-4"></i>
                    <p class="text-emerald-950/30 italic">No articles published yet.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<script>
    (function() {
        const searchInput = document.getElementById('article-search');
        const searchClear = document.getElementById('search-clear');
        const searchCount = document.getElementById('search-count');
        const articlesContainer = document.getElementById('articles-container');
        let debounceTimer;

        if (searchInput) {
            searchInput.addEventListener('input', function() {
                const query = this.value.trim();
                
                searchClear.classList.toggle('hidden', query.length === 0);

                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(() => {
                    if (query.length > 0) {
                        performSearch(query);
                    } else {
                        resetToPaginated();
                    }
                }, 300);
            });
        }

        async function performSearch(query) {
            try {
                const response = await fetch(`ajax/search_articles.php?q=${encodeURIComponent(query)}`);
                const data = await response.json();

                if (data.success) {
                    renderResults(data.results);
                    searchCount.classList.remove('hidden');
                    searchCount.textContent = data.count + ' article' + (data.count !== 1 ? 's' : '') + ' found across all pages';
                }
            } catch (error) {
                console.error('Search failed:', error);
            }
        }

        function renderResults(articles) {
            if (articles.length === 0) {
                articlesContainer.innerHTML = `
                    <div class="col-span-full py-20 text-center border border-dashed border-emerald-950/10 rounded-[40px]">
                        <i class="fas fa-search text-4xl text-emerald-950/10 mb-4"></i>
                        <p class="text-emerald-950/30 italic">No articles match your search.</p>
                    </div>
                `;
                return;
            }

            let html = '<div class="space-y-8">';
            articles.forEach(article => {
                html += `
                    <a href="/article/${article.id}/${article.slug || 'article'}" 
                       hx-get="/article/${article.id}/${article.slug || 'article'}" hx-target="#main-content" hx-push-url="true" hx-select="#main-content"
                       class="article-card block card-professional group">
                        <div class="flex flex-col md:flex-row">
                            ${article.cover_image ? `
                                <div class="md:w-80 flex-none">
                                    <div class="aspect-[16/10] md:aspect-auto md:h-full overflow-hidden rounded-t-[24px] md:rounded-l-[24px] md:rounded-tr-none">
                                        <img src="/${article.cover_image.replace(/^\//, '')}" 
                                             alt="${article.title}"
                                             class="article-image w-full h-full object-cover">
                                    </div>
                                </div>
                            ` : ''}

                            <div class="flex-1 p-8 md:p-10 flex flex-col justify-center">
                                <div class="flex items-center space-x-4 mb-4 text-emerald-950/40 text-[11px] font-bold uppercase tracking-widest">
                                    <span>${article.formatted_date}</span>
                                    <span class="w-1 h-1 rounded-full bg-emerald-950/20"></span>
                                    <span>${article.reading_time} min read</span>
                                </div>

                                <h3 class="text-2xl md:text-3xl font-display font-bold text-emerald-950 mb-4 leading-tight group-hover:text-emerald-700 transition-colors">
                                    ${article.title}
                                </h3>

                                <p class="text-emerald-950/60 text-sm leading-relaxed line-clamp-3 mb-6 font-medium">
                                    ${article.excerpt}
                                </p>

                                <div class="flex items-center justify-between mt-auto pt-6 border-t border-emerald-950/5">
                                    <span class="text-sm font-bold text-emerald-950">${article.writer}</span>
                                    <span class="text-emerald-600 font-bold text-xs group-hover:translate-x-1 transition-transform flex items-center">
                                        Read Article <i class="fas fa-arrow-right ml-2"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </a>
                `;
            });
            html += '</div>';
            articlesContainer.innerHTML = html;
            // Re-process HTMX for the new elements
            htmx.process(articlesContainer);
        }

        function resetToPaginated() {
            // Using HTMX to reload the component or just reload page if not easy
            // But since we want smooth experience, let's just fetch current page again
            const url = new URL(window.location.href);
            fetch(url.href)
                .then(response => response.text())
                .then(html => {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    const newContent = doc.getElementById('articles-container').innerHTML;
                    articlesContainer.innerHTML = newContent;
                    searchCount.classList.add('hidden');
                });
        }

        window.clearSearch = function() {
            searchInput.value = '';
            searchClear.classList.add('hidden');
            resetToPaginated();
            searchInput.focus();
        };
    })();
</script>

<?php
if (!$isHtmx) {
    require_once 'includes/footer.php';
} else {
    echo '</main>';
}
?>
