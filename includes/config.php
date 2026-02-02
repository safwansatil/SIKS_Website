<?php
/**
 * IUT-SIKS Web Portal Configuration
 * 
 * Contains business logic for data ingestion, caching, and global constants.
 * Adheres to formal English standards and professional UI requirements.
 */

// Timezone Setting
date_default_timezone_set('Asia/Dhaka');

// Include Composer Autoloader
require_once dirname(__DIR__) . '/vendor/autoload.php';

// Global Constants
define('SITE_NAME', 'IUT-SIKS');
define('SITE_TAGLINE', 'Society of Islamic Knowledge Seekers');
define('IUT_ADDRESS', 'Islamic University of Technology, Board Bazar, Gazipur-1704');
define('MAPS_URL', 'https://www.google.com/maps/search/?api=1&query=Islamic+University+of+Technology');

// Social Media Links
define('YOUTUBE_URL', 'https://www.youtube.com/@IUTSIKSOfficial');
define('FACEBOOK_URL', 'https://www.facebook.com/iutsiks'); // Placeholder, PRD said "Link to official page"

// Data Source Configuration
define('GOOGLE_SHEETS_CSV_URL', 'https://docs.google.com/spreadsheets/d/1oD22Op0-b0D5tgNqZFbDWJPaju0mQ_H234Rx0ZgRtKM/export?format=csv');
define('CACHE_FILE', 'prayer_cache.json');
define('CACHE_EXPIRATION', 3600); // 1 hour

/**
 * Fetches prayer times from Google Sheets or local cache.
 * 
 * @return array|null Returns an array of prayer times or null on failure.
 */
function getPrayerTimes()
{
    $cachePath = dirname(__DIR__) . DIRECTORY_SEPARATOR . CACHE_FILE;

    // Check if cache exists and is valid
    if (file_exists($cachePath) && (time() - filemtime($cachePath) < CACHE_EXPIRATION)) {
        $cachedData = json_decode(file_get_contents($cachePath), true);
        if ($cachedData) {
            return $cachedData;
        }
    }

    // Cache invalid or missing, fetch from source
    $csvData = fetchUrlContent(GOOGLE_SHEETS_CSV_URL);
    if ($csvData) {
        $parsedData = parsePrayerCsv($csvData);
        if ($parsedData) {
            file_put_contents($cachePath, json_encode($parsedData));
            return $parsedData;
        }
    }

    // Fallback to cache if network fails, even if expired
    if (file_exists($cachePath)) {
        return json_decode(file_get_contents($cachePath), true);
    }

    return null;
}

/**
 * Robustly fetches content from a URL using file_get_contents or cURL.
 * 
 * @param string $url The URL to fetch.
 * @return string|false The content or false on failure.
 */
function fetchUrlContent($url)
{
    // Try file_get_contents first
    $context = stream_context_create([
        "http" => [
            "header" => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) PHP/" . PHP_VERSION . "\r\n",
            "follow_location" => 1,
            "timeout" => 10
        ]
    ]);

    $content = @file_get_contents($url, false, $context);

    if ($content === false && function_exists('curl_init')) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) PHP/' . PHP_VERSION);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        $content = curl_exec($ch);
        curl_close($ch);
    }

    return $content;
}

/**
 * Parses the prayer time CSV data.
 * Assumes CSV structure: Prayer Name, Time
 * 
 * @param string $csvContent The CSV content.
 * @return array Parsed prayer times.
 */
function parsePrayerCsv($csvContent)
{
    $lines = explode("\n", str_replace("\r", "", $csvContent));
    $prayers = [];

    foreach ($lines as $line) {
        $data = str_getcsv($line);
        if (count($data) >= 2) {
            $name = trim($data[0]);
            $time = trim($data[1]);

            // Aggressive Regex Filtering for Sunrise and Headers
            // Catch: Sunrise, Shurooq, Churooq, Prayer Name, Time
            if (
                preg_match('/(sunrise|shurooq|churooq|prayer\s*name|time)/i', $name) ||
                preg_match('/(time)/i', $time)
            ) {
                continue;
            }

            // Basic validation to ensure it looks like a prayer time (e.g., "5:15 AM")
            if (!empty($name) && !empty($time)) {
                $prayers[] = [
                    'name' => $name,
                    'time' => $time
                ];
            }
        }
    }

    return $prayers;
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
        // If it's before the first prayer (Fajr), the "current" might be Isha from yesterday, 
        // but highlighting Fajr as "Next" is usually better. 
        // For simplicity, if after all prayers, last one is active. If before all, last one (from yesterday) is technically active.
        $activePrayer = end($prayers)['name'];
    }

    return $activePrayer;
}

/**
 * Static Events Data
 */
define('EVENTS', [
    [
        'id' => 1,
        'title' => 'Annual Quran Competition',
        'category' => 'Community',
        'date' => '2026-03-15',
        'time' => '10:00 AM',
        'location' => 'IUT Auditorium',
        'description' => 'A university-wide competition focused on Quranic recitation and memorization. Open to all students.',
        'icon' => 'fa-book-quran'
    ],
    [
        'id' => 2,
        'title' => 'Islamic Calligraphy Workshop',
        'category' => 'Community',
        'date' => '2026-03-20',
        'time' => '02:30 PM',
        'location' => 'Art Studio',
        'description' => 'Learn the foundations of Thuluth and Naskh scripts from professional calligraphers.',
        'icon' => 'fa-pen-nib'
    ],
    [
        'id' => 3,
        'title' => 'SIKS Futsal Cup',
        'category' => 'Sports',
        'date' => '2026-04-05',
        'time' => '05:00 PM',
        'location' => 'IUT Sports Ground',
        'description' => 'A friendly football tournament to build brotherhood and physical well-being among society members.',
        'icon' => 'fa-futbol'
    ],
    [
        'id' => 4,
        'title' => 'Ramadan Preparation Seminar',
        'category' => 'Community',
        'date' => '2026-02-28',
        'time' => '07:30 PM',
        'location' => 'IUT Mosque',
        'description' => 'A special lecture series dedicated to spiritual and physical preparation for the holy month of Ramadan.',
        'icon' => 'fa-moon'
    ]
]);

/**
 * Moments of Community Data
 */
define('MOMENTS', [
    ['title' => 'Annual Gathering', 'icon' => 'fa-users'],
    ['title' => 'Islamic Congregation', 'icon' => 'fa-mosque'],
    ['title' => 'Cricket Tournament', 'icon' => 'fa-trophy'],
    ['title' => 'Fundraiser For Palestine', 'icon' => 'fa-hand-holding-heart'],
    ['title' => 'Islamic Lecture', 'icon' => 'fa-chalkboard-teacher'],
    ['title' => 'Charity Drive', 'icon' => 'fa-box-open']
]);

/**
 * Growing Together Values
 */
define('VALUES', [
    [
        'title' => 'Purpose-Driven',
        'desc' => 'Guided by Islamic values and principles',
        'icon' => 'fa-compass'
    ],
    [
        'title' => 'Community',
        'desc' => 'Building a stronger community every day',
        'icon' => 'fa-people-group'
    ],
    [
        'title' => 'Knowledge',
        'desc' => 'Continuous learning and spiritual growth',
        'icon' => 'fa-book-open'
    ],
    [
        'title' => 'Excellence',
        'desc' => 'Striving for the highest standards',
        'icon' => 'fa-star'
    ]
]);


