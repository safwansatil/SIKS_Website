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

// Handle remove cover image
if (isset($_GET['remove_cover'])) {
    $id = $_GET['remove_cover'];
    try {
        $stmt = $pdo->prepare("SELECT cover_image FROM articles WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        if ($row && $row['cover_image'] && file_exists(dirname(__DIR__) . '/' . $row['cover_image'])) {
            unlink(dirname(__DIR__) . '/' . $row['cover_image']);
        }
        $stmt = $pdo->prepare("UPDATE articles SET cover_image = NULL WHERE id = ?");
        $stmt->execute([$id]);
        $message = "Cover image removed.";
    } catch (PDOException $e) {
        $message = "Error: " . $e->getMessage();
    }
    header("Location: manage_articles.php?mode=edit&id=$id");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $writer = $_POST['writer'];
    $desc = $_POST['description'];
    $slug = generateSlug($title);
    $readingTime = calculateReadingTime($desc);

    // Handle cover image upload
    $coverImage = null;
    if (isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] === UPLOAD_ERR_OK) {
        $coverImage = handleFileUpload($_FILES['cover_image'], 'articles');
    }

    if ($edit_id) {
        try {
            $sql = "UPDATE articles SET title=?, slug=?, writer=?, description=?, reading_time=?";
            $params = [$title, $slug, $writer, $desc, $readingTime];
            
            if ($coverImage) {
                $sql .= ", cover_image=?";
                $params[] = $coverImage;
            }
            
            $sql .= " WHERE id=?";
            $params[] = $edit_id;
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $message = "Article updated successfully.";
            $mode = 'list';
        } catch (PDOException $e) {
            $message = "Error: " . $e->getMessage();
        }
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO articles (title, slug, writer, description, cover_image, reading_time) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$title, $slug, $writer, $desc, $coverImage, $readingTime]);
            $message = "Article added successfully.";
            $mode = 'list';
        } catch (PDOException $e) {
            $message = "Error: " . $e->getMessage();
        }
    }
}

$articles = [];
$article = [];

if ($mode === 'list') {
    $stmt = $pdo->query("SELECT * FROM articles ORDER BY last_edited DESC");
    $articles = $stmt->fetchAll();
} elseif ($edit_id) {
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
        table { width: 100%; border-collapse: collapse; background: white; margin-top: 1rem; border-radius: 8px; overflow: hidden; }
        th, td { padding: 0.75rem; border: 1px solid #eee; text-align: left; }
        th { background: #065f46; color: white; }
        .form-group { margin-bottom: 1.25rem; }
        label { display: block; margin-bottom: 0.25rem; font-weight: bold; font-size: 0.9rem; }
        .label-hint { display: block; color: #666; font-size: 0.8rem; font-weight: normal; margin-top: 2px; }
        input, textarea { width: 100%; padding: 0.5rem; border: 1px solid #ccc; border-radius: 6px; box-sizing: border-box; }
        textarea { font-family: inherit; }
        .btn { padding: 0.5rem 1rem; background: #065f46; color: white; border: none; border-radius: 6px; cursor: pointer; text-decoration: none; display: inline-block; font-weight: bold; }
        .msg { padding: 1rem; background: #d1fae5; color: #065f46; margin-bottom: 1rem; border-radius: 8px; }
        .cover-preview { max-width: 200px; border-radius: 8px; margin-top: 0.5rem; }
        .char-count { text-align: right; color: #999; font-size: 0.8rem; margin-top: 4px; }
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
                    <th>Reading Time</th>
                    <th>Last Edited</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($articles as $a): ?>
                    <tr>
                        <td>
                            <?php if (!empty($a['cover_image'])): ?>
                                <img src="../<?php echo htmlspecialchars($a['cover_image']); ?>" style="width: 40px; height: 30px; object-fit: cover; border-radius: 4px; vertical-align: middle; margin-right: 8px;">
                            <?php endif; ?>
                            <?php echo htmlspecialchars($a['title']); ?>
                        </td>
                        <td><?php echo htmlspecialchars($a['writer']); ?></td>
                        <td><?php echo $a['reading_time'] ?? '?'; ?> min</td>
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
        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label>Article Title *</label>
                <input type="text" name="title" value="<?php echo htmlspecialchars($article['title'] ?? ''); ?>" required>
            </div>
            <div class="form-group">
                <label>Writer Name *</label>
                <input type="text" name="writer" value="<?php echo htmlspecialchars($article['writer'] ?? ''); ?>" required>
            </div>
            <div class="form-group">
                <label>
                    Cover Image
                    <span class="label-hint">Optional featured image for the article header.</span>
                </label>
                <?php if (!empty($article['cover_image'])): ?>
                    <img src="../<?php echo htmlspecialchars($article['cover_image']); ?>" class="cover-preview" alt="Current cover">
                    <p style="margin-top: 4px;"><a href="manage_articles.php?remove_cover=<?php echo $edit_id; ?>" onclick="return confirm('Remove this cover image?')" style="color: #dc2626; font-size: 0.85rem; font-weight: bold;">Remove Image</a> | <span style="color: #666; font-size: 0.8rem;">or upload new to replace</span></p>
                <?php endif; ?>
                <input type="file" name="cover_image" accept="image/*">
            </div>
            <div class="form-group">
                <label>
                    Article Content *
                    <span class="label-hint">Supports Arabic, Bengali, and special characters. Paste text from any source.</span>
                </label>
                <textarea name="description" rows="15" required id="article-content"><?php echo htmlspecialchars($article['description'] ?? ''); ?></textarea>
                <div class="char-count" id="char-count">0 characters | ~0 min read</div>
            </div>
            <button type="submit" class="btn"><?php echo $edit_id ? 'Update' : 'Add'; ?> Article</button>
        </form>

        <script>
            const textarea = document.getElementById('article-content');
            const counter = document.getElementById('char-count');
            
            function updateCount() {
                const text = textarea.value;
                const chars = text.length;
                const words = text.trim() ? text.trim().split(/\s+/).length : 0;
                const readTime = Math.max(1, Math.ceil(words / 200));
                counter.textContent = `${chars} characters | ~${words} words | ~${readTime} min read`;
            }
            
            textarea.addEventListener('input', updateCount);
            updateCount();
        </script>
    <?php endif; ?>
</body>
</html>
