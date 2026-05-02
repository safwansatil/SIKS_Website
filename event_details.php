<?php
require_once 'includes/config.php';
require_once 'includes/header.php';

$id = $_GET['id'] ?? null;
$event = null;

if ($id) {
    $stmt = $pdo->prepare("SELECT * FROM events WHERE id = ?");
    $stmt->execute([$id]);
    $event = $stmt->fetch();
}

if (!$event) {
    header('Location: events.php');
    exit;
}
?>

<section class="py-24">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-12">
            <a href="events.php" class="text-emerald-950/40 hover:text-emerald-950 font-bold text-xs uppercase tracking-widest flex items-center transition-colors">
                <i class="fas fa-arrow-left mr-2"></i>
                Back to Events
            </a>
        </div>

        <div class="bg-white border border-emerald-950/10 rounded-[48px] overflow-hidden shadow-2xl shadow-emerald-950/5">
            <div class="p-12">
                <div class="w-20 h-20 rounded-[24px] bg-emerald-950/5 flex items-center justify-center mb-10 border border-emerald-950/5">
                    <i class="fas <?php echo $event['logo'] ?: 'fa-calendar'; ?> text-emerald-950 text-4xl"></i>
                </div>
                
                <span class="inline-block px-4 py-2 rounded-full border border-emerald-950/10 text-emerald-950/60 text-[10px] font-bold uppercase tracking-widest mb-8">
                    <?php echo htmlspecialchars($event['category']); ?>
                </span>
                
                <h1 class="text-4xl md:text-5xl font-display font-bold text-emerald-950 mb-6 tracking-tight leading-tight">
                    <?php echo htmlspecialchars($event['name']); ?>
                </h1>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-12">
                    <div class="p-6 bg-emerald-50/50 border border-emerald-950/5 rounded-3xl">
                        <p class="text-[10px] uppercase font-bold text-emerald-950/40 mb-2 tracking-widest">Schedule</p>
                        <p class="text-emerald-950 font-bold text-lg"><?php echo date('F d, Y', strtotime($event['event_date'])); ?></p>
                        <p class="text-emerald-950/60 font-medium"><?php echo htmlspecialchars($event['event_time']); ?></p>
                    </div>
                    <div class="p-6 bg-emerald-50/50 border border-emerald-950/5 rounded-3xl">
                        <p class="text-[10px] uppercase font-bold text-emerald-950/40 mb-2 tracking-widest">Venue</p>
                        <p class="text-emerald-950 font-bold text-lg"><?php echo htmlspecialchars($event['venue']); ?></p>
                        <p class="text-emerald-950/60 font-medium">IUT Campus</p>
                    </div>
                </div>

                <?php if ($event['tag']): ?>
                    <div class="flex flex-wrap gap-2 mb-12">
                        <?php foreach (explode(',', $event['tag']) as $tag): ?>
                            <span class="px-4 py-1.5 bg-white border border-emerald-950/10 rounded-full text-[10px] font-bold text-emerald-950/60">
                                #<?php echo trim($tag); ?>
                            </span>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <div class="prose prose-emerald max-w-none">
                    <h3 class="text-xl font-bold text-emerald-950 mb-4">Event Description</h3>
                    <p class="text-emerald-950/70 leading-relaxed font-medium mb-8">
                        <?php echo nl2br(htmlspecialchars($event['description'])); ?>
                    </p>
                </div>

                <?php 
                // Fetch images if any
                $stmt = $pdo->prepare("SELECT * FROM event_images WHERE event_id = ?");
                $stmt->execute([$event['id']]);
                $images = $stmt->fetchAll();
                if ($images): 
                ?>
                    <div class="mt-12">
                        <h3 class="text-xl font-bold text-emerald-950 mb-6">Gallery</h3>
                        <div class="grid grid-cols-2 gap-4">
                            <?php foreach ($images as $img): ?>
                                <img src="<?php echo htmlspecialchars($img['image_path']); ?>" alt="Event Image" class="rounded-3xl w-full h-48 object-cover border border-emerald-950/5">
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="px-12 py-8 bg-emerald-950 flex flex-col sm:flex-row items-center justify-between">
                <p class="text-white/60 text-xs font-bold uppercase tracking-widest mb-4 sm:mb-0">Join us for this special occasion</p>
                <a href="<?php echo MAPS_URL; ?>" target="_blank" class="px-8 py-4 bg-white text-emerald-950 rounded-2xl text-xs font-bold hover:bg-emerald-50 transition-colors">
                    Get Directions
                </a>
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
