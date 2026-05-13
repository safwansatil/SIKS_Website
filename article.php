<?php
require_once 'includes/config.php';
require_once 'includes/header.php';

$id = $_GET['id'] ?? null;
$article = getArticleById($id);

if (!$article) {
    header('Location: articles.php');
    exit;
}

$readingTime = $article['reading_time'] ?? calculateReadingTime($article['description'] ?? '');
?>

<!-- Article Hero -->
<section class="relative <?php echo !empty($article['cover_image']) ? '-mt-24 min-h-[50vh]' : ''; ?> flex items-end overflow-hidden">
    <?php if (!empty($article['cover_image'])): ?>
        <img src="<?php echo htmlspecialchars($article['cover_image']); ?>" 
             alt="<?php echo htmlspecialchars($article['title']); ?>"
             class="absolute inset-0 w-full h-full object-cover">
        <div class="absolute inset-0 img-overlay-dark"></div>
        
        <div class="relative z-10 w-full">
            <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 pb-16 pt-40">
                <a href="articles.php" 
                   hx-get="articles.php" hx-target="#main-content" hx-push-url="true" hx-select="#main-content"
                   class="inline-flex items-center text-white/60 hover:text-white font-bold text-xs uppercase tracking-widest mb-8 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>
                    All Articles
                </a>
                <h1 class="text-4xl md:text-5xl font-display font-bold text-white mb-6 tracking-tight leading-tight">
                    <?php echo htmlspecialchars($article['title']); ?>
                </h1>
                <div class="flex items-center space-x-4 text-white/60 text-sm font-medium">
                    <span class="font-bold text-white"><?php echo htmlspecialchars($article['writer']); ?></span>
                    <span class="w-1 h-1 rounded-full bg-white/30"></span>
                    <span><?php echo date('F d, Y', strtotime($article['last_edited'])); ?></span>
                    <span class="w-1 h-1 rounded-full bg-white/30"></span>
                    <span><?php echo $readingTime; ?> min read</span>
                </div>
            </div>
        </div>
    <?php endif; ?>
</section>

<!-- Article Content -->
<section class="py-16 bg-white">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <?php if (empty($article['cover_image'])): ?>
            <!-- No cover image: show title inline -->
            <a href="articles.php" 
               hx-get="articles.php" hx-target="#main-content" hx-push-url="true" hx-select="#main-content"
               class="inline-flex items-center text-emerald-950/40 hover:text-emerald-950 font-bold text-xs uppercase tracking-widest mb-8 transition-colors">
                <i class="fas fa-arrow-left mr-2"></i>
                All Articles
            </a>
            <h1 class="text-4xl md:text-5xl font-display font-bold text-emerald-950 mb-6 tracking-tight leading-tight">
                <?php echo htmlspecialchars($article['title']); ?>
            </h1>
            <div class="flex items-center space-x-4 text-emerald-950/40 text-sm font-medium mb-12">
                <span class="font-bold text-emerald-950"><?php echo htmlspecialchars($article['writer']); ?></span>
                <span class="w-1 h-1 rounded-full bg-emerald-950/20"></span>
                <span><?php echo date('F d, Y', strtotime($article['last_edited'])); ?></span>
                <span class="w-1 h-1 rounded-full bg-emerald-950/20"></span>
                <span><?php echo $readingTime; ?> min read</span>
            </div>
        <?php endif; ?>

        <!-- Article Body -->
        <article class="prose prose-lg prose-emerald max-w-none">
            <div class="text-emerald-950/80 text-lg leading-[1.9] font-medium whitespace-pre-wrap" style="font-family: 'Inter', 'Noto Sans Bengali', 'Noto Naskh Arabic', sans-serif;">
<?php echo nl2br(htmlspecialchars($article['description'])); ?>
            </div>
        </article>

        <!-- Divider -->
        <div class="my-16 border-t border-emerald-950/10"></div>

        <!-- Author Card -->
        <div class="p-8 bg-emerald-50/50 border border-emerald-100 rounded-3xl">
            <p class="text-[10px] font-bold uppercase tracking-widest text-emerald-950/40 mb-1">Written by</p>
            <p class="text-xl font-bold text-emerald-950"><?php echo htmlspecialchars($article['writer']); ?></p>
            <p class="text-emerald-950/50 text-sm font-medium">Last updated <?php echo date('F d, Y', strtotime($article['last_edited'])); ?></p>
        </div>

        <!-- Back to Articles -->
        <div class="mt-12 text-center">
            <a href="articles.php" 
               hx-get="articles.php" hx-target="#main-content" hx-push-url="true" hx-select="#main-content"
               class="inline-flex items-center px-8 py-4 bg-emerald-950 text-white rounded-2xl text-sm font-bold hover:bg-black transition-colors">
                <i class="fas fa-arrow-left mr-2"></i>
                Back to All Articles
            </a>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
