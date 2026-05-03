<?php
/**
 * IUT-SIKS Web Portal Configuration
 * 
 * Contains business logic for data ingestion, caching, and global constants.
 * Adheres to formal English standards and professional UI requirements.
 */

// Timezone Setting
date_default_timezone_set('Asia/Dhaka');

// Include Database Connection
require_once __DIR__ . '/db.php';

// Include Composer Autoloader
require_once dirname(__DIR__) . '/vendor/autoload.php';

// Global Constants
define('SITE_NAME', 'IUT-SIKS');
define('SITE_TAGLINE', 'Society of Islamic Knowledge Seekers');
define('IUT_ADDRESS', 'Islamic University of Technology, Board Bazar, Gazipur-1704');
define('MAPS_URL', 'https://www.google.com/maps/search/?api=1&query=Islamic+University+of+Technology');
define('MASJID_NAME', 'Masjid-e-Zainab IUT');

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
 */
function getEvents($is_past = false, $limit = null)
{
    global $pdo;
    if (!$pdo) return [];

    try {
        $sql = "SELECT * FROM events WHERE is_past = ? ORDER BY event_date DESC";
        if ($limit) $sql .= " LIMIT " . (int)$limit;
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$is_past ? 1 : 0]);
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        return [];
    }
}

/**
 * Fetch random Ayat from Al Quran Cloud API
 */
function getRandomAyat()
{
    $url = "https://api.alquran.cloud/v1/ayah/random/en.asad";
    $data = @json_decode(file_get_contents($url), true);
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
    $data = @json_decode(file_get_contents($url), true);
    if ($data && isset($data['hadiths'][0])) {
        return [
            'text' => $data['hadiths'][0]['text'],
            'source' => 'Sahih al-Bukhari'
        ];
    }
    return null;
}



