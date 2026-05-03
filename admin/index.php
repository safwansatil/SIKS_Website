<?php
$activeNav = 'dashboard';
$pageTitle = 'Dashboard';
require_once 'header.php';

// Stats
$countArticles = $pdo->query("SELECT COUNT(*) FROM articles")->fetchColumn();
$countEvents = $pdo->query("SELECT COUNT(*) FROM events")->fetchColumn();
$countHero = $pdo->query("SELECT COUNT(*) FROM hero_slides")->fetchColumn();
?>

<div class="page-header">
    <h1 class="page-title">Welcome Back, Admin</h1>
    <div style="font-weight: 500; color: var(--text-muted);"><?php echo date('l, F d, Y'); ?></div>
</div>

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1.5rem; margin-bottom: 3rem;">
    <div class="card" style="padding: 1.5rem; margin-bottom: 0;">
        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1rem;">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: #ecfdf5; color: #059669; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
                <i class="fas fa-pen-nib"></i>
            </div>
            <span class="badge badge-success">+2 this week</span>
        </div>
        <div style="font-size: 2rem; font-weight: 700; color: var(--secondary);"><?php echo $countArticles; ?></div>
        <div style="font-weight: 600; color: var(--text-muted); font-size: 0.875rem;">Total Articles</div>
    </div>

    <div class="card" style="padding: 1.5rem; margin-bottom: 0;">
        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1rem;">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: #eff6ff; color: #2563eb; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
                <i class="fas fa-calendar-alt"></i>
            </div>
        </div>
        <div style="font-size: 2rem; font-weight: 700; color: var(--secondary);"><?php echo $countEvents; ?></div>
        <div style="font-weight: 600; color: var(--text-muted); font-size: 0.875rem;">Published Events</div>
    </div>

    <div class="card" style="padding: 1.5rem; margin-bottom: 0;">
        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1rem;">
            <div style="width: 48px; height: 48px; border-radius: 12px; background: #fff7ed; color: #ea580c; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
                <i class="fas fa-images"></i>
            </div>
        </div>
        <div style="font-size: 2rem; font-weight: 700; color: var(--secondary);"><?php echo $countHero; ?></div>
        <div style="font-weight: 600; color: var(--text-muted); font-size: 0.875rem;">Hero Slides</div>
    </div>
</div>

<div class="grid-2">
    <div class="card">
        <h3 style="font-family: 'Outfit', sans-serif; margin-bottom: 1.5rem;">Quick Actions</h3>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
            <a href="manage_articles.php?mode=add" class="btn btn-secondary" style="justify-content: flex-start; padding: 1rem;">
                <i class="fas fa-plus-circle text-primary"></i> New Article
            </a>
            <a href="manage_events.php?mode=add" class="btn btn-secondary" style="justify-content: flex-start; padding: 1rem;">
                <i class="fas fa-calendar-plus text-primary"></i> New Event
            </a>
            <a href="manage_prayers.php" class="btn btn-secondary" style="justify-content: flex-start; padding: 1rem;">
                <i class="fas fa-clock text-primary"></i> Update Prayers
            </a>
            <a href="manage_hero.php" class="btn btn-secondary" style="justify-content: flex-start; padding: 1rem;">
                <i class="fas fa-images text-primary"></i> Manage Hero
            </a>
        </div>
    </div>

    <div class="card">
        <h3 style="font-family: 'Outfit', sans-serif; margin-bottom: 1.5rem;">System Information</h3>
        <div style="display: flex; flex-direction: column; gap: 0.75rem;">
            <div style="display: flex; justify-content: space-between; font-size: 0.9rem;">
                <span style="color: var(--text-muted);">PHP Version:</span>
                <span style="font-weight: 600;"><?php echo phpversion(); ?></span>
            </div>
            <div style="display: flex; justify-content: space-between; font-size: 0.9rem;">
                <span style="color: var(--text-muted);">Database:</span>
                <span style="font-weight: 600;">MySQL (PDO)</span>
            </div>
            <div style="display: flex; justify-content: space-between; font-size: 0.9rem;">
                <span style="color: var(--text-muted);">Server Time:</span>
                <span style="font-weight: 600;"><?php echo date('H:i:s'); ?></span>
            </div>
            <div style="margin-top: 1rem; padding-top: 1rem; border-top: 1px solid var(--border);">
                <p style="font-size: 0.8rem; color: var(--text-muted); line-height: 1.4;">
                    If you encounter any issues with image uploads, ensure that the <code>uploads/</code> directory has write permissions (755).
                </p>
            </div>
        </div>
    </div>
</div>

<?php require_once 'footer.php'; ?>
