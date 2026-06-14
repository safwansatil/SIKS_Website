<?php
/**
 * Dynamic XML Sitemap Generator
 * 
 * Generates a sitemap.xml from static pages + database content (articles, events).
 * Served via .htaccess rewrite: /sitemap.xml → sitemap.php
 */

require_once 'includes/config.php';

header('Content-Type: application/xml; charset=utf-8');

$baseUrl = 'https://iutsiks.iutoic-dhaka.edu';

echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <!-- Static Pages -->
    <url>
        <loc><?php echo $baseUrl; ?>/</loc>
        <changefreq>daily</changefreq>
        <priority>1.0</priority>
    </url>
    <url>
        <loc><?php echo $baseUrl; ?>/about</loc>
        <changefreq>monthly</changefreq>
        <priority>0.8</priority>
    </url>
    <url>
        <loc><?php echo $baseUrl; ?>/events</loc>
        <changefreq>weekly</changefreq>
        <priority>0.9</priority>
    </url>
    <url>
        <loc><?php echo $baseUrl; ?>/articles</loc>
        <changefreq>weekly</changefreq>
        <priority>0.9</priority>
    </url>
    <url>
        <loc><?php echo $baseUrl; ?>/library</loc>
        <changefreq>weekly</changefreq>
        <priority>0.7</priority>
    </url>

<?php
// Dynamic Article Pages
if ($pdo) {
    try {
        $stmt = $pdo->query("SELECT id, title, slug, last_edited FROM articles ORDER BY last_edited DESC");
        $articles = $stmt->fetchAll();
        foreach ($articles as $article) {
            $slug = $article['slug'] ?: generateSlug($article['title']);
            $lastmod = date('Y-m-d', strtotime($article['last_edited']));
            echo "    <url>\n";
            echo "        <loc>{$baseUrl}/article/{$article['id']}/{$slug}</loc>\n";
            echo "        <lastmod>{$lastmod}</lastmod>\n";
            echo "        <changefreq>monthly</changefreq>\n";
            echo "        <priority>0.7</priority>\n";
            echo "    </url>\n";
        }
    } catch (PDOException $e) {
        // Silently skip on error
    }

    // Dynamic Event Pages
    try {
        $stmt = $pdo->query("SELECT id, name, slug, event_date FROM events ORDER BY event_date DESC");
        $events = $stmt->fetchAll();
        foreach ($events as $event) {
            $slug = $event['slug'] ?: generateSlug($event['name']);
            $lastmod = date('Y-m-d', strtotime($event['event_date']));
            echo "    <url>\n";
            echo "        <loc>{$baseUrl}/event/{$event['id']}/{$slug}</loc>\n";
            echo "        <lastmod>{$lastmod}</lastmod>\n";
            echo "        <changefreq>monthly</changefreq>\n";
            echo "        <priority>0.6</priority>\n";
            echo "    </url>\n";
        }
    } catch (PDOException $e) {
        // Silently skip on error
    }
}
?>
</urlset>
