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

    $today = date('Y-m-d');
    try {
        if ($is_past === null) {
            // Get all events
            $sql = "SELECT * FROM events ORDER BY event_date DESC";
        } else {
            if ($is_past) {
                // Past events: strictly before today, ordered most recent first
                $sql = "SELECT * FROM events WHERE event_date < ? ORDER BY event_date DESC";
            } else {
                // Upcoming events: today and onwards, ordered soonest first
                $sql = "SELECT * FROM events WHERE event_date >= ? ORDER BY event_date ASC";
            }
        }
        
        if ($limit) $sql .= " LIMIT " . (int)$limit;
        
        if ($is_past === null) {
            $stmt = $pdo->query($sql);
        } else {
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$today]);
        }
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        return [];
    }
}

/**
 * Get all dynamic event categories
 */
function getEventCategories()
{
    global $pdo;
    if (!$pdo) return [];

    try {
        $stmt = $pdo->query("SELECT * FROM event_categories ORDER BY name ASC");
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

    // Generate unique filename
    $mimeToExt = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/gif' => 'gif',
        'image/webp' => 'webp',
        'image/svg+xml' => 'svg'
    ];
    $ext = $mimeToExt[$mimeType] ?? 'png';
    $filename = uniqid() . '_' . time() . '.' . $ext;
    $destPath = $uploadDir . $filename;

    if (move_uploaded_file($file['tmp_name'], $destPath)) {
        // For hero slides: resize/crop to exactly 1920x1080
        if ($subdir === 'hero') {
            resizeImageToFit($destPath, 1920, 1080);
        }
        // For events/articles cover: optimize if needed (future proofing)
        
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

function getRandomAyat($forceRefresh = false)
{
    $cacheFile = dirname(__DIR__) . '/cache/ayat_cache.json';
    $cacheTime = 10; // 10 seconds cache
    
    // Check cache if not forcing refresh
    if (!$forceRefresh && file_exists($cacheFile) && (time() - filemtime($cacheFile)) < $cacheTime) {
        $cached = json_decode(file_get_contents($cacheFile), true);
        if ($cached) {
            return $cached;
        }
    }
    
    // Try multiple API endpoints for redundancy
    $apis = [
        'https://api.alquran.cloud/v1/ayah/random/en.sahih',
        'https://cdn.jsdelivr.net/gh/fawazahmed0/quran-api@1/editions/eng-asad/random.json'
    ];
    
    $data = null;
    foreach ($apis as $api) {
        $response = @file_get_contents($api);
        if ($response !== false) {
            $json = json_decode($response, true);
            
            // Handle different API response formats
            if (isset($json['data'])) {
                // Al Quran Cloud format
                $data = [
                    'text' => $json['data']['text'],
                    'surah' => $json['data']['surah']['englishName'],
                    'ayah' => $json['data']['numberInSurah'],
                    'source' => 'Quran'
                ];
                break;
            } elseif (isset($json['text']) && isset($json['surah'])) {
                // Simple format
                $data = [
                    'text' => $json['text'],
                    'surah' => $json['surah'],
                    'ayah' => $json['ayah'] ?? 1,
                    'source' => 'Quran'
                ];
                break;
            }
        }
    }
    
    // Save to cache
    if ($data) {
        if (!is_dir(dirname($cacheFile))) {
            mkdir(dirname($cacheFile), 0755, true);
        }
        file_put_contents($cacheFile, json_encode($data));
        return $data;
    }
    
    // Fallback to hardcoded ayat if APIs fail
    return getFallbackAyat();
}
function getFallbackAyat()
{
    $ayatList = [
        ['text' => 'Indeed, prayer prohibits immorality and wrongdoing.', 'surah' => 'Al-Ankabut', 'ayah' => 45],
        ['text' => 'And whatever good you do - Allah knows it.', 'surah' => 'Al-Baqarah', 'ayah' => 197],
        ['text' => 'Indeed, Allah is with the patient.', 'surah' => 'Al-Baqarah', 'ayah' => 153],
        ['text' => 'So remember Me; I will remember you.', 'surah' => 'Al-Baqarah', 'ayah' => 152],
        ['text' => 'And do good; indeed, Allah loves the doers of good.', 'surah' => 'Al-Baqarah', 'ayah' => 195]
    ];
    
    return $ayatList[array_rand($ayatList)];
}

function getRandomHadith($forceRefresh = false)
{
    $cacheFile = dirname(__DIR__) . '/cache/hadith_cache.json';
    $cacheTime = 10; // 10 seconds cache
    
    // Check cache
    if (!$forceRefresh && file_exists($cacheFile) && (time() - filemtime($cacheFile)) < $cacheTime) {
        $cached = json_decode(file_get_contents($cacheFile), true);
        if ($cached) {
            return $cached;
        }
    }
    
    // Primary API: Sunnah.com (more reliable)
    $hadiths = [
        // Pre-fetch some high-quality hadiths as fallback
        ['text' => 'The best among you are those who have the best manners and character.', 'source' => 'Sahih al-Bukhari'],
        ['text' => 'None of you truly believes until he loves for his brother what he loves for himself.', 'source' => 'Sahih al-Bukhari'],
        ['text' => 'The strong believer is better and more beloved to Allah than the weak believer, while there is good in both.', 'source' => 'Sahih Muslim'],
        ['text' => 'Do not be people without minds of your own, saying that if others treat you well you will treat them well, and if they do wrong you will do wrong. Instead, accustom yourselves to do good if people do good and not to do wrong if they do evil.', 'source' => 'Jami at-Tirmidhi'],
        ['text' => 'Make things easy for people and do not make them difficult, and give them good news and do not repel them.', 'source' => 'Sahih al-Bukhari']
    ];
    
    // Try API first
    $randomId = rand(1, 700); // Broader range for more variety
    
    // Try multiple hadith APIs
    $apis = [
        "https://cdn.jsdelivr.net/gh/fawazahmed0/hadith-api@1/editions/eng-bukhari/{$randomId}.json",
        "https://api.sunnah.com/v1/hadiths/bukhari/random", // Requires API key
        "https://hadithapi.com/api/hadiths/random?limit=1"  // Requires API key
    ];
    
    $data = null;
    foreach ($apis as $api) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $api);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_USERAGENT, 'IUT-SIKS-Website/1.0');
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode === 200 && $response) {
            $json = json_decode($response, true);
            if ($json) {
                // Handle different API response formats
                if (isset($json['hadiths'][0])) {
                    $data = [
                        'text' => $json['hadiths'][0]['text'],
                        'source' => $json['hadiths'][0]['reference']['book'] ?? 'Sahih al-Bukhari'
                    ];
                    break;
                } elseif (isset($json['hadith']['text'])) {
                    $data = [
                        'text' => $json['hadith']['text'],
                        'source' => $json['hadith']['source'] ?? 'Hadith'
                    ];
                    break;
                }
            }
        }
    }
    
    // If no API response, use random from fallback array
    if (!$data) {
        $randomIndex = array_rand($hadiths);
        $data = $hadiths[$randomIndex];
    }
    
    // Save to cache
    if ($data && is_array($data)) {
        if (!is_dir(dirname($cacheFile))) {
            mkdir(dirname($cacheFile), 0755, true);
        }
        file_put_contents($cacheFile, json_encode($data));
    }
    
    return $data;
}

