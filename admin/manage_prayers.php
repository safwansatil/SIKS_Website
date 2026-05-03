<?php
require_once 'auth.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_prayer'])) {
    $id = $_POST['id'];
    $time = $_POST['time'];

    try {
        $stmt = $pdo->prepare("UPDATE prayer_times SET prayer_time = ? WHERE id = ?");
        $stmt->execute([$time, $id]);
        $message = "Prayer time updated successfully.";
    } catch (PDOException $e) {
        $message = "Error: " . $e->getMessage();
    }
}

$prayers = getPrayerTimes();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Prayers - SIKS Admin</title>
    <style>
        body { font-family: sans-serif; margin: 2rem; background: #f4f4f4; }
        .nav { margin-bottom: 2rem; }
        .nav a { margin-right: 1rem; text-decoration: none; color: #047857; font-weight: bold; }
        table { width: 100%; border-collapse: collapse; background: white; border-radius: 8px; overflow: hidden; }
        th, td { padding: 1rem; text-align: left; border-bottom: 1px solid #eee; }
        th { background: #065f46; color: white; }
        .msg { padding: 1rem; background: #d1fae5; color: #065f46; border-radius: 4px; margin-bottom: 1rem; }
        input[type="text"] { padding: 0.5rem; border: 1px solid #ccc; border-radius: 4px; }
        button { padding: 0.5rem 1rem; background: #065f46; color: white; border: none; border-radius: 4px; cursor: pointer; }
    </style>
</head>
<body>
    <div class="nav">
        <a href="index.php">&larr; Dashboard</a>
    </div>
    <h1>Manage Prayer Times (Masjid-e-Zainab IUT)</h1>

    <?php if ($message): ?>
        <div class="msg"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>

    <table>
        <thead>
            <tr>
                <th>Prayer Name</th>
                <th>Current Time</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($prayers as $index => $prayer): ?>
                <tr>
                    <td><?php echo htmlspecialchars($prayer['name']); ?></td>
                    <form method="POST">
                        <input type="hidden" name="id" value="<?php echo $index + 1; ?>">
                        <td>
                            <input type="text" name="time" value="<?php echo htmlspecialchars($prayer['time']); ?>" required>
                        </td>
                        <td>
                            <button type="submit" name="update_prayer">Update</button>
                        </td>
                    </form>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
