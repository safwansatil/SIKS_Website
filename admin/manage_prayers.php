<?php
$activeNav = 'prayers';
$pageTitle = 'Prayer Times';
require_once 'header.php';

$message = '';
$messageType = 'success';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        foreach ($_POST['prayer'] as $id => $time) {
            $stmt = $pdo->prepare("UPDATE prayer_times SET prayer_time = ? WHERE id = ?");
            $stmt->execute([$time, $id]);
        }
        $message = "Prayer times updated successfully.";
    } catch (PDOException $e) {
        $message = "Error: " . $e->getMessage();
        $messageType = 'error';
    }
}

$prayers = getPrayerTimes();
// We need IDs too, so we'll fetch manually here
$stmt = $pdo->query("SELECT * FROM prayer_times ORDER BY id ASC");
$prayersFull = $stmt->fetchAll();
?>

<div class="page-header">
    <h1 class="page-title">Jama'at Times</h1>
</div>

<?php if ($message): ?>
    <div class="alert alert-<?php echo $messageType; ?>">
        <i class="fas <?php echo $messageType === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'; ?>"></i>
        <?php echo htmlspecialchars($message); ?>
    </div>
<?php endif; ?>

<div class="card" style="max-width: 600px;">
    <h2 style="font-family: 'Outfit', sans-serif; margin-bottom: 2rem; font-size: 1.25rem;">Update Today's Times</h2>
    <form method="POST">
        <div style="display: flex; flex-direction: column; gap: 1rem;">
            <?php foreach ($prayersFull as $p): ?>
                <div style="display: grid; grid-template-columns: 1fr 2fr; align-items: center; gap: 1rem; padding: 1rem; background: #f8fafc; border-radius: 0.75rem; border: 1px solid var(--border);">
                    <label style="margin-bottom: 0; color: var(--primary-dark);"><?php echo $p['prayer_name']; ?></label>
                    <input type="text" name="prayer[<?php echo $p['id']; ?>]" 
                           value="<?php echo htmlspecialchars($p['prayer_time']); ?>" 
                           placeholder="e.g. 5:15 AM"
                           pattern="^(1[012]|[1-9]):[0-5][0-9]\s(AM|PM)$"
                           title="Please enter time in 12-hour format with AM/PM (e.g., 5:15 AM)"
                           required>
                </div>
            <?php endforeach; ?>
        </div>
        
        <div style="margin-top: 2rem;">
            <button type="submit" class="btn btn-primary" style="width: 100%;">
                <i class="fas fa-save"></i> Save All Times
            </button>
        </div>
    </form>
</div>

<?php require_once 'footer.php'; ?>
