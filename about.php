<?php
require_once 'includes/config.php';
require_once 'includes/header.php';

$aboutTitle = getAboutContent('title');
$aboutCards = getAboutContent('card');
$pastEvents = getEvents(true); // true = is_past
?>

<!-- About Section -->
<section class="py-24">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <?php if ($aboutTitle): ?>
            <div class="max-w-3xl mb-16">
                <h2 class="text-5xl font-display font-bold text-emerald-950 mb-6 tracking-tight">
                    <?php echo htmlspecialchars($aboutTitle[0]['title']); ?>
                </h2>
                <p class="text-xl text-emerald-950/60 leading-relaxed font-medium">
                    <?php echo nl2br(htmlspecialchars($aboutTitle[0]['description'])); ?>
                </p>
            </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-24">
            <?php foreach ($aboutCards as $index => $card): 
                $isDark = ($index % 2 !== 0);
            ?>
                <div class="p-10 <?php echo $isDark ? 'bg-emerald-950 text-white shadow-xl shadow-emerald-950/10' : 'bg-white border border-emerald-950/10 shadow-sm'; ?> rounded-[40px]">
                    <h3 class="text-2xl font-bold mb-4 <?php echo $isDark ? 'text-white' : 'text-emerald-950'; ?>">
                        <?php echo htmlspecialchars($card['title']); ?>
                    </h3>
                    <p class="leading-relaxed text-sm font-medium italic <?php echo $isDark ? 'text-white/70' : 'text-emerald-950/70'; ?>">
                        <?php echo nl2br(htmlspecialchars($card['description'])); ?>
                    </p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Past Events Carousel Section -->
<section class="py-24 bg-emerald-50/50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-end justify-between mb-16">
            <div class="max-w-2xl">
                <h2 class="text-4xl font-display font-bold text-emerald-950 mb-4 tracking-tight">Legacy of Excellence</h2>
                <p class="text-emerald-950/40 font-medium italic">A look back at the milestones and memories that define our journey.</p>
            </div>
            <div class="hidden sm:flex space-x-4">
                <button onclick="scrollCarousel(-1)" class="w-12 h-12 rounded-full border border-emerald-950/10 flex items-center justify-center text-emerald-950 hover:bg-emerald-950 hover:text-white transition-all">
                    <i class="fas fa-chevron-left"></i>
                </button>
                <button onclick="scrollCarousel(1)" class="w-12 h-12 rounded-full border border-emerald-950/10 flex items-center justify-center text-emerald-950 hover:bg-emerald-950 hover:text-white transition-all">
                    <i class="fas fa-chevron-right"></i>
                </button>
            </div>
        </div>

        <div id="past-events-carousel" class="flex overflow-x-auto space-x-6 pb-12 no-scrollbar scroll-smooth">
            <?php if ($pastEvents): ?>
                <?php foreach ($pastEvents as $event): ?>
                    <a href="event_details.php?id=<?php echo $event['id']; ?>" class="flex-none w-[300px] sm:w-[400px] group">
                        <div class="relative aspect-[4/3] rounded-[40px] overflow-hidden mb-6 border border-emerald-950/5">
                            <div class="absolute inset-0 bg-emerald-950/20 group-hover:bg-transparent transition-colors duration-500 z-10"></div>
                            <img src="<?php echo $event['logo'] ?: 'https://images.unsplash.com/photo-1511795409834-ef04bbd61622?q=80&w=2069&auto=format&fit=crop'; ?>" 
                                 alt="Event Photo" class="absolute inset-0 w-full h-full object-cover grayscale group-hover:grayscale-0 transition-all duration-700 scale-110 group-hover:scale-100">
                            <div class="absolute bottom-6 left-6 z-20">
                                <span class="px-3 py-1 bg-white/90 backdrop-blur-md rounded-full text-[9px] font-black uppercase tracking-widest text-emerald-950">
                                    <?php echo date('M Y', strtotime($event['event_date'])); ?>
                                </span>
                            </div>
                        </div>
                        <h3 class="text-xl font-bold text-emerald-950 mb-2 group-hover:text-emerald-600 transition-colors">
                            <?php echo htmlspecialchars($event['name']); ?>
                        </h3>
                        <p class="text-emerald-950/40 text-xs font-semibold uppercase tracking-widest">
                            <?php echo htmlspecialchars($event['venue']); ?>
                        </p>
                    </a>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="p-20 text-center w-full border border-dashed border-emerald-950/10 rounded-[40px]">
                    <p class="text-emerald-950/30 italic">No past events recorded yet.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<script>
    function scrollCarousel(direction) {
        const carousel = document.getElementById('past-events-carousel');
        const scrollAmount = 400;
        carousel.scrollBy({
            left: direction * scrollAmount,
            behavior: 'smooth'
        });
    }
</script>

<style>
    .no-scrollbar::-webkit-scrollbar {
        display: none;
    }
    .no-scrollbar {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }
</style>

<?php require_once 'includes/footer.php'; ?>