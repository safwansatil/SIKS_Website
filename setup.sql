-- SIKS Website Database Setup

CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Default admin: admin / admin123
INSERT INTO admins (username, password) VALUES ('admin', '$2y$12$g1NRTXatbSFgQe5Y0J7eAOkB9l73UDGbwD0ojNQ77jbkD.ZZjzMje') ON DUPLICATE KEY UPDATE username=username;

CREATE TABLE IF NOT EXISTS prayer_times (
    id INT AUTO_INCREMENT PRIMARY KEY,
    prayer_name VARCHAR(50) NOT NULL,
    prayer_time VARCHAR(20) NOT NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Initial Prayer Times
INSERT INTO prayer_times (prayer_name, prayer_time) VALUES 
('Fajr', '5:00 AM'),
('Dhuhr', '1:15 PM'),
('Asr', '4:30 PM'),
('Maghrib', '6:30 PM'),
('Isha', '8:00 PM'),
('Jumuah', '1:30 PM');

CREATE TABLE IF NOT EXISTS events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    event_date DATE NOT NULL,
    event_time VARCHAR(50),
    venue VARCHAR(255) NOT NULL,
    short_description TEXT,
    description TEXT,
    logo VARCHAR(255), -- Icon name or image path
    tag VARCHAR(50),
    category VARCHAR(50) DEFAULT 'Community',
    is_past BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS event_images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    event_id INT,
    image_path VARCHAR(255) NOT NULL,
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS articles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    writer VARCHAR(100) NOT NULL,
    description TEXT,
    last_edited TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS about_content (
    id INT AUTO_INCREMENT PRIMARY KEY,
    type VARCHAR(50), -- 'title', 'subtitle', 'card'
    title VARCHAR(255),
    description TEXT,
    sort_order INT DEFAULT 0
);

-- Initial About Content
INSERT INTO about_content (type, title, description, sort_order) VALUES 
('title', 'About IUT SIKS', 'The Society of Islamic Knowledge Seekers is a student-led organization at the Islamic University of Technology, dedicated to fostering a balanced environment of spiritual growth and academic excellence.', 1),
('card', 'Our Vision', 'To be a leading society that empowers students to integrate Islamic principles into their professional and personal lives, creating a generation of technically proficient and spiritually grounded leaders.', 2),
('card', 'Our Mission', 'We strive to provide platforms for spiritual learning, community service, and ethical development through organized events, lectures, and interactive sessions.', 3);
