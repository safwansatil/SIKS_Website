<?php
$activeNav = 'library';
$pageTitle = 'Manage Library';
require_once 'header.php';

$message = '';
$messageType = 'success';
$mode = $_GET['mode'] ?? 'list';
$edit_id = $_GET['id'] ?? null;

// Handle Delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    try {
        // Get file path to delete from server
        $stmt = $pdo->prepare("SELECT file_path FROM library_documents WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        if ($row && $row['file_path'] && file_exists(dirname(__DIR__) . '/' . $row['file_path'])) {
            unlink(dirname(__DIR__) . '/' . $row['file_path']);
        }
        
        $stmt = $pdo->prepare("DELETE FROM library_documents WHERE id = ?");
        $stmt->execute([$id]);
        $message = "Document deleted successfully.";
    } catch (PDOException $e) {
        $message = "Error: " . $e->getMessage();
        $messageType = 'error';
    }
}

// Handle Add/Edit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $category = $_POST['category'];

    if ($edit_id) {
        try {
            $stmt = $pdo->prepare("UPDATE library_documents SET title = ?, category = ? WHERE id = ?");
            $stmt->execute([$title, $category, $edit_id]);
            $message = "Document updated successfully.";
            $mode = 'list';
        } catch (PDOException $e) {
            $message = "Error: " . $e->getMessage();
            $messageType = 'error';
        }
    } else {
        // Handle file upload
        if (isset($_FILES['pdf_file']) && $_FILES['pdf_file']['error'] === UPLOAD_ERR_OK) {
            $uploadResult = handlePDFUpload($_FILES['pdf_file']);
            if (is_array($uploadResult) && isset($uploadResult['success'])) {
                try {
                    $stmt = $pdo->prepare("INSERT INTO library_documents (title, filename, file_path, file_size, category) VALUES (?, ?, ?, ?, ?)");
                    $stmt->execute([
                        $title, 
                        $uploadResult['filename'], 
                        $uploadResult['file_path'], 
                        $uploadResult['file_size'], 
                        $category
                    ]);
                    $message = "Document uploaded successfully.";
                    $mode = 'list';
                } catch (PDOException $e) {
                    $message = "Database Error: " . $e->getMessage();
                    $messageType = 'error';
                }
            } else {
                $errorMsg = is_array($uploadResult) ? ($uploadResult['error'] ?? 'Unknown error') : 'Upload failed';
                $message = "Upload Error: " . $errorMsg;
                $messageType = 'error';
            }
        } else {
            $message = "Please select a valid PDF file.";
            $messageType = 'error';
        }
    }
}

$documents = [];
$document = [];

if ($mode === 'list') {
    $stmt = $pdo->query("SELECT * FROM library_documents ORDER BY uploaded_at DESC");
    $documents = $stmt->fetchAll();
} elseif ($edit_id) {
    $stmt = $pdo->prepare("SELECT * FROM library_documents WHERE id = ?");
    $stmt->execute([$edit_id]);
    $document = $stmt->fetch();
}
?>

<div class="page-header">
    <h1 class="page-title">Digital Library Management</h1>
    <?php if ($mode === 'list'): ?>
        <a href="manage_library.php?mode=add" class="btn btn-primary">
            <i class="fas fa-upload"></i> Upload New Document
        </a>
    <?php endif; ?>
</div>

<?php if ($message): ?>
    <div class="alert alert-<?php echo $messageType; ?>">
        <i class="fas <?php echo $messageType === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'; ?>"></i>
        <?php echo htmlspecialchars($message); ?>
    </div>
<?php endif; ?>

<?php if ($mode === 'list'): ?>
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Category</th>
                    <th>Size</th>
                    <th>Downloads</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($documents as $doc): ?>
                    <tr>
                        <td>
                            <div style="font-weight: 600;"><?php echo htmlspecialchars($doc['title']); ?></div>
                            <div style="font-size: 0.75rem; color: var(--text-muted);"><?php echo htmlspecialchars($doc['filename']); ?></div>
                        </td>
                        <td><span class="badge badge-blue"><?php echo htmlspecialchars($doc['category']); ?></span></td>
                        <td><?php echo round($doc['file_size'] / 1024 / 1024, 2); ?> MB</td>
                        <td><?php echo number_format($doc['downloads']); ?></td>
                        <td>
                            <div style="display: flex; gap: 0.5rem;">
                                <a href="manage_library.php?mode=edit&id=<?php echo $doc['id']; ?>" class="btn btn-secondary" style="padding: 0.4rem 0.8rem; font-size: 0.85rem;">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="manage_library.php?delete=<?php echo $doc['id']; ?>" class="btn btn-danger" style="padding: 0.4rem 0.8rem; font-size: 0.85rem;" onclick="return confirm('Delete document and file?')">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

<?php else: ?>
    <div class="card">
        <h2 style="font-family: 'Outfit', sans-serif; margin-bottom: 2rem;"><?php echo $edit_id ? 'Edit Document Details' : 'Upload New Document'; ?></h2>
        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label>Document Title *</label>
                <input type="text" name="title" value="<?php echo htmlspecialchars($document['title'] ?? ''); ?>" required placeholder="Enter document title">
            </div>

            <div class="form-group">
                <label>Category *</label>
                <select name="category" required>
                    <option value="Book" <?php echo ($document['category'] ?? '') === 'Book' ? 'selected' : ''; ?>>Book</option>
                    <option value="Research Paper" <?php echo ($document['category'] ?? '') === 'Research Paper' ? 'selected' : ''; ?>>Research Paper</option>
                    <option value="Lecture Note" <?php echo ($document['category'] ?? '') === 'Lecture Note' ? 'selected' : ''; ?>>Lecture Note</option>
                    <option value="Other" <?php echo ($document['category'] ?? '') === 'Other' ? 'selected' : ''; ?>>Other</option>
                </select>
            </div>

            <?php if (!$edit_id): ?>
                <div class="form-group">
                    <label>PDF File * (Max 25MB)</label>
                    <input type="file" name="pdf_file" accept="application/pdf" required>
                    <p style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.25rem;">Only PDF files are allowed.</p>
                </div>
            <?php else: ?>
                <div class="form-group">
                    <label>File</label>
                    <input type="text" value="<?php echo htmlspecialchars($document['filename']); ?>" disabled style="background: #f1f5f9;">
                    <p style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.25rem;">To change the file, please delete and re-upload.</p>
                </div>
            <?php endif; ?>

            <div style="display: flex; gap: 1rem; margin-top: 3rem; pt: 2rem; border-top: 1px solid var(--border);">
                <button type="submit" class="btn btn-primary" style="flex: 2;">
                    <i class="fas fa-save"></i> <?php echo $edit_id ? 'Update Details' : 'Upload Document'; ?>
                </button>
                <a href="manage_library.php" class="btn btn-secondary" style="flex: 1;">
                    Cancel
                </a>
            </div>
        </form>
    </div>
<?php endif; ?>

<?php require_once 'footer.php'; ?>
