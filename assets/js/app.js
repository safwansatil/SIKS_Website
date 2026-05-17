/**
 * SIKS Web Portal - Modern UX Logic
 */

// Toast Notification System
function showToast(message, type = 'success') {
    const container = document.getElementById('toast-container');
    if (!container) return;

    const toast = document.createElement('div');
    toast.className = 'toast';
    
    const icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';
    const color = type === 'success' ? 'text-emerald-400' : 'text-red-400';
    
    toast.innerHTML = `
        <i class="fas ${icon} ${color}"></i>
        <span>${message}</span>
    `;

    container.appendChild(toast);

    // Remove toast after 3 seconds
    setTimeout(() => {
        toast.classList.add('toast-out');
        setTimeout(() => {
            toast.remove();
        }, 500);
    }, 3000);
}

// Share Functionality
function copyToClipboard(text, message = 'Link copied to clipboard!') {
    const url = text || window.location.href;
    if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(url).then(() => {
            showToast(message);
        }).catch(err => {
            console.error('Clipboard error:', err);
            fallbackCopy(url, message);
        });
    } else {
        fallbackCopy(url, message);
    }
}

function fallbackCopy(url, message) {
    const textArea = document.createElement("textarea");
    textArea.value = url;
    document.body.appendChild(textArea);
    textArea.select();
    try {
        document.execCommand('copy');
        showToast(message);
    } catch (err) {
        prompt('Press Ctrl+C to copy this link:', url);
    }
    document.body.removeChild(textArea);
}

// Reading Progress Bar
function updateReadingProgress() {
    const progressBar = document.getElementById('reading-progress-bar');
    if (!progressBar) return;

    const winScroll = document.documentElement.scrollTop;
    const height = document.documentElement.scrollHeight - document.documentElement.clientHeight;
    if (height <= 0) {
        progressBar.style.width = '0%';
        return;
    }

    const scrolled = (winScroll / height) * 100;
    progressBar.style.width = scrolled + '%';

    // Optional color change at 100%
    if (scrolled >= 99) {
        progressBar.classList.remove('bg-emerald-600');
        progressBar.classList.add('bg-emerald-400');
    } else {
        progressBar.classList.add('bg-emerald-600');
        progressBar.classList.remove('bg-emerald-400');
    }
}

// Back to Top Visibility
function handleScrollTopButton() {
    const backToTop = document.getElementById('back-to-top');
    if (!backToTop) return;

    if (window.scrollY > 300) {
        backToTop.classList.remove('opacity-0', 'invisible', 'translate-y-10');
        backToTop.classList.add('opacity-100', 'visible', 'translate-y-0');
    } else {
        backToTop.classList.add('opacity-0', 'invisible', 'translate-y-10');
        backToTop.classList.remove('opacity-100', 'visible', 'translate-y-0');
    }
}

// Initialize Global Listeners
function initUX() {
    window.addEventListener('scroll', () => {
        updateReadingProgress();
        handleScrollTopButton();
        handleHeaderScroll();
    });

    const backToTop = document.getElementById('back-to-top');
    if (backToTop) {
        backToTop.addEventListener('click', () => {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }

    // Modern Padding Fix: Watch the header height and adjust body padding automatically
    const header = document.getElementById('main-header-container');
    if (header) {
        const resizeObserver = new ResizeObserver(entries => {
            for (let entry of entries) {
                const height = entry.contentRect.height;
                // Add a small buffer if the bar is hidden but still in DOM
                document.body.style.paddingTop = height + 'px';
            }
        });
        resizeObserver.observe(header);
    }

    updateAdaptiveUI();
    handleHeaderScroll();
}

function handleHeaderScroll() {
    const navbar = document.getElementById('main-navbar');
    const bar = document.getElementById('countdown-bar');
    if (!navbar) return;

    const isDetail = document.body.getAttribute('data-page-type') === 'detail';

    if (window.scrollY > 20) {
        navbar.classList.add('header-solid');
    } else {
        navbar.classList.remove('header-solid');
    }
}

function updateAdaptiveUI() {
    const bar = document.getElementById('countdown-bar');
    if (!bar) return;

    const labels = bar.querySelectorAll('.countdown-label');
    const isDetail = document.body.getAttribute('data-page-type') === 'detail';

    if (isDetail) {
        // Bubble mode: Use !important to override any other state
        bar.style.cssText = `
            position: fixed !important;
            top: 100px !important;
            right: 20px !important;
            width: auto !important;
            height: 36px !important;
            border-radius: 99px !important;
            z-index: 1000 !important;
            display: flex !important;
            opacity: 1 !important;
            transform: none !important;
            padding: 0 16px !important;
        `;
        const inner = bar.querySelector('div');
        const content = inner?.querySelector('div');
        if (inner) inner.className = '';
        if (content) content.className = 'flex justify-between items-center h-9 space-x-6';
        labels?.forEach(l => l.classList.add('hidden'));
    } else {
        // Bar mode: Reset inline styles to let classes take over
        bar.style.cssText = '';
        bar.className = 'glass-dark relative w-full border-b border-white/5 h-8 z-[90]';
        const inner = bar.querySelector('div');
        const content = inner?.querySelector('div');
        if (inner) inner.className = 'max-w-7xl mx-auto px-4 sm:px-6 lg:px-8';
        if (content) content.className = 'flex justify-between items-center h-8';
        labels?.forEach(l => l.classList.remove('hidden'));
    }
}

    // Reset Progress Bar on new page load
    document.body.addEventListener('htmx:afterSettle', (evt) => {
        const title = evt.detail.target.querySelector('h1')?.innerText;
        if (title) {
            document.title = `${title} | SIKS`;
        }

        // Update Page Type for Adaptive UI
        const isDetail = window.location.pathname.includes('/article/') || window.location.pathname.includes('/event/');
        document.body.setAttribute('data-page-type', isDetail ? 'detail' : 'main');
        
        const progressBar = document.getElementById('reading-progress-bar');
        if (progressBar) progressBar.style.width = '0%';
        
        window.scrollTo({ top: 0, behavior: 'instant' });
        updateAdaptiveUI();
    });

    // Search Focus Shortcut (/)
    document.addEventListener('keydown', (e) => {
        if (e.key === '/' && document.activeElement.tagName !== 'INPUT' && document.activeElement.tagName !== 'TEXTAREA') {
            e.preventDefault();
            const searchInput = document.querySelector('input[name="search"]');
            if (searchInput) {
                searchInput.focus();
                showToast('Search focused', 'success');
            }
        }
    });

    // Smooth scroll for internal links
    document.addEventListener('click', (e) => {
        const anchor = e.target.closest('a');
        if (anchor && anchor.hash && anchor.origin === window.location.origin) {
            const target = document.querySelector(anchor.hash);
            if (target) {
                e.preventDefault();
                target.scrollIntoView({ behavior: 'smooth' });
            }
        }
    });
}

// Run on initial load
document.addEventListener('DOMContentLoaded', initUX);
