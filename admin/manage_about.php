<?php
$activeNav = 'about';
$pageTitle = 'About Content';
require_once 'header.php';

$message = '';
$messageType = 'success';

// Handle Delete (Only for cards)
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    try {
        $stmt = $pdo->prepare("SELECT type, image_path FROM about_content WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        if ($row && $row['type'] === 'card') {
            if ($row['image_path'] && file_exists(dirname(__DIR__) . '/' . $row['image_path'])) {
                unlink(dirname(__DIR__) . '/' . $row['image_path']);
            }
            $stmt = $pdo->prepare("DELETE FROM about_content WHERE id = ?");
            $stmt->execute([$id]);
            $message = "Card deleted successfully.";
        } else {
            $message = "Cannot delete core sections.";
            $messageType = 'error';
        }
    } catch (PDOException $e) { $message = "Error: " . $e->getMessage(); $messageType = 'error'; }
}

// Handle Add/Update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_card'])) {
        $title = $_POST['title'];
        $desc = $_POST['description'];
        $sort = (int)$_POST['sort_order'];
        $imagePath = null;
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $imagePath = handleFileUpload($_FILES['image'], 'about');
        }
        try {
            $stmt = $pdo->prepare("INSERT INTO about_content (type, title, description, image_path, sort_order) VALUES ('card', ?, ?, ?, ?)");
            $stmt->execute([$title, $desc, $imagePath, $sort]);
            $message = "Card added.";
        } catch (PDOException $e) { $message = "Error: " . $e->getMessage(); $messageType = 'error'; }
    } elseif (isset($_POST['update_content'])) {
        $id = $_POST['id'];
        $title = $_POST['title'];
        $desc = $_POST['description'];
        $sort = (int)$_POST['sort_order'];
        $imagePath = null;
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $imagePath = handleFileUpload($_FILES['image'], 'about');
        }
        try {
            $sql = "UPDATE about_content SET title=?, description=?, sort_order=?";
            $params = [$title, $desc, $sort];
            if ($imagePath) { $sql .= ", image_path=?"; $params[] = $imagePath; }
            $sql .= " WHERE id=?"; $params[] = $id;
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $message = "Section updated.";
        } catch (PDOException $e) { $message = "Error: " . $e->getMessage(); $messageType = 'error'; }
    }
}

$contents = getAboutContent();
?>

<div class="page-header">
    <h1 class="page-title">About Page Sections</h1>
</div>

<?php if ($message): ?>
    <div class="alert alert-<?php echo $messageType; ?>">
        <i class="fas <?php echo $messageType === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'; ?>"></i>
        <?php echo htmlspecialchars($message); ?>
    </div>
<?php endif; ?>

<div class="grid-2">
    <div>
        <h2 style="font-family: 'Outfit', sans-serif; margin-bottom: 1.5rem; font-size: 1.25rem;">Current Sections</h2>
        <?php foreach ($contents as $c): ?>
            <div class="card" style="padding: 1.5rem;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                    <span class="badge <?php echo $c['type'] === 'card' ? 'badge-blue' : 'badge-success'; ?>">
                        <?php echo strtoupper($c['type']); ?>
                    </span>
                    <?php if ($c['type'] === 'card'): ?>
                        <a href="manage_about.php?delete=<?php echo $c['id']; ?>" class="text-danger" style="font-size: 0.8rem; font-weight: 700; text-decoration: none;" onclick="return confirm('Delete card?')">Delete</a>
                    <?php endif; ?>
                </div>
                <form method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="id" value="<?php echo $c['id']; ?>">
                    <div class="form-group">
                        <label>Title</label>
                        <input type="text" name="title" value="<?php echo htmlspecialchars($c['title']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="description" rows="4" required><?php echo htmlspecialchars($c['description']); ?></textarea>
                    </div>
                    <div class="grid-2">
                        <div class="form-group">
                            <label>Sort Order</label>
                            <input type="number" name="sort_order" value="<?php echo $c['sort_order']; ?>">
                        </div>
                        <div class="form-group">
                            <label>Replace Image</label>
                            <input type="file" name="image" accept="image/*">
                        </div>
                    </div>
                    <button type="submit" name="update_content" class="btn btn-secondary" style="width: 100%;">Update</button>
                </form>
            </div>
        <?php endforeach; ?>
    </div>

    <div>
        <div class="card" style="position: sticky; top: 1rem;">
            <h2 style="font-family: 'Outfit', sans-serif; margin-bottom: 1.5rem; font-size: 1.25rem;">Add New Info Card</h2>
            <form method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label>Card Title</label>
                    <input type="text" name="title" required placeholder="e.g. Our History">
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" rows="5" required placeholder="Card details..."></textarea>
                </div>
                <div class="form-group">
                    <label>Image (Optional)</label>
                    <input type="file" name="image" accept="image/*">
                </div>
                <div class="form-group">
                    <label>Sort Order</label>
                    <input type="number" name="sort_order" value="10">
                </div>
                <button type="submit" name="add_card" class="btn btn-primary" style="width: 100%;">Add Card</button>
            </form>
        </div>
    </div>
</div>

<?php require_once 'footer.php'; ?>
