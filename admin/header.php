<?php
require_once 'auth.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle ?? 'Admin Dashboard'; ?> - SIKS</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #059669;
            --primary-dark: #065f46;
            --primary-light: #ecfdf5;
            --secondary: #1e293b;
            --bg: #f8fafc;
            --card: #ffffff;
            --text: #0f172a;
            --text-muted: #64748b;
            --border: #e2e8f0;
            --danger: #ef4444;
            --success: #10b981;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: var(--bg); color: var(--text); line-height: 1.5; }

        /* Layout */
        .admin-container { display: flex; min-height: 100vh; }
        
        /* Sidebar */
        .sidebar { width: 260px; background: var(--secondary); color: white; padding: 2rem 1rem; flex-shrink: 0; position: sticky; top: 0; height: 100vh; }
        .sidebar-logo { font-family: 'Outfit', sans-serif; font-size: 1.5rem; font-bold; margin-bottom: 2.5rem; padding: 0 1rem; color: var(--primary); display: flex; align-items: center; }
        .sidebar-logo i { margin-right: 0.75rem; }
        .nav-item { display: flex; align-items: center; padding: 0.875rem 1rem; color: #cbd5e1; text-decoration: none; border-radius: 0.5rem; margin-bottom: 0.25rem; transition: all 0.2s; font-weight: 500; }
        .nav-item:hover { background: rgba(255,255,255,0.05); color: white; }
        .nav-item.active { background: var(--primary); color: white; }
        .nav-item i { width: 1.25rem; margin-right: 1rem; font-size: 1.1rem; }

        /* Main Content */
        .main-content { flex: 1; padding: 2.5rem; max-width: 1200px; margin: 0 auto; }
        .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2.5rem; }
        .page-title { font-family: 'Outfit', sans-serif; font-size: 1.875rem; font-weight: 700; color: var(--secondary); }

        /* Components */
        .card { background: var(--card); border: 1px solid var(--border); border-radius: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.05); padding: 2rem; margin-bottom: 2rem; }
        .form-group { margin-bottom: 1.5rem; }
        label { display: block; font-weight: 600; font-size: 0.875rem; color: var(--secondary); margin-bottom: 0.5rem; }
        input[type="text"], input[type="date"], input[type="number"], input[type="password"], textarea, select {
            width: 100%; padding: 0.75rem 1rem; border: 1px solid var(--border); border-radius: 0.5rem; font-family: inherit; font-size: 0.9375rem; transition: all 0.2s; background: #fff;
        }
        input:focus, textarea:focus, select:focus { outline: none; border-color: var(--primary); ring: 2px solid var(--primary-light); }
        
        .btn { display: inline-flex; align-items: center; justify-content: center; padding: 0.75rem 1.5rem; border-radius: 0.5rem; font-weight: 600; font-size: 0.9375rem; cursor: pointer; transition: all 0.2s; text-decoration: none; border: none; gap: 0.5rem; }
        .btn-primary { background: var(--primary); color: white; }
        .btn-primary:hover { background: var(--primary-dark); transform: translateY(-1px); box-shadow: 0 4px 12px rgba(5, 150, 105, 0.2); }
        .btn-secondary { background: white; border: 1px solid var(--border); color: var(--secondary); }
        .btn-secondary:hover { background: var(--bg); border-color: #cbd5e1; }
        .btn-danger { background: var(--danger); color: white; }
        .btn-danger:hover { background: #dc2626; }
        
        .alert { padding: 1rem 1.25rem; border-radius: 0.75rem; margin-bottom: 2rem; font-weight: 500; font-size: 0.9375rem; display: flex; align-items: center; gap: 0.75rem; }
        .alert-success { background: #f0fdf4; color: #166534; border: 1px solid #bbf7d0; }
        .alert-error { background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; }

        /* Table */
        .table-container { background: white; border-radius: 1rem; border: 1px solid var(--border); overflow: hidden; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #f8fafc; padding: 1rem 1.5rem; text-align: left; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; tracking: 0.05em; color: var(--text-muted); border-bottom: 1px solid var(--border); }
        td { padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--border); font-size: 0.9375rem; color: var(--secondary); }
        tr:last-child td { border-bottom: none; }
        
        .badge { display: inline-flex; align-items: center; padding: 0.25rem 0.75rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 700; }
        .badge-success { background: #f0fdf4; color: #166534; }
        .badge-blue { background: #eff6ff; color: #1e40af; }
        
        .img-preview-sm { width: 48px; height: 48px; border-radius: 0.5rem; object-fit: cover; }
        
        /* Grid */
        .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; }
        
        @media (max-width: 768px) {
            .admin-container { flex-direction: column; }
            .sidebar { 
                width: 100%; height: auto; position: relative; padding: 1rem;
                display: none; /* Hidden by default on mobile */
            }
            .sidebar.active { display: block; }
            .mobile-toggle { 
                display: flex; align-items: center; justify-content: space-between; 
                padding: 1rem 1.5rem; background: var(--secondary); color: white;
            }
            .grid-2 { grid-template-columns: 1fr; }
            .main-content { padding: 1.5rem; }
        }
        @media (min-width: 769px) {
            .mobile-toggle { display: none; }
            .sidebar { display: block !important; }
        }
    </style>
    <script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
    <script>
        /**
         * Robust UTF-8 aware Base64 encoding
         */
        function b64EncodeUnicode(str) {
            return btoa(encodeURIComponent(str).replace(/%([0-9A-F]{2})/g,
                function(match, p1) {
                    return String.fromCharCode('0x' + p1);
            }));
        }

        function toggleSidebar() {
            const sidebar = document.querySelector('.sidebar');
            sidebar.classList.toggle('active');
        }

        // Auto-apply base64 to designated textareas on form submit
        // Skips textareas managed by TinyMCE (they sync their own content)
        document.addEventListener('submit', function(e) {
            const forms = document.querySelectorAll('form[data-b64-bypass]');
            forms.forEach(form => {
                if (e.target === form) {
                    const targets = form.querySelectorAll('[data-b64-target]');
                    targets.forEach(t => {
                        // Skip if TinyMCE is managing this textarea
                        if (typeof tinymce !== 'undefined' && tinymce.get(t.id)) {
                            return;
                        }
                        if (t.value) {
                            t.value = b64EncodeUnicode(t.value);
                        }
                    });
                }
            });
        });
    </script>
</head>
<body>
    <div class="mobile-toggle">
        <div class="sidebar-logo" style="margin-bottom: 0; padding: 0;">
            <i class="fas fa-shield-alt"></i> SIKS Admin
        </div>
        <button onclick="toggleSidebar()" style="background: none; border: none; color: white; font-size: 1.5rem;">
            <i class="fas fa-bars"></i>
        </button>
    </div>
    <div class="admin-container">
        <div class="sidebar">
            <div class="sidebar-logo">
                <i class="fas fa-shield-alt"></i> SIKS Admin
            </div>
            <nav>
                <a href="index.php" class="nav-item <?php echo $activeNav === 'dashboard' ? 'active' : ''; ?>">
                    <i class="fas fa-th-large"></i> Dashboard
                </a>
                <a href="manage_hero.php" class="nav-item <?php echo $activeNav === 'hero' ? 'active' : ''; ?>">
                    <i class="fas fa-images"></i> Hero Carousel
                </a>
                <a href="manage_events.php" class="nav-item <?php echo $activeNav === 'events' ? 'active' : ''; ?>">
                    <i class="fas fa-calendar-alt"></i> Events
                </a>
                <a href="manage_articles.php" class="nav-item <?php echo $activeNav === 'articles' ? 'active' : ''; ?>">
                    <i class="fas fa-pen-nib"></i> Articles
                </a>
                <a href="manage_library.php" class="nav-item <?php echo $activeNav === 'library' ? 'active' : ''; ?>">
                    <i class="fas fa-book"></i> Library Management
                </a>
                <a href="manage_about.php" class="nav-item <?php echo $activeNav === 'about' ? 'active' : ''; ?>">
                    <i class="fas fa-info-circle"></i> About Content
                </a>
                <a href="manage_prayers.php" class="nav-item <?php echo $activeNav === 'prayers' ? 'active' : ''; ?>">
                    <i class="fas fa-clock"></i> Prayer Times
                </a>
                <div style="margin-top: 2rem; padding: 0 1rem; font-size: 0.75rem; font-weight: 700; color: #64748b; text-transform: uppercase;">Settings</div>
                <a href="change_password.php" class="nav-item <?php echo $activeNav === 'security' ? 'active' : ''; ?>">
                    <i class="fas fa-lock"></i> Security
                </a>
                <a href="logout.php" class="nav-item" style="color: #f87171;">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </nav>
        </div>
        <div class="main-content">
