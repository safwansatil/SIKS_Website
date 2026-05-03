<?php
require_once 'auth.php';

$message = '';
$mode = $_GET['mode'] ?? 'list';
$edit_id = $_GET['id'] ?? null;

// Handle Delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    try {
        $stmt = $pdo->prepare("DELETE FROM events WHERE id = ?");
        $stmt->execute([$id]);
        $message = "Event deleted successfully.";
    } catch (PDOException $e) {
        $message = "Error: " . $e->getMessage();
    }
}

// Handle Add/Edit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $date = $_POST['date'];
    $time = $_POST['time'];
    $venue = $_POST['venue'];
    $short_desc = $_POST['short_description'];
    $desc = $_POST['description'];
    $tag = $_POST['tag'];
    $category = $_POST['category'] ?? 'Community';
    $is_past = isset($_POST['is_past']) ? 1 : 0;
    $logo = $_POST['logo'];

    // Save new category to database if it doesn't exist
    if (!empty($category)) {
        try {
            $cstmt = $pdo->prepare("INSERT IGNORE INTO event_categories (name) VALUES (?)");
            $cstmt->execute([$category]);
        } catch (PDOException $e) {
            // Ignore errors for unique constraint
        }
    }

    // Handle cover image upload
    $coverImage = null;
    if (isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] === UPLOAD_ERR_OK) {
        $coverImage = handleFileUpload($_FILES['cover_image'], 'events');
    }

    // Handle gallery images
    if (isset($_FILES['gallery_images'])) {
        $galleryPaths = [];
        foreach ($_FILES['gallery_images']['tmp_name'] as $key => $tmp) {
            if ($_FILES['gallery_images']['error'][$key] === UPLOAD_ERR_OK) {
                $file = [
                    'tmp_name' => $_FILES['gallery_images']['tmp_name'][$key],
                    'name' => $_FILES['gallery_images']['name'][$key],
                    'error' => $_FILES['gallery_images']['error'][$key]
                ];
                $path = handleFileUpload($file, 'events');
                if ($path) $galleryPaths[] = $path;
            }
        }
    }

    if ($edit_id) {
        try {
            $sql = "UPDATE events SET name=?, event_date=?, event_time=?, venue=?, short_description=?, description=?, tag=?, category=?, is_past=?, logo=?";
            $params = [$name, $date, $time, $venue, $short_desc, $desc, $tag, $category, $is_past, $logo];
            
            if ($coverImage) {
                $sql .= ", cover_image=?";
                $params[] = $coverImage;
            }
            
            $sql .= " WHERE id=?";
            $params[] = $edit_id;
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            
            // Add gallery images
            if (!empty($galleryPaths)) {
                foreach ($galleryPaths as $gpath) {
                    $stmt = $pdo->prepare("INSERT INTO event_images (event_id, image_path) VALUES (?, ?)");
                    $stmt->execute([$edit_id, $gpath]);
                }
            }
            
            $message = "Event updated successfully.";
            $mode = 'list';
        } catch (PDOException $e) {
            $message = "Error: " . $e->getMessage();
        }
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO events (name, event_date, event_time, venue, short_description, description, tag, category, is_past, logo, cover_image) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$name, $date, $time, $venue, $short_desc, $desc, $tag, $category, $is_past, $logo, $coverImage]);
            
            $newEventId = $pdo->lastInsertId();
            
            // Add gallery images
            if (!empty($galleryPaths)) {
                foreach ($galleryPaths as $gpath) {
                    $stmt = $pdo->prepare("INSERT INTO event_images (event_id, image_path) VALUES (?, ?)");
                    $stmt->execute([$newEventId, $gpath]);
                }
            }
            
            $message = "Event added successfully.";
            $mode = 'list';
        } catch (PDOException $e) {
            $message = "Error: " . $e->getMessage();
        }
    }
}

// Delete gallery image
if (isset($_GET['delete_image'])) {
    $imgId = $_GET['delete_image'];
    $returnId = $_GET['return_id'] ?? null;
    try {
        // Also delete the file from disk
        $stmt = $pdo->prepare("SELECT image_path FROM event_images WHERE id = ?");
        $stmt->execute([$imgId]);
        $imgRow = $stmt->fetch();
        if ($imgRow && $imgRow['image_path'] && file_exists(dirname(__DIR__) . '/' . $imgRow['image_path'])) {
            unlink(dirname(__DIR__) . '/' . $imgRow['image_path']);
        }
        $stmt = $pdo->prepare("DELETE FROM event_images WHERE id = ?");
        $stmt->execute([$imgId]);
        $message = "Image deleted.";
        if ($returnId) {
            $mode = 'edit';
            $edit_id = $returnId;
        }
    } catch (PDOException $e) {
        $message = "Error: " . $e->getMessage();
    }
}

// Handle remove cover image
if (isset($_GET['remove_cover'])) {
    $id = $_GET['remove_cover'];
    try {
        $stmt = $pdo->prepare("SELECT cover_image FROM events WHERE id = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        if ($row && $row['cover_image'] && file_exists(dirname(__DIR__) . '/' . $row['cover_image'])) {
            unlink(dirname(__DIR__) . '/' . $row['cover_image']);
        }
        $stmt = $pdo->prepare("UPDATE events SET cover_image = NULL WHERE id = ?");
        $stmt->execute([$id]);
        $message = "Cover image removed.";
    } catch (PDOException $e) {
        $message = "Error: " . $e->getMessage();
    }
    header("Location: manage_events.php?mode=edit&id=$id");
    exit;
}

$events = [];
$event = [];
$eventImages = [];

if ($mode === 'list') {
    $stmt = $pdo->query("SELECT * FROM events ORDER BY event_date DESC");
    $events = $stmt->fetchAll();
} elseif (($mode === 'edit' || $mode === 'add') && $edit_id) {
    $stmt = $pdo->prepare("SELECT * FROM events WHERE id = ?");
    $stmt->execute([$edit_id]);
    $event = $stmt->fetch();
    
    // Fetch existing gallery images
    $stmt = $pdo->prepare("SELECT * FROM event_images WHERE event_id = ?");
    $stmt->execute([$edit_id]);
    $eventImages = $stmt->fetchAll();
}

// Common FA icons for the picker
$iconOptions = [
    'fa-calendar', 'fa-mosque', 'fa-book-quran', 'fa-users', 'fa-trophy', 'fa-futbol',
    'fa-pen-nib', 'fa-moon', 'fa-star', 'fa-heart', 'fa-hand-holding-heart',
    'fa-chalkboard-teacher', 'fa-graduation-cap', 'fa-microphone', 'fa-music',
    'fa-palette', 'fa-chess', 'fa-dumbbell', 'fa-basketball', 'fa-volleyball',
    'fa-table-tennis-paddle-ball', 'fa-flag', 'fa-medal', 'fa-award',
    'fa-lightbulb', 'fa-book-open', 'fa-scroll', 'fa-bullhorn', 'fa-people-group',
    'fa-seedling', 'fa-globe', 'fa-handshake', 'fa-gift', 'fa-camera',
    'fa-film', 'fa-code', 'fa-laptop-code', 'fa-robot', 'fa-briefcase', 'fa-box-open'
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Events - SIKS Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: sans-serif; margin: 2rem; background: #f4f4f4; }
        .nav { margin-bottom: 2rem; }
        .nav a { margin-right: 1rem; text-decoration: none; color: #047857; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; background: white; margin-top: 1rem; border-radius: 8px; overflow: hidden; }
        th, td { padding: 0.75rem; border: 1px solid #eee; text-align: left; }
        th { background: #065f46; color: white; }
        .form-group { margin-bottom: 1.25rem; }
        label { display: block; margin-bottom: 0.25rem; font-weight: bold; font-size: 0.9rem; }
        input, textarea, select { width: 100%; padding: 0.5rem; border: 1px solid #ccc; border-radius: 6px; box-sizing: border-box; }
        .btn { padding: 0.5rem 1rem; background: #065f46; color: white; border: none; border-radius: 6px; cursor: pointer; text-decoration: none; display: inline-block; font-weight: bold; }
        .btn-red { background: #dc2626; }
        .msg { padding: 1rem; background: #d1fae5; color: #065f46; margin-bottom: 1rem; border-radius: 8px; }
        .icon-picker-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(50px, 1fr)); gap: 6px; margin-top: 0.5rem; }
        .icon-picker-item { width: 50px; height: 50px; display: flex; align-items: center; justify-content: center; border: 2px solid #e5e7eb; border-radius: 10px; cursor: pointer; transition: all 0.2s; font-size: 1.2rem; color: #374151; background: white; }
        .icon-picker-item:hover { border-color: #10b981; background: #ecfdf5; color: #065f46; }
        .icon-picker-item.selected { border-color: #065f46; background: #065f46; color: white; }
        .gallery-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(120px, 1fr)); gap: 8px; margin-top: 0.5rem; }
        .gallery-item { position: relative; border-radius: 8px; overflow: hidden; }
        .gallery-item img { width: 100%; height: 100px; object-fit: cover; }
        .gallery-item .delete-btn { position: absolute; top: 4px; right: 4px; background: #dc2626; color: white; border: none; border-radius: 50%; width: 24px; height: 24px; cursor: pointer; font-size: 0.7rem; display: flex; align-items: center; justify-content: center; }
        .cover-preview { max-width: 200px; border-radius: 8px; margin-top: 0.5rem; }
        .badge { display: inline-block; padding: 2px 8px; border-radius: 4px; font-size: 0.75rem; font-weight: bold; }
        .badge-yes { background: #fef3c7; color: #92400e; }
        .badge-no { background: #dbeafe; color: #1e40af; }
    </style>
</head>
<body>
    <div class="nav">
        <a href="index.php">&larr; Dashboard</a>
        <a href="manage_events.php">List Events</a>
        <a href="manage_events.php?mode=add">Add New Event</a>
    </div>

    <h1>Manage Events</h1>

    <?php if ($message): ?>
        <div class="msg"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <?php if ($mode === 'list'): ?>
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Name</th>
                    <th>Venue</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($events as $e): ?>
                    <tr>
                        <td><?php echo $e['event_date']; ?></td>
                        <td>
                            <?php if ($e['cover_image']): ?>
                                <img src="../<?php echo htmlspecialchars($e['cover_image']); ?>" style="width: 40px; height: 30px; object-fit: cover; border-radius: 4px; vertical-align: middle; margin-right: 8px;">
                            <?php endif; ?>
                            <?php echo htmlspecialchars($e['name']); ?>
                        </td>
                        <td><?php echo htmlspecialchars($e['venue']); ?></td>
                        <td><span class="badge <?php echo $e['is_past'] ? 'badge-yes' : 'badge-no'; ?>"><?php echo $e['is_past'] ? 'Past' : 'Upcoming'; ?></span></td>
                        <td>
                            <a href="manage_events.php?mode=edit&id=<?php echo $e['id']; ?>">Edit</a> | 
                            <a href="manage_events.php?delete=<?php echo $e['id']; ?>" onclick="return confirm('Are you sure?')">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

    <?php else: ?>
        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label>Event Name *</label>
                <input type="text" name="name" value="<?php echo htmlspecialchars($event['name'] ?? ''); ?>" required>
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div class="form-group">
                    <label>Date *</label>
                    <input type="date" name="date" value="<?php echo $event['event_date'] ?? ''; ?>" required>
                </div>
                <div class="form-group">
                    <label>Time</label>
                    <input type="text" name="time" value="<?php echo htmlspecialchars($event['event_time'] ?? ''); ?>" placeholder="e.g. 10:00 AM">
                </div>
            </div>
            <div class="form-group">
                <label>Venue *</label>
                <input type="text" name="venue" value="<?php echo htmlspecialchars($event['venue'] ?? ''); ?>" required>
            </div>
            
            <!-- Cover Image Upload -->
            <div class="form-group">
                <label>Cover Image</label>
                <?php if (!empty($event['cover_image'])): ?>
                    <img src="../<?php echo htmlspecialchars($event['cover_image']); ?>" class="cover-preview" alt="Current cover">
                    <p style="margin-top: 4px;"><a href="manage_events.php?remove_cover=<?php echo $edit_id; ?>" onclick="return confirm('Remove this cover image?')" style="color: #dc2626; font-size: 0.85rem; font-weight: bold;">Remove Image</a> | <span style="color: #666; font-size: 0.8rem;">or upload new to replace</span></p>
                <?php endif; ?>
                <input type="file" name="cover_image" accept="image/*">
            </div>

            <!-- Gallery Images Upload -->
            <div class="form-group">
                <label>Gallery Images (multiple allowed)</label>
                <input type="file" name="gallery_images[]" accept="image/*" multiple>
                <?php if ($eventImages): ?>
                    <div class="gallery-grid" style="margin-top: 1rem;">
                        <?php foreach ($eventImages as $img): ?>
                            <div class="gallery-item">
                                <img src="../<?php echo htmlspecialchars($img['image_path']); ?>" alt="Gallery">
                                <a href="manage_events.php?delete_image=<?php echo $img['id']; ?>&return_id=<?php echo $edit_id; ?>&mode=edit" 
                                   onclick="return confirm('Delete this image?')" class="delete-btn">&times;</a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label>Short Description (shown on event card)</label>
                <textarea name="short_description" rows="2"><?php echo htmlspecialchars($event['short_description'] ?? ''); ?></textarea>
            </div>
            <div class="form-group">
                <label>Full Description</label>
                <textarea name="description" rows="6"><?php echo htmlspecialchars($event['description'] ?? ''); ?></textarea>
            </div>
            <div class="form-group">
                <label>Tags (comma separated)</label>
                <input type="text" name="tag" value="<?php echo htmlspecialchars($event['tag'] ?? ''); ?>" placeholder="e.g. Workshop, Seminar, Fun">
            </div>
            <div class="form-group">
                <label>Category *</label>
                <div style="display: flex; gap: 0.5rem;">
                    <select name="category" id="category-select" style="flex: 2;">
                        <?php 
                        $cats = getEventCategories();
                        $currentCat = $event['category'] ?? 'Community';
                        $catFound = false;
                        foreach ($cats as $cat): 
                            if ($cat['name'] === $currentCat) $catFound = true;
                        ?>
                            <option value="<?php echo htmlspecialchars($cat['name']); ?>" <?php echo $currentCat === $cat['name'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($cat['name']); ?>
                            </option>
                        <?php endforeach; ?>
                        <?php if (!$catFound && !empty($currentCat)): ?>
                            <option value="<?php echo htmlspecialchars($currentCat); ?>" selected><?php echo htmlspecialchars($currentCat); ?></option>
                        <?php endif; ?>
                    </select>
                    <input type="text" id="new-category" placeholder="Or type new category..." style="flex: 1;" onchange="if(this.value) { const sel = document.getElementById('category-select'); const opt = document.createElement('option'); opt.value = this.value; opt.text = this.value; opt.selected = true; sel.add(opt); }">
                </div>
                <p style="color: #666; font-size: 0.75rem; margin-top: 4px;">Select an existing category or type a new one to add it.</p>
            </div>
            
            <!-- Icon Picker -->
            <div class="form-group">
                <label>Event Icon</label>
                <input type="hidden" name="logo" id="selected-icon" value="<?php echo htmlspecialchars($event['logo'] ?? ''); ?>">
                <p style="color: #666; font-size: 0.85rem; margin-bottom: 0.5rem;">Click to select an icon (currently: <strong id="current-icon-name"><?php echo htmlspecialchars($event['logo'] ?? 'none'); ?></strong>)</p>
                <div class="icon-picker-grid">
                    <?php foreach ($iconOptions as $icon): ?>
                        <div class="icon-picker-item <?php echo ($event['logo'] ?? '') === $icon ? 'selected' : ''; ?>" 
                             onclick="selectIcon('<?php echo $icon; ?>', this)" title="<?php echo $icon; ?>">
                            <i class="fas <?php echo $icon; ?>"></i>
                        </div>
                    <?php endforeach; ?>
                </div>
                <input type="text" id="custom-icon" placeholder="Or type custom FA icon class (e.g. fa-star)" 
                       value="<?php echo htmlspecialchars($event['logo'] ?? ''); ?>"
                       onchange="document.getElementById('selected-icon').value = this.value; document.getElementById('current-icon-name').innerText = this.value;"
                       style="margin-top: 0.5rem;">
            </div>

            <div class="form-group">
                <label>
                    <input type="checkbox" name="is_past" <?php echo ($event['is_past'] ?? 0) ? 'checked' : ''; ?>> Mark as Past Event
                </label>
            </div>
            <button type="submit" class="btn"><?php echo $edit_id ? 'Update' : 'Add'; ?> Event</button>
        </form>

        <script>
            function selectIcon(iconClass, element) {
                document.querySelectorAll('.icon-picker-item').forEach(el => el.classList.remove('selected'));
                element.classList.add('selected');
                document.getElementById('selected-icon').value = iconClass;
                document.getElementById('current-icon-name').innerText = iconClass;
                document.getElementById('custom-icon').value = iconClass;
            }
        </script>
    <?php endif; ?>
</body>
</html>
