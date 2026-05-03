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
    $category = $_POST['category'];
    $is_past = isset($_POST['is_past']) ? 1 : 0;
    $logo = $_POST['logo']; // We'll just take a string (icon class or URL) for now

    if ($edit_id) {
        try {
            $stmt = $pdo->prepare("UPDATE events SET name=?, event_date=?, event_time=?, venue=?, short_description=?, description=?, tag=?, category=?, is_past=?, logo=? WHERE id=?");
            $stmt->execute([$name, $date, $time, $venue, $short_desc, $desc, $tag, $category, $is_past, $logo, $edit_id]);
            $message = "Event updated successfully.";
            $mode = 'list';
        } catch (PDOException $e) {
            $message = "Error: " . $e->getMessage();
        }
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO events (name, event_date, event_time, venue, short_description, description, tag, category, is_past, logo) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$name, $date, $time, $venue, $short_desc, $desc, $tag, $category, $is_past, $logo]);
            $message = "Event added successfully.";
            $mode = 'list';
        } catch (PDOException $e) {
            $message = "Error: " . $e->getMessage();
        }
    }
}

$events = [];
if ($mode === 'list') {
    $stmt = $pdo->query("SELECT * FROM events ORDER BY event_date DESC");
    $events = $stmt->fetchAll();
} elseif ($mode === 'edit' && $edit_id) {
    $stmt = $pdo->prepare("SELECT * FROM events WHERE id = ?");
    $stmt->execute([$edit_id]);
    $event = $stmt->fetch();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Events - SIKS Admin</title>
    <style>
        body { font-family: sans-serif; margin: 2rem; background: #f4f4f4; }
        .nav { margin-bottom: 2rem; }
        .nav a { margin-right: 1rem; text-decoration: none; color: #047857; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; background: white; margin-top: 1rem; }
        th, td { padding: 0.75rem; border: 1px solid #ddd; text-align: left; }
        th { background: #eee; }
        .form-group { margin-bottom: 1rem; }
        label { display: block; margin-bottom: 0.25rem; font-weight: bold; }
        input, textarea, select { width: 100%; padding: 0.5rem; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        .btn { padding: 0.5rem 1rem; background: #065f46; color: white; border: none; border-radius: 4px; cursor: pointer; text-decoration: none; display: inline-block; }
        .btn-red { background: #dc2626; }
        .msg { padding: 1rem; background: #d1fae5; color: #065f46; margin-bottom: 1rem; border-radius: 4px; }
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
                    <th>Category</th>
                    <th>Past?</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($events as $e): ?>
                    <tr>
                        <td><?php echo $e['event_date']; ?></td>
                        <td><?php echo htmlspecialchars($e['name']); ?></td>
                        <td><?php echo htmlspecialchars($e['category']); ?></td>
                        <td><?php echo $e['is_past'] ? 'Yes' : 'No'; ?></td>
                        <td>
                            <a href="manage_events.php?mode=edit&id=<?php echo $e['id']; ?>">Edit</a> | 
                            <a href="manage_events.php?delete=<?php echo $e['id']; ?>" onclick="return confirm('Are you sure?')">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

    <?php else: ?>
        <form method="POST">
            <div class="form-group">
                <label>Event Name</label>
                <input type="text" name="name" value="<?php echo htmlspecialchars($event['name'] ?? ''); ?>" required>
            </div>
            <div class="form-group">
                <label>Date</label>
                <input type="date" name="date" value="<?php echo $event['event_date'] ?? ''; ?>" required>
            </div>
            <div class="form-group">
                <label>Time</label>
                <input type="text" name="time" value="<?php echo htmlspecialchars($event['event_time'] ?? ''); ?>" placeholder="e.g. 10:00 AM">
            </div>
            <div class="form-group">
                <label>Venue</label>
                <input type="text" name="venue" value="<?php echo htmlspecialchars($event['venue'] ?? ''); ?>" required>
            </div>
            <div class="form-group">
                <label>Short Description</label>
                <textarea name="short_description"><?php echo htmlspecialchars($event['short_description'] ?? ''); ?></textarea>
            </div>
            <div class="form-group">
                <label>Full Description</label>
                <textarea name="description" rows="5"><?php echo htmlspecialchars($event['description'] ?? ''); ?></textarea>
            </div>
            <div class="form-group">
                <label>Tag</label>
                <input type="text" name="tag" value="<?php echo htmlspecialchars($event['tag'] ?? ''); ?>" placeholder="e.g. Workshop, Seminar">
            </div>
            <div class="form-group">
                <label>Category</label>
                <select name="category">
                    <option value="Community" <?php echo ($event['category'] ?? '') === 'Community' ? 'selected' : ''; ?>>Community</option>
                    <option value="Sports" <?php echo ($event['category'] ?? '') === 'Sports' ? 'selected' : ''; ?>>Sports</option>
                </select>
            </div>
            <div class="form-group">
                <label>Logo (Icon Class or URL)</label>
                <input type="text" name="logo" value="<?php echo htmlspecialchars($event['logo'] ?? ''); ?>" placeholder="e.g. fa-users or image-url">
            </div>
            <div class="form-group">
                <label>
                    <input type="checkbox" name="is_past" <?php echo ($event['is_past'] ?? 0) ? 'checked' : ''; ?>> Is Past Event?
                </label>
            </div>
            <button type="submit" class="btn"><?php echo $edit_id ? 'Update' : 'Add'; ?> Event</button>
        </form>
    <?php endif; ?>
</body>
</html>
