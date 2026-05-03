<?php
require_once 'includes/config.php';
require_once 'includes/header.php';

// Fetch articles
$articles = [];
if ($pdo) {
    try {
        $stmt = $pdo->query("SELECT * FROM articles ORDER BY last_edited DESC");
        $articles = $stmt->fetchAll();
    } catch (PDOException $e) {
        $articles = [];
    }
}
?>

<section class="py-24">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-4xl font-display font-bold text-emerald-950 mb-4 tracking-tight">Society Articles</h2>
            <p class="text-emerald-950/40 max-w-2xl mx-auto font-medium italic">Insights, reflections, and knowledge shared by our community.</p>
        </div>

        <!-- Search Bar -->
        <div class="max-w-xl mx-auto mb-12">
            <div class="relative">
                <i class="fas fa-search absolute left-5 top-1/2 -translate-y-1/2 text-emerald-950/30"></i>
                <input type="text" id="article-search" 
                       placeholder="Search articles by title or content..." 
                       class="w-full pl-12 pr-12 py-4 rounded-2xl border border-emerald-950/10 bg-white text-emerald-950 font-medium placeholder-emerald-950/30 focus:outline-none focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/10 transition-all"
                       autocomplete="off">
                <button id="search-clear" class="absolute right-4 top-1/2 -translate-y-1/2 text-emerald-950/20 hover:text-emerald-950/60 transition-colors hidden" onclick="clearSearch()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <p id="search-count" class="text-center text-emerald-950/30 text-xs font-bold uppercase tracking-widest mt-3 hidden"></p>
        </div>

        <?php if ($articles): ?>
            <div class="space-y-8">
                <?php foreach ($articles as $article): 
                    $readingTime = $article['reading_time'] ?? calculateReadingTime($article['description'] ?? '');
                ?>
                    <a href="article.php?id=<?php echo $article['id']; ?>" class="article-card block card-professional group">
                        <div class="flex flex-col md:flex-row">
                            <!-- Cover Image -->
                            <?php if (!empty($article['cover_image'])): ?>
                                <div class="md:w-80 flex-none">
                                    <div class="aspect-[16/10] md:aspect-auto md:h-full overflow-hidden rounded-t-[24px] md:rounded-l-[24px] md:rounded-tr-none">
                                        <img src="<?php echo htmlspecialchars($article['cover_image']); ?>" 
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
        <?php else: ?>
            <div class="col-span-full py-20 text-center border border-dashed border-emerald-950/10 rounded-[40px]">
                <i class="fas fa-pen-fancy text-4xl text-emerald-950/10 mb-4"></i>
                <p class="text-emerald-950/30 italic">No articles published yet.</p>
            </div>
        <?php endif; ?>
    </div>
</section>

<script>
    const searchInput = document.getElementById('article-search');
    const searchClear = document.getElementById('search-clear');
    const searchCount = document.getElementById('search-count');

    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const query = this.value.toLowerCase().trim();
            const cards = document.querySelectorAll('.article-card');
            let visible = 0;

            searchClear.classList.toggle('hidden', query.length === 0);

            cards.forEach(card => {
                const title = card.querySelector('h3')?.textContent.toLowerCase() || '';
                const excerpt = card.querySelector('p.line-clamp-3')?.textContent.toLowerCase() || '';
                const writer = card.querySelector('.text-sm.font-bold')?.textContent.toLowerCase() || '';
                const match = title.includes(query) || excerpt.includes(query) || writer.includes(query);
                card.style.display = match ? '' : 'none';
                if (match) visible++;
            });

            if (query.length > 0) {
                searchCount.classList.remove('hidden');
                searchCount.textContent = visible + ' article' + (visible !== 1 ? 's' : '') + ' found';
            } else {
                searchCount.classList.add('hidden');
            }
        });
    }

    function clearSearch() {
        searchInput.value = '';
        searchInput.dispatchEvent(new Event('input'));
        searchInput.focus();
    }
</script>

<?php require_once 'includes/footer.php'; ?>
