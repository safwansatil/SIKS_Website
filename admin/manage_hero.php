<?php
require_once 'auth.php';

$message = '';

// Handle Delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    try {
        // Get image path before deleting
        $stmt = $pdo->prepare("SELECT image_path FROM hero_slides WHERE id = ?");
        $stmt->execute([$id]);
        $slide = $stmt->fetch();
        
        $stmt = $pdo->prepare("DELETE FROM hero_slides WHERE id = ?");
        $stmt->execute([$id]);
        
        // Delete file
        if ($slide && file_exists(dirname(__DIR__) . '/' . $slide['image_path'])) {
            unlink(dirname(__DIR__) . '/' . $slide['image_path']);
        }
        $message = "Slide deleted successfully.";
    } catch (PDOException $e) {
        $message = "Error: " . $e->getMessage();
    }
}

// Handle Add
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? '';
    $subtitle = $_POST['subtitle'] ?? '';
    $sort_order = $_POST['sort_order'] ?? 0;
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    
    $imagePath = handleFileUpload($_FILES['image'] ?? [], 'hero');
    
    if ($imagePath) {
        try {
            $stmt = $pdo->prepare("INSERT INTO hero_slides (image_path, title, subtitle, sort_order, is_active) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$imagePath, $title, $subtitle, $sort_order, $is_active]);
            $message = "Slide added successfully.";
        } catch (PDOException $e) {
            $message = "Error: " . $e->getMessage();
        }
    } else {
        $message = "Error: Please select a valid image file (JPG, PNG, GIF, WebP).";
    }
}

// Fetch slides
$slides = [];
try {
    $stmt = $pdo->query("SELECT * FROM hero_slides ORDER BY sort_order ASC");
    $slides = $stmt->fetchAll();
} catch (PDOException $e) {
    $slides = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Hero Carousel - SIKS Admin</title>
    <style>
        body { font-family: sans-serif; margin: 2rem; background: #f4f4f4; }
        .nav { margin-bottom: 2rem; }
        .nav a { margin-right: 1rem; text-decoration: none; color: #047857; font-weight: bold; }
        .msg { padding: 1rem; background: #d1fae5; color: #065f46; margin-bottom: 1rem; border-radius: 8px; }
        .msg.error { background: #fee2e2; color: #dc2626; }
        .card { background: white; padding: 1.5rem; border-radius: 12px; margin-bottom: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        .slides-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 1rem; }
        .slide-card { background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        .slide-card img { width: 100%; height: 180px; object-fit: cover; }
        .slide-card .info { padding: 1rem; }
        .form-group { margin-bottom: 1rem; }
        label { display: block; margin-bottom: 0.25rem; font-weight: bold; }
        input, textarea { width: 100%; padding: 0.5rem; border: 1px solid #ccc; border-radius: 6px; box-sizing: border-box; }
        .btn { padding: 0.5rem 1rem; background: #065f46; color: white; border: none; border-radius: 6px; cursor: pointer; text-decoration: none; display: inline-block; font-weight: bold; }
        .btn-red { background: #dc2626; }
        .badge { display: inline-block; padding: 2px 8px; border-radius: 4px; font-size: 0.75rem; font-weight: bold; }
        .badge-green { background: #d1fae5; color: #065f46; }
        .badge-gray { background: #f3f4f6; color: #6b7280; }
    </style>
</head>
<body>
    <div class="nav">
        <a href="index.php">&larr; Dashboard</a>
    </div>
    <h1>Manage Homepage Carousel</h1>

    <?php if ($message): ?>
        <div class="msg <?php echo strpos($message, 'Error') !== false ? 'error' : ''; ?>">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <!-- Add New Slide -->
    <div class="card">
        <h2>Add New Slide</h2>
        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label>Image (Required)</label>
                <input type="file" name="image" accept="image/*" required>
            </div>
            <div class="form-group">
                <label>Title (Optional)</label>
                <input type="text" name="title" placeholder="Optional overlay title">
            </div>
            <div class="form-group">
                <label>Subtitle (Optional)</label>
                <textarea name="subtitle" rows="2" placeholder="Optional subtitle text"></textarea>
            </div>
            <div class="form-group">
                <label>Sort Order</label>
                <input type="number" name="sort_order" value="0">
            </div>
            <div class="form-group">
                <label>
                    <input type="checkbox" name="is_active" checked> Active
                </label>
            </div>
            <button type="submit" class="btn">Upload Slide</button>
        </form>
    </div>

    <!-- Current Slides -->
    <h2 style="margin-top: 2rem;">Current Slides (<?php echo count($slides); ?>)</h2>
    <?php if ($slides): ?>
        <div class="slides-grid">
            <?php foreach ($slides as $slide): ?>
                <div class="slide-card">
                    <img src="../<?php echo htmlspecialchars($slide['image_path']); ?>" alt="Slide">
                    <div class="info">
                        <p style="font-weight: bold; margin: 0 0 0.25rem 0;"><?php echo htmlspecialchars($slide['title'] ?: '(No title)'); ?></p>
                        <p style="color: #666; font-size: 0.875rem; margin: 0 0 0.5rem 0;">Order: <?php echo $slide['sort_order']; ?></p>
                        <span class="badge <?php echo $slide['is_active'] ? 'badge-green' : 'badge-gray'; ?>">
                            <?php echo $slide['is_active'] ? 'Active' : 'Inactive'; ?>
                        </span>
                        <br><br>
                        <a href="manage_hero.php?delete=<?php echo $slide['id']; ?>" onclick="return confirm('Delete this slide?')" class="btn btn-red" style="font-size: 0.8rem; padding: 0.3rem 0.7rem;">Delete</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="card" style="text-align: center; color: #999;">
            <p>No slides uploaded yet. Add your first carousel image above.</p>
        </div>
    <?php endif; ?>
</body>
</html>
