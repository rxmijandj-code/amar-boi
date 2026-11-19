CREATE TABLE IF NOT EXISTS content (
 id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
 title VARCHAR(220) NOT NULL, category VARCHAR(40) NOT NULL DEFAULT 'quran',
 arabic LONGTEXT NULL, translation LONGTEXT NULL, surah VARCHAR(120) NULL,
 audio_url TEXT NULL, summary TEXT NULL, is_visible TINYINT(1) NOT NULL DEFAULT 1,
 sort_order INT NOT NULL DEFAULT 0, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
 INDEX idx_content_visible_order (is_visible,sort_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS slides (
 id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY, eyebrow VARCHAR(160) NULL,
 title VARCHAR(220) NOT NULL, subtitle TEXT NULL, image_url TEXT NULL, link_url TEXT NULL,
 is_visible TINYINT(1) NOT NULL DEFAULT 1, sort_order INT NOT NULL DEFAULT 0,
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE IF NOT EXISTS settings (
 setting_key VARCHAR(80) PRIMARY KEY, setting_value LONGTEXT NULL,
 updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
INSERT INTO settings(setting_key,setting_value) VALUES
 ('site_name','আমার বই'),('tagline','জ্ঞান • আমল • জীবন'),('logo_url','assets/logo.png'),
 ('reciter_name',''),('reciter_audio_url',''),('about_text','কুরআন, দোয়া, হাদিস ও ইসলামিক জ্ঞান শেখার একটি সহজ প্ল্যাটফর্ম।')
ON DUPLICATE KEY UPDATE setting_value=VALUES(setting_value);
