<?php
require_once 'auth.php';

$message = '';

// Handle Delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    try {
        // Don't allow deleting 'title' type
        $stmt = $pdo->prepare("SELECT type, image_path FROM about_content WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        
        if ($row && $row['type'] === 'card') {
            $stmt = $pdo->prepare("DELETE FROM about_content WHERE id = ?");
            $stmt->execute([$id]);
            
            if ($row['image_path'] && file_exists(dirname(__DIR__) . '/' . $row['image_path'])) {
                unlink(dirname(__DIR__) . '/' . $row['image_path']);
            }
            $message = "Card deleted successfully.";
        } else {
            $message = "Error: Main sections cannot be deleted.";
        }
    } catch (PDOException $e) {
        $message = "Error: " . $e->getMessage();
    }
}

// Handle Add New Card
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_card'])) {
    $title = $_POST['title'];
    $desc = $_POST['description'];
    $sort_order = $_POST['sort_order'] ?? 10;

    $imagePath = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $imagePath = handleFileUpload($_FILES['image'], 'about');
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO about_content (type, title, description, image_path, sort_order) VALUES ('card', ?, ?, ?, ?)");
        $stmt->execute([$title, $desc, $imagePath, $sort_order]);
        $message = "New card added successfully.";
    } catch (PDOException $e) {
        $message = "Error: " . $e->getMessage();
    }
}

// Handle Update Content
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_content'])) {
    $id = $_POST['id'];
    $title = $_POST['title'];
    $desc = $_POST['description'];
    $sort_order = $_POST['sort_order'] ?? 0;

    $imagePath = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $imagePath = handleFileUpload($_FILES['image'], 'about');
    }

    try {
        $sql = "UPDATE about_content SET title = ?, description = ?, sort_order = ?";
        $params = [$title, $desc, $sort_order];
        
        if ($imagePath) {
            $sql .= ", image_path = ?";
            $params[] = $imagePath;
        }
        
        $sql .= " WHERE id = ?";
        $params[] = $id;
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $message = "Content updated successfully.";
    } catch (PDOException $e) {
        $message = "Error: " . $e->getMessage();
    }
}

$contents = getAboutContent();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage About Page - SIKS Admin</title>
    <style>
        body { font-family: sans-serif; margin: 2rem; background: #f4f4f4; }
        .nav { margin-bottom: 2rem; }
        .nav a { margin-right: 1rem; text-decoration: none; color: #047857; font-weight: bold; }
        .card { background: white; padding: 1.5rem; border-radius: 12px; margin-bottom: 1.5rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        .msg { padding: 1rem; background: #d1fae5; color: #065f46; margin-bottom: 1rem; border-radius: 8px; }
        .msg.error { background: #fee2e2; color: #dc2626; }
        label { display: block; font-weight: bold; margin-bottom: 0.5rem; font-size: 0.9rem; }
        input, textarea { width: 100%; padding: 0.5rem; border: 1px solid #ccc; border-radius: 6px; box-sizing: border-box; margin-bottom: 1rem; }
        button { padding: 0.5rem 1rem; background: #065f46; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: bold; }
        .btn-red { background: #dc2626; margin-top: 1rem; }
        .cover-preview { max-width: 200px; border-radius: 8px; margin: 0.5rem 0; }
        .type-badge { display: inline-block; padding: 3px 10px; border-radius: 4px; font-size: 0.75rem; font-weight: bold; background: #ecfdf5; color: #065f46; text-transform: uppercase; }
        .card-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; }
    </style>
</head>
<body>
    <div class="nav">
        <a href="index.php">&larr; Dashboard</a>
    </div>
    <h1>Manage About Page Content</h1>

    <?php if ($message): ?>
        <div class="msg <?php echo strpos($message, 'Error') !== false ? 'error' : ''; ?>">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <!-- Add New Card Section -->
    <div class="card" style="border-top: 4px solid #065f46;">
        <h2>Add New Card</h2>
        <form method="POST" enctype="multipart/form-data">
            <div style="display: grid; grid-template-columns: 3fr 1fr; gap: 1rem;">
                <div>
                    <label>Card Title</label>
                    <input type="text" name="title" required placeholder="e.g. Our History">
                </div>
                <div>
                    <label>Sort Order</label>
                    <input type="number" name="sort_order" value="10">
                </div>
            </div>
            <div>
                <label>Description</label>
                <textarea name="description" rows="3" required placeholder="Detailed information for this card..."></textarea>
            </div>
            <div>
                <label>Image (Optional)</label>
                <input type="file" name="image" accept="image/*">
            </div>
            <button type="submit" name="add_card">Add Card</button>
        </form>
    </div>

    <h2 style="margin: 2rem 0 1rem;">Current Sections & Cards</h2>
    <?php foreach ($contents as $c): ?>
        <div class="card">
            <div class="card-header">
                <span class="type-badge"><?php echo ucfirst($c['type']); ?></span>
                <?php if ($c['type'] === 'card'): ?>
                    <a href="manage_about.php?delete=<?php echo $c['id']; ?>" 
                       onclick="return confirm('Delete this card?')" 
                       style="color: #dc2626; font-size: 0.8rem; font-weight: bold; text-decoration: none;">Delete Card</a>
                <?php endif; ?>
            </div>
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="id" value="<?php echo $c['id']; ?>">
                <div style="display: grid; grid-template-columns: 3fr 1fr; gap: 1rem;">
                    <div>
                        <label>Title</label>
                        <input type="text" name="title" value="<?php echo htmlspecialchars($c['title']); ?>" required>
                    </div>
                    <div>
                        <label>Sort Order</label>
                        <input type="number" name="sort_order" value="<?php echo $c['sort_order']; ?>">
                    </div>
                </div>
                <div>
                    <label>Description</label>
                    <textarea name="description" rows="4" required><?php echo htmlspecialchars($c['description']); ?></textarea>
                </div>
                <div>
                    <label>Image (Optional)</label>
                    <?php if (!empty($c['image_path'])): ?>
                        <img src="../<?php echo htmlspecialchars($c['image_path']); ?>" class="cover-preview" alt="Current image">
                        <p style="color: #666; font-size: 0.8rem;">Upload new image to replace.</p>
                    <?php endif; ?>
                    <input type="file" name="image" accept="image/*">
                </div>
                <button type="submit" name="update_content">Update Section</button>
            </form>
        </div>
    <?php endforeach; ?>
</body>
</html>
