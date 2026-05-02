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

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php if ($articles): ?>
                <?php foreach ($articles as $article): ?>
                    <div class="bg-white border border-emerald-950/10 rounded-[32px] p-8 hover:border-emerald-950/30 transition-all duration-300 shadow-sm">
                        <div class="flex items-center justify-between mb-6">
                            <span class="text-[10px] font-black uppercase tracking-widest text-emerald-600 bg-emerald-50 px-3 py-1 rounded-full">Article</span>
                            <span class="text-emerald-950/30 text-[10px] font-bold"><?php echo date('M d, Y', strtotime($article['last_edited'])); ?></span>
                        </div>
                        <h3 class="text-2xl font-bold text-emerald-950 mb-4"><?php echo htmlspecialchars($article['title']); ?></h3>
                        <p class="text-emerald-950/60 text-sm line-clamp-3 mb-6">
                            <?php echo htmlspecialchars($article['description']); ?>
                        </p>
                        <div class="flex items-center justify-between mt-auto pt-6 border-t border-emerald-950/5">
                            <div class="flex items-center space-x-2">
                                <div class="w-8 h-8 rounded-full bg-emerald-950 flex items-center justify-center text-white text-[10px] font-bold">
                                    <?php echo strtoupper(substr($article['writer'], 0, 1)); ?>
                                </div>
                                <span class="text-xs font-bold text-emerald-950"><?php echo htmlspecialchars($article['writer']); ?></span>
                            </div>
                            <button onclick="openArticleModal(<?php echo htmlspecialchars(json_encode($article)); ?>)" class="text-emerald-600 font-bold text-xs hover:underline">Read More</button>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-span-full py-20 text-center border border-dashed border-emerald-950/10 rounded-[40px]">
                    <p class="text-emerald-950/30 italic">No articles published yet.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Article Modal -->
<div id="article-modal" class="fixed inset-0 z-[100] hidden items-center justify-center p-4 sm:p-6">
    <div class="absolute inset-0 bg-emerald-950/40 backdrop-blur-sm" onclick="closeArticleModal()"></div>
    <div class="relative w-full max-w-3xl bg-white rounded-[40px] shadow-2xl overflow-hidden animate-zoom-in">
        <div class="p-10 max-h-[80vh] overflow-y-auto" id="article-modal-content">
            <!-- Content Injected via JS -->
        </div>
        <div class="px-10 py-6 bg-emerald-50 border-t border-emerald-100 flex justify-end">
            <button onclick="closeArticleModal()"
                class="px-8 py-3 bg-emerald-950 text-white rounded-2xl text-xs font-bold hover:bg-emerald-900 transition-colors">Close Reader</button>
        </div>
    </div>
</div>

<script>
    function openArticleModal(article) {
        const modal = document.getElementById('article-modal');
        const content = document.getElementById('article-modal-content');

        content.innerHTML = `
            <div class="mb-8">
                <span class="text-[10px] font-black uppercase tracking-widest text-emerald-600 bg-emerald-50 px-3 py-1 rounded-full mb-4 inline-block">Full Article</span>
                <h2 class="text-3xl md:text-4xl font-display font-bold text-emerald-950 mb-4 tracking-tight">${article.title}</h2>
                <div class="flex items-center space-x-4 text-emerald-950/40 text-xs font-bold uppercase tracking-widest">
                    <span>By ${article.writer}</span>
                    <span>•</span>
                    <span>Last Edited: ${article.last_edited}</span>
                </div>
            </div>
            <div class="prose prose-emerald max-w-none">
                <p class="text-emerald-950/70 leading-relaxed font-medium whitespace-pre-wrap">${article.description}</p>
            </div>
        `;

        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';
    }

    function closeArticleModal() {
        const modal = document.getElementById('article-modal');
        modal.classList.remove('flex');
        modal.classList.add('hidden');
        document.body.style.overflow = 'auto';
    }
</script>

<style>
    @keyframes zoom-in {
        from { opacity: 0; transform: scale(0.95); }
        to { opacity: 1; transform: scale(1); }
    }
    .animate-zoom-in { animation: zoom-in 0.3s ease-out; }
</style>

<?php require_once 'includes/footer.php'; ?>
