<?php
/**
 * IUT-SIKS Web Portal Configuration
 * 
 * Contains business logic for data ingestion, caching, and global constants.
 * Adheres to formal English standards and professional UI requirements.
 */

// Timezone Setting
date_default_timezone_set('Asia/Dhaka');

// UTF-8 Header
header('Content-Type: text/html; charset=utf-8');

// Include Database Connection
require_once __DIR__ . '/db.php';

// Include Composer Autoloader
if (file_exists(dirname(__DIR__) . '/vendor/autoload.php')) {
    require_once dirname(__DIR__) . '/vendor/autoload.php';
}

// Global Constants
define('SITE_NAME', 'IUT-SIKS');
define('SITE_TAGLINE', 'Society of Islamic Knowledge Seekers');
define('IUT_ADDRESS', 'Islamic University of Technology, Board Bazar, Gazipur-1704');
define('MAPS_URL', 'https://www.google.com/maps/search/?api=1&query=Islamic+University+of+Technology');
define('MASJID_NAME', 'Masjid-e-Zainab IUT');
define('UPLOAD_DIR', dirname(__DIR__) . '/uploads/');
define('UPLOAD_URL', 'uploads/');

// Social Media Links
define('YOUTUBE_URL', 'https://www.youtube.com/@IUTSIKSOfficial');
define('FACEBOOK_URL', 'https://www.facebook.com/iutsiks');

/**
 * Fetches prayer times from the database.
 * 
 * @return array|null Returns an array of prayer times or null on failure.
 */
function getPrayerTimes()
{
    global $pdo;
    if (!$pdo) return null;

    try {
        $stmt = $pdo->query("SELECT prayer_name as name, prayer_time as time FROM prayer_times ORDER BY id ASC");
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        return null;
    }
}

/**
 * Determines which prayer is currently active or upcoming.
 * 
 * @param array $prayers Array of prayer times.
 * @return string|null The name of the current prayer.
 */
function getCurrentPrayer($prayers)
{
    $now = time();
    $today = date('Y-m-d ');

    $activePrayer = null;

    // Sort prayers by time to be safe
    usort($prayers, function ($a, $b) use ($today) {
        return strtotime($today . $a['time']) - strtotime($today . $b['time']);
    });

    foreach ($prayers as $index => $prayer) {
        $prayerTime = strtotime($today . $prayer['time']);
        $nextPrayerTime = isset($prayers[$index + 1]) ? strtotime($today . $prayers[$index + 1]['time']) : null;

        // If it's after the current prayer and before the next one (or it's the last one)
        if ($now >= $prayerTime) {
            if ($nextPrayerTime === null || $now < $nextPrayerTime) {
                $activePrayer = $prayer['name'];
            }
        }
    }

    // Default to the last prayer if it's late at night (e.g., after Isha but before next Fajr)
    if ($activePrayer === null && !empty($prayers)) {
        $activePrayer = end($prayers)['name'];
    }

    return $activePrayer;
}

/**
 * Helper to get About Content by type
 */
function getAboutContent($type = null)
{
    global $pdo;
    if (!$pdo) return [];

    try {
        if ($type) {
            $stmt = $pdo->prepare("SELECT * FROM about_content WHERE type = ? ORDER BY sort_order ASC");
            $stmt->execute([$type]);
        } else {
            $stmt = $pdo->query("SELECT * FROM about_content ORDER BY sort_order ASC");
        }
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        return [];
    }
}

/**
 * Helper to get Events
 * 
 * @param bool|null $is_past null=all, true=past only, false=upcoming only
 * @param int|null $limit Max results
 * @return array
 */
function getEvents($is_past = false, $limit = null)
{
    global $pdo;
    if (!$pdo) return [];

    try {
        if ($is_past === null) {
            // Get all events
            $sql = "SELECT * FROM events ORDER BY event_date DESC";
        } else {
            $sql = "SELECT * FROM events WHERE is_past = ? ORDER BY ";
            // Upcoming: soonest first. Past: most recent first.
            $sql .= $is_past ? "event_date DESC" : "event_date ASC";
        }
        if ($limit) $sql .= " LIMIT " . (int)$limit;
        
        if ($is_past === null) {
            $stmt = $pdo->query($sql);
        } else {
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$is_past ? 1 : 0]);
        }
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        return [];
    }
}

/**
 * Get a single event by ID
 */
function getEventById($id)
{
    global $pdo;
    if (!$pdo || !$id) return null;

    try {
        $stmt = $pdo->prepare("SELECT * FROM events WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    } catch (PDOException $e) {
        return null;
    }
}

/**
 * Get images for a specific event
 */
function getEventImages($eventId)
{
    global $pdo;
    if (!$pdo) return [];

    try {
        $stmt = $pdo->prepare("SELECT * FROM event_images WHERE event_id = ?");
        $stmt->execute([$eventId]);
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        return [];
    }
}

/**
 * Get hero slides for homepage carousel
 */
function getHeroSlides()
{
    global $pdo;
    if (!$pdo) return [];

    try {
        $stmt = $pdo->query("SELECT * FROM hero_slides WHERE is_active = 1 ORDER BY sort_order ASC");
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        return [];
    }
}

/**
 * Get a single article by ID
 */
function getArticleById($id)
{
    global $pdo;
    if (!$pdo || !$id) return null;

    try {
        $stmt = $pdo->prepare("SELECT * FROM articles WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    } catch (PDOException $e) {
        return null;
    }
}

/**
 * Generate a URL-safe slug from a title
 */
function generateSlug($title)
{
    $slug = strtolower(trim($title));
    $slug = preg_replace('/[^a-z0-9-]/', '-', $slug);
    $slug = preg_replace('/-+/', '-', $slug);
    $slug = trim($slug, '-');
    return $slug ?: 'untitled';
}

/**
 * Calculate estimated reading time from text
 */
function calculateReadingTime($text)
{
    $wordCount = str_word_count(strip_tags($text));
    $minutes = max(1, ceil($wordCount / 200));
    return $minutes;
}

/**
 * Handle file upload
 * 
 * @param array $file The $_FILES entry
 * @param string $subdir Subdirectory under uploads/ (e.g., 'events', 'articles', 'hero')
 * @return string|false The relative path to the uploaded file, or false on failure
 */
function handleFileUpload($file, $subdir = 'general')
{
    if (!isset($file['tmp_name']) || empty($file['tmp_name']) || $file['error'] !== UPLOAD_ERR_OK) {
        return false;
    }

    $uploadDir = UPLOAD_DIR . $subdir . '/';
    
    // Create directory if it doesn't exist
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    // Validate file type
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/svg+xml'];
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    if (!in_array($mimeType, $allowedTypes)) {
        return false;
    }

    // Generate unique filename and use safe extension based on MIME type
    $mimeToExt = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/gif' => 'gif',
        'image/webp' => 'webp',
        'image/svg+xml' => 'svg'
    ];
    $ext = ($subdir === 'hero') ? 'jpg' : ($mimeToExt[$mimeType] ?? 'png');
    $filename = uniqid() . '_' . time() . '.' . $ext;
    $destPath = $uploadDir . $filename;

    if (move_uploaded_file($file['tmp_name'], $destPath)) {
        // For hero slides: resize/crop to exactly 1920x1080
        if ($subdir === 'hero') {
            resizeImageToFit($destPath, 1920, 1080);
        }
        return UPLOAD_URL . $subdir . '/' . $filename;
    }

    return false;
}

/**
 * Resize and crop an image to exact dimensions (cover-fit)
 * Uses GD library to create a center-cropped image at the target size
 */
function resizeImageToFit($filePath, $targetW = 1920, $targetH = 1080)
{
    $info = @getimagesize($filePath);
    if (!$info) return false;

    $srcW = $info[0];
    $srcH = $info[1];
    $mime = $info['mime'];

    // Create source image based on type
    switch ($mime) {
        case 'image/jpeg': $src = @imagecreatefromjpeg($filePath); break;
        case 'image/png':  $src = @imagecreatefrompng($filePath);  break;
        case 'image/gif':  $src = @imagecreatefromgif($filePath);  break;
        case 'image/webp': $src = @imagecreatefromwebp($filePath); break;
        default: return false;
    }

    if (!$src) return false;

    // Calculate crop dimensions (center crop to match target aspect ratio)
    $targetRatio = $targetW / $targetH;
    $srcRatio = $srcW / $srcH;

    if ($srcRatio > $targetRatio) {
        // Source is wider: crop sides
        $cropH = $srcH;
        $cropW = (int)($srcH * $targetRatio);
        $cropX = (int)(($srcW - $cropW) / 2);
        $cropY = 0;
    } else {
        // Source is taller: crop top/bottom
        $cropW = $srcW;
        $cropH = (int)($srcW / $targetRatio);
        $cropX = 0;
        $cropY = (int)(($srcH - $cropH) / 2);
    }

    // Create destination canvas
    $dst = imagecreatetruecolor($targetW, $targetH);
    imagecopyresampled($dst, $src, 0, 0, $cropX, $cropY, $targetW, $targetH, $cropW, $cropH);

    // Save as JPEG at high quality
    imagejpeg($dst, $filePath, 90);

    imagedestroy($src);
    imagedestroy($dst);

    return true;
}

/**
 * Fetch random Ayat from Al Quran Cloud API
 */
function getRandomAyat()
{
    $url = "https://api.alquran.cloud/v1/ayah/random/en.asad";
    $data = @json_decode(@file_get_contents($url), true);
    if ($data && isset($data['data'])) {
        return [
            'text' => $data['data']['text'],
            'surah' => $data['data']['surah']['englishName'],
            'ayah' => $data['data']['numberInSurah']
        ];
    }
    return null;
}

/**
 * Fetch random Hadith from fawazahmed0/hadith-api
 */
function getRandomHadith()
{
    $randomId = rand(1, 500);
    $url = "https://cdn.jsdelivr.net/gh/fawazahmed0/hadith-api@1/editions/eng-bukhari/$randomId.json";
    $data = @json_decode(@file_get_contents($url), true);
    if ($data && isset($data['hadiths'][0])) {
        return [
            'text' => $data['hadiths'][0]['text'],
            'source' => 'Sahih al-Bukhari'
        ];
    }
    return null;
}
