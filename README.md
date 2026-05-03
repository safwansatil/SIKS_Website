# SIKS Web Portal - Phase 2

Welcome to the SIKS (Society of Islamic Knowledge Seekers) Web Portal repository. This project serves as the official platform for the society at the Islamic University of Technology (IUT), providing students with access to articles, event updates, and daily Islamic reminders.

## 🚀 Overview
This application is a server-side rendered web portal built with a lightweight PHP backend and a highly responsive, modern frontend using Tailwind CSS. 

Phase 2 focuses on an elevated UI/UX experience, robust administrative controls, and an immersive dark-theme aesthetic designed to look professional and premium.

## 💻 Tech Stack
- **Backend:** PHP 7.4+ (PDO for secure database interactions)
- **Database:** MySQL
- **Frontend:** HTML5, Tailwind CSS (via CDN), Vanilla JavaScript
- **Icons:** FontAwesome
- **Image Processing:** PHP GD Library (for auto-resizing hero images)

## 📁 Directory Structure
```
SIKS_Website/
├── admin/                  # Secure administrative dashboard
│   ├── index.php           # Admin dashboard overview
│   ├── login.php           # Admin authentication
│   ├── auth.php            # Session validation logic
│   └── manage_*.php        # CRUD interfaces for entities
├── assets/                 # Static assets (images, logos)
├── css/                    # Custom stylesheets (styles.css)
├── includes/               # Reusable backend components
│   ├── config.php          # Core logic, constants, file upload handler
│   ├── db.php              # PDO Database connection
│   ├── header.php          # Global site header & navigation
│   └── footer.php          # Global site footer
├── uploads/                # User-uploaded content (articles, events, hero)
├── index.php               # Public homepage (Hero, Jamaat times, Events)
├── articles.php            # Public article listing with live search
├── article.php             # Individual article view
├── events.php              # Public event listing
├── event_details.php       # Individual event view with lightbox gallery
├── about.php               # Society information
├── setup.sql               # Database schema and initial seed data
└── README.md               # Project documentation
```

## 🛠️ Setup & Installation

1. **Prerequisites:**
   Ensure you have a local server environment running (e.g., XAMPP, MAMP, or Laravel Valet) with PHP 7.4+ and MySQL. GD library must be enabled in `php.ini` for image processing.

2. **Clone the Repository:**
   ```bash
   git clone https://github.com/safwansatil/SIKS_Website.git
   cd SIKS_Website
   git checkout siks-phase-2
   ```

3. **Database Setup:**
   - Create a new MySQL database named `siks_local` (or your preferred name).
   - Import the `setup.sql` file into this database to create the necessary tables (`admins`, `articles`, `events`, `event_images`, `hero_slides`, `prayer_times`, `event_categories`).
   
4. **Database Updates (Migration):**
   If you are updating an existing installation (e.g., in CWP), follow these steps:
   - Open your hosting control panel (CWP) and go to **phpMyAdmin**.
   - Select your database and go to the **SQL** tab.
   - Open `setup.sql` from this repository.
   - Copy the `CREATE TABLE IF NOT EXISTS` and `ALTER TABLE` commands from the bottom of the file (Migration Helpers section).
   - Paste them into the phpMyAdmin SQL box and click **Go**.
   - This will add any new columns or tables (like `event_categories`) without affecting your existing data.

5. **Configuration:**
   - Open `includes/db.php`.
   - Update the PDO connection string with your local database credentials:
     ```php
     $host = '127.0.0.1';
     $db   = 'siks_local';
     $user = 'root';
     $pass = ''; // Add your password here
     ```

5. **Run the Application:**
   Serve the project directory using your local server. For example:
   ```bash
   php -S localhost:8000
   ```
   Visit `http://localhost:8000` in your browser.

6. **Admin Access:**
   Navigate to `http://localhost:8000/admin`. 
   *(Default credentials depend on your `setup.sql` seed data. Ensure you create an admin account or insert a hashed password directly into the `admins` table).*

## 🌟 Key Features & Phase 2 Highlights

*   **Immersive Hero Section:** Full-viewport (`h-screen`) dark gradient aesthetic with auto-resizing images. Uploaded hero images are automatically center-cropped to `1920x1080` via PHP GD for a consistent look.
*   **Dynamic Event Management:** 
    *   Subtle, professional UI components.
    *   Interactive, JS-powered lightbox gallery for event photos.
    *   Auto-scrolling "Recent Events" carousel on the homepage.
*   **Article Live Search:** A seamless, client-side JavaScript search bar on the Articles page to filter content instantly by title, excerpt, or author.
*   **Robust Media Handling:** Secure file upload logic in `config.php` that verifies MIME types and forces safe file extensions to prevent upload vulnerabilities. Admins can seamlessly upload, replace, or completely remove cover images.
*   **Auto-linking:** Event descriptions automatically detect and convert URLs into clickable, styled hyperlinks while maintaining XSS protection.

## 🔒 Security Notes
*   All database queries utilize **PDO Prepared Statements** to prevent SQL injection.
*   All user-generated output is escaped using `htmlspecialchars()` to prevent XSS.
*   File uploads are verified using `finfo` magic bytes, and extensions are mapped safely based on MIME type.
*   The admin panel is protected by session-based authentication validated on every protected route via `auth.php`.
