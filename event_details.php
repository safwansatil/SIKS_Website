require_once 'includes/config.php';

// Check if this is an HTMX request
$isHtmx = isset($_SERVER['HTTP_HX_REQUEST']);

if (!$isHtmx) {
    require_once 'includes/header.php';
} else {
    echo '<main id="main-content" class="pt-24 animate-page">';
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : null;
$event = getEventById($id);

if (!$event) {
    // If not found, redirect to events list
    if ($isHtmx) {
        header('HX-Redirect: /events');
    } else {
        header('Location: /events');
    }
    exit;
}

// Canonical URL check: Redirect if accessed via event_details.php?id=X or if slug is missing/wrong
$canonicalUrl = "/event/" . $event['id'] . "/" . ($event['slug'] ?: generateSlug($event['name']));
$currentUri = $_SERVER['REQUEST_URI'];

if (!$isHtmx && strpos($currentUri, $canonicalUrl) === false) {
    header("Location: $canonicalUrl", true, 301);
    exit;
}

$images = getEventImages($event['id']);
$tags = !empty($event['tag']) ? array_map('trim', explode(',', $event['tag'])) : [];

// Determine hero image
$heroImage = null;
if (!empty($event['cover_image'])) {
    $heroImage = $event['cover_image'];
} elseif ($images && count($images) > 0) {
    $heroImage = $images[0]['image_path'];
}
?>

<!-- Event Hero -->
<section class="relative -mt-24 <?php echo $heroImage ? 'min-h-[60vh]' : 'min-h-[40vh]'; ?> flex items-end overflow-hidden">
    <?php if ($heroImage): ?>
        <img src="<?php echo htmlspecialchars($heroImage); ?>" 
             alt="<?php echo htmlspecialchars($event['name']); ?>"
             class="absolute inset-0 w-full h-full object-cover">
        <div class="absolute inset-0 img-overlay-dark"></div>
    <?php else: ?>
        <div class="absolute inset-0" style="background: linear-gradient(135deg, #022c22 0%, #064e3b 50%, #065f46 100%);">
            <div class="absolute inset-0 flex items-center justify-center opacity-10">
                <i class="fas <?php echo $event['logo'] ?: 'fa-calendar'; ?> text-[200px] text-white"></i>
            </div>
        </div>
    <?php endif; ?>
    
    <div class="relative z-10 w-full">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 pb-16 pt-32">
            <!-- Back Link -->
            <a href="events" 
               hx-get="events" hx-target="#main-content" hx-push-url="true" hx-select="#main-content"
               class="inline-flex items-center text-white/60 hover:text-white font-bold text-xs uppercase tracking-widest mb-8 transition-colors">
                <i class="fas fa-arrow-left mr-2"></i>
                Back to Events
            </a>

            <!-- Event Title -->
            <h1 class="text-4xl md:text-6xl font-display font-bold text-white mb-6 tracking-tight leading-tight">
                <?php echo htmlspecialchars($event['name']); ?>
            </h1>

            <!-- Tags -->
            <?php if ($tags): ?>
                <div class="flex flex-wrap gap-2 mb-6">
                    <?php foreach ($tags as $tag): ?>
                        <span class="px-3 py-1 bg-white/10 backdrop-blur-md border border-white/20 rounded-full text-[10px] font-bold text-white uppercase tracking-wider">
                            #<?php echo htmlspecialchars($tag); ?>
                        </span>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <!-- Quick Info Bar -->
            <div class="flex flex-wrap items-center gap-6 text-white/70 text-sm font-medium">
                <span><i class="far fa-calendar-alt mr-2"></i><?php echo date('F d, Y', strtotime($event['event_date'])); ?></span>
                <?php if ($event['event_time']): ?>
                    <span><i class="far fa-clock mr-2"></i><?php echo htmlspecialchars($event['event_time']); ?></span>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<!-- Event Content -->
<section class="py-16 bg-white">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Info Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 -mt-24 mb-16 relative z-20">
            <div class="card-professional p-8 bg-white">
                <p class="text-[10px] uppercase font-bold text-emerald-950/40 mb-3 tracking-widest">Schedule</p>
                <p class="text-emerald-950 font-bold text-xl"><?php echo date('F d, Y', strtotime($event['event_date'])); ?></p>
                <p class="text-emerald-950/60 font-medium mt-1"><?php echo htmlspecialchars($event['event_time']); ?></p>
            </div>
            <div class="card-professional p-8 bg-white">
                <p class="text-[10px] uppercase font-bold text-emerald-950/40 mb-3 tracking-widest">Venue</p>
                <p class="text-emerald-950 font-bold text-xl"><?php echo htmlspecialchars($event['venue']); ?></p>
                <!-- <p class="text-emerald-950/60 font-medium mt-1">IUT Campus</p> -->
            </div>
        </div>

        <!-- Short Description -->
        <?php if (!empty($event['short_description'])): ?>
            <div class="mb-12 p-8 bg-emerald-50/50 border border-emerald-100 rounded-3xl">
                <p class="text-emerald-950/80 text-lg font-medium leading-relaxed italic">
                    <?php echo nl2br(htmlspecialchars($event['short_description'])); ?>
                </p>
            </div>
        <?php endif; ?>

        <!-- Full Description -->
        <?php if (!empty($event['description'])): ?>
            <div class="mb-16">
                <h3 class="text-2xl font-display font-bold text-emerald-950 mb-6">About This Event</h3>
                <div class="prose prose-lg max-w-none text-emerald-950/70 leading-relaxed font-medium">
                <?php 
                    $safeDesc = htmlspecialchars($event['description']);
                    // Auto-linkify URLs
                    $safeDesc = preg_replace(
                        '/(https?:\/\/[^\s<]+)/i', 
                        '<a href="$1" target="_blank" class="text-emerald-600 underline underline-offset-2 hover:text-emerald-800 transition-colors break-all">$1</a>', 
                        $safeDesc
                    );
                    echo nl2br($safeDesc); 
                ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- Gallery -->
        <?php if ($images && count($images) > 0): ?>
            <div class="mb-16">
                <h3 class="text-2xl font-display font-bold text-emerald-950 mb-8">Gallery</h3>
                <div class="masonry-grid">
                    <?php foreach ($images as $idx => $img): ?>
                        <div class="rounded-2xl overflow-hidden border border-emerald-950/5 shadow-sm hover:shadow-xl transition-shadow duration-300 cursor-pointer"
                             onclick="openLightbox(<?php echo $idx; ?>)">
                            <img src="<?php echo htmlspecialchars($img['image_path']); ?>" 
                                 alt="Event Photo" 
                                 class="w-full h-auto object-cover hover:scale-105 transition-transform duration-500">
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <script>
                const galleryImages = <?php echo json_encode(array_map(function($img) { return $img['image_path']; }, $images)); ?>;
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

        <!-- Get Directions CTA -->
        <!-- <div class="bg-emerald-50/50 border border-emerald-100 rounded-3xl p-8 sm:p-10 flex flex-col sm:flex-row items-center justify-between">
            <div class="mb-4 sm:mb-0">
                <p class="text-emerald-950 font-bold text-sm mb-1">Join us for this occasion</p>
                <p class="text-emerald-950/40 text-xs font-medium"><?php echo htmlspecialchars($event['venue']); ?>, IUT Campus</p>
            </div>
            <a href="<?php echo MAPS_URL; ?>" target="_blank" 
               class="px-8 py-3 bg-emerald-950 text-white rounded-xl text-xs font-bold hover:bg-black transition-colors flex items-center group">
                <i class="fas fa-directions mr-2 text-[10px]"></i>
                Get Directions
                <i class="fas fa-arrow-right ml-2 text-[9px] opacity-0 group-hover:opacity-100 group-hover:translate-x-1 transition-all"></i>
            </a>
        </div> -->
    </div>
</section>

<?php
if (!$isHtmx) {
    require_once 'includes/footer.php';
} else {
    echo '</main>';
}
?>
