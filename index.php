<?php
require_once 'includes/config.php';
require_once 'includes/header.php';

// Data for home page highlights
$nextPrayerTimes = getPrayerTimes();
$currentHomePrayer = $nextPrayerTimes ? getCurrentPrayer($nextPrayerTimes) : null;

// Fetch random reminders
$randomAyat = getRandomAyat();
$randomHadith = getRandomHadith();
?>

<!-- Hero Section -->
<section id="home" class="relative min-h-[90vh] flex items-center justify-center overflow-hidden">
    <!-- Background Decor -->
    <div class="absolute inset-0 z-0">
        <div class="absolute top-[-10%] left-[-10%] w-[40%] h-[40%] bg-emerald-50 blur-[120px] rounded-full"></div>
        <div class="absolute bottom-[-10%] right-[-10%] w-[40%] h-[40%] bg-emerald-100 blur-[120px] rounded-full"></div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center">
        <h1 class="text-7xl md:text-9xl font-display font-bold text-emerald-950 mb-8 leading-tight tracking-tight">
            IUT <span class="text-black">SIKS</span>
        </h1>

        <p class="text-emerald-950/60 text-lg md:text-xl max-w-2xl mx-auto mb-12 leading-relaxed font-medium">
            Fostering spiritual growth and academic excellence at Islamic University of Technology.
        </p>

        <div class="flex flex-col sm:flex-row items-center justify-center space-y-4 sm:space-y-0 sm:space-x-6">
            <a href="events.php"
                class="w-full sm:w-auto px-10 py-5 bg-emerald-950 hover:bg-black text-white rounded-2xl font-bold transition-all duration-300 shadow-2xl shadow-black/10 flex items-center justify-center group">
                Explore Events
                <i class="fas fa-chevron-right ml-3 text-xs transition-transform group-hover:translate-x-1"></i>
            </a>
            <a href="articles.php"
                class="w-full sm:w-auto px-10 py-5 bg-white border border-black/5 hover:border-black/20 text-emerald-950 rounded-2xl font-bold transition-all duration-300 flex items-center justify-center">
                Read Articles
            </a>
        </div>
    </div>
</section>

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

<!-- Daily Reminders Section -->
<section id="reminders" class="py-32 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-20">
            <h2 class="text-4xl font-display font-bold text-emerald-950 mb-6 tracking-tight">Daily Reminders</h2>
            <p class="text-emerald-950/40 max-w-2xl mx-auto font-medium italic">Spiritual guidance to keep your heart connected.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
            <!-- Ayat Reminder -->
            <div class="p-10 bg-emerald-50 border border-emerald-100 rounded-[40px] shadow-sm relative overflow-hidden">
                <div class="absolute top-0 right-0 p-8 opacity-5">
                    <i class="fas fa-book-quran text-7xl text-emerald-950"></i>
                </div>
                <span class="inline-block px-4 py-1.5 rounded-full bg-emerald-100 text-emerald-700 text-[10px] font-bold uppercase tracking-widest mb-6">Ayat of the Day</span>
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
            <div class="p-10 bg-white border border-emerald-100 rounded-[40px] shadow-xl shadow-emerald-900/5 relative overflow-hidden">
                <div class="absolute top-0 right-0 p-8 opacity-5">
                    <i class="fas fa-quote-right text-7xl text-emerald-950"></i>
                </div>
                <span class="inline-block px-4 py-1.5 rounded-full bg-emerald-50 text-emerald-600 text-[10px] font-bold uppercase tracking-widest mb-6">Hadith of the Day</span>
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

<script>
    // Sync the section countdown with the header countdown
    const prayers = <?php echo json_encode($nextPrayerTimes); ?>;
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

<style>
    @keyframes fade-in {
        from {
            opacity: 0;
            transform: translateY(20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .animate-fade-in {
        animation: fade-in 1s cubic-bezier(0.16, 1, 0.3, 1) forwards;
    }
</style>

<?php require_once 'includes/footer.php'; ?>