</main>

<!-- Footer -->
<footer class="bg-emerald-950 text-white pt-24 pb-12 border-t border-white/5">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-12 mb-20">
            <!-- Brand Area -->
            <div class="col-span-2 lg:col-span-2 space-y-8">
                <div class="flex items-center space-x-4 group cursor-pointer" onclick="window.location.href='index.php'">
                    <img src="assets/images/logofooter.png?v=2" alt="Society of Islamic Knowledge Seekers Logo" class="h-12 w-auto group-hover:scale-110 transition-transform duration-500">
                    <div class="flex flex-col">
                        <span class="text-xs font-black tracking-widest text-white uppercase leading-tight">
                            Society of Islamic <br> Knowledge Seekers
                        </span>
                    </div>
                </div>
                <p class="text-white/40 text-[11px] leading-relaxed max-w-xs font-medium italic">
                    Fostering spiritual growth, intellectual discourse, and community bonding within the IUT campus
                    since its inception.
                </p>
                <div class="flex space-x-3">
                    <a href="<?php echo YOUTUBE_URL; ?>" target="_blank"
                        class="w-9 h-9 rounded-full bg-white/5 border border-white/10 flex items-center justify-center text-white/50 hover:bg-white hover:text-emerald-950 hover:border-white transition-all duration-500">
                        <i class="fab fa-youtube text-xs"></i>
                    </a>
                    <a href="<?php echo FACEBOOK_URL; ?>" target="_blank"
                        class="w-9 h-9 rounded-full bg-white/5 border border-white/10 flex items-center justify-center text-white/50 hover:bg-white hover:text-emerald-950 hover:border-white transition-all duration-500">
                        <i class="fab fa-facebook-f text-xs"></i>
                    </a>
                </div>
            </div>

            <!-- Society Column -->
            <div class="space-y-6">
                <h3 class="text-white/30 font-black text-[9px] uppercase tracking-[0.2em]">Society</h3>
                <ul class="space-y-4">
                    <li><a href="about.php"
                            class="text-white/60 hover:text-white text-[11px] font-bold transition-all duration-300">About
                            SIKS</a></li>
                </ul>
            </div>

            <!-- Resources Column -->
            <div class="space-y-6">
                <h3 class="text-white/30 font-black text-[9px] uppercase tracking-[0.2em]">Resources</h3>
                <ul class="space-y-4">
                    <li><a href="index.php#reminders"
                            class="text-white/60 hover:text-white text-[11px] font-bold transition-all duration-300">Daily
                            Reminders</a></li>
                    <li><a href="index.php#upcoming"
                            class="text-white/60 hover:text-white text-[11px] font-bold transition-all duration-300">Jamaat
                            Times</a></li>
                </ul>
            </div>

            <!-- Connect Column -->
            <div class="space-y-6">
                <h3 class="text-white/30 font-black text-[9px] uppercase tracking-[0.2em]">Connect</h3>
                <ul class="space-y-4">
                    <li><a href="events.php"
                            class="text-white/60 hover:text-white text-[11px] font-bold transition-all duration-300">Upcoming
                            Events</a></li>
                    <li><a href="mailto:siks@iut-dhaka.edu"
                            class="text-white/60 hover:text-white text-[11px] font-bold transition-all duration-300">Email
                            Us</a></li>
                    <li>
                        <div class="mt-4 pt-4 border-t border-white/5">
                            <p class="text-white/20 text-[9px] font-bold uppercase tracking-widest leading-relaxed">
                                <?php echo IUT_ADDRESS; ?>
                            </p>
                        </div>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Bottom Copyright Bar -->
<div class="pt-8 border-t border-white/5 flex flex-col md:flex-row justify-between items-center space-y-4 md:space-y-0">
    <p class="text-white/20 text-[8px] font-black uppercase tracking-[0.3em]">
        &copy; <?php echo date('Y'); ?> Society of Islamic Knowledge Seekers.
    </p>
    <div class="flex items-center space-x-1">
        <span class="text-white/10 text-[8px] font-black uppercase tracking-[0.2em]">Any suggestions?</span>
        <a href="https://github.com/Tajwarbot" target="_blank"
            class="text-white/20 hover:text-white text-[8px] font-black uppercase tracking-[0.2em] transition-colors">Tajwar</a>
        <span class="text-white/10 text-[8px]">&</span>
        <a href="https://github.com/safwansatil" target="_blank"
            class="text-white/20 hover:text-white text-[8px] font-black uppercase tracking-[0.2em] transition-colors">Safwan</a>
    </div>
</div>
    </div>
</footer>

<!-- Scripts -->
<script>
    // Smooth scroll for internal links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            document.querySelector(this.getAttribute('href')).scrollIntoView({
                behavior: 'smooth'
            });
        });
    });

    // Tab system logic (placeholder for events)
    function switchTab(categoryId) {
        // Logic to handle event filtering
    }
</script>
</body>

</html>