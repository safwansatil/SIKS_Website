<?php
require_once 'includes/config.php';
require_once 'includes/header.php';

$events = getEvents();
?>

<section class="py-24">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-4xl font-display font-bold text-emerald-950 mb-4 tracking-tight">Society Events</h2>
            <p class="text-emerald-950/40 max-w-2xl mx-auto font-medium italic">Stay updated with our upcoming programs,
                workshops, and sports activities.</p>
        </div>

        <!-- Filter Tabs -->
        <div class="flex justify-center mb-16">
            <div class="inline-flex p-1.5 bg-white rounded-2xl border border-emerald-950/10 shadow-sm">
                <button onclick="filterEvents('All')" id="tab-All"
                    class="tab-btn px-8 py-3 rounded-xl text-xs font-bold transition-all duration-300 bg-emerald-950 text-white shadow-lg shadow-emerald-950/20">All
                    Programs</button>
                <button onclick="filterEvents('Community')" id="tab-Community"
                    class="tab-btn px-8 py-3 rounded-xl text-xs font-bold transition-all duration-300 text-emerald-950/60 hover:bg-emerald-950 hover:text-white">Community</button>
                <button onclick="filterEvents('Sports')" id="tab-Sports"
                    class="tab-btn px-8 py-3 rounded-xl text-xs font-bold transition-all duration-300 text-emerald-950/60 hover:bg-emerald-950 hover:text-white">Sports</button>
            </div>
        </div>

        <!-- Events Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8" id="events-grid">
            <?php foreach ($events as $event): ?>
                <div class="event-card group bg-white border border-emerald-950/10 rounded-[32px] overflow-hidden hover:border-emerald-950/30 hover:shadow-2xl hover:shadow-emerald-950/5 transition-all duration-500 shadow-sm"
                    data-category="<?php echo $event['category']; ?>">
                    <div class="p-8">
                        <div
                            class="w-14 h-14 rounded-2xl bg-emerald-950/5 flex items-center justify-center mb-8 group-hover:scale-110 transition-transform duration-300 text-emerald-950 border border-emerald-950/5">
                            <i class="fas <?php echo $event['logo'] ?: 'fa-calendar'; ?> text-2xl"></i>
                        </div>
                        <span
                            class="text-[10px] uppercase tracking-widest text-emerald-950/60 font-bold px-3 py-1.5 rounded-full border border-emerald-950/10">
                            <?php echo $event['category']; ?>
                        </span>
                        <h3 class="text-2xl font-bold text-emerald-950 mt-6 mb-4 leading-tight">
                            <?php echo htmlspecialchars($event['name']); ?>
                        </h3>
                        <div class="space-y-3 mb-8">
                            <div class="flex items-center text-emerald-950/40 text-xs font-semibold">
                                <i class="far fa-calendar-alt w-5"></i>
                                <span><?php echo date('F d, Y', strtotime($event['event_date'])); ?></span>
                            </div>
                            <div class="flex items-center text-emerald-950/40 text-xs font-semibold">
                                <i class="far fa-clock w-5"></i>
                                <span><?php echo htmlspecialchars($event['event_time']); ?></span>
                            </div>
                        </div>
                        <a href="event_details.php?id=<?php echo $event['id']; ?>"
                            class="w-full py-4 bg-emerald-950/5 hover:bg-emerald-950 hover:text-white text-emerald-950 rounded-2xl text-xs font-bold transition-all duration-300 flex items-center justify-center group/btn border border-emerald-950/5">
                            View Details
                            <i
                                class="fas fa-arrow-right ml-2 opacity-0 -translate-x-2 group-hover/btn:opacity-100 group-hover/btn:translate-x-0 transition-all"></i>
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<script>
    function filterEvents(category) {
        const cards = document.querySelectorAll('.event-card');
        const buttons = document.querySelectorAll('.tab-btn');

        buttons.forEach(btn => {
            if (btn.id === 'tab-' + category) {
                btn.classList.add('bg-emerald-950', 'text-white', 'shadow-lg', 'shadow-emerald-950/20');
                btn.classList.remove('text-emerald-950/60', 'hover:bg-emerald-950', 'hover:text-white');
            } else {
                btn.classList.remove('bg-emerald-950', 'text-white', 'shadow-lg', 'shadow-emerald-950/20');
                btn.classList.add('text-emerald-950/60', 'hover:bg-emerald-950', 'hover:text-white');
            }
        });

        cards.forEach(card => {
            if (category === 'All' || card.dataset.category === category) {
                card.style.display = 'block';
                setTimeout(() => card.style.opacity = '1', 10);
            } else {
                card.style.opacity = '0';
                setTimeout(() => card.style.display = 'none', 300);
            }
        });
    }
</script>

<style>
    .event-card {
        transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
    }
</style>

<?php require_once 'includes/footer.php'; ?>