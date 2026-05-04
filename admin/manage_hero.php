<?php
$activeNav = 'hero';
$pageTitle = 'Hero Carousel';
require_once 'header.php';

// Ensure table exists
try {
    $pdo->query("SELECT 1 FROM hero_slides LIMIT 1");
} catch (PDOException $e) {
    $pdo->exec("CREATE TABLE IF NOT EXISTS hero_slides (
        id INT AUTO_INCREMENT PRIMARY KEY,
        image_path VARCHAR(255) NOT NULL,
        title VARCHAR(255),
        subtitle TEXT,
        sort_order INT DEFAULT 0,
        is_active BOOLEAN DEFAULT TRUE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");
}

$message = '';
$messageType = 'success';

// Handle Delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    try {
        $stmt = $pdo->prepare("SELECT image_path FROM hero_slides WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        if ($row && file_exists(dirname(__DIR__) . '/' . $row['image_path'])) {
            unlink(dirname(__DIR__) . '/' . $row['image_path']);
        }
        $stmt = $pdo->prepare("DELETE FROM hero_slides WHERE id = ?");
        $stmt->execute([$id]);
        $message = "Slide removed successfully.";
    } catch (PDOException $e) {
        $message = "Error: " . $e->getMessage();
        $messageType = 'error';
    }
}

// Handle Add Slide
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_slide'])) {
    $subtitle = $_POST['subtitle']; // This is our short description
    $sort_order = (int)$_POST['sort_order'];

    if (isset($_FILES['hero_image']) && $_FILES['hero_image']['error'] === UPLOAD_ERR_OK) {
        $imagePath = handleFileUpload($_FILES['hero_image'], 'hero');
        if ($imagePath) {
            try {
                $stmt = $pdo->prepare("INSERT INTO hero_slides (image_path, subtitle, sort_order) VALUES (?, ?, ?)");
                $stmt->execute([$imagePath, $subtitle, $sort_order]);
                $message = "New slide added successfully.";
            } catch (PDOException $e) {
                $message = "Database Error: " . $e->getMessage();
                $messageType = 'error';
            }
        } else {
            $message = "Failed to process image. Make sure GD library is enabled.";
            $messageType = 'error';
        }
    } else {
        $message = "Please select a valid image.";
        $messageType = 'error';
    }
}

// Handle Update Order/Status
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_slides'])) {
    try {
        foreach ($_POST['order'] as $id => $order) {
            $active = isset($_POST['active'][$id]) ? 1 : 0;
            $stmt = $pdo->prepare("UPDATE hero_slides SET sort_order = ?, is_active = ? WHERE id = ?");
            $stmt->execute([$order, $active, $id]);
        }
        $message = "Carousel updated successfully.";
    } catch (PDOException $e) {
        $message = "Error: " . $e->getMessage();
        $messageType = 'error';
    }
}

$slides = [];
try {
    $stmt = $pdo->query("SELECT * FROM hero_slides ORDER BY sort_order ASC, created_at DESC");
    $slides = $stmt->fetchAll();
} catch (PDOException $e) {}
?>

<div class="page-header">
    <h1 class="page-title">Hero Carousel</h1>
</div>

<?php if ($message): ?>
    <div class="alert alert-<?php echo $messageType; ?>">
        <i class="fas <?php echo $messageType === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'; ?>"></i>
        <?php echo htmlspecialchars($message); ?>
    </div>
<?php endif; ?>

<div class="grid-2">
    <!-- Add New Slide -->
    <div class="card">
        <h2 style="font-family: 'Outfit', sans-serif; margin-bottom: 1.5rem; font-size: 1.25rem;">Add New Slide</h2>
        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label>Select Background Image *</label>
                <input type="file" name="hero_image" accept="image/*" required id="hero-img-input">
                <div id="hero-img-preview" style="margin-top: 1rem; border-radius: 0.75rem; overflow: hidden; display: none; aspect-ratio: 16/9; background: #eee;">
                    <img id="preview-img" style="width: 100%; height: 100%; object-fit: cover;">
                </div>
                <p style="font-size: 0.75rem; color: var(--text-muted); mt: 0.5rem;">Recommended size: 1920x1080. It will be auto-cropped.</p>
            </div>
            <div class="form-group">
                <label>Short Description (Optional)</label>
                <textarea name="subtitle" rows="3" placeholder="A short meaningful text to show over the image..."></textarea>
            </div>
            <div class="form-group">
                <label>Sort Order</label>
                <input type="number" name="sort_order" value="0">
            </div>
            <button type="submit" name="add_slide" class="btn btn-primary" style="width: 100%;">
                <i class="fas fa-upload"></i> Upload & Add to Carousel
            </button>
        </form>
    </div>

    <!-- Current Slides -->
    <div class="card" style="padding: 1.5rem;">
        <h2 style="font-family: 'Outfit', sans-serif; margin-bottom: 1.5rem; font-size: 1.25rem;">Manage Carousel</h2>
        <?php if ($slides): ?>
            <form method="POST">
                <div style="display: flex; flex-direction: column; gap: 1rem; margin-bottom: 2rem;">
                    <?php foreach ($slides as $slide): ?>
                        <div style="display: flex; align-items: center; gap: 1rem; padding: 1rem; border: 1px solid var(--border); border-radius: 0.75rem; background: #fff;">
                            <img src="../<?php echo htmlspecialchars($slide['image_path']); ?>" style="width: 80px; height: 50px; object-fit: cover; border-radius: 0.25rem;">
                            <div style="flex: 1;">
                                <div style="font-size: 0.8rem; color: var(--text-muted); margin-bottom: 0.25rem;">Order</div>
                                <input type="number" name="order[<?php echo $slide['id']; ?>]" value="<?php echo $slide['sort_order']; ?>" style="width: 60px; padding: 0.25rem 0.5rem;">
                            </div>
                            <div style="display: flex; align-items: center; gap: 0.5rem;">
                                <input type="checkbox" name="active[<?php echo $slide['id']; ?>]" <?php echo $slide['is_active'] ? 'checked' : ''; ?> id="active-<?php echo $slide['id']; ?>">
                                <label for="active-<?php echo $slide['id']; ?>" style="margin-bottom: 0; font-weight: 500;">Active</label>
                            </div>
                            <a href="manage_hero.php?delete=<?php echo $slide['id']; ?>" class="btn btn-danger" style="padding: 0.4rem;" onclick="return confirm('Remove this slide?')">
                                <i class="fas fa-times"></i>
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div style="display: flex; gap: 1rem;">
                    <button type="submit" name="update_slides" class="btn btn-primary" style="flex: 2;">
                        Save Changes
                    </button>
                    <a href="manage_hero.php" class="btn btn-secondary" style="flex: 1;">Cancel</a>
                </div>
            </form>
        <?php else: ?>
            <div style="text-align: center; padding: 3rem; color: var(--text-muted);">
                <i class="fas fa-images" style="font-size: 2rem; opacity: 0.2; margin-bottom: 1rem;"></i>
                <p>No slides added yet.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
    document.getElementById('hero-img-input').addEventListener('change', function() {
        const preview = document.getElementById('hero-img-preview');
        const img = document.getElementById('preview-img');
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                img.src = e.target.result;
                preview.style.display = 'block';
            }
            reader.readAsDataURL(file);
        }
    });
</script>

<?php require_once 'footer.php'; ?>
