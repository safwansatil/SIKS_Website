<?php
require_once 'includes/config.php';
require_once 'includes/header.php';
?>

<section class="py-24">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-4xl font-display font-bold text-emerald-950 mb-4 tracking-tight">Daily Reminders</h2>
            <p class="text-emerald-600/70 max-w-2xl mx-auto font-medium">Spiritual guidance and community updates to
                keep your heart connected.</p>
        </div>

        <div class="max-w-4xl mx-auto">
            <div
                class="p-12 bg-white border border-emerald-100 rounded-[48px] shadow-2xl shadow-emerald-100/30 mb-12 relative overflow-hidden">
                <div class="absolute top-0 right-0 p-8 opacity-5">
                    <i class="fas fa-quote-right text-8xl text-emerald-950"></i>
                </div>

                <div class="relative z-10">
                    <span
                        class="inline-block px-4 py-1.5 rounded-full bg-emerald-50 text-emerald-600 text-[10px] font-bold uppercase tracking-widest mb-8 border border-emerald-100">Ayah
                        of the Day</span>
                    <h3 class="text-3xl font-display font-medium text-emerald-950 leading-relaxed mb-10 italic">"And
                        seek help through patience and prayer, and indeed, it is difficult except for the humbly
                        submissive [to Allah]."</h3>
                    <div class="flex items-center space-x-4">
                        <div class="w-12 h-12 rounded-full bg-emerald-950 flex items-center justify-center text-white">
                            <i class="fas fa-book-open text-sm"></i>
                        </div>
                        <div>
                            <p class="text-emerald-950 font-bold">Surah Al-Baqarah</p>
                            <p class="text-emerald-600/60 text-xs font-bold uppercase tracking-widest">Verse 45</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div
                    class="p-8 bg-emerald-50 rounded-[32px] border border-emerald-100 italic text-emerald-800 text-sm font-medium leading-relaxed">
                    <p>Daily reminders are updated every morning by the SIKS committee. Stay tuned for more spiritual
                        insights and academic tips.</p>
                </div>
                <div
                    class="p-8 bg-white border border-emerald-100 rounded-[32px] flex items-center space-x-4 shadow-sm hover:shadow-lg transition-all duration-300">
                    <div
                        class="w-12 h-12 rounded-2xl bg-emerald-500 flex items-center justify-center text-white shadow-lg shadow-emerald-200">
                        <i class="fas fa-bell"></i>
                    </div>
                    <div>
                        <p class="text-emerald-950 font-bold text-sm">Notifications</p>
                        <p class="text-emerald-600/60 text-xs font-bold uppercase tracking-widest">Enabled for Jamaat
                            times</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>