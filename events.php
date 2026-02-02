<?php
require_once 'includes/config.php';
require_once 'includes/header.php';
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
            <?php foreach (EVENTS as $event): ?>
                <div class="event-card group bg-white border border-emerald-950/10 rounded-[32px] overflow-hidden hover:border-emerald-950/30 hover:shadow-2xl hover:shadow-emerald-950/5 transition-all duration-500 shadow-sm"
                    data-category="<?php echo $event['category']; ?>">
                    <div class="p-8">
                        <div
                            class="w-14 h-14 rounded-2xl bg-emerald-950/5 flex items-center justify-center mb-8 group-hover:scale-110 transition-transform duration-300 text-emerald-950 border border-emerald-950/5">
                            <i class="fas <?php echo $event['icon']; ?> text-2xl"></i>
                        </div>
                        <span
                            class="text-[10px] uppercase tracking-widest text-emerald-950/60 font-bold px-3 py-1.5 rounded-full border border-emerald-950/10">
                            <?php echo $event['category']; ?>
                        </span>
                        <h3 class="text-2xl font-bold text-emerald-950 mt-6 mb-4 leading-tight">
                            <?php echo $event['title']; ?>
                        </h3>
                        <div class="space-y-3 mb-8">
                            <div class="flex items-center text-emerald-950/40 text-xs font-semibold">
                                <i class="far fa-calendar-alt w-5"></i>
                                <span><?php echo date('F d, Y', strtotime($event['date'])); ?></span>
                            </div>
                            <div class="flex items-center text-emerald-950/40 text-xs font-semibold">
                                <i class="far fa-clock w-5"></i>
                                <span><?php echo $event['time']; ?></span>
                            </div>
                        </div>
                        <button onclick='openModal(<?php echo json_encode($event); ?>)'
                            class="w-full py-4 bg-emerald-950/5 hover:bg-emerald-950 hover:text-white text-emerald-950 rounded-2xl text-xs font-bold transition-all duration-300 flex items-center justify-center group/btn border border-emerald-950/5">
                            View Details
                            <i
                                class="fas fa-arrow-right ml-2 opacity-0 -translate-x-2 group-hover/btn:opacity-100 group-hover/btn:translate-x-0 transition-all"></i>
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Modal Template -->
<div id="event-modal" class="fixed inset-0 z-[100] hidden items-center justify-center p-4 sm:p-6">
    <div class="absolute inset-0 bg-emerald-950/40 backdrop-blur-sm" onclick="closeModal()"></div>
    <div class="relative w-full max-w-xl bg-white rounded-[40px] shadow-2xl overflow-hidden animate-zoom-in">
        <div class="p-10" id="modal-content">
            <!-- Content Injected via JS -->
        </div>
        <div class="px-10 py-6 bg-emerald-50 border-t border-emerald-100 flex justify-end">
            <button onclick="closeModal()"
                class="px-8 py-3 bg-emerald-950 text-white rounded-2xl text-xs font-bold hover:bg-emerald-900 transition-colors">Close
                Details</button>
        </div>
    </div>
</div>

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

    function openModal(event) {
        const modal = document.getElementById('event-modal');
        const content = document.getElementById('modal-content');

        content.innerHTML = `
            <div class="w-20 h-20 rounded-[24px] bg-emerald-950/5 flex items-center justify-center mb-8 border border-emerald-950/5">
                <i class="fas ${event.icon} text-emerald-950 text-3xl"></i>
            </div>
            <h3 class="text-3xl font-display font-bold text-emerald-950 mb-3 tracking-tight">${event.title}</h3>
            <span class="inline-block px-4 py-2 rounded-full border border-emerald-950/10 text-emerald-950/60 text-[10px] font-bold uppercase tracking-widest mb-8">${event.category}</span>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-8">
                <div class="p-5 bg-white border border-emerald-950/10 rounded-3xl shadow-sm">
                    <p class="text-[10px] uppercase font-bold text-emerald-950/40 mb-2">Schedule</p>
                    <p class="text-emerald-950 font-bold">${event.date}</p>
                    <p class="text-emerald-950/60 text-sm font-medium">${event.time}</p>
                </div>
                <div class="p-5 bg-white border border-emerald-950/10 rounded-3xl shadow-sm">
                    <p class="text-[10px] uppercase font-bold text-emerald-950/40 mb-2">Venue</p>
                    <p class="text-emerald-950 font-bold">${event.location}</p>
                </div>
            </div>
            
            <div class="prose prose-emerald max-w-none">
                <p class="text-emerald-950/70 leading-relaxed font-medium text-sm italic">${event.description}</p>
            </div>
        `;

        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';
    }

    function closeModal() {
        const modal = document.getElementById('event-modal');
        modal.classList.remove('flex');
        modal.classList.add('hidden');
        document.body.style.overflow = 'auto';
    }
</script>

<style>
    @keyframes zoom-in {
        from {
            opacity: 0;
            transform: scale(0.95);
        }

        to {
            opacity: 1;
            transform: scale(1);
        }
    }

    .animate-zoom-in {
        animation: zoom-in 0.3s ease-out;
    }

    .event-card {
        transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
    }
</style>

<?php require_once 'includes/footer.php'; ?>