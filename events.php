<?php
require_once 'includes/config.php';
require_once 'includes/header.php';

$selectedCategory = $_GET['category'] ?? null;
$categories = getEventCategories();

$upcomingEvents = getEvents(false, null, $selectedCategory);
$pastEvents = getEvents(true, null, $selectedCategory);
?>

<section class="py-24">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-4xl font-display font-bold text-emerald-950 mb-4 tracking-tight">Society Events</h2>
            <p class="text-emerald-950/40 max-w-2xl mx-auto font-medium italic">Stay updated with our upcoming programs, workshops, and activities.</p>
        </div>

        <!-- Category Filter -->
        <div class="flex flex-wrap items-center justify-center gap-3 mb-16">
            <a href="events.php" 
               class="px-6 py-2 rounded-full font-bold text-xs uppercase tracking-widest transition-all <?php echo !$selectedCategory ? 'bg-emerald-950 text-white shadow-lg' : 'bg-emerald-50 text-emerald-950/40 hover:bg-emerald-100'; ?>">
                All Events
            </a>
            <?php foreach ($categories as $cat): ?>
                <a href="events.php?category=<?php echo urlencode($cat['name']); ?>" 
                   class="px-6 py-2 rounded-full font-bold text-xs uppercase tracking-widest transition-all <?php echo $selectedCategory === $cat['name'] ? 'bg-emerald-950 text-white shadow-lg' : 'bg-emerald-50 text-emerald-950/40 hover:bg-emerald-100'; ?>">
                    <?php echo htmlspecialchars($cat['name']); ?>
                </a>
            <?php endforeach; ?>
        </div>

        <!-- Upcoming Events -->
        <?php if ($upcomingEvents): ?>
            <div class="mb-8">
                <div class="inline-flex items-center space-x-2 px-4 py-1.5 rounded-full bg-emerald-50 border border-emerald-200 mb-10">
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                    </span>
                    <span class="text-[10px] font-black uppercase tracking-widest text-emerald-700">Upcoming Events</span>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-24">
                <?php foreach ($upcomingEvents as $event): ?>
                    <a href="event_details.php?id=<?php echo $event['id']; ?>" class="card-professional group block">
                        <!-- Event Cover Image -->
                        <div class="relative aspect-[16/10] overflow-hidden">
                            <?php if (!empty($event['cover_image'])): ?>
                                <img src="<?php echo htmlspecialchars($event['cover_image']); ?>" 
                                     alt="<?php echo htmlspecialchars($event['name']); ?>"
                                     class="w-full h-full object-cover">
                            <?php else: ?>
                                <div class="w-full h-full bg-gradient-to-br from-emerald-900 to-emerald-950 flex items-center justify-center">
                                    <i class="fas <?php echo $event['logo'] ?: 'fa-calendar'; ?> text-5xl text-emerald-500/30"></i>
                                </div>
                            <?php endif; ?>
                            <div class="absolute inset-0 bg-gradient-to-t from-emerald-950/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
                            
                            <!-- Date Badge -->
                            <div class="absolute top-4 right-4">
                                <div class="bg-white/95 backdrop-blur-md rounded-2xl px-4 py-2 text-center shadow-lg">
                                    <p class="text-emerald-950 font-bold text-lg leading-none"><?php echo date('d', strtotime($event['event_date'])); ?></p>
                                    <p class="text-emerald-950/60 text-[9px] font-bold uppercase tracking-wider"><?php echo date('M', strtotime($event['event_date'])); ?></p>
                                </div>
                            </div>
                        </div>

                        <!-- Card Content -->
                        <div class="p-6">
                            <h3 class="text-xl font-bold text-emerald-950 mb-3 leading-tight group-hover:text-emerald-700 transition-colors">
                                <?php echo htmlspecialchars($event['name']); ?>
                            </h3>
                            
                            <?php if (!empty($event['short_description'])): ?>
                                <p class="text-emerald-950/50 text-sm line-clamp-2 mb-4 font-medium">
                                    <?php echo htmlspecialchars($event['short_description']); ?>
                                </p>
                            <?php endif; ?>

                            <div class="flex items-center justify-between pt-4 border-t border-emerald-950/5">
                                <div class="flex items-center space-x-4 text-emerald-950/40 text-xs font-semibold">
                                    <span><i class="far fa-clock mr-1"></i><?php echo htmlspecialchars($event['event_time']); ?></span>
                                    <span><i class="fas fa-location-dot mr-1"></i><?php echo htmlspecialchars($event['venue']); ?></span>
                                </div>
                                <i class="fas fa-arrow-right text-emerald-950/20 group-hover:text-emerald-600 group-hover:translate-x-1 transition-all text-xs"></i>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="py-20 text-center border border-dashed border-emerald-950/10 rounded-[40px] mb-24">
                <i class="fas fa-calendar-plus text-4xl text-emerald-950/10 mb-4"></i>
                <p class="text-emerald-950/30 italic">No upcoming events at the moment.</p>
            </div>
        <?php endif; ?>

        <!-- Past Events / Legacy of Excellence -->
        <?php if ($pastEvents): ?>
            <div class="mb-16">
                <div class="flex items-end justify-between mb-12">
                    <div class="max-w-2xl">
                        <div class="inline-flex items-center space-x-2 px-4 py-1.5 rounded-full bg-emerald-950/5 border border-emerald-950/10 mb-6">
                            <span class="text-[10px] font-black uppercase tracking-widest text-emerald-950/50">Past Events</span>
                        </div>
                        <h2 class="text-3xl font-display font-bold text-emerald-950 mb-4 tracking-tight">Past Events</h2>
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
                    <?php foreach ($pastEvents as $event): ?>
                        <a href="event_details.php?id=<?php echo $event['id']; ?>" class="flex-none w-[300px] sm:w-[400px] group">
                            <div class="relative aspect-[4/3] rounded-[32px] overflow-hidden mb-6 border border-emerald-950/5">
                                <div class="absolute inset-0 bg-emerald-950/20 group-hover:bg-transparent transition-colors duration-500 z-10"></div>
                                <?php if (!empty($event['cover_image'])): ?>
                                    <img src="<?php echo htmlspecialchars($event['cover_image']); ?>" 
                                         alt="<?php echo htmlspecialchars($event['name']); ?>"
                                         class="absolute inset-0 w-full h-full object-cover grayscale group-hover:grayscale-0 transition-all duration-700 scale-110 group-hover:scale-100">
                                <?php else: ?>
                                    <div class="absolute inset-0 w-full h-full bg-gradient-to-br from-emerald-800 to-emerald-950 flex items-center justify-center">
                                        <i class="fas <?php echo $event['logo'] ?: 'fa-calendar'; ?> text-5xl text-emerald-500/20"></i>
                                    </div>
                                <?php endif; ?>
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
                </div>
            </div>
        <?php endif; ?>
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

<?php require_once 'includes/footer.php'; ?>