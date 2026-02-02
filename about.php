<?php
require_once 'includes/config.php';
require_once 'includes/header.php';
?>

<!-- Growing Together / About -->
<section class="py-24">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl mb-16">
            <h2 class="text-5xl font-display font-bold text-emerald-950 mb-6 tracking-tight">About IUT SIKS</h2>
            <p class="text-xl text-emerald-950/60 leading-relaxed font-medium">The Society of Islamic Knowledge Seekers
                is a student-led organization at the Islamic University of Technology, dedicated to fostering a balanced
                environment of spiritual growth and academic excellence.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-12 mb-24">
            <div class="p-10 bg-white border border-emerald-950/10 rounded-[40px] shadow-sm">
                <h3 class="text-2xl font-bold text-emerald-950 mb-4">Our Vision</h3>
                <p class="text-emerald-950/70 leading-relaxed text-sm font-medium italic">To be a leading society that
                    empowers
                    students to integrate Islamic principles into their professional and personal lives, creating a
                    generation of technically proficient and spiritually grounded leaders.</p>
            </div>
            <div class="p-10 bg-emerald-950 rounded-[40px] shadow-xl shadow-emerald-950/10">
                <h3 class="text-2xl font-bold text-white mb-4">Our Mission</h3>
                <p class="text-white/70 leading-relaxed text-sm font-medium italic">We strive to provide platforms for
                    spiritual learning, community service, and ethical development through organized events, lectures,
                    and interactive sessions.</p>
            </div>
        </div>

        <div class="text-center mb-16">
            <h2 class="text-3xl font-display font-bold text-emerald-950 mb-4">Growing Together</h2>
            <p class="text-emerald-950/40 max-w-2xl mx-auto font-medium italic">Building a stronger community every day
                through
                our core shared principles.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            <?php foreach (VALUES as $value): ?>
                <div
                    class="p-8 bg-white border border-emerald-950/10 rounded-[32px] hover:border-emerald-950/30 hover:shadow-2xl hover:shadow-emerald-950/5 transition-all duration-300 group">
                    <div
                        class="w-14 h-14 rounded-2xl bg-emerald-950/5 flex items-center justify-center mb-8 group-hover:scale-110 transition-transform duration-300 text-emerald-950">
                        <i class="fas <?php echo $value['icon']; ?> text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-emerald-950 mb-3">
                        <?php echo $value['title']; ?>
                    </h3>
                    <p class="text-emerald-950/50 text-sm leading-relaxed font-medium">
                        <?php echo $value['desc']; ?>
                    </p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>