<?php
require_once 'auth.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_content'])) {
    $id = $_POST['id'];
    $title = $_POST['title'];
    $desc = $_POST['description'];

    try {
        $stmt = $pdo->prepare("UPDATE about_content SET title = ?, description = ? WHERE id = ?");
        $stmt->execute([$title, $desc, $id]);
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
        .card { background: white; padding: 1.5rem; border-radius: 8px; margin-bottom: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        .msg { padding: 1rem; background: #d1fae5; color: #065f46; margin-bottom: 1rem; border-radius: 4px; }
        label { display: block; font-weight: bold; margin-bottom: 0.5rem; }
        input, textarea { width: 100%; padding: 0.5rem; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; margin-bottom: 1rem; }
        button { padding: 0.5rem 1rem; background: #065f46; color: white; border: none; border-radius: 4px; cursor: pointer; }
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
            <h3>Type: <?php echo ucfirst($c['type']); ?></h3>
            <form method="POST">
                <input type="hidden" name="id" value="<?php echo $c['id']; ?>">
                <div class="form-group">
                    <label>Title</label>
                    <input type="text" name="title" value="<?php echo htmlspecialchars($c['title']); ?>" required>
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" rows="4" required><?php echo htmlspecialchars($c['description']); ?></textarea>
                </div>
                <button type="submit" name="update_content">Update Section</button>
            </form>
        </div>
    <?php endforeach; ?>
</body>
</html>
