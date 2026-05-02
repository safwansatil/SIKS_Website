-- Create database for local testing
CREATE DATABASE IF NOT EXISTS siks_local CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE siks_local;

-- Admins table
CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO admins (username, password) VALUES ('admin', '$2y$12$g1NRTXatbSFgQe5Y0J7eAOkB9l73UDGbwD0ojNQ77jbkD.ZZjzMje');

-- Prayer Times (from live site)
CREATE TABLE IF NOT EXISTS prayer_times (
    id INT AUTO_INCREMENT PRIMARY KEY,
    prayer_name VARCHAR(50) NOT NULL,
    prayer_time VARCHAR(20) NOT NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO prayer_times (prayer_name, prayer_time) VALUES 
('Fajr', '5:00 AM'),
('Dhuhr', '1:20 PM'),
('Asr', '5:00 PM'),
('Maghrib', '6:30 PM'),
('Isha', '8:00 PM'),
('Jumuah', '1:30 PM');

-- Events
CREATE TABLE IF NOT EXISTS events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    event_date DATE NOT NULL,
    event_time VARCHAR(50),
    venue VARCHAR(255) NOT NULL,
    short_description TEXT,
    description LONGTEXT,
    logo VARCHAR(255),
    cover_image VARCHAR(255),
    tag VARCHAR(50),
    category VARCHAR(50) DEFAULT 'Community',
    is_past BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS event_images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    event_id INT,
    image_path VARCHAR(255) NOT NULL,
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Event from live site (past event)
INSERT INTO events (name, event_date, event_time, venue, short_description, description, logo, tag, category, is_past) VALUES 
('Tajwar dekhte pas?', '2026-11-15', '10:00 AM', 'IUT', 'A reflection session exploring the beauty of Tajweed and Quranic recitation.', 'A reflection session exploring the beauty of Tajweed and Quranic recitation at the Islamic University of Technology campus. Join us for an enriching experience of learning and spiritual growth.', 'fa-book-quran', 'Quran, Tajweed', 'Community', 1);

-- Additional events for a fuller demo
INSERT INTO events (name, event_date, event_time, venue, short_description, description, logo, tag, category, is_past) VALUES 
('IUT SIKS Futsal Cup 2026', '2026-06-10', '4:00 PM', 'IUT Main Ground', 'Inter-departmental futsal tournament organized by IUT SIKS.', 'The annual IUT SIKS Futsal Cup brings together departments in a spirit of brotherhood and sportsmanship. Teams compete in a knockout format over the course of two weeks. All IUT students are welcome to participate and spectate.', 'fa-futbol', 'Sports, Futsal, Tournament', 'Sports', 0),
('Ramadan Iftar Mahfil', '2026-03-15', '6:15 PM', 'Masjid-e-Zainab IUT', 'Community iftar gathering for IUT students and faculty during Ramadan.', 'IUT SIKS organized a community iftar gathering at the university mosque during the blessed month of Ramadan. The event featured a collective dua session, a brief reminder on the virtues of Ramadan, and a shared iftar meal for all attendees.', 'fa-moon', 'Ramadan, Iftar, Community', 'Community', 1),
('Quran Recitation Competition', '2026-07-20', '10:00 AM', 'IUT Auditorium', 'Annual Quran recitation and memorization competition for IUT students.', 'The annual Quran Recitation Competition is one of the flagship events of IUT SIKS. Participants are judged on their Tajweed, pronunciation, and melodious recitation. Categories include memorization (Hifz) and recitation (Tilawah). Prizes and certificates are awarded to the top performers.', 'fa-book-quran', 'Quran, Competition, Tilawah', 'Community', 0),
('Islamic Calligraphy Workshop', '2026-02-20', '2:00 PM', 'IUT Fine Arts Room', 'A hands-on workshop on Arabic calligraphy for beginners and enthusiasts.', 'IUT SIKS hosted a calligraphy workshop where participants learned the fundamentals of Naskh and Thuluth scripts. Materials were provided and each participant created their own calligraphic piece by the end of the session.', 'fa-pen-nib', 'Workshop, Calligraphy, Art', 'Community', 1),
('Knowledge Seekers Seminar: Fiqh of Worship', '2026-08-05', '11:00 AM', 'IUT Lecture Hall B', 'A seminar series on the practical fiqh of daily worship for university students.', 'Part of the Knowledge Seekers Seminar Series, this session covers the fiqh of Salah, Wudu, and other daily acts of worship. The session is led by a guest scholar and includes a Q&A portion for students to clarify common doubts.', 'fa-chalkboard-teacher', 'Seminar, Fiqh, Knowledge', 'Community', 0);

-- Articles (from live site)
CREATE TABLE IF NOT EXISTS articles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE,
    writer VARCHAR(100) NOT NULL,
    description LONGTEXT,
    cover_image VARCHAR(255),
    reading_time INT DEFAULT 1,
    last_edited TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO articles (title, slug, writer, description, reading_time) VALUES 
('The Bloody Plains of Badr', 'the-bloody-plains-of-badr', 'IUT SIKS Editorial', 'A fierce battle is raging between the followers of Mecca''s Abu Jahl and the companions of Muhammad, the Rasulullah (peace be upon him), from Medina. A mere 313 Muslims have stood up against 1,000 disbelievers. The worshippers of Lat, Manat, Uzza, and Hubal began to crumble.\n\nBy what power did a handful of destitute men emerge against a thousand elite warriors of Mecca, equipped with the most modern weaponry of that time? This remains a matter of research even today.\n\nThe Battle of Badr, fought on the 17th of Ramadan, 2 AH (March 13, 624 CE), stands as one of the most significant events in Islamic history. It was the first major military confrontation between the early Muslims and the Quraysh of Mecca.\n\nThe Muslims, though vastly outnumbered and poorly equipped, achieved a decisive victory through their unwavering faith in Allah, strategic leadership of the Prophet Muhammad (peace be upon him), and divine assistance. The Quran itself bears testimony to this event:\n\n\"And already had Allah given you victory at Badr while you were few in number. Then fear Allah; perhaps you will be grateful.\" (Quran 3:123)\n\nThe aftermath of Badr reshaped the political and spiritual landscape of the Arabian Peninsula. It established the Muslim community as a formidable force and served as a powerful demonstration that numbers and material strength alone do not determine the outcome of a struggle when faith and righteousness are on the other side.\n\nFor the students and members of IUT SIKS, the lessons of Badr are timeless: steadfastness in the face of adversity, reliance upon Allah, and the courage to stand for truth regardless of the odds.', 5);

-- About Content (from live site)
CREATE TABLE IF NOT EXISTS about_content (
    id INT AUTO_INCREMENT PRIMARY KEY,
    type VARCHAR(50),
    title VARCHAR(255),
    description LONGTEXT,
    image_path VARCHAR(255),
    sort_order INT DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO about_content (type, title, description, sort_order) VALUES 
('title', 'About IUT SIKS', 'There shall be a Society of Students to be known as the Islamic University of Technology Society of Islamic Knowledge Seekers, hereinafter called the IUT SIKS', 1),
('card', 'Our Vision', 'The motivation behind the function of IUT SIKS is deeply rooted in the guiding principles of the Qur''an and Sunnah. The organization draws inspiration from the timeless message of Surah Al-Asr, which emphasizes the importance of faith, righteous actions, truth, and patience. As Allah says: \"By time, indeed, mankind is in loss, Except for those who have believed and done righteous deeds and advised each other to truth and advised each other to patience.\" Surah Al-Asr (103:1-3) This verse serves as a reminder for IUT SIKS to focus on nurturing faith, fostering good deeds, and promoting truth and perseverance within the community.', 2),
('card', 'Aims', 'To create an environment where students can collectively seek authentic Islamic knowledge based on the Quran, Hadith, and works of renowned scholars. To inspire students to actively practice Islam, appreciate the blessings of being Muslim, and guide them to avoid un-Islamic actions. To instill a sense of responsibility, unity, and leadership in students as future Muslim leaders. To build a strong foundation for students, encouraging further study in Islamic and academic fields. These aims will be pursued in accordance with Islamic principles of justice, peace, and Taqwa (God-consciousness).', 3);

-- Hero Slides table (empty - user will upload images via admin)
CREATE TABLE IF NOT EXISTS hero_slides (
    id INT AUTO_INCREMENT PRIMARY KEY,
    image_path VARCHAR(255) NOT NULL,
    title VARCHAR(255),
    subtitle TEXT,
    sort_order INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
