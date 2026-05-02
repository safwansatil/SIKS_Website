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
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>