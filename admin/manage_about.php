<?php
require_once 'auth.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_content'])) {
    $id = $_POST['id'];
    $title = $_POST['title'];
    $desc = $_POST['description'];

    // Handle image upload for about content
    $imagePath = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $imagePath = handleFileUpload($_FILES['image'], 'about');
    }

    try {
        $sql = "UPDATE about_content SET title = ?, description = ?";
        $params = [$title, $desc];
        
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
        .card { background: white; padding: 1.5rem; border-radius: 12px; margin-bottom: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        .msg { padding: 1rem; background: #d1fae5; color: #065f46; margin-bottom: 1rem; border-radius: 8px; }
        label { display: block; font-weight: bold; margin-bottom: 0.5rem; font-size: 0.9rem; }
        input, textarea { width: 100%; padding: 0.5rem; border: 1px solid #ccc; border-radius: 6px; box-sizing: border-box; margin-bottom: 1rem; }
        button { padding: 0.5rem 1rem; background: #065f46; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: bold; }
        .cover-preview { max-width: 200px; border-radius: 8px; margin: 0.5rem 0; }
        .type-badge { display: inline-block; padding: 3px 10px; border-radius: 4px; font-size: 0.75rem; font-weight: bold; background: #ecfdf5; color: #065f46; text-transform: uppercase; }
    </style>
</head>
<body>
    <div class="nav">
        <a href="index.php">&larr; Dashboard</a>
    </div>
    <h1>Manage About Page Content</h1>

    <?php if ($message): ?>
        <div class="msg"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <?php foreach ($contents as $c): ?>
        <div class="card">
            <span class="type-badge"><?php echo ucfirst($c['type']); ?></span>
            <form method="POST" enctype="multipart/form-data" style="margin-top: 1rem;">
                <input type="hidden" name="id" value="<?php echo $c['id']; ?>">
                <div>
                    <label>Title</label>
                    <input type="text" name="title" value="<?php echo htmlspecialchars($c['title']); ?>" required>
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
