# IUT-SIKS Web Portal

Official web portal for the Society of Islamic Knowledge Seekers (SIKS) at Islamic University of Technology.

## 🚀 Deployment Instructions (CWP)

### 1. Database Setup
1.  Log in to your **CentOS Web Panel (CWP)**.
2.  Go to **SQL Services** -> **MySQL Manager**.
3.  Create a new database named `siks_db` (or your preferred name).
4.  Create a new database user and assign it to the database with all privileges.
5.  Open **phpMyAdmin** and import the `setup.sql` file located in the root directory.

### 2. Configuration
**CRITICAL:** You must update the database credentials in `includes/db.php`.
1.  Open `includes/db.php`.
2.  Replace the placeholders with your actual database host, name, username, and password:
    ```php
    $host = 'localhost';
    $dbname = 'your_database_name';
    $username = 'your_database_user';
    $password = 'your_database_password';
    ```

### 3. Verification
To check if the website is working fine:
1.  Visit the homepage. You should see the **Masjid-e-Zainab IUT** prayer schedule.
2.  Check the **Daily Reminders** section; it should fetch a random Ayat and Hadith on every refresh.
3.  Navigate to **Events** and click "View Details" on an event to ensure the detail page loads correctly.
4.  Navigate to **About** and scroll to the bottom to see the "Past Events" carousel.

---

## 🔐 Admin Panel

Access the admin panel at: `http://your-domain.com/admin`

### Default Credentials:
- **Username**: `admin`
- **Password**: `admin123`

> [!WARNING]
> Change the default password immediately after logging in via the Articles or Events management pages (if you implement a password change feature) or directly in the `admins` table in phpMyAdmin using `PASSWORD_DEFAULT` hash.

### Features:
- **Prayers**: Update Jamaat timings for the university mosque.
- **Events**: Manage upcoming and past society events with full details.
- **About Page**: Update the Vision, Mission, and main description.
- **Articles**: Write and publish community articles.

---

## 🛠 Tech Stack
- **Backend**: PHP 8.0+
- **Database**: MySQL (PDO)
- **Frontend**: Tailwind CSS (CDN), FontAwesome
- **APIs**: Al Quran Cloud (Ayat), fawazahmed0 Hadith API
