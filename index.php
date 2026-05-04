<?php
require_once 'includes/config.php';
require_once 'includes/header.php';

// Data for home page highlights
$nextPrayerTimes = getPrayerTimes();
$currentHomePrayer = $nextPrayerTimes ? getCurrentPrayer($nextPrayerTimes) : null;
$heroSlides = getHeroSlides();

// Fetch random reminders
$randomAyat = getRandomAyat();
$randomHadith = getRandomHadith();

// Fetch upcoming events for showcase
$upcomingEvents = getEvents(false, 3); // Get next 3 upcoming events
?>

<!-- Hero Section with Image Carousel -->
<section id="home" class="relative h-screen flex items-center justify-center overflow-hidden -mt-24">
    <!-- Carousel Background -->
    <?php if ($heroSlides && count($heroSlides) > 0): ?>
        <div id="hero-carousel" class="absolute inset-0 z-0">
            <?php foreach ($heroSlides as $index => $slide): ?>
                <div class="carousel-slide absolute inset-0 transition-all duration-1000 ease-in-out <?php echo $index === 0 ? 'carousel-slide-active' : 'carousel-slide-hidden'; ?>"
                     data-slide="<?php echo $index; ?>">
                    <img src="<?php echo htmlspecialchars($slide['image_path']); ?>" 
                         alt="Hero Background"
                         class="w-full h-full object-cover">
                    <!-- Graceful Description Overlay -->
                    <?php if (!empty($slide['subtitle'])): ?>
                        <div class="absolute bottom-32 left-8 md:left-16 z-20 max-w-lg animate-fade-in-up">
                            <div class="bg-black/20 backdrop-blur-md border border-white/10 p-6 rounded-3xl">
                                <p class="text-white/90 text-sm md:text-base font-medium leading-relaxed italic">
                                    "<?php echo htmlspecialchars($slide['subtitle']); ?>"
                                </p>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
            <!-- Dynamic Dark Overlay -->
            <div class="absolute inset-0 bg-gradient-to-b from-black/60 via-black/40 to-black/80 z-10"></div>
        </div>

            <!-- Hero Carousel Navigation Buttons -->
            <div class="absolute inset-y-0 left-4 md:left-8 z-30 hidden md:flex items-center">
                <button onclick="prevSlide()" class="w-14 h-14 rounded-full bg-black/10 hover:bg-black/30 backdrop-blur-md border border-white/10 text-white flex items-center justify-center transition-all group">
                    <i class="fas fa-chevron-left group-hover:-translate-x-1 transition-transform"></i>
                </button>
            </div>
            <div class="absolute inset-y-0 right-4 md:right-8 z-30 hidden md:flex items-center">
                <button onclick="nextSlide()" class="w-14 h-14 rounded-full bg-black/10 hover:bg-black/30 backdrop-blur-md border border-white/10 text-white flex items-center justify-center transition-all group">
                    <i class="fas fa-chevron-right group-hover:translate-x-1 transition-transform"></i>
                </button>
            </div>

            <div class="absolute bottom-12 right-12 z-30 flex flex-col space-y-3">
                <?php foreach ($heroSlides as $index => $slide): ?>
                    <button class="carousel-dot-v <?php echo $index === 0 ? 'carousel-dot-v-active' : ''; ?>"
                            onclick="goToSlide(<?php echo $index; ?>)"
                            data-dot="<?php echo $index; ?>"></button>
                <?php endforeach; ?>
            </div>
    <?php else: ?>
        <!-- Fallback: decorative gradient background if no slides -->
        <div class="absolute inset-0 z-0" style="background: linear-gradient(135deg, #0a0a0a 0%, #1a1a1a 50%, #0a0a0a 100%);">
            <div class="absolute top-[-10%] left-[-10%] w-[40%] h-[40%] bg-white/5 blur-[120px] rounded-full"></div>
            <div class="absolute bottom-[-10%] right-[-10%] w-[40%] h-[40%] bg-white/3 blur-[120px] rounded-full"></div>
        </div>
    <?php endif; ?>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-20 text-center">
        <h1 class="text-7xl md:text-9xl font-display font-bold mb-8 leading-tight tracking-tight text-white drop-shadow-2xl">
            IUT <span style="color: #88c9a1;">SIKS</span>
        </h1>

        <p class="text-lg md:text-xl max-w-2xl mx-auto mb-12 leading-relaxed font-medium text-white/70 drop-shadow-lg">
            Fostering spiritual growth and academic excellence at Islamic University of Technology.
        </p>

        <div class="flex flex-col sm:flex-row items-center justify-center space-y-4 sm:space-y-0 sm:space-x-6">
            <a href="events.php"
                class="w-full sm:w-56 px-10 py-5 border border-transparent bg-white text-emerald-950 hover:bg-emerald-50 rounded-2xl font-bold transition-all duration-300 shadow-2xl shadow-black/10 flex items-center justify-center group">
                Explore Events
                <i class="fas fa-chevron-right ml-3 text-xs transition-transform group-hover:translate-x-1"></i>
            </a>
            <a href="articles.php"
                class="w-full sm:w-56 px-10 py-5 bg-white/10 border border-white/20 text-white hover:bg-white/20 backdrop-blur-md rounded-2xl font-bold transition-all duration-300 flex items-center justify-center">
                Read Articles
            </a>
        </div>
    </div>
</section>

<!-- Hero Carousel JS -->
<?php if ($heroSlides && count($heroSlides) > 1): ?>
<script>
    let currentSlide = 0;
    const totalSlides = <?php echo count($heroSlides); ?>;
    let slideInterval;

    function goToSlide(index) {
        const slides = document.querySelectorAll('.carousel-slide');
        const dots = document.querySelectorAll('.carousel-dot-v');
        
        slides.forEach(s => { s.classList.remove('carousel-slide-active'); s.classList.add('carousel-slide-hidden'); });
        dots.forEach(d => d.classList.remove('carousel-dot-v-active'));
        
        slides[index].classList.remove('carousel-slide-hidden');
        slides[index].classList.add('carousel-slide-active');
        dots[index].classList.add('carousel-dot-v-active');
        
        currentSlide = index;
        resetInterval();
    }

    function prevSlide() {
        goToSlide((currentSlide - 1 + totalSlides) % totalSlides);
    }

    function nextSlide() {
        goToSlide((currentSlide + 1) % totalSlides);
    }

    function resetInterval() {
        clearInterval(slideInterval);
        slideInterval = setInterval(nextSlide, 7000);
    }

    slideInterval = setInterval(nextSlide, 7000);
</script>
<?php endif; ?>

<!-- Dynamic Prayer Schedule Section -->
<section id="upcoming" class="py-20 text-white relative overflow-hidden" style="background-color: #062021;">
    <!-- Abstract Background Elements -->
    <div class="absolute top-0 left-0 w-full h-full opacity-5 pointer-events-none">
        <div class="absolute top-[-20%] right-[-10%] w-[500px] h-[500px] rounded-full bg-white blur-[150px]"></div>
        <div class="absolute bottom-[-20%] left-[-10%] w-[500px] h-[500px] rounded-full bg-white blur-[150px]"></div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="flex flex-col md:flex-row items-center justify-between mb-16 space-y-8 md:space-y-0">
            <div class="text-left">
                <div
                    class="inline-flex items-center space-x-2 px-3 py-1 rounded-full bg-white/5 border border-white/10 mb-4">
                    <span class="text-[9px] font-black uppercase tracking-widest text-white/50">Jamaat Timetable</span>
                </div>
                <h2 class="text-4xl font-display font-bold text-white tracking-tight"><?php echo MASJID_NAME; ?></h2>
            </div>

            <!-- Redesigned Minimal Countdown Capsule -->
            <div
                class="inline-flex items-center bg-white/5 border border-white/10 rounded-full px-6 py-3 space-x-6 backdrop-blur-md">
                <div class="flex items-center space-x-3">
                    <span class="relative flex h-2 w-2">
                        <span
                            class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-20"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                    </span>
                    <span class="text-[10px] font-black uppercase tracking-widest text-white/40">Next:</span>
                    <span class="text-[11px] font-bold text-white" id="section-next-name">...</span>
                </div>
                <div class="h-4 w-px bg-white/10"></div>
                <div class="flex items-center space-x-3">
                    <i class="fas fa-clock text-emerald-500/50 text-[10px]"></i>
                    <span class="text-sm font-mono font-bold text-white tracking-widest"
                        id="section-countdown-timer">--:--:--</span>
                </div>
            </div>
        </div>

        <!-- Prayer Grid - Single Line -->
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
            <?php if ($nextPrayerTimes): ?>
                <?php foreach ($nextPrayerTimes as $prayer):
                    $isActiveHome = ($currentHomePrayer === $prayer['name']);
                    ?>
                    <div
                        class="relative group p-6 rounded-[32px] transition-all duration-500 border <?php echo $isActiveHome ? 'bg-white text-emerald-950 border-white shadow-[0_20px_40px_rgba(0,0,0,0.3)] scale-105' : 'bg-[#041a1b] border-white/5 hover:border-white/10 hover:bg-[#082a2b]'; ?>">

                        <p
                            class="text-[9px] font-black uppercase tracking-widest mb-4 <?php echo $isActiveHome ? 'text-emerald-950/40' : 'text-white/30'; ?>">
                            <?php echo $prayer['name']; ?>
                        </p>
                        <p class="text-2xl font-display font-bold">
                            <?php echo $prayer['time']; ?>
                        </p>
                        <div
                            class="mt-4 flex items-center <?php echo $isActiveHome ? 'text-emerald-950/20' : 'text-white/10'; ?> text-[7px] font-bold uppercase tracking-[0.2em]">
                            <i class="fas fa-clock mr-1.5"></i>
                            <span>Jamaat</span>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-span-full py-12 text-center border border-dashed border-white/10 rounded-[40px]">
                    <p class="text-white/40 italic">Schedule currently unavailable.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<script>
    // Sync the section countdown with the header countdown
    function updateSectionCountdown() {
        if (!prayers || prayers.length === 0) return;
        const now = new Date();
        const todayStr = now.getFullYear() + '-' + (now.getMonth() + 1) + '-' + now.getDate() + ' ';
        let nextPrayer = null;
        let minDiff = Infinity;

        prayers.forEach(p => {
            const pTime = new Date(todayStr + p.time);
            let diff = pTime - now;
            if (diff < 0) {
                pTime.setDate(pTime.getDate() + 1);
                diff = pTime - now;
            }
            if (diff < minDiff) {
                minDiff = diff;
                nextPrayer = p;
            }
        });

        if (nextPrayer) {
            document.getElementById('section-next-name').innerText = nextPrayer.name;
            const h = Math.floor(minDiff / 3600000);
            const m = Math.floor((minDiff % 3600000) / 60000);
            const s = Math.floor((minDiff % 60000) / 1000);
            document.getElementById('section-countdown-timer').innerText = `${h.toString().padStart(2, '0')}:${m.toString().padStart(2, '0')}:${s.toString().padStart(2, '0')}`;
        }
    }
    setInterval(updateSectionCountdown, 1000);
    updateSectionCountdown();
</script>

<!-- Daily Reminders Section -->
<section id="reminders" class="py-32 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-20">
            <h2 class="text-4xl font-display font-bold text-emerald-950 mb-6 tracking-tight">Daily Reminders</h2>
            <p class="text-emerald-950/40 max-w-2xl mx-auto font-medium italic">Spiritual guidance to keep your heart connected.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
            <!-- Ayat Reminder -->
            <div class="card-professional p-10 relative overflow-hidden">
                <div class="absolute top-0 right-0 p-8 opacity-5">
                    <i class="fas fa-book-quran text-7xl text-emerald-950"></i>
                </div>
                <span class="inline-block px-4 py-1.5 rounded-full bg-emerald-50 text-emerald-700 text-[10px] font-bold uppercase tracking-widest mb-6 border border-emerald-100">Ayat of the Day</span>
                <?php if ($randomAyat): ?>
                    <p class="text-xl font-display font-medium text-emerald-950 leading-relaxed mb-8 italic">"<?php echo htmlspecialchars($randomAyat['text']); ?>"</p>
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-full bg-emerald-950 flex items-center justify-center text-white">
                            <i class="fas fa-leaf text-xs"></i>
                        </div>
                        <div>
                            <p class="text-emerald-950 font-bold text-sm">Surah <?php echo $randomAyat['surah']; ?></p>
                            <p class="text-emerald-600/60 text-[10px] font-bold uppercase tracking-widest">Verse <?php echo $randomAyat['ayah']; ?></p>
                        </div>
                    </div>
                <?php else: ?>
                    <p class="text-emerald-950/40 italic">Unable to fetch Ayat at the moment.</p>
                <?php endif; ?>
            </div>

            <!-- Hadith Reminder -->
            <div class="card-professional p-10 relative overflow-hidden shadow-xl shadow-emerald-900/5">
                <div class="absolute top-0 right-0 p-8 opacity-5">
                    <i class="fas fa-quote-right text-7xl text-emerald-950"></i>
                </div>
                <span class="inline-block px-4 py-1.5 rounded-full bg-emerald-50 text-emerald-600 text-[10px] font-bold uppercase tracking-widest mb-6 border border-emerald-100">Hadith of the Day</span>
                <?php if ($randomHadith): ?>
                    <p class="text-xl font-display font-medium text-emerald-950 leading-relaxed mb-8 italic">"<?php echo htmlspecialchars($randomHadith['text']); ?>"</p>
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-full bg-emerald-600 flex items-center justify-center text-white">
                            <i class="fas fa-heart text-xs"></i>
                        </div>
                        <div>
                            <p class="text-emerald-950 font-bold text-sm"><?php echo $randomHadith['source']; ?></p>
                            <p class="text-emerald-600/60 text-[10px] font-bold uppercase tracking-widest">Random Selection</p>
                        </div>
                    </div>
                <?php else: ?>
                    <p class="text-emerald-950/40 italic">Unable to fetch Hadith at the moment.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<!-- Upcoming Events Showcase Section -->
<?php if ($upcomingEvents && count($upcomingEvents) > 0): ?>
<section class="py-24 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between mb-14 flex-wrap gap-4">
            <div>
                <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full bg-emerald-50 border border-emerald-100 mb-4">
                    <span class="text-[9px] font-black uppercase tracking-widest text-emerald-600">Join Us</span>
                </div>
                <h2 class="text-3xl sm:text-4xl font-display font-bold text-emerald-950 tracking-tight">Upcoming Events</h2>
            </div>
            <a href="events.php" class="inline-flex items-center px-6 py-3 rounded-full bg-emerald-950/5 border border-emerald-950/10 text-emerald-950/60 text-sm font-bold hover:bg-emerald-950/10 transition-colors">
                View All <i class="fas fa-arrow-right ml-2 text-xs"></i>
            </a>
        </div>

        <!-- Responsive Grid - Same as About Page -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 md:gap-8">
            <?php foreach ($upcomingEvents as $evt): ?>
                <a href="event_details.php?id=<?php echo $evt['id']; ?>" class="group block transition-all duration-300 hover:-translate-y-2">
                    <div class="rounded-3xl overflow-hidden bg-emerald-950 border border-emerald-950/10 shadow-lg hover:shadow-xl transition-all duration-300 h-full">
                        <!-- Card Image -->
                        <div class="h-52 sm:h-56 overflow-hidden">
                            <?php if (!empty($evt['cover_image'])): ?>
                                <img src="<?php echo htmlspecialchars($evt['cover_image']); ?>" 
                                     alt="<?php echo htmlspecialchars($evt['name']); ?>"
                                     class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                            <?php else: ?>
                                <div class="w-full h-full bg-emerald-900/50 flex items-center justify-center">
                                    <i class="fas <?php echo htmlspecialchars($evt['logo'] ?? 'fa-calendar'); ?> text-5xl text-white/20"></i>
                                </div>
                            <?php endif; ?>
                        </div>
                        <!-- Card Info -->
                        <div class="p-5">
                            <p class="text-[10px] font-bold uppercase tracking-widest text-white/30 mb-2">
                                <?php echo date('M d, Y', strtotime($evt['event_date'])); ?>
                            </p>
                            <h3 class="text-white font-bold text-lg leading-snug line-clamp-2 group-hover:text-emerald-300 transition-colors">
                                <?php echo htmlspecialchars($evt['name']); ?>
                            </h3>
                            <?php if (!empty($evt['venue'])): ?>
                                <p class="text-white/30 text-xs font-medium mt-2">
                                    <i class="fas fa-map-marker-alt mr-1"></i>
                                    <?php echo htmlspecialchars($evt['venue']); ?>
                                </p>
                            <?php endif; ?>
                        </div>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>


<script>
(function() {
    const track = document.getElementById('events-track');
    if (!track) return;

    const slides = track.querySelectorAll('.events-slide');
    const perPage = window.innerWidth < 768 ? 1 : 3;
    const totalPages = Math.ceil(slides.length / perPage);
    let current = 0;
    let autoInterval;

    window.eventTotalPages = totalPages;
    window.currentEventSlide = 0;

    window.goToEventSlide = function(page) {
        current = page;
        window.currentEventSlide = page;
        const slideWidth = slides[0].offsetWidth + 24; // width + gap
        const offset = page * perPage * slideWidth;
        track.style.transform = `translateX(-${offset}px)`;

        // Update dots
        document.querySelectorAll('.events-dot').forEach(d => {
            d.classList.remove('bg-emerald-950', 'w-6');
            d.classList.add('bg-emerald-950/20');
        });
        const activeDot = document.querySelector(`[data-edot="${page}"]`);
        if (activeDot) {
            activeDot.classList.add('bg-emerald-950', 'w-6');
            activeDot.classList.remove('bg-emerald-950/20');
        }

        resetAutoSlide();
    };

    function nextSlide() {
        goToEventSlide((current + 1) % totalPages);
    }

    function resetAutoSlide() {
        clearInterval(autoInterval);
        autoInterval = setInterval(nextSlide, 4000);
    }

    autoInterval = setInterval(nextSlide, 4000);
})();
</script>
<?php endif; ?>

<?php require_once 'includes/footer.php'; ?>