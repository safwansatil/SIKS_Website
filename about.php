<?php
require_once 'includes/config.php';
require_once 'includes/header.php';

$aboutTitle = getAboutContent('title');
$aboutCards = getAboutContent('card');
?>

<!-- About Section -->
<section class="py-24">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <?php if ($aboutTitle): ?>
            <div class="max-w-4xl mx-auto text-center mb-20">
                <h2 class="text-5xl font-display font-bold text-emerald-950 mb-8 tracking-tight">
                    <?php echo htmlspecialchars($aboutTitle[0]['title']); ?>
                </h2>
                <p class="text-xl text-emerald-950/60 leading-relaxed font-medium">
                    <?php echo nl2br(htmlspecialchars($aboutTitle[0]['description'])); ?>
                </p>
            </div>
        <?php endif; ?>

        <!-- Full-Width Cards Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-24">
            <?php foreach ($aboutCards as $index => $card): 
                $isDark = ($index % 2 !== 0);
                $totalCards = count($aboutCards);
                $isLast = ($index === $totalCards - 1);
                $spanFull = ($isLast && $totalCards % 2 !== 0);
            ?>
                <div class="<?php echo $spanFull ? 'md:col-span-2' : ''; ?> p-12 <?php echo $isDark ? 'bg-emerald-950 text-white shadow-xl shadow-emerald-950/10' : 'bg-white border border-emerald-950/10 shadow-sm'; ?> rounded-[40px] transition-all duration-300 hover:shadow-2xl">
                    <?php if (isset($card['image_path']) && $card['image_path']): ?>
                        <div class="mb-8 rounded-3xl overflow-hidden aspect-video">
                            <img src="<?php echo htmlspecialchars($card['image_path']); ?>" alt="<?php echo htmlspecialchars($card['title']); ?>" class="w-full h-full object-cover">
                        </div>
                    <?php endif; ?>
                    <h3 class="text-3xl font-display font-bold mb-6 <?php echo $isDark ? 'text-white' : 'text-emerald-950'; ?>">
                        <?php echo htmlspecialchars($card['title']); ?>
                    </h3>
                    <p class="leading-relaxed text-base font-medium <?php echo $isDark ? 'text-white/70' : 'text-emerald-950/70'; ?>">
                        <?php echo nl2br(htmlspecialchars($card['description'])); ?>
                    </p>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Past Events Showcase -->
        <?php $pastEvents = getEvents(true, 6); ?>
        <?php if ($pastEvents): ?>
            <div class="mt-32">
                <div class="inline-flex items-center space-x-2 px-4 py-1.5 rounded-full bg-emerald-50 border border-emerald-200 mb-10">
                    <span class="text-[10px] font-black uppercase tracking-widest text-emerald-700">Legacy of Excellence</span>
                </div>
                <h2 class="text-4xl font-display font-bold text-emerald-950 mb-12 tracking-tight">Our Journey Through Events</h2>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                    <?php foreach ($pastEvents as $event): ?>
                        <a href="event_details.php?id=<?php echo $event['id']; ?>" class="group block">
                            <div class="relative aspect-video rounded-3xl overflow-hidden mb-6 border border-emerald-950/5">
                                <?php if (!empty($event['cover_image'])): ?>
                                    <img src="<?php echo htmlspecialchars($event['cover_image']); ?>" 
                                         alt="<?php echo htmlspecialchars($event['name']); ?>"
                                         class="w-full h-full object-cover grayscale group-hover:grayscale-0 transition-all duration-500 group-hover:scale-105">
                                <?php else: ?>
                                    <div class="w-full h-full bg-emerald-900 flex items-center justify-center">
                                        <i class="fas <?php echo $event['logo'] ?: 'fa-calendar'; ?> text-3xl text-white/20"></i>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <h4 class="text-lg font-bold text-emerald-950 group-hover:text-emerald-600 transition-colors">
                                <?php echo htmlspecialchars($event['name']); ?>
                            </h4>
                            <p class="text-emerald-950/40 text-xs font-bold uppercase tracking-widest mt-1">
                                <?php echo date('M Y', strtotime($event['event_date'])); ?>
                            </p>
                        </a>
                    <?php endforeach; ?>
                </div>
                
                <div class="mt-16 text-center">
                    <a href="events.php" class="inline-flex items-center px-8 py-4 rounded-2xl bg-emerald-950 text-white font-bold hover:bg-black transition-all shadow-xl shadow-emerald-950/20">
                        View Full Gallery <i class="fas fa-arrow-right ml-3 text-xs"></i>
                    </a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>