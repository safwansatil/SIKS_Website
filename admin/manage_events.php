<?php
$activeNav = 'events';
$pageTitle = 'Manage Events';
require_once 'header.php';

$message = '';
$messageType = 'success';
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
        $messageType = 'error';
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
    
    // Base64 bypass check
    if (preg_match('%^[a-zA-Z0-9/+]*={0,2}$%', $desc)) {
        $decoded = base64_decode($desc, true);
        if ($decoded !== false) {
            $desc = $decoded;
        }
    }

    $tag = $_POST['tag'];
    $category = $_POST['category'] ?? 'Community';
    $is_past = isset($_POST['is_past']) ? 1 : 0;
    $logo = $_POST['logo'];

    // Save new category to database if it doesn't exist
    if (!empty($category)) {
        try {
            $cstmt = $pdo->prepare("INSERT IGNORE INTO event_categories (name) VALUES (?)");
            $cstmt->execute([$category]);
        } catch (PDOException $e) {}
    }

    // Handle cover image upload
    $coverImage = null;
    if (isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] === UPLOAD_ERR_OK) {
        $coverImage = handleFileUpload($_FILES['cover_image'], 'events');
    }

    // Handle gallery images
    $galleryPaths = [];
    if (isset($_FILES['gallery_images'])) {
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
                // Delete old cover image if replacing
                $stmt = $pdo->prepare("SELECT cover_image FROM events WHERE id = ?");
                $stmt->execute([$edit_id]);
                $oldCover = $stmt->fetchColumn();
                if ($oldCover && $oldCover !== $coverImage && file_exists(dirname(__DIR__) . '/' . $oldCover)) {
                    unlink(dirname(__DIR__) . '/' . $oldCover);
                }
                
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
            $messageType = 'error';
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
            $messageType = 'error';
        }
    }
}

// Delete gallery image
if (isset($_GET['delete_image'])) {
    $imgId = $_GET['delete_image'];
    $returnId = $_GET['return_id'] ?? null;
    try {
        $stmt = $pdo->prepare("SELECT image_path FROM event_images WHERE id = ?");
        $stmt->execute([$imgId]);
        $imgRow = $stmt->fetch();
        if ($imgRow && $imgRow['image_path'] && file_exists(dirname(__DIR__) . '/' . $imgRow['image_path'])) {
            unlink(dirname(__DIR__) . '/' . $imgRow['image_path']);
        }
        $stmt = $pdo->prepare("DELETE FROM event_images WHERE id = ?");
        $stmt->execute([$imgId]);
        $message = "Gallery image deleted.";
        if ($returnId) { $mode = 'edit'; $edit_id = $returnId; }
    } catch (PDOException $e) {
        $message = "Error: " . $e->getMessage();
        $messageType = 'error';
    }
}

// Remove cover image
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
        $mode = 'edit'; $edit_id = $id;
    } catch (PDOException $e) {
        $message = "Error: " . $e->getMessage();
        $messageType = 'error';
    }
}

$events = [];
$event = [];
$eventImages = [];

if ($mode === 'list') {
    $catFilter = $_GET['cat'] ?? null;
    $sql = "SELECT * FROM events";
    $params = [];
    if ($catFilter) {
        $sql .= " WHERE category = ?";
        $params[] = $catFilter;
    }
    $sql .= " ORDER BY event_date DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $events = $stmt->fetchAll();
} elseif ($edit_id) {
    $stmt = $pdo->prepare("SELECT * FROM events WHERE id = ?");
    $stmt->execute([$edit_id]);
    $event = $stmt->fetch();
    $stmt = $pdo->prepare("SELECT * FROM event_images WHERE event_id = ?");
    $stmt->execute([$edit_id]);
    $eventImages = $stmt->fetchAll();
}

$iconOptions = [
    'fa-calendar', 'fa-mosque', 'fa-book-quran', 'fa-users', 'fa-trophy', 'fa-futbol',
    'fa-pen-nib', 'fa-moon', 'fa-star', 'fa-heart', 'fa-hand-holding-heart',
    'fa-chalkboard-teacher', 'fa-graduation-cap', 'fa-microphone', 'fa-music',
    'fa-palette', 'fa-chess', 'fa-dumbbell', 'fa-basketball', 'fa-volleyball',
    'fa-table-tennis-paddle-ball', 'fa-flag', 'fa-medal', 'fa-award'
];
?>

<div class="page-header" style="flex-wrap: wrap; gap: 1rem;">
    <h1 class="page-title">Events Management</h1>
    <?php if ($mode === 'list'): ?>
        <div style="display: flex; gap: 1rem; align-items: center;">
            <select onchange="window.location.href='manage_events.php?cat=' + this.value" style="padding: 0.5rem; border-radius: 0.5rem; border: 1px solid var(--border); font-size: 0.85rem;">
                <option value="">All Categories</option>
                <?php 
                $allCats = getEventCategoriesForAdminEdit();
                $currentCatFilter = $_GET['cat'] ?? '';
                foreach ($allCats as $c): ?>
                    <option value="<?php echo htmlspecialchars($c['name']); ?>" <?php echo $currentCatFilter === $c['name'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($c['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <a href="manage_events.php?mode=add" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add New Event
            </a>
        </div>
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
                    <th>Event</th>
                    <th>Date & Time</th>
                    <th>Venue</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($events as $e): ?>
                    <tr>
                        <td>
                            <div style="display: flex; align-items: center; gap: 1rem;">
                                <?php if ($e['cover_image']): ?>
                                    <img src="../<?php echo htmlspecialchars($e['cover_image']); ?>" class="img-preview-sm">
                                <?php else: ?>
                                    <div class="img-preview-sm" style="background: var(--primary-light); display: flex; align-items: center; justify-content: center; color: var(--primary);">
                                        <i class="fas <?php echo $e['logo'] ?: 'fa-calendar'; ?>"></i>
                                    </div>
                                <?php endif; ?>
                                <span style="font-weight: 600;"><?php echo htmlspecialchars($e['name']); ?></span>
                            </div>
                        </td>
                        <td>
                            <div style="font-weight: 500;"><?php echo date('M d, Y', strtotime($e['event_date'])); ?></div>
                            <div style="font-size: 0.8rem; color: var(--text-muted);"><?php echo htmlspecialchars($e['event_time']); ?></div>
                        </td>
                        <td><?php echo htmlspecialchars($e['venue']); ?></td>
                        <td>
                            <span class="badge <?php echo $e['is_past'] ? 'badge-success' : 'badge-blue'; ?>">
                                <?php echo $e['is_past'] ? 'Past' : 'Upcoming'; ?>
                            </span>
                        </td>
                        <td>
                            <div style="display: flex; gap: 0.5rem;">
                                <a href="manage_events.php?mode=edit&id=<?php echo $e['id']; ?>" class="btn btn-secondary" style="padding: 0.4rem 0.8rem; font-size: 0.85rem;">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="manage_events.php?delete=<?php echo $e['id']; ?>" class="btn btn-danger" style="padding: 0.4rem 0.8rem; font-size: 0.85rem;" onclick="return confirm('Are you sure?')">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($events)): ?>
                    <tr><td colspan="5" style="text-align: center; padding: 3rem; color: var(--text-muted); font-style: italic;">No events found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

<?php else: ?>
    <div class="card">
        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 2rem;">
            <h2 style="font-family: 'Outfit', sans-serif; margin: 0;"><?php echo $edit_id ? 'Edit Event' : 'Add New Event'; ?></h2>
            <a href="manage_events.php" class="btn btn-secondary" style="padding: 0.5rem 1rem;">
                <i class="fas fa-arrow-left"></i> Back to List
            </a>
        </div>
        <form method="POST" enctype="multipart/form-data" data-b64-bypass>
            <div class="form-group">
                <label>Event Name *</label>
                <input type="text" name="name" value="<?php echo htmlspecialchars($event['name'] ?? ''); ?>" required placeholder="Enter event title">
            </div>

            <div class="grid-2">
                <div class="form-group">
                    <label>Event Date *</label>
                    <input type="date" name="date" value="<?php echo $event['event_date'] ?? ''; ?>" required>
                </div>
                <div class="form-group">
                    <label>Event Time</label>
                    <input type="text" name="time" value="<?php echo htmlspecialchars($event['event_time'] ?? ''); ?>" 
                           placeholder="e.g. 10:00 AM or 10:00 AM - 12:00 PM"
                           pattern="^(1[012]|[1-9]):[0-5][0-9]\s(AM|PM)(\s-\s(1[012]|[1-9]):[0-5][0-9]\s(AM|PM))?$"
                           title="Please enter time in 12-hour format (e.g., 10:00 AM) or a range (e.g., 10:00 AM - 12:00 PM)">
                </div>
            </div>

            <div class="form-group">
                <label>Venue *</label>
                <input type="text" name="venue" value="<?php echo htmlspecialchars($event['venue'] ?? ''); ?>" required placeholder="e.g. Auditorium, Main Building">
            </div>

            <div class="grid-2">
                <div class="form-group">
                    <label>Cover Image (Required for Premium Look)</label>
                    <?php if (!empty($event['cover_image'])): ?>
                        <div style="position: relative; width: 120px; height: 80px; margin-bottom: 0.5rem;">
                            <img src="../<?php echo htmlspecialchars($event['cover_image']); ?>" style="width: 100%; height: 100%; object-fit: cover; border-radius: 0.5rem;">
                            <a href="manage_events.php?remove_cover=<?php echo $edit_id; ?>" style="position: absolute; -top: 8px; -right: 8px; background: var(--danger); color: white; width: 20px; height: 20px; border-radius: 50%; display: flex; align-items: center; justify-content: center; text-decoration: none; font-size: 0.7rem;" title="Remove cover">&times;</a>
                        </div>
                    <?php endif; ?>
                    <input type="file" name="cover_image" accept="image/*">
                </div>
                <div class="form-group">
                    <label>Category *</label>
                    <div style="display: flex; gap: 0.5rem;">
                        <select name="category" id="category-select" style="flex: 1;">
                            <?php 
                            $cats = getEventCategoriesForAdminEdit();
                            $currentCat = $event['category'] ?? 'Community';
                            foreach ($cats as $cat): ?>
                                <option value="<?php echo htmlspecialchars($cat['name']); ?>" <?php echo $currentCat === $cat['name'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($cat['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <input type="text" id="new-category" placeholder="Or new..." style="flex: 1;" onchange="addNewCategory(this.value)">
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label>Gallery Images (Multiple Upload)</label>
                <input type="file" name="gallery_images[]" accept="image/*" multiple id="gallery-input">
                <div id="gallery-preview" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(80px, 1fr)); gap: 0.75rem; margin-top: 1rem;"></div>
                
                <?php if ($eventImages): ?>
                    <div style="margin-top: 1.5rem; border-top: 1px solid var(--border); pt: 1rem;">
                        <p style="font-size: 0.8rem; font-weight: 600; margin-bottom: 0.75rem; color: var(--text-muted);">Current Gallery:</p>
                        <div style="display: flex; flex-wrap: wrap; gap: 1rem;">
                            <?php foreach ($eventImages as $img): ?>
                                <div style="position: relative; width: 100px; height: 70px;">
                                    <img src="../<?php echo htmlspecialchars($img['image_path']); ?>" style="width: 100%; height: 100%; object-fit: cover; border-radius: 0.5rem;">
                                    <a href="manage_events.php?delete_image=<?php echo $img['id']; ?>&return_id=<?php echo $edit_id; ?>" 
                                       onclick="return confirm('Delete this gallery image?')"
                                       style="position: absolute; top: -5px; right: -5px; background: var(--danger); color: white; width: 18px; height: 18px; border-radius: 50%; display: flex; align-items: center; justify-content: center; text-decoration: none; font-size: 0.6rem;">&times;</a>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label>Short Description (For cards)</label>
                <textarea name="short_description" rows="2" placeholder="Briefly describe the event..."><?php echo htmlspecialchars($event['short_description'] ?? ''); ?></textarea>
            </div>

            <div class="form-group">
                <label>Full Description</label>
                <textarea name="description" rows="8" placeholder="Detailed event details, agenda, etc." data-b64-target><?php echo htmlspecialchars($event['description'] ?? ''); ?></textarea>
            </div>

            <div class="grid-2">
                <div class="form-group">
                    <label>Tags (comma separated)</label>
                    <input type="text" name="tag" value="<?php echo htmlspecialchars($event['tag'] ?? ''); ?>" placeholder="e.g. Workshop, Seminar, IUT">
                </div>
                <div class="form-group">
                    <label>Event Icon</label>
                    <input type="hidden" name="logo" id="selected-icon" value="<?php echo htmlspecialchars($event['logo'] ?? 'fa-calendar'); ?>">
                    <div style="display: flex; gap: 0.5rem; flex-wrap: wrap; margin-top: 0.5rem;">
                        <?php foreach ($iconOptions as $icon): ?>
                            <div class="icon-picker-item <?php echo ($event['logo'] ?? 'fa-calendar') === $icon ? 'selected' : ''; ?>" 
                                 onclick="selectIcon('<?php echo $icon; ?>', this)" 
                                 style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; border: 1px solid var(--border); border-radius: 0.5rem; cursor: pointer; transition: all 0.2s;">
                                <i class="fas <?php echo $icon; ?>"></i>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label style="display: flex; align-items: center; gap: 0.75rem; cursor: pointer;">
                    <input type="checkbox" name="is_past" <?php echo ($event['is_past'] ?? 0) ? 'checked' : ''; ?> style="width: 1.25rem; height: 1.25rem; accent-color: var(--primary);">
                    <span>Mark as Past Event</span>
                </label>
            </div>

            <div style="display: flex; gap: 1rem; margin-top: 3rem; pt: 2rem; border-top: 1px solid var(--border);">
                <button type="submit" class="btn btn-primary" style="flex: 2;">
                    <i class="fas fa-save"></i> <?php echo $edit_id ? 'Save Changes' : 'Create Event'; ?>
                </button>
                <a href="manage_events.php" class="btn btn-secondary" style="flex: 1;">
                    Cancel
                </a>
            </div>
        </form>
    </div>

    <style>
        .icon-picker-item.selected { border-color: var(--primary) !important; background: var(--primary-light) !important; color: var(--primary) !important; }
    </style>

    <script>
        function selectIcon(icon, el) {
            document.querySelectorAll('.icon-picker-item').forEach(i => i.classList.remove('selected'));
            el.classList.add('selected');
            document.getElementById('selected-icon').value = icon;
        }

        function addNewCategory(val) {
            if(!val) return;
            const select = document.getElementById('category-select');
            const opt = document.createElement('option');
            opt.value = val;
            opt.text = val;
            opt.selected = true;
            select.add(opt);
        }

        document.getElementById('gallery-input').addEventListener('change', function() {
            const preview = document.getElementById('gallery-preview');
            preview.innerHTML = '';
            Array.from(this.files).forEach(file => {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const div = document.createElement('div');
                    div.style.position = 'relative';
                    div.style.aspectRatio = '1';
                    div.style.borderRadius = '0.75rem';
                    div.style.overflow = 'hidden';
                    div.style.border = '2px solid var(--primary-light)';
                    div.innerHTML = `<img src="${e.target.result}" style="width: 100%; height: 100%; object-fit: cover;">`;
                    preview.appendChild(div);
                }
                reader.readAsDataURL(file);
            });
        });
    </script>
<?php endif; ?>

<?php require_once 'footer.php'; ?>
