<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?php echo SITE_NAME; ?> |
        <?php echo SITE_TAGLINE; ?>
    </title>
    <link rel="icon" type="image/png" href="/assets/images/Logo-green.png?v=2">

    <!-- Meta Tags for SEO -->
    <meta name="description"
        content="Official portal of the Society of Islamic Knowledge Seekers (SIKS) at the Islamic University of Technology. View prayer times, upcoming events, and community updates.">
    <meta name="keywords" content="IUT, SIKS, Islamic Society, Prayer Times, IUT Mosque, Islamic Knowledge">
    
    <!-- Canonical URL -->
    <link rel="canonical" href="https://iutsiks.iutoic-dhaka.edu<?php echo explode('?', $_SERVER['REQUEST_URI'])[0]; ?>">

    <!-- Tailwind CSS via CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        emerald: {
                            50: '#ecfdf5',
                            100: '#d1fae5',
                            200: '#a7f3d0',
                            300: '#6ee7b7',
                            400: '#34d399',
                            500: '#10b981',
                            600: '#059669',
                            700: '#047857',
                            800: '#065f46',
                            900: '#064e3b',
                            950: '#022c22',
                        },
                    },
                    fontFamily: {
                        sans: ['Inter', 'Noto Sans Bengali', 'Noto Sans Arabic', 'ui-sans-serif', 'system-ui', '-apple-system', 'BlinkMacSystemFont', 'Segoe UI', 'Roboto', 'Helvetica Neue', 'Arial', 'Noto Sans', 'sans-serif'],
                        display: ['Outfit', 'sans-serif'],
                        arabic: ['Noto Naskh Arabic', 'Noto Sans Arabic', 'serif'],
                        bengali: ['Noto Sans Bengali', 'sans-serif'],
                    },
                }
            }
        }
    </script>

    <!-- HTMX -->
    <script src="https://unpkg.com/htmx.org@1.9.10"></script>

    <!-- Google Fonts (Inter, Outfit + Arabic/Bengali support) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@400;500;600;700&family=Noto+Sans+Bengali:wght@400;500;600;700&family=Noto+Naskh+Arabic:wght@400;500;600;700&display=swap"
        rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Custom Styles -->
    <link rel="stylesheet" href="/css/styles.css">
    
    <!-- Modern UX Logic -->
    <script src="/assets/js/app.js" defer></script>

    <style>
        :root {
            --emerald-essence: #022c22;
            --emerald-deep: #022c22;
            --emerald-light: #ffffff;
        }

        ::selection {
            background: rgba(0, 0, 0, 0.1);
            color: #022c22;
        }

        @keyframes fade-in-page {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-page {
            animation: fade-in-page 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        .glass-effect {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        }

        /* Fixed readability over dark backgrounds */
        .header-solid {
            background: rgba(255, 255, 255, 0.98) !important;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.03);
        }

        /* Always white navbar */
        #main-navbar {
            background: rgba(255, 255, 255, 0.98) !important;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.03);
            backdrop-filter: none;
        }

        .glass-dark {
            background: rgba(2, 44, 34, 0.8);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }

        body {
            background-color: #ffffff;
            color: var(--emerald-deep);
            overflow-x: hidden;
        }

        .nav-link {
            position: relative;
            font-size: 0.75rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: #000000;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .nav-link:hover {
            color: var(--emerald-essence);
            opacity: 0.7;
        }

        /* Active link indicator - Black */
        .active-link::after {
            content: '';
            position: absolute;
            bottom: -4px;
            left: 0;
            width: 100%;
            height: 2px;
            background-color: #000000;
            border-radius: 2px;
        }

        #countdown-bar {
            transition: transform 0.4s cubic-bezier(0.16, 1, 0.3, 1), opacity 0.4s ease;
        }

        .bar-hidden {
            transform: translateY(-100%);
            opacity: 0;
            pointer-events: none;
        }

        /* Hide scrollbar for Chrome, Safari and Opera */
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }

        /* Hide scrollbar for IE, Edge and Firefox */
        .no-scrollbar {
            -ms-overflow-style: none;
            /* IE and Edge */
            scrollbar-width: none;
            /* Firefox */
        }

        /* HTMX Indicator Styles */
        .htmx-indicator {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 3px;
            background: #10b981;
            z-index: 9999;
            opacity: 0;
            transition: opacity 200ms ease-in;
            pointer-events: none;
        }
        .htmx-request .htmx-indicator {
            opacity: 1;
            animation: htmx-progress 2s infinite ease-in-out;
        }
        @keyframes htmx-progress {
            0% { width: 0; left: 0; }
            50% { width: 70%; left: 15%; }
            100% { width: 100%; left: 100%; }
        }
    </style>
</head>

<?php 
    $currentScript = basename($_SERVER['SCRIPT_NAME']);
    $isDetailPage = in_array($currentScript, ['article.php', 'event_details.php']);
?>
<body class="bg-white font-sans text-emerald-950 overflow-x-hidden" 
      data-page-type="<?php echo $isDetailPage ? 'detail' : 'main'; ?>">
    <!-- Reading Progress Bar -->
    <div id="reading-progress-bar" class="fixed top-[64px] left-0 w-0 h-1.5 bg-emerald-600 z-[120] transition-all duration-150 shadow-[0_0_10px_rgba(16,185,129,0.3)]"></div>

    <!-- Global Toast Container -->
    <div id="toast-container"></div>

    <!-- Back to Top Button -->
    <button id="back-to-top" 
            class="fixed bottom-8 right-8 bg-emerald-950 text-white w-12 h-12 rounded-full shadow-2xl opacity-0 invisible translate-y-10 transition-all duration-500 hover:bg-emerald-800 z-[150] flex items-center justify-center">
        <i class="fas fa-arrow-up"></i>
    </button>
    <!-- HTMX Loading Indicator (Modern Skeleton) -->
    <div class="htmx-indicator fixed inset-0 z-[60] pointer-events-none">
        <!-- Top Loading Bar -->
        <div class="absolute top-0 left-0 h-1 bg-emerald-600 w-full">
            <div class="h-full bg-emerald-400 skeleton w-full"></div>
        </div>
        <!-- Content Shimmer Overlay -->
        <div class="absolute inset-0 bg-white/50 backdrop-blur-[2px] flex items-center justify-center">
            <div class="w-full max-w-3xl px-8 space-y-8 animate-pulse">
                <div class="h-12 bg-emerald-950/5 rounded-2xl w-3/4"></div>
                <div class="h-64 bg-emerald-950/5 rounded-[32px] w-full"></div>
                <div class="space-y-4">
                    <div class="h-4 bg-emerald-950/5 rounded w-full"></div>
                    <div class="h-4 bg-emerald-950/5 rounded w-5/6"></div>
                    <div class="h-4 bg-emerald-950/5 rounded w-4/6"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Global Loader -->
    <div id="global-loader"
        class="fixed inset-0 z-[9999] bg-white flex items-center justify-center transition-opacity duration-700 ease-out">
        <div class="relative flex flex-col items-center">
            <img src="/assets/images/loader-logo.png?v=2" alt="Loading..." class="h-24 w-auto animate-pulse">
        </div>
    </div>
    <script>
        // Check if user has visited in this session
        if (sessionStorage.getItem('siks_visited')) {
            document.getElementById('global-loader').style.display = 'none';
        } else {
            window.addEventListener('load', () => {
                const loader = document.getElementById('global-loader');
                setTimeout(() => {
                    loader.style.opacity = '0';
                    setTimeout(() => {
                        loader.style.display = 'none';
                        sessionStorage.setItem('siks_visited', 'true');
                    }, 700);
                }, 800); // 800ms delay for brand visibility
            });
        }
    </script>
    <!-- Navbar Container -->
    <div class="fixed top-0 left-0 right-0 z-[100]" id="main-header-container">
        <!-- Main Navigation Header -->
        <header class="glass-effect" id="main-navbar">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-16">
                    <!-- Logo & Brand -->
                    <a href="/" hx-get="/" hx-target="#main-content" hx-push-url="true" hx-select="#main-content" class="flex items-center space-x-4 group cursor-pointer">
                        <div class="relative">
                            <img src="/assets/images/logo.png?v=2" alt="Society of Islamic Knowledge Seekers Logo"
                                class="h-10 w-auto group-hover:scale-110 transition-transform duration-500">
                        </div>
                        <div class="flex flex-col">
                            <span
                                class="text-[13px] font-black tracking-tight text-black uppercase leading-[1.1] max-w-[200px] md:max-w-none">
                                Society of Islamic <br class="md:hidden"> Knowledge Seekers
                            </span>
                        </div>
                    </a>

                    <!-- Navigation Links - Justified to Right -->
                    <nav class="hidden md:flex items-center space-x-10">
                        <?php
                        $currentPage = basename($_SERVER['PHP_SELF'], '.php');
                        $links = [
                            'index' => 'Home',
                            'about' => 'About',
                            'events' => 'Events',
                            'articles' => 'Articles',
                            'library' => 'Library'
                        ];
                        foreach ($links as $path => $label):
                            $isActive = ($currentPage == $path || ($currentPage == '' && $path == 'index'));
                            ?>
                            <a href="/<?php echo $path; ?>" 
                               hx-get="/<?php echo $path; ?>" 
                               hx-target="#main-content" 
                               hx-push-url="true" 
                               hx-select="#main-content"
                               class="nav-link <?php echo $isActive ? 'active-link' : ''; ?>">
                                <?php echo $label; ?>
                            </a>
                        <?php endforeach; ?>
                    </nav>
                </div>

                <!-- Mobile Navigation Tabs (visible only on small screens) -->
                <nav class="md:hidden pb-0 overflow-x-auto no-scrollbar -mt-2 border-t border-black/5">
                    <div class="flex justify-between w-full">
                        <?php
                        // Reuse $links if available, otherwise redefine
                        if (isset($links)) {
                            foreach ($links as $path => $label):
                                $isActive = ($currentPage == $path || ($currentPage == '' && $path == 'index'));
                                ?>
                                <a href="/<?php echo $path; ?>"
                                   hx-get="/<?php echo $path; ?>" 
                                   hx-target="#main-content" 
                                   hx-push-url="true" 
                                   hx-select="#main-content"
                                    class="py-3 px-2 text-[11px] font-bold uppercase tracking-[0.15em] transition-all duration-300 border-b-2 text-center flex-1 
                                      <?php echo $isActive ? 'text-emerald-950 border-emerald-950' : 'text-gray-400 border-transparent hover:text-emerald-700'; ?>">
                                    <?php echo $label; ?>
                                </a>
                                <?php
                            endforeach;
                        }
                        ?>
                    </div>
                </nav>
            </div>
        </header>

    <?php 
        $barClasses = $isDetailPage 
            ? "fixed top-24 right-4 w-auto rounded-full shadow-2xl border border-white/10 px-4 h-9 z-[110]" 
            : "relative w-full border-b border-white/5 h-8 z-[90]";
    ?>
    <!-- Adaptive Countdown Bar -->
    <div id="countdown-bar" class="glass-dark <?php echo $barClasses; ?>"
        style="animation-delay: 0.3s;">
        <div class="<?php echo $isDetailPage ? '' : 'max-w-7xl mx-auto px-4 sm:px-6 lg:px-8'; ?>">
            <div class="flex justify-between items-center <?php echo $isDetailPage ? 'h-9 space-x-6' : 'h-8'; ?>">
                <div class="flex items-center space-x-4">
                    <span class="relative flex h-1.5 w-1.5">
                        <span
                            class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-1.5 w-1.5 bg-emerald-500"></span>
                    </span>
                    <span class="<?php echo $isDetailPage ? 'text-[8px]' : 'text-[9px]'; ?> font-black uppercase tracking-widest text-emerald-100">
                        <span class="countdown-label <?php echo $isDetailPage ? 'hidden md:inline' : ''; ?>">Upcoming Jamaat:</span>
                        <span id="header-next-name" class="ml-1 text-white">Loading...</span>
                        <span id="header-next-time" class="ml-2 text-emerald-200"></span>
                    </span>
                </div>
                <div class="flex items-center space-x-2">
                    <i class="fas fa-clock <?php echo $isDetailPage ? 'text-[8px]' : 'text-[9px]'; ?> text-emerald-400/50"></i>
                    <span class="<?php echo $isDetailPage ? 'text-[9px]' : 'text-[10px]'; ?> font-mono font-bold text-white tracking-widest"
                        id="header-countdown-timer">00:00:00</span>
                </div>
            </div>
        </div>
    </div>
    </div>

    <!-- Passing Prayer Data to JS -->
    <?php
    $prayerData = getPrayerTimes();
    ?>
    <script>
        const prayers = <?php echo json_encode($prayerData); ?>;
        // Helper function to parse time string like "5:00 PM" or "05:00 PM"
function parseTimeToDate(timeStr, baseDate) {
    // Create a copy of the base date
    const targetDate = new Date(baseDate);
    
    // Parse time like "5:00 PM" or "05:00 AM"
    const timeMatch = timeStr.match(/(\d+):(\d+)\s*(AM|PM)/i);
    if (!timeMatch) return null;
    
    let hours = parseInt(timeMatch[1]);
    const minutes = parseInt(timeMatch[2]);
    const ampm = timeMatch[3].toUpperCase();
    
    // Convert to 24-hour format
    if (ampm === 'PM' && hours !== 12) {
        hours += 12;
    } else if (ampm === 'AM' && hours === 12) {
        hours = 0;
    }
    
    targetDate.setHours(hours, minutes, 0, 0);
    return targetDate;
}

function updateCountdown() {
    if (!prayers || prayers.length === 0) return;

    const now = new Date();
    let nextPrayer = null;
    let minDiff = Infinity;

    prayers.forEach(p => {
        // Use the helper function instead of direct Date parsing
        let pTime = parseTimeToDate(p.time, now);
        
        if (!pTime) return;
        
        let diff = pTime - now;

        // If this prayer time has passed today, add 24 hours
        if (diff < 0) {
            pTime = new Date(pTime.getTime() + (24 * 60 * 60 * 1000));
            diff = pTime - now;
        }

        if (diff < minDiff) {
            minDiff = diff;
            nextPrayer = p;
        }
    });

    if (nextPrayer) {
        const nameEl = document.getElementById('header-next-name');
        const timeEl = document.getElementById('header-next-time');
        const timerEl = document.getElementById('header-countdown-timer');

        if (nameEl) nameEl.innerText = nextPrayer.name;
        if (timeEl) timeEl.innerText = nextPrayer.time;

        const h = Math.floor(minDiff / 3600000);
        const m = Math.floor((minDiff % 3600000) / 60000);
        const s = Math.floor((minDiff % 60000) / 1000);
        if (timerEl) timerEl.innerText = `${h.toString().padStart(2, '0')}:${m.toString().padStart(2, '0')}:${s.toString().padStart(2, '0')}`;
    }
}
        // Header scroll interaction (Removed old listener, now managed in app.js)

        setInterval(updateCountdown, 1000);
        updateCountdown();

        // HTMX: Update active link and re-initialize components
        document.body.addEventListener('htmx:afterSettle', function(evt) {
            // Get the first segment of the path (e.g., 'article', 'event', 'about')
            let segments = window.location.pathname.split('/').filter(s => s !== '');
            let baseSegment = segments[0] || 'index';
            if (baseSegment.endsWith('.php')) baseSegment = baseSegment.replace('.php', '');
            
            // Map detail paths back to their parent list pages
            let activeMapping = {
                'article': 'articles',
                'event': 'events',
                'category': 'events'
            };
            let targetNav = activeMapping[baseSegment] || baseSegment;

            // Update desktop nav
            document.querySelectorAll('nav.hidden.md\\:flex .nav-link').forEach(link => {
                const linkPath = link.getAttribute('href').replace(/^\//, '').replace('.php', '');
                if (linkPath === targetNav || (linkPath === 'index' && targetNav === '')) {
                    link.classList.add('active-link');
                } else {
                    link.classList.remove('active-link');
                }
            });

            // Update mobile nav
            document.querySelectorAll('nav.md\\:hidden a').forEach(link => {
                const linkPath = link.getAttribute('href').replace(/^\//, '').replace('.php', '');
                if (linkPath === targetNav || (linkPath === 'index' && targetNav === '')) {
                    link.classList.add('text-emerald-950', 'border-emerald-950');
                    link.classList.remove('text-gray-400', 'border-transparent');
                } else {
                    link.classList.remove('text-emerald-950', 'border-emerald-950');
                    link.classList.add('text-gray-400', 'border-transparent');
                }
            });

            // Scroll to top on page change
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });

        // Global HTMX Error Handling
        document.body.addEventListener('htmx:responseError', function(evt) {
            console.error('HTMX Error:', evt.detail.xhr.status, evt.detail.xhr.statusText);
            // Fallback to normal navigation on severe errors
            if (evt.detail.xhr.status >= 400) {
                window.location.href = evt.detail.pathInfo.requestPath;
            }
        });

        document.body.addEventListener('htmx:sendError', function(evt) {
            console.warn('Network error detected. Retrying with standard navigation...');
            window.location.href = evt.detail.pathInfo.requestPath;
        });
    </script>

    <main id="main-content" class="animate-page">
