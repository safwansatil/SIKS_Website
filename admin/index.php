<?php
require_once 'auth.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - SIKS</title>
    <style>
        body { font-family: sans-serif; margin: 2rem; background: #f4f4f4; }
        h1 { color: #065f46; }
        .nav { margin-bottom: 2rem; }
        .nav a { margin-right: 1rem; text-decoration: none; color: #047857; font-weight: bold; }
        .nav a:hover { text-decoration: underline; }
        .card-grid { display: grid; grid-template-cols: repeat(auto-fill, minmax(200px, 1fr)); gap: 1rem; }
        .card { background: white; padding: 1.5rem; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
        .card h2 { margin-top: 0; font-size: 1.25rem; }
        .card p { color: #666; font-size: 0.875rem; }
        .logout { color: red !important; }
    </style>
</head>
<body>
    <h1>SIKS Admin Dashboard</h1>
    <div class="nav">
        <a href="index.php">Dashboard</a>
        <a href="manage_prayers.php">Prayers</a>
        <a href="manage_events.php">Events</a>
        <a href="manage_about.php">About Page</a>
        <a href="manage_articles.php">Articles</a>
        <a href="logout.php" class="logout">Logout</a>
    </div>

    <div class="card-grid">
        <div class="card">
            <h2>Prayer Times</h2>
            <p>Update Jamaat timings for Masjid-e-Zainab IUT.</p>
            <a href="manage_prayers.php">Manage &rarr;</a>
        </div>
        <div class="card">
            <h2>Events</h2>
            <p>Add, edit, or delete society events.</p>
            <a href="manage_events.php">Manage &rarr;</a>
        </div>
        <div class="card">
            <h2>About Content</h2>
            <p>Manage Vision, Mission, and other About page sections.</p>
            <a href="manage_about.php">Manage &rarr;</a>
        </div>
        <div class="card">
            <h2>Articles</h2>
            <p>Write and manage articles for the community.</p>
            <a href="manage_articles.php">Manage &rarr;</a>
        </div>
    </div>
</body>
</html>
