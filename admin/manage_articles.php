<?php
require_once 'auth.php';

$message = '';
$mode = $_GET['mode'] ?? 'list';
$edit_id = $_GET['id'] ?? null;

if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    try {
        $stmt = $pdo->prepare("DELETE FROM articles WHERE id = ?");
        $stmt->execute([$id]);
        $message = "Article deleted successfully.";
    } catch (PDOException $e) {
        $message = "Error: " . $e->getMessage();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $writer = $_POST['writer'];
    $desc = $_POST['description'];

    if ($edit_id) {
        try {
            $stmt = $pdo->prepare("UPDATE articles SET title=?, writer=?, description=? WHERE id=?");
            $stmt->execute([$title, $writer, $desc, $edit_id]);
            $message = "Article updated successfully.";
            $mode = 'list';
        } catch (PDOException $e) {
            $message = "Error: " . $e->getMessage();
        }
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO articles (title, writer, description) VALUES (?, ?, ?)");
            $stmt->execute([$title, $writer, $desc]);
            $message = "Article added successfully.";
            $mode = 'list';
        } catch (PDOException $e) {
            $message = "Error: " . $e->getMessage();
        }
    }
}

$articles = [];
if ($mode === 'list') {
    $stmt = $pdo->query("SELECT * FROM articles ORDER BY last_edited DESC");
    $articles = $stmt->fetchAll();
} elseif ($mode === 'edit' && $edit_id) {
    $stmt = $pdo->prepare("SELECT * FROM articles WHERE id = ?");
    $stmt->execute([$edit_id]);
    $article = $stmt->fetch();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Articles - SIKS Admin</title>
    <style>
        body { font-family: sans-serif; margin: 2rem; background: #f4f4f4; }
        .nav { margin-bottom: 2rem; }
        .nav a { margin-right: 1rem; text-decoration: none; color: #047857; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; background: white; margin-top: 1rem; }
        th, td { padding: 0.75rem; border: 1px solid #ddd; text-align: left; }
        th { background: #eee; }
        .form-group { margin-bottom: 1rem; }
        label { display: block; margin-bottom: 0.25rem; font-weight: bold; }
        input, textarea { width: 100%; padding: 0.5rem; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        .btn { padding: 0.5rem 1rem; background: #065f46; color: white; border: none; border-radius: 4px; cursor: pointer; text-decoration: none; display: inline-block; }
        .msg { padding: 1rem; background: #d1fae5; color: #065f46; margin-bottom: 1rem; border-radius: 4px; }
    </style>
</head>
<body>
    <div class="nav">
        <a href="index.php">&larr; Dashboard</a>
        <a href="manage_articles.php">List Articles</a>
        <a href="manage_articles.php?mode=add">Add New Article</a>
    </div>

    <h1>Manage Articles</h1>

    <?php if ($message): ?>
        <div class="msg"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <?php if ($mode === 'list'): ?>
        <table>
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Writer</th>
                    <th>Last Edited</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($articles as $a): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($a['title']); ?></td>
                        <td><?php echo htmlspecialchars($a['writer']); ?></td>
                        <td><?php echo $a['last_edited']; ?></td>
                        <td>
                            <a href="manage_articles.php?mode=edit&id=<?php echo $a['id']; ?>">Edit</a> | 
                            <a href="manage_articles.php?delete=<?php echo $a['id']; ?>" onclick="return confirm('Are you sure?')">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

    <?php else: ?>
        <form method="POST">
            <div class="form-group">
                <label>Article Title</label>
                <input type="text" name="title" value="<?php echo htmlspecialchars($article['title'] ?? ''); ?>" required>
            </div>
            <div class="form-group">
                <label>Writer Name</label>
                <input type="text" name="writer" value="<?php echo htmlspecialchars($article['writer'] ?? ''); ?>" required>
            </div>
            <div class="form-group">
                <label>Description / Content</label>
                <textarea name="description" rows="10" required><?php echo htmlspecialchars($article['description'] ?? ''); ?></textarea>
            </div>
            <button type="submit" class="btn"><?php echo $edit_id ? 'Update' : 'Add'; ?> Article</button>
        </form>
    <?php endif; ?>
</body>
</html>
