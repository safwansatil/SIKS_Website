<?php
require_once 'includes/config.php';

// Check if this is an HTMX request
$isHtmx = isset($_SERVER['HTTP_HX_REQUEST']);

if (!$isHtmx) {
    require_once 'includes/header.php';
} else {
    echo '<main id="main-content" class="animate-page">';
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : null;
$article = getArticleById($id);

if (!$article) {
    if ($isHtmx) {
        header('HX-Redirect: /articles');
    } else {
        header('Location: /articles');
    }
    exit;
}

// Canonical URL check: Redirect if accessed via article.php?id=X or if slug is missing/wrong
$canonicalUrl = "/article/" . $article['id'] . "/" . ($article['slug'] ?: generateSlug($article['title']));
$currentUri = $_SERVER['REQUEST_URI'];

if (!$isHtmx && strpos($currentUri, $canonicalUrl) === false) {
    header("Location: $canonicalUrl", true, 301);
    exit;
}

$readingTime = $article['reading_time'] ?? calculateReadingTime($article['description'] ?? '');
?>

<!-- Article Hero -->
<section class="relative <?php echo !empty($article['cover_image']) ? '-mt-24 min-h-[50vh]' : ''; ?> flex items-end overflow-hidden">
    <?php if (!empty($article['cover_image'])): ?>
        <img src="/<?php echo ltrim(htmlspecialchars($article['cover_image']), '/'); ?>" 
             alt="<?php echo htmlspecialchars($article['title']); ?>"
             class="absolute inset-0 w-full h-full object-cover">
        <div class="absolute inset-0 img-overlay-dark"></div>
        
        <div class="relative z-10 w-full">
            <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 pb-16 pt-40">
                <a href="/articles" 
                   hx-get="/articles" hx-target="#main-content" hx-push-url="true" hx-select="#main-content"
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
                    <span class="flex items-center">
                        <i class="far fa-clock mr-1.5 opacity-60"></i>
                        <?php echo $readingTime; ?> min read
                    </span>
                    <span class="w-1 h-1 rounded-full bg-white/30"></span>
                    <button onclick="copyToClipboard()" 
                            class="inline-flex items-center gap-2 text-white/60 hover:text-white transition-all hover:scale-105">
                        <i class="fas fa-share-alt"></i>
                        <span class="font-bold">Share</span>
                    </button>
                </div>
                <?php if (!empty($article['updated_at'])): ?>
                    <div class="mt-4 text-white/40 text-[10px] uppercase tracking-widest font-bold italic">
                        Last updated: <?php echo date('F d, Y', strtotime($article['updated_at'])); ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php else: ?>
        <!-- Minimal Hero (No Image) -->
        <div class="w-full pt-40 pb-16">
            <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
                <a href="/articles" 
                   hx-get="/articles" hx-target="#main-content" hx-push-url="true" hx-select="#main-content"
                   class="inline-flex items-center text-emerald-950/40 hover:text-emerald-950 font-bold text-xs uppercase tracking-widest mb-8 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>
                    All Articles
                </a>
                <h1 class="text-4xl md:text-5xl font-display font-bold text-emerald-950 mb-6 tracking-tight leading-tight">
                    <?php echo htmlspecialchars($article['title']); ?>
                </h1>
                <div class="flex flex-wrap items-center gap-4 text-emerald-950/40 text-sm font-medium">
                    <span class="font-bold text-emerald-950"><?php echo htmlspecialchars($article['writer']); ?></span>
                    <span class="w-1 h-1 rounded-full bg-emerald-950/10"></span>
                    <span><?php echo date('F d, Y', strtotime($article['last_edited'])); ?></span>
                    <span class="w-1 h-1 rounded-full bg-emerald-950/10"></span>
                    <span class="flex items-center">
                        <i class="far fa-clock mr-1.5 opacity-60"></i>
                        <?php echo $readingTime; ?> min read
                    </span>
                    <span class="w-1 h-1 rounded-full bg-emerald-950/10"></span>
                    <button onclick="copyToClipboard()" 
                            class="inline-flex items-center gap-2 hover:text-emerald-950 transition-all hover:scale-105">
                        <i class="fas fa-share-alt"></i>
                        <span class="font-bold">Share</span>
                    </button>
                </div>
            </div>
        </div>
<?php endif; ?>
</section>

<!-- Article Content -->
<section class="py-16 bg-white">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Article Body -->
        <article class="prose prose-lg prose-emerald max-w-none">
            <div class="text-emerald-950/80 text-lg leading-[1.9] font-medium whitespace-pre-wrap" style="font-family: 'Inter', 'Noto Sans Bengali', 'Noto Naskh Arabic', sans-serif;">
<?php echo nl2br(htmlspecialchars($article['description'])); ?>
            </div>
        </article>

        <!-- Divider -->
        <div class="my-16 border-t border-emerald-950/10"></div>

        <!-- Gallery Images -->
        <?php $articleImages = getArticleImages($article['id']); ?>
        <?php if ($articleImages): ?>
            <div class="mt-16 mb-24">
                <h3 class="text-2xl font-display font-bold text-emerald-950 mb-10">Article Gallery</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <?php foreach ($articleImages as $idx => $img): ?>
                        <div class="group cursor-pointer" onclick="openLightbox(<?php echo $idx; ?>)">
                            <div class="relative aspect-video rounded-[32px] overflow-hidden border border-emerald-950/5 shadow-sm hover:shadow-xl transition-all duration-500">
                                <img src="/<?php echo ltrim(htmlspecialchars($img['image_path']), '/'); ?>" 
                                     alt="<?php echo htmlspecialchars($img['caption'] ?: $article['title']); ?>"
                                     class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                                <?php if ($img['caption']): ?>
                                    <div class="absolute inset-x-0 bottom-0 p-6 bg-gradient-to-t from-black/60 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                        <p class="text-white text-xs font-medium"><?php echo htmlspecialchars($img['caption']); ?></p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <script>
                const galleryImages = <?php echo json_encode(array_map(function($img) { return '/' . ltrim($img['image_path'], '/'); }, $articleImages)); ?>;
                let currentLightboxIndex = 0;
                let lightboxEl = null;

                function createLightbox() {
                    if (lightboxEl) return;
                    lightboxEl = document.createElement('div');
                    lightboxEl.id = 'lightbox';
                    lightboxEl.style.cssText = 'position:fixed;top:0;left:0;width:100%;height:100%;z-index:99999;background:rgba(0,0,0,0.95);display:none;align-items:center;justify-content:center;';
                    lightboxEl.innerHTML = `
                        <button onclick="closeLightbox()" style="position:absolute;top:24px;right:24px;width:48px;height:48px;border-radius:50%;background:rgba(255,255,255,0.1);border:none;color:white;font-size:20px;cursor:pointer;display:flex;align-items:center;justify-content:center;z-index:10;"><i class="fas fa-times"></i></button>
                        <div style="position:absolute;top:28px;left:24px;color:rgba(255,255,255,0.5);font-size:14px;font-weight:bold;z-index:10;" id="lightbox-counter"></div>
                        <button onclick="navigateLightbox(-1)" style="position:absolute;left:16px;top:50%;transform:translateY(-50%);width:48px;height:48px;border-radius:50%;background:rgba(255,255,255,0.1);border:none;color:white;font-size:18px;cursor:pointer;display:flex;align-items:center;justify-content:center;z-index:10;"><i class="fas fa-chevron-left"></i></button>
                        <img id="lightbox-img" src="" alt="Gallery" style="max-width:90vw;max-height:90vh;object-fit:contain;border-radius:8px;">
                        <button onclick="navigateLightbox(1)" style="position:absolute;right:16px;top:50%;transform:translateY(-50%);width:48px;height:48px;border-radius:50%;background:rgba(255,255,255,0.1);border:none;color:white;font-size:18px;cursor:pointer;display:flex;align-items:center;justify-content:center;z-index:10;"><i class="fas fa-chevron-right"></i></button>
                    `;
                    lightboxEl.addEventListener('click', function(e) {
                        if (e.target === lightboxEl) closeLightbox();
                    });
                    document.body.appendChild(lightboxEl);
                }

                function openLightbox(index) {
                    createLightbox();
                    currentLightboxIndex = index;
                    document.getElementById('lightbox-img').src = galleryImages[index];
                    document.getElementById('lightbox-counter').textContent = (index + 1) + ' / ' + galleryImages.length;
                    lightboxEl.style.display = 'flex';
                    document.body.style.overflow = 'hidden';
                }

                function closeLightbox() {
                    if (lightboxEl) lightboxEl.style.display = 'none';
                    document.body.style.overflow = '';
                }

                function navigateLightbox(dir) {
                    currentLightboxIndex = (currentLightboxIndex + dir + galleryImages.length) % galleryImages.length;
                    document.getElementById('lightbox-img').src = galleryImages[currentLightboxIndex];
                    document.getElementById('lightbox-counter').textContent = (currentLightboxIndex + 1) + ' / ' + galleryImages.length;
                }

                document.addEventListener('keydown', function(e) {
                    if (!lightboxEl || lightboxEl.style.display === 'none') return;
                    if (e.key === 'Escape') closeLightbox();
                    if (e.key === 'ArrowLeft') navigateLightbox(-1);
                    if (e.key === 'ArrowRight') navigateLightbox(1);
                });
            </script>
        <?php endif; ?>

        <!-- Author Card & Library Link -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="md:col-span-2 p-8 bg-emerald-50/50 border border-emerald-100 rounded-3xl">
                <p class="text-[10px] font-bold uppercase tracking-widest text-emerald-950/40 mb-1">Written by</p>
                <p class="text-xl font-bold text-emerald-950"><?php echo htmlspecialchars($article['writer']); ?></p>
                <p class="text-emerald-950/50 text-sm font-medium">Last updated <?php echo date('F d, Y', strtotime($article['last_edited'])); ?></p>
            </div>
            <a href="/library" 
               hx-get="/library" hx-target="#main-content" hx-push-url="true" hx-select="#main-content"
               class="p-8 bg-emerald-950 text-white rounded-3xl flex flex-col justify-center items-center text-center group hover:bg-black transition-colors">
                <i class="fas fa-book text-2xl mb-3 text-emerald-400 group-hover:scale-110 transition-transform"></i>
                <p class="font-bold">Explore Library</p>
                <p class="text-[10px] text-white/40 uppercase tracking-widest mt-1">Free PDFs & Books</p>
            </a>
        </div>

        <!-- Back to Articles -->
        <div class="mt-12 text-center">
            <a href="/articles" 
               hx-get="/articles" hx-target="#main-content" hx-push-url="true" hx-select="#main-content"
               class="inline-flex items-center px-8 py-4 bg-emerald-950 text-white rounded-2xl text-sm font-bold hover:bg-black transition-colors">
                <i class="fas fa-arrow-left mr-2"></i>
                Back to All Articles
            </a>
        </div>
    </div>
</section>

<?php
if (!$isHtmx) {
    require_once 'includes/footer.php';
} else {
    echo '</main>';
}
?>
